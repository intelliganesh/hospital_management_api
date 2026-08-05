<?php

namespace App\Enums\Consultation;

enum TypeEnum: string
{
    case None = 'None';
    case Allopathy = "Allopathy";
    case Proctology = 'Proctology';
    case NonProctology = 'Non Proctology';
}