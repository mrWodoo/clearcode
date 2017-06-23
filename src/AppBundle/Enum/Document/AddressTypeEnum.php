<?php

namespace AppBundle\Enum\Document;

abstract class AddressTypeEnum
{
    const HOME      = 'home';
    const SHIPPING  = 'shipping';
    const BILLING   = 'billing';

    /**
     * @return array
     */
    public static function getTypes() : array
    {
        return [
            self::HOME,
            self::SHIPPING,
            self::BILLING
        ];
    }
}
