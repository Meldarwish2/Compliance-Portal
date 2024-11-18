<?php

namespace App\Http\Controllers;

use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // public function index()
    // {
    //     return view('users.index');
    // }

    // public function getUsers(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = User::with('roles')->get();
    //         return DataTables::of($data)
    //             ->addColumn('roles', function ($row) {
    //                 return $row->roles->pluck('name')->implode(', ');
    //             })
    //             ->addColumn('action', function ($row) {
    //                 $btn = '<a href="' . route('users.edit', $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>';
    //                 $btn .= ' <form action="' . route('users.destroy', $row->id) . '" method="POST" style="display:inline;">
    //                 ' . csrf_field() . '
    //                 ' . method_field('DELETE') . '
    //                 <button type="submit" class="delete btn btn-danger btn-sm">Delete</button>
    //               </form>';
    //                 return $btn;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }
    // }

    // public function create()
    // {
    //     $roles = Role::all();
    //     return view('users.create', compact('roles'));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:8',
    //         'roles' => 'required|array',
    //         'roles.*' => 'exists:roles,id',
    //     ]);

    //     $user = new User();
    //     $user->name = $request->name;
    //     $user->email = $request->email;
    //     $user->password = Hash::make($request->password);
    //     $user->save();
    //     $user->roles()->sync($request->roles);
    //     return redirect()->route('users.index')->with('success', 'User created successfully.');
    // }

    // public function edit($id)
    // {
    //     $user = User::findOrFail($id);
    //     return view('users.edit', compact('user'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,' . $id,
    //         'password' => 'nullable|min:8',
    //     ]);

    //     $user = User::findOrFail($id);
    //     $user->name = $request->name;
    //     $user->email = $request->email;

    //     if ($request->password) {
    //         $user->password = Hash::make($request->password);
    //     }

    //     $user->save();

    //     return redirect()->route('users.index')->with('success', 'User updated successfully.');
    // }

    // public function destroy($id)
    // {
    //     $user = User::findOrFail($id);
    //     $user->delete();

    //     return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    // }
    // public function show($id)
    // {

    //     $user = User::findOrFail($id);
    //     return view('users.show', compact('user'));
    // }

      // Show all users (admin only)
      public function index()
      {
          $users = User::all();
          return view('users.index', compact('users'));
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
          $user->roles()->sync($request->roles);

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
            return DataTables::of($data)
                ->addColumn('roles', function ($row) {
                    return $row->roles->pluck('name')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('users.edit', $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $btn .= ' <form action="' . route('users.destroy', $row->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="delete btn btn-danger btn-sm">Delete</button>
                  </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
