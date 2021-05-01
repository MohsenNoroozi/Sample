<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;

class CreditService
{
    private ?Authenticatable $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function check_if_has_enough_credits($needed_credits): array
    {
        return [
            'status' => $this->user['monthly_credits'] + $this->user['prepaid_credits'] >= $needed_credits,
            'remaining' => ($this->user['monthly_credits'] + $this->user['prepaid_credits']) - $needed_credits,
        ];
    }

    public function deduct_credits($credits): array
    {
        if(($this->user['monthly_credits'] >= $credits)) {
            $monthly_credits = $this->user['monthly_credits'] - $credits;
            $prepaid_credits = $this->user['prepaid_credits'];
        } else {
            $monthly_credits = 0;
            $prepaid_credits = $this->user['prepaid_credits'] - ($credits - $this->user['monthly_credits']);
        }

        $this->user->update([
            'monthly_credits' => $monthly_credits,
            'prepaid_credits' => $prepaid_credits,
        ]);

        return [
            'monthly_credits' => $this->user['monthly_credits'],
            'prepaid_credits' => $this->user['prepaid_credits']
        ];
    }
}
