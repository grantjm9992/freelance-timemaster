<?php

namespace App\ValueObject;

class CheckStatus
{
    protected const OPEN = 'open';
    protected const CLOSED = 'closed';
    protected const PENDING_APPROVAL = 'pendingApproval';

    public static function open(): string
    {
        return self::OPEN;
    }

    public static function closed(): string
    {
        return self::CLOSED;
    }

    public static function pendingApproval(): string
    {
        return self::PENDING_APPROVAL;
    }
}
