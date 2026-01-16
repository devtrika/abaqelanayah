<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EmailUniqueWhenNotNull implements Rule
{
    protected $table;
    protected $column;
    protected $ignoreId;
    protected $ignoreColumn;
    protected $whereConditions;

    /**
     * Create a new rule instance.
     *
     * @param string $table
     * @param string $column
     * @param mixed $ignoreId
     * @param string $ignoreColumn
     * @param array $whereConditions
     */
    public function __construct(
        string $table = 'users',
        string $column = 'email',
        $ignoreId = null,
        string $ignoreColumn = 'id',
        array $whereConditions = []
    ) {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreId = $ignoreId;
        $this->ignoreColumn = $ignoreColumn;
        $this->whereConditions = $whereConditions;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // If email is null or empty, it's always valid (no uniqueness check needed)
        if (is_null($value) || $value === '') {
            return true;
        }

        // Build the query
        $query = DB::table($this->table)
            ->where($this->column, $value);

        // Add ignore condition if provided
        if ($this->ignoreId !== null) {
            $query->where($this->ignoreColumn, '!=', $this->ignoreId);
        }

        // Add additional where conditions
        foreach ($this->whereConditions as $column => $value) {
            if ($value === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $value);
            }
        }

        // Return true if no records found (email is unique)
        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'البريد الإلكتروني مستخدم من قبل.';
    }

    /**
     * Static method for users table with soft deletes
     *
     * @param mixed $ignoreId
     * @return static
     */
    public static function forUsers($ignoreId = null)
    {
        return new static(
            'users',
            'email',
            $ignoreId,
            'id',
            ['deleted_at' => null]
        );
    }

    /**
     * Static method for admins table
     *
     * @param mixed $ignoreId
     * @return static
     */
    public static function forAdmins($ignoreId = null)
    {
        return new static(
            'admins',
            'email',
            $ignoreId,
            'id',
            []
        );
    }
}
