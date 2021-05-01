<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function verificationUrl($notifiable): string
    {
        $hash = sha1($notifiable->getEmailForVerification());

        $signedURL = URL::signedRoute('api.verification.verify', [
            'id' => $notifiable->getKey(),
            'hash' => $hash,
        ]);
        $path = $notifiable->getKey() . '/' . $hash;
        $query = parse_url($signedURL)['query'];
        return env('EMAIL_VERIFY_URL') . $path . '?' . $query;
    }
}
