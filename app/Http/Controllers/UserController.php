<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\NewUser;
use App\Mail\Welcome;
use App\Models\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request) {
        $search = $request->get('search');
        $page = $request->get('page', 1);
        $pageSize = $request->get('pageSize', 10);
        $skip = $pageSize * ($page - 1);
        $sort = $request->get('sortBy', 'created_at');
        $order = 'created_at';

        if (in_array($sort, ['created_at', 'name', 'email'])) {
            $order = $sort;
        }

        $queries = User::query()->where("active", true);

        if ($search) {
            $queries->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $queries->withCount(['orders as orders_counts'])
            ->orderByDesc($order)
            ->skip($skip)
            ->take($pageSize)
            ->get();
        
        return response()->json([
            'message' => 'success',
            'data' => [
                'page' => $page,
                'users' => UserResource::collection($users)
                ]
            ]);
    }

    public function store(CreateUserRequest $request) {
        $body = $request->validated();
        $user = new User;
        $user->email = $body['email'];
        $user->password = $body['password'];
        $user->name = $body['name'];

        $user->save();


        $admin = User::query()
            ->where('role', UserRole::Admin->value)
            ->where('active', true)
            ->get('email');

        Mail::to($user->email)->queue(new Welcome($user));
        if (count($admin) > 0) {
            Mail::to($admin)->queue(new NewUser($user));
        }

        return response()->json([
            'message' => 'success',
            'data' => $user
        ]);
    }
}
