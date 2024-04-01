<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id
        ]);

        $account = new Account([
            'number_account' => 'ACC-' . uniqid(), 
            'balance' => 0 
        ]);
        
        $user->account()->save($account);

        return response()->json(['message' => 'User created successfully', 'user' => $user, 'account' => $account], 201);
    }

    public function index()
    {
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    public function getUserConnecté()
{
    $user = Auth::user();
    return response()->json(['user' => $user]);
}

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['user' => $user], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'password' => 'required',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role_id;
        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
