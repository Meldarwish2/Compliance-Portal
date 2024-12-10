<?php

namespace App\Http\Controllers;

use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
   
      // Show all users (admin only)
      public function index()
      {
        $roles = Role::all();
          $users = User::all();
          return view('users.index', compact(['users','roles']));
      }

      // Show user profile (admin or the user themselves)
      public function show(User $user)
      {
          return view('users.show', compact('user'));
      }

      // Assign role to a user
      public function assignRole(Request $request, User $user)
      {
          $request->validate([
              'role' => 'required|in:admin,auditor,client',
          ]);

          $user->assignRole($request->role);
          return back()->with('success', 'Role assigned successfully.');
      }

      // Revoke role from a user
      public function revokeRole(User $user, $role)
      {
          $user->removeRole($role);
          return back()->with('success', 'Role revoked successfully.');
      }

      // Show form to create new user (admin only)
      public function create()
      { $roles = Role::all();
          return view('users.create', compact('roles'));
      }

      // Store new user (admin only)
      public function store(Request $request)
      {
      
        $validated =   $request->validate([
              'name' => 'required|string|max:255',
              'email' => 'required|email|unique:users',
              'password' => 'required|min:8',
          ]);

          $user = User::create($request->only('name', 'email', 'password'));
          $user->roles()->sync([$request->role]);

//          $user->assignRole('client'); // Default role can be client

          return redirect()->route('users.index')->with('success', 'User created successfully.');
      }

      // Edit user details (admin only)
      public function edit(User $user)
      {
          $roles = Role::all();
          return view('users.edit', compact(['user','roles']));
      }

      // Update user details (admin only)
    public function update(Request $request, $id)
     {
         $request->validate([
             'name' => 'required',
             'email' => 'required|email|unique:users,email,' . $id,
             'password' => 'nullable|min:8',
         ]);

         $user = User::findOrFail($id);
         $user->name = $request->name;
         $user->email = $request->email;

         if ($request->password) {
             $user->password = Hash::make($request->password);
         }

         $user->save();
         $user->roles()->sync([$request->role]);

         return redirect()->route('users.index')->with('success', 'User updated successfully.');
     }


      // Delete user (admin only)
      public function destroy(User $user)
      {
          $user->delete();
          return redirect()->route('users.index')->with('success', 'User deleted successfully.');
      }

      public function getUsers(Request $request)
      {
          if ($request->ajax()) {
              $data = User::with('roles')->get();
              $currentUserId = auth()->id(); 
              return DataTables::of($data)
                  ->addColumn('roles', function ($row) {
                      return $row->roles->pluck('name')->implode(', ');
                  })
                  ->addColumn('action', function ($row) use ($currentUserId) {
                      $btn = '';
                      if ($row->id !== $currentUserId) {
                          $btn .= '<a href="' . route('users.edit', $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>';
                          $btn .= ' <form action="' . route('users.destroy', $row->id) . '" method="POST" style="display:inline;">
                              ' . csrf_field() . '
                              ' . method_field('DELETE') . '
                              <button type="submit" class="delete btn btn-danger btn-sm">Delete</button>
                          </form>';
                      } else {
                          $btn = '<span class="text-muted"></span>';
                      }
                      return $btn;
                  })
                  ->rawColumns(['action'])
                  ->make(true);
          }
      }
      
}
