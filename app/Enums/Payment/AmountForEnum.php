<?php

namespace App\Enums\Payment;

enum AmountForEnum: string
{
    case Test = 'Test';
    case Surgery = 'Surgery';
    case Medicine = 'Medicine';
    case Room_Rent = 'Room Rent';
    case Ambulance = 'Ambulance';
    case Enrollment = 'Enrollment';
    case Appointment = 'Appointment';
    case Lab_Charges = 'Lab Charges';
    case ICU_Charges = 'ICU Charges';
    case Consultation = 'Consultation';
    case Admission_Fee = 'Admission Fee';
    case Discharge_Fee = 'Discharge Fee';
    case Miscellaneous = 'Miscellaneous';
    case Nursing_Charges = 'Nursing Charges';
    case Equipment_Charges = 'Equipment Charges';
    case Operation_Theatre = 'Operation Theatre';
}