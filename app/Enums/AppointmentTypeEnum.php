<?php

namespace App\Enums;

enum AppointmentTypeEnum: string
{
    case FollowUp = 'Follow-up';
    case FirstVisit = 'First Visit';
    case PostSurgeryFollowUp = 'Post Surgery Follow up';
}