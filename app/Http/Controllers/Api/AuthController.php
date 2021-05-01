<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validData = $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($validData)) {
            /* create a CHEAT token. */
            /* We don`t use any kind of api tokens, instead we use Sanctum cookie-based approach */
            try {
                $bytes = random_bytes(8);
            } catch (Exception $e) {
                $bytes = '7fe52m95ed70a9869d9f9af7d8400a6673bb9ce9';
            }
            /* Login successful */
            activity()
                ->withProperties([
                    ['IP' => $request->ip()],
                ])
                ->log('Successful Login');
            return response()->json(bin2hex($bytes));
        }
        return response()->json('The credentials do not match our records.', 401);
    }

    public function register(Request $request): JsonResponse
    {
        $validData = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'company' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'max:40'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ]);

        $user = User::create([
            'first_name' => $validData['first_name'],
            'last_name' => $validData['last_name'],
            'company' => $validData['company'],
            'phone_number' => $validData['phone_number'],
            'email' => $validData['email'],
            'password' => bcrypt($validData['password']),
            'monthly_credits' => 0,
            'prepaid_credits' => 100,
        ]);

        $token = $this->login($request);
        event(new Registered($user));

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Successful Register');

        return Response()->json($token->original ?? 1, 201);
    }

    public function forgot_password(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|string|email|exists:users']);

        $status = Password::sendResetLink($request->only('email'));

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
                ['email' => $request->only('email')],
            ])
            ->log('Forgot Password');

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['message' => __($status)], 400);
    }

    public function reset_password(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                activity()
                    ->on($user)
                    ->withProperties([
                        ['IP' => $request->ip()],
                        ['email' => $request->only('email')],
                    ])
                    ->log('Reset Password');

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            $token = $this->login($request);
            return Response()->json($token->original ?? 1);
        } else {
            return response()->json(['message' => __($status)], 400);
        }
    }

    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
                ['email' => $request->only('email')],
            ])
            ->log('Verify Email');

        return response()->json('Your email verified.', 204);
    }

    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('Your E-Mail had been verified before.', 204);
        }

        $request->user()->sendEmailVerificationNotification();

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
                ['email' => $request->only('email')],
            ])
            ->log('Resend Verification Email');

        return response()->json('Email sent. Please check your inbox.', 202);
    }

    public function logout(Request $request)
    {
        activity()
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Logout');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function user(Request $request): UserResource
    {
        $user = User
            ::with(['plans' => function($q){
                $q->select('id', 'title', 'credits', 'type');
            }])
            ->where('users.id', Auth::id())
            ->first();

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Get User Data');

        return new UserResource($user);
    }

    public function user_update(UserUpdateRequest $request): JsonResponse
    {
        $user = User::findOrFail(Auth::id());
        User::find(Auth::id())->update($request->all());

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
                ["old" => $user],
                ["new" => $request->all()]
            ])
            ->log('Update User');

        return response()->json('Your account has been updated.');
    }

    public function user_change_password(UserChangePasswordRequest $request): JsonResponse
    {
        User::find(Auth::id())->update(['password' => Hash::make($request->input('password'))]);

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Change Password');

        return response()->json('Your password has been changed.');
    }
}
