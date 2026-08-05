<?php

namespace App\Enums;

enum MaritalStatusEnum: string
{
    case Single = 'Single';
    case Married = 'Married';
    case Widowed = 'Widowed';
    case Divorced = 'Divorced';
    case Unmarried = 'Unmarried';
}