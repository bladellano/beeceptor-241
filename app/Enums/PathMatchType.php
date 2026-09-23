<?php

namespace App\Enums;

enum PathMatchType: string
{
    case Exact = 'exact';
    case StartsWith = 'starts_with';
    case Contains = 'contains';
}
