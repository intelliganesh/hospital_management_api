<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface MailServiceContract
{
    /**
     * Summary of sendWelcomeMail
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function sendWelcomeMail(Request $request): bool;

}

interface ExtraMailServiceContract
{


    public function billingMail($billing);

    public function prescriptionMail($prescription);

    public function sendAppointmentConfirmation($appointment): void;

}


