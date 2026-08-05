<?php
namespace App\Enums\Payment;

enum PaymentTypeEnum: string {
    case Bank_Transfer = 'Bank Transfer';
    case By_Insurance  = 'By Insurance';
    case Card          = 'Card';
    case Cash          = 'Cash';
    case Cheque        = 'Cheque';
    case EMI           = 'EMI';
    case Online        = 'Online';
    case Other         = 'Other';
    case UPI           = 'UPI';
    case Wallet        = 'Wallet';
}
