<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Class AuthTest
 * @group auth
 * @package Tests\Feature
 */
class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test registered users can login.
     * And non-registered users can NOT login.
     *
     * @return void
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create();
        $response = $this->postJson(route('api.login', [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]));
        $response->assertStatus(200);

        $user = User::factory()->make();
        $response = $this->postJson(route('api.login', [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]));
        $response->assertStatus(422);
    }

    /**
     * Test non-registered users can signup.
     * And registered users can NOT signup
     *
     * @return void
     */
    public function test_user_can_signup()
    {
        $user = User::factory()->make();
        $response = $this->postJson(route('api.register', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'company' => $user->company,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]));
        $response->assertStatus(201);

        $user = User::factory()->create();
        $response = $this->postJson(route('api.register', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'company' => $user->company,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]));
        $response->assertStatus(422);
    }

    /**
     * Test users can request for resetting their password.
     * Send an email including request link
     *
     * @return void
     */
    public function test_user_can_request_for_resetting_password()
    {
        $user = User::factory()->create();
        $response = $this->postJson(route('api.password.email', [
            'email' => $user->email,
        ]));
        $response->assertStatus(200);
    }

    /**
     * Test users can reset their password
     */
    public function test_user_can_update_his_password()
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $response = $this->postJson(route('api.password.update', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'SomeOtherPassword',
            'password_confirmation' => 'SomeOtherPassword',
        ]));
        $response->assertStatus(200);
    }

    /**
     * Test users can verify their email address
     */
    public function test_user_can_verify_his_email()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $notification = new VerifyEmail();

        Auth::login($user);
        $url = parse_url($notification->verificationUrl($user));
        $response = $this->getJson('api' . $url['path'] . "?" . $url['query']);
        $response->assertStatus(204);
    }

    /**
     * Test users can resend the verification email
     */
    public function test_user_can_resend_email()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        Auth::login($user);
        $response = $this->getJson(route('api.verification.resend'));
        $response->assertStatus(202);
    }
}
