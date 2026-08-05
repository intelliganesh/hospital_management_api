<?php

namespace App\Services;

use App\Events\MailEvent;
use App\Mail\GeneralEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmailService
{
    public function __construct(
        private readonly CheckValidation $checkValidationService
    ) {
    }

    public function send(Request $request): bool
    {
        return $this->sendMail(
            $request->input('email'),
            $request->input('subject'),
            $request->input('body')
        );
    }

    /**
     * @throws ValidationException
     */
    public function sendMail(string $email, string $subject, string $body): bool
    {
        $data = compact('email', 'subject', 'body');

        $this->checkValidationService->checkValidation($this->validate($data));

        event(new MailEvent($email, new GeneralEmailMail($subject, $body)));

        return true;
    }

    private function validate(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);
    }
}
