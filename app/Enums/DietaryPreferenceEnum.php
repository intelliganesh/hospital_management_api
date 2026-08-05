<?php
namespace App\Enums;

enum DietaryPreferenceEnum: string {
    case Vegan         = 'Vegan';
    case Eggtarian     = 'Eggtarian';
    case Vegetarian    = 'Vegetarian';
    case NonVegetarian = 'Non Vegetarian';
}
