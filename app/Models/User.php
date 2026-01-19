<?php

namespace App\Models;

use App\Http\Resources\Api\UserResource;
use App\Services\Unifonic\WhatsAppOtpService;
use App\Traits\SmsTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Jobs\SendEmailJob;
use App\Jobs\SendSms;
use App\Traits\HasAutoMedia;
use PhpParser\Node\Stmt\Const_;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property mixed country_code
 * @property mixed phone
 */
class User extends  Authenticatable implements HasMedia
{
    use Notifiable, HasApiTokens, SmsTrait, SoftDeletes, HasFactory, HasAutoMedia;

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_notify'   => 'boolean',
    ];

    protected $fillable = [
        'name',
        'country_code',
        'phone',
        'email',
        'password',
        'is_notify',
        'code',
        'code_expire',
        'wallet_balance',
        'city_id',
        'district_id',
        'type',
        'gender',
        'is_active',
        'is_blocked',
        'lat',
        'lng',
        'accept_orders'

    ];



     protected array $autoMedia = [
        // Single file: request field "image" -> collection "product_image"
        'image'   => 'user_image',
        // Multiple files: request field "gallery[]" -> collection "product_gallery"
        'gallery' => ['collection' => 'product_gallery', 'multiple' => true],

    ];
  public function scopeSearch($query, $searchArray = [])
{
    $searchArray = is_array($searchArray) ? $searchArray : [];

    $query->where(function ($q) use ($searchArray) {
        foreach ($searchArray as $key => $value) {
            if (is_null($value) || $value === '') continue;

            if (str_contains($key, '_id')) {
                $q->where($key, $value);
            } elseif ($key === 'created_at_min') {
                $q->whereDate('created_at', '>=', $value);
            } elseif ($key === 'created_at_max') {
                $q->whereDate('created_at', '<=', $value);
            } elseif ($key !== 'order') {
                $q->where($key, 'like', "%{$value}%");
            }
        }
    });

    $orderDirection = isset($searchArray['order']) ? $searchArray['order'] : 'DESC';

    return $query->orderBy('created_at', $orderDirection);
}

    public function rooms()
    {
        return $this->morphMany(RoomMember::class, 'memberable');
    }

    public function ownRooms()
    {
        return $this->morphMany(Room::class, 'createable');
    }

    public function joinedRooms()
    {
        return $this->morphMany(RoomMember::class, 'memberable')
            ->with('room')
            ->get()
            ->sortByDesc('room.last_message_id')
            ->pluck('room');
    }

    // Define media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile')
            ->singleFile()
            ->useFallbackUrl(asset('storage/images/default.png'))
            ->useFallbackPath(public_path('storage/images/default.png'));

      
    }

    // Define media conversions
    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->nonQueued(); // Process immediately
    }

   
    public function setPhoneAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['phone'] = fixPhone($value);
        }
    }

    public function setCountryCodeAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['country_code'] = fixPhone($value);
        }
    }

    public function getFullPhoneAttribute()
    {
        return $this->attributes['country_code'] . $this->attributes['phone'];
    }

    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('profile') ?: asset('storage/images/default.png');
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function replays()
    {
        return $this->morphMany(ComplaintReplay::class, 'replayer');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    }


    public function markAsActive()
    {
        $this->update(['code' => null, 'code_expire' => null, 'is_active' => 1]);
        return $this;
    }

    public function sendVerificationCode()
    {
        $this->update([
            'code'        => $this->activationCode(),
            'code_expire' => Carbon::now()->addMinute(),
        ]);

        // $this->sendCodeAtSms($this->code);
        $this->sendEmail($this->code);

        return ['user' => new UserResource($this->refresh())];
    }

    private function activationCode()
{
    if (config('app.env') === 'production') {
        return mt_rand(11111, 99999);
    }

    // For local, staging, etc.
    return 12345;
}


    public function sendCodeAtSms($code, $full_phone = null){
        // $msg = trans('apis.activeCode');
        // dispatch(new SendSms($full_phone ?? $this->full_phone, $msg . $code));
        
        $phone = $full_phone ?? $this->full_phone;
        (new WhatsAppOtpService())->sendToPhone($phone, $code);
    }

   public function sendEmail($title, $code, $full_phone = null)
{
    $msg = __('apis.activeCode');

    $data = [
        'title' => $title,
        'message' => $msg . $code
    ];

    \Mail::to($this->email)->send(new \App\Mail\SendMail($data));
}


    public function consultationMessages()
    {
        return $this->hasMany(ConsultationMessage::class , 'client_id');
    }

    public function logout()
    {
        $this->tokens()->delete();
        if (request()->device_id) {
            $this->devices()->where(['device_id' => request()->device_id])->delete();
        }
        return true;
    }

    public function devices()
    {
        return $this->morphMany(Device::class, 'morph');
    }

    /**
     * Get the device tokens for the user.
     */
    public function deviceTokens()
    {
        return $this->hasMany(UserDeviceToken::class);
    }

    /**
     * Get active device tokens for the user.
     */
    public function activeDeviceTokens()
    {
        return $this->deviceTokens()->active();
    }

    public function login()
    {
        $this->updateUserDevice();
        $this->updateUserLang();

        // Create token with 24-hour expiration
        $token = $this->createToken('bearer-token', ['*'], now()->addHours(24))->plainTextToken;

        return UserResource::make($this)->setToken($token);
    }

    public function updateUserLang()
    {
        if (request()->header('Lang') != null
            && in_array(request()->header('Lang'), languages())) {
            $this->update(['lang' => request()->header('Lang')]);
        }
    }

    public function updateUserDevice()
    {
        if (request()->device_id) {
            $this->devices()->updateOrCreate([
                'device_id' => request()->device_id,
            ], [
                'device_type' => request()->device_type,
            ]);
        }
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function provider()
    {
        return $this->hasOne(Provider::class );
    }

    /**
     * Get user's course enrollments
     */
 
    public function userUpdates()
    {
        return $this->hasMany(UserUpdate::class);
    }

    public function phoneUpdates()
    {
        return $this->hasMany(UserUpdate::class)->where('type', 'phone');
    }

    /**
     * Get loyalty points balance in SAR
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Alias for British spelling used in some places: favourites()
    public function favourites()
    {
        return $this->favorites();
    }
}
