<?php

namespace App\Enums;

enum AccessLevel: string
{
    case Free = 'free';
    case Member = 'member';
    case Premium = 'premium';
}
