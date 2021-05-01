<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequests\UserCreateRequest;
use App\Http\Requests\AdminRequests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        activity("Admin")
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Get Users');

        return UserResource::collection(User::with('plans')->get())->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserCreateRequest $request
     * @return UserResource
     */
    public function store(UserCreateRequest $request): UserResource
    {
        /* Convert request to array */
        $data = $request->all();

        /* Hashing password */
        $data['password'] = bcrypt($request->input('password'));

        /* Store data to database */
        $user = User::create($data);

        /* Send Register email to the user */
        event(new Registered($user));

        activity("Admin")
            ->on($user)
            ->withProperties([
                ['IP' => $request->ip()],
                ['user_data' => $request->all()],
            ])
            ->log('Store a user');

        return new UserResource($user->loadMissing('plans'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @param Request $request
     * @return UserResource
     */
    public function show(User $user, Request $request): UserResource
    {
        activity("Admin")
            ->on($user)
            ->withProperties([
                ['IP' => $request->ip()],
                ['user_data' => $request->all()],
            ])
            ->log('Store a user');

        return new UserResource($user->loadMissing('plans'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        /* Convert request to array & Remove null inputs  */
        $data = array_filter($request->all(), function($value) {
            return $value !== null;
        });

        /* Hashing password */
        if(!empty($data['password'])) $data['password'] = bcrypt($data['password']);

        $user->update($data);

        activity("Admin")
            ->on($user)
            ->withProperties([
                ['IP' => $request->ip()],
                ['user' => $request->all()],
            ])
            ->log('Update a user');

        return new UserResource($user->loadMissing('plans'));
    }
}
