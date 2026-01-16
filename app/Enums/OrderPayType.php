<?php

namespace App\Enums;

/**
 * Class OrderPayType
 *
 * @method static string all()
 * @method static string|null nameFor($value)
 * @method static array toArray()
 * @method static array forApi()
 * @method static string slug(string $value)
 */
class OrderPayType extends Base
{
    public const MADA      = 'mada';
    public const COD       = 'cod';
    public const VISA      = 'visa';
    public const APPLEPAY  = 'applepay';
    public const GOOGLEPAY = 'googlepay';
    public const TABBY     = 'tabby';
    public const TAMARA    = 'tamara';
}
