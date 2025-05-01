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
        // Log the incoming request for debugging
        \Illuminate\Support\Facades\Log::info('Update name request:', $request->all());
        
        $request->validate(['new_name' => 'required|string|max:255']);
        $user->update(['full_name' => $request->new_name]);
        
        // Log the successful update
        \Illuminate\Support\Facades\Log::info('Name updated successfully for user ID: ' . $user->id);
        
        // Check if request wants JSON response
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        // Otherwise redirect back with success message
        return redirect()->route('user.management')->with('success', 'Nama lengkap berhasil diperbarui');
    }

    public function updateGroup(User $user, Request $request)
    {
        // Log the incoming request for debugging
        \Illuminate\Support\Facades\Log::info('Update group request:', $request->all());
        
        $request->validate(['new_group' => 'required|string|not_in:admin']);
        $user->update(['group' => $request->new_group]);
        
        // Log the successful update
        \Illuminate\Support\Facades\Log::info('Group updated successfully for user ID: ' . $user->id);
        
        // Check if request wants JSON response
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        // Otherwise redirect back with success message
        return redirect()->route('user.management')->with('success', 'Grup berhasil diperbarui');
    }
    
    /**
     * Get all available groups for user management
     */
    public function getGroups()
    {
        // Get all groups from the groups table
        $allGroups = \App\Models\Group::pluck('group_name')->toArray();
        
        // Log the groups from database
        \Illuminate\Support\Facades\Log::info('All groups from database:', $allGroups);
        
        // Return all groups except admin
        $filteredGroups = array_values(array_filter($allGroups, function($group) {
            return $group !== 'admin';
        }));
        
        return response()->json($filteredGroups);
    }

    public function resetPassword(User $user, Request $request)
    {
        $tempPassword = Str::random(8);
        $user->update(['password' => Hash::make($tempPassword)]);
        
        // Check if request wants JSON response (either through Accept header or want_json parameter)
        if ($request->wantsJson() || $request->has('want_json')) {
            return response()->json(['password' => $tempPassword]);
        }
        
        // Otherwise redirect back with success message and password
        return redirect()->route('user.management')
            ->with('success', 'Password berhasil direset')
            ->with('password', $tempPassword);
    }

    public function destroy(User $user, Request $request)
    {
        $user->delete();
        
        // Check if request wants JSON response
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        // Otherwise redirect back with success message
        return redirect()->route('user.management')->with('success', 'Pengguna berhasil dihapus');
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
        try {
            // Log the incoming request for debugging
            \Illuminate\Support\Facades\Log::info('User creation request:', $request->all());
            
            // Validate request data
            $validated = $request->validate([
                'username' => 'required|string|max:255|unique:users,username|alpha_num',
                'full_name' => 'required|string|min:3|max:255',
                'group' => 'required|string|max:255'
            ], [
                'username.unique' => 'Username sudah digunakan',
                'username.alpha_num' => 'Username hanya boleh mengandung huruf dan angka',
                'full_name.min' => 'Nama lengkap minimal 3 karakter',
                'full_name.required' => 'Nama lengkap wajib diisi',
                'group.required' => 'Grup wajib dipilih'
            ]);
            
            // Log successful validation
            \Illuminate\Support\Facades\Log::info('Validation passed for username: ' . $validated['username']);

            // Generate random password
            $password = Str::random(12);
            
            // Create new user
            $user = User::create([
                'username' => $validated['username'],
                'full_name' => $validated['full_name'],
                'group' => $validated['group'],
                'password' => Hash::make($password)
            ]);

            // Generate debug log
            \Illuminate\Support\Facades\Log::info('User created with password: ' . $password);
            
            // Return success response with credentials
            $response = [
                'success' => true,
                'message' => 'Pengguna berhasil dibuat',
                'username' => $user->username,
                'password' => $password
            ];
            
            // Verify password is in response
            \Illuminate\Support\Facades\Log::info('Response data: ', $response);
            
            return response()->json($response);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors (like duplicate entries)
            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1062) { // MySQL duplicate entry error code
                return response()->json([
                    'success' => false,
                    'message' => 'Username sudah digunakan. Silakan pilih username lain.'
                ], 422);
            }
            
            // Generic database error
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data ke database'
            ], 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}