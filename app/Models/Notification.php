<?php

namespace App\Models;

use App\Traits\NotificationMessageTrait;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class Notification extends DatabaseNotification
{
    use NotificationMessageTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function getTypeAttribute($value)
    {
        return $this->data['type'] ;
    }

    public function getTitleAttribute($value)
    {
        return $this->getTitle($this->data , lang() ) ;
    }

    public function getBodyAttribute($value)
    {
        return $this->getBody($this->data ,  lang());
    }

    public function getSenderAttribute($value)
    {
        $def    = 'App\Models\\' . $this->data['sender_model'];
        $sender = $def::find($this->data['sender']);
        return [
            'name'   => $sender->name,
            'avatar' => $sender->avatar,
        ];
    }

}
