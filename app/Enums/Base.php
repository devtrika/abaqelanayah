<?php

namespace App\Enums;

use Illuminate\Support\Facades\Log;

/**
 * Class Base
 * @package App\Enums
 */
abstract class Base
{
    public $reflection;

    public function __construct()
    {
        try {
            $this->reflection = new \ReflectionClass(static::class);
        } catch (\ReflectionException $e) {
            report($e);
        }
    }

    public static function __callStatic($method, $args)
    {
        if ($method == 'all') {
            return (new static())->all();
        }

        if ($method == 'nameFor') {
            return (new static())->nameFor(...$args);
        }

        if ($method == 'toArray') {
            return (new static())->toArray();
        }

        if ($method == 'forApi') {
            return (new static())->forApi();
        }

        if ($method == 'slug') {
            return (new static())->slug(...$args);
        }

        throw new \BadMethodCallException("Method {$method} not found in " . static::class);
    }

    /**
     * returns a string of constants integer values.
     *
     * @return string
     */
    protected function all() : string
    {
        $constantsArray = $this->reflection->getConstants();

        return implode(',', array_values($constantsArray));
    }

    /**
     * returns the array representation of all constants
     *
     * @return array
     */
    protected function toArray(): array
    {
        return $this->reflection->getConstants();
    }

    /**
     * returns the string representation from an integer.
     *
     * @param int $integer
     * @return string|null
     */
    protected function nameFor(int $integer)
    {
        $flippedConstantsArray = array_flip($this->reflection->getConstants());

        if (isset($flippedConstantsArray[$integer])) {
            $removeUnderScores = str_replace('_', ' ', $flippedConstantsArray[$integer]);
            return __(ucfirst(strtolower($removeUnderScores)));
        }

        return null;
    }

    /**
     * Convert constants to array of objects when used by controllers
     * e.g. [{id: 1, name: 'new'}, ...]
     *
     * @return array
     */
    protected function forApi()
    {
        $constants = $this->reflection->getConstants();
        $newForm = [];

        foreach ($constants as $key => $val) {
            $newForm[] = ['id' => $val, 'name' => __(strtolower($key))];
        }

        return $newForm;
    }

    /**
     * Convert a constant value to its slug representation
     *
     * @param mixed $value The constant value to convert
     * @return string The slug representation or empty string if not found
     */
    protected function slug($value): string
    {
        $constants = array_flip($this->reflection->getConstants());

        // Check if the value exists in the constants array
        if (isset($constants[$value])) {
            return strtolower($constants[$value]);
        }

        // Log the error for debugging purposes
        Log::warning('Enum value not found', [
            'class' => static::class,
            'value' => $value,
            'available_values' => array_keys($constants)
        ]);

        // Return empty string or a default value
        return '';
    }
}
