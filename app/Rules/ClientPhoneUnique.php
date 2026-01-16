<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;

class ClientPhoneUnique implements Rule
{
    protected $currentId;
    protected $formatError = false;

    public function __construct($currentId = null)
    {
        $this->currentId = $currentId;
    }

    public function passes($attribute, $value)
    {
        try {
            // Convert phone number to unified format using phone library
            $phoneNumber = new PhoneNumber($value, 'SA');
            $formattedPhone = $phoneNumber->formatE164();

            // Check uniqueness in users table only for type 'client'
            // Allow the same phone number if it's used by a provider
            $query = DB::table('users')
                ->where('phone', $value)
                ->where('type', 'client')
                ->whereNull('deleted_at');
            if ($this->currentId) {
                $query->where('id', '!=', $this->currentId);
            }

            return !$query->exists();
        } catch (\Exception $e) {
            // If phone number conversion fails, mark as format error
            $this->formatError = true;
            return false;
        }
    }

    public function message()
    {
        if ($this->formatError) {
            return 'صيغة رقم الهاتف غير صحيحة. يجب أن يكون رقم هاتف سعودي صحيح.';
        }
        
        return 'رقم الهاتف مستخدم من قبل عميل آخر.';
    }
}
