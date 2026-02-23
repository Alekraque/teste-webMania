<?php

namespace App\Domain\Enums;

enum ExpeditionStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
}
