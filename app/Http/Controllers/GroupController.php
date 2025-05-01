<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    public function index()
    {
        $query = Group::query();

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('group_name', 'like', "%{$search}%")
                    ->orWhere('group_description', 'like', "%{$search}%");
            });
        }

        $sortColumns = ['group_name', 'group_description', 'created_at'];
        $sort = request('sort');
        $direction = request('direction');

        if ($sort && in_array($sort, $sortColumns)) {
            $query->orderBy($sort, $direction ?? 'asc');
        } else {
            $query->orderBy('group_name');
        }

        $groups = $query->paginate(50)->appends(request()->query());

        return view('groupmanage', ['groups' => $groups]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|unique:groups,group_name|not_in:admin',
            'group_description' => 'required|string'
        ]);

        $group = Group::create([
            'group_name' => $request->group_name,
            'group_description' => $request->group_description
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'group_name' => $group->group_name
            ]);
        }

        return redirect()->route('group.management')
            ->with('success', 'Grup "' . $group->group_name . '" berhasil dibuat');
    }

    public function updateDescription(Group $group, Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Update group description request:', $request->all());
        
        $request->validate(['description' => 'required|string']);
        $group->update(['group_description' => $request->description]);
        
        // Log the successful update
        Log::info('Group description updated successfully for group: ' . $group->group_name);
        
        // Check if request wants JSON response
        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        // Otherwise redirect back with success message
        return redirect()->route('group.management')
            ->with('success', 'Deskripsi grup "' . $group->group_name . '" berhasil diperbarui');
    }

    public function destroy(Group $group, Request $request)
    {
        // Prevent deletion of admin group
        if ($group->group_name === 'admin') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Grup admin tidak dapat dihapus'], 403);
            }
            return redirect()->route('group.management')
                ->with('error', 'Grup admin tidak dapat dihapus');
        }

        // Check if group is being used by users
        $usersCount = \App\Models\User::where('group', $group->group_name)->count();
        if ($usersCount > 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Grup tidak dapat dihapus karena sedang digunakan oleh ' . $usersCount . ' pengguna'
                ], 403);
            }
            return redirect()->route('group.management')
                ->with('error', 'Grup tidak dapat dihapus karena sedang digunakan oleh ' . $usersCount . ' pengguna');
        }

        // Check if group is being used by data
        $dataCount = \App\Models\Data::where('group', $group->group_name)->count();
        if ($dataCount > 0) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Grup tidak dapat dihapus karena sedang digunakan oleh ' . $dataCount . ' data'
                ], 403);
            }
            return redirect()->route('group.management')
                ->with('error', 'Grup tidak dapat dihapus karena sedang digunakan oleh ' . $dataCount . ' data');
        }

        // Delete the group
        $group->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('group.management')
            ->with('success', 'Grup "' . $group->group_name . '" berhasil dihapus');
    }

    public function show($groupName)
    {
        $group = Group::findOrFail($groupName);
        
        return response()->json([
            'group_name' => $group->group_name,
            'group_description' => $group->group_description,
            'created_at' => $group->created_at->toDateTimeString(),
            'updated_at' => $group->updated_at->toDateTimeString()
        ]);
    }
}