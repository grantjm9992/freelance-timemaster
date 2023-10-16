<?php

namespace App\ValueObject;

class UserRole
{
    protected const SUPER_ADMIN = 'super_admin';
    protected const COMPANY_ADMIN = 'company_admin';
    protected const USER = 'user';

    public static function superAdmin(): string
    {
        return self::SUPER_ADMIN;
    }

    public static function companyAdmin(): string
    {
        return self::COMPANY_ADMIN;
    }

    public static function user(): string
    {
        return self::USER;
    }
}
