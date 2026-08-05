<?php
namespace App\Enums;

use App\Models\IPD;
use App\Models\Ward;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Master\Food;
use App\Models\Master\Test;
use App\Models\Appointments;
use App\Models\Master\Rooms;
use App\Models\Bed;
use App\Models\Master\Doctors;
use App\Models\Master\Findings;
use App\Models\Master\Expenses;


enum ServiceType: string
{
    case IPD = 'ipd';
    case OPD = 'opd';
    case Ward = 'ward';
    case Test = 'test';
    case Food = 'food';
    case Patient = 'patient';
    case Payment = 'payment';
    case Invoice = "invoice";
    case Findings = 'findings';
    case Hospital = 'hospital';
    case DoctorCode = 'doctors';
    case Appointments = 'appointment';
    case Voucher = 'voucher';
    case Room = 'room';
    case Bed = 'bed';

    public function prefixKey(): string
    {
        return $this->value . '_prefix';
    }

    public function model(): string
    {
        return match ($this) {
            self::IPD => IPD::class,
            self::Test => Test::class,
            self::Ward => Ward::class,
            self::Food => Food::class,
            self::OPD => Patient::class,
            self::Patient => Patient::class,
            self::Invoice => Invoice::class,
            self::Payment => Payment::class,
            self::Findings => Findings::class,
            self::DoctorCode => Doctors::class,
            self::Appointments => Appointments::class,
            self::Voucher => Expenses::class,
            self::Room => Rooms::class,
            self::Bed => Bed::class,
        };
    }
}
