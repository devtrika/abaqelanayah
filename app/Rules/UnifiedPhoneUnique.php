<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Propaganistas\LaravelPhone\PhoneNumber;

class UnifiedPhoneUnique implements Rule
{
    protected $currentId;

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

            // Check uniqueness in users table only
            $query = DB::table('users')
                ->where('phone', $formattedPhone)
                ->whereNull('deleted_at');

            if ($this->currentId) {
                $query->where('id', '!=', $this->currentId);
            }

            return !$query->exists();
        } catch (\Exception $e) {
            // If phone number conversion fails, it's invalid
            return false;
        }
    }

    public function message()
    {
        return 'رقم الهاتف مستخدم من قبل.';
    }
}

