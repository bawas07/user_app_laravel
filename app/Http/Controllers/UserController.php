<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

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

        $queries = User::query();

        if ($search) {
            $queries->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $queries->orderByDesc($order)
            ->skip($skip)
            ->take($pageSize)
            ->get();
        
        return response()->json([
            'message' => 'success',
            'data' => [
                'page' => $page,
                'users' => $users
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

        return response()->json([
            'message' => 'success',
            'data' => $user
            ]);
    }
}
