<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|numeric|exists:roles,id'
        ]);

        $request['password'] = Hash::make($request['password']);
        $request['status'] = UserStatus::APPROVED;
        $user = User::create($request->all());

        return new UserResource($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => 'string|min:8|confirmed',
        ]);

        $request['password'] = Hash::make($request['password']);
        $user->update($request->all());

        return new UserResource($user, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => trans('message.deleted')], 204);
    }

    /**
     * Update the status to approved.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function approve(User $user)
    {
        $user->status = UserStatus::APPROVED;
        $user->update();

        return new UserResource($user, 202);
    }

    /**
     * Update the status to blocked.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function block(User $user)
    {
        $user->status = UserStatus::BLOCKED;
        $user->update();

        return new UserResource($user, 202);
    }

    /**
     * Update the role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function role(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|numeric|exists:roles,id',
        ]);

        $user->role = $request['role'];
        $user->update();

        return new UserResource($user, 202);
    }
}