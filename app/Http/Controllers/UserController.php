<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $query = User::query();

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        $sortColumns = ['username', 'full_name', 'group', 'created_at'];
        $sort = request('sort');
        $direction = request('direction');

        if ($sort && in_array($sort, $sortColumns)) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->orderByDesc('created_at');
        }

        $users = $query->paginate(50)->appends(request()->query());
        $groups = User::where('group', '!=', 'admin')->distinct()->pluck('group');

        return view('usermanage', ['users' => $users, 'groups' => $groups]);
    }

    public function updateName(User $user, Request $request)
    {
        $request->validate(['username' => 'required|string|max:255']);
        $user->update(['full_name' => $request->name]);
        return response()->json(['success' => true]);
    }

    public function updateGroup(User $user, Request $request)
    {
        $request->validate(['group' => 'required|string|not_in:admin']);
        $user->update(['group' => $request->group]);
        return response()->json(['success' => true]);
    }

    public function resetPassword(User $user)
    {
        $tempPassword = Str::random(8);
        $user->update(['password' => Hash::make($tempPassword)]);
        return response()->json(['password' => $tempPassword]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:32',
            'full_name' => 'required|string|max:255',
            'group' => 'required|string|max:255'
        ]);

        $password = Str::random(12);
        
        User::create([
            'username' => $validated['username'],
            'full_name' => $validated['full_name'],
            'group' => $validated['group'],
            'password' => Hash::make($password)
        ]);

        return redirect()->route('user.management')
            ->with('success', 'User created successfully. Password: ' . $password);
    }
}