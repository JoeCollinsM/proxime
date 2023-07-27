<?php

namespace App\Http\Controllers\Staff;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController
{
    public function index()

    {

        $roles = Role::all();
        $staffs = Staff::query()->whereNotNull('role_id')->paginate();

        return view('staff.staffs', compact('roles', 'staffs'));

    }

    public function store(Request $request)

    {

        $request->validate([
            'role_id' => 'required|numeric|exists:roles,id',
            'name' => 'required',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|unique:staff,phone',
            'password' => 'required|min:8|confirmed'
        ]);

        $role = Role::findOrFail($request->role_id);

        $staff = $role->staffs()->create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if (!$staff) return redirect()->back()->withErrors('Unable to create staff');

        return redirect()->back()->withSuccess('Staff created successfully');

    }

    public function update(Request $request, Staff $staff)

    {
        if (!$staff) return abort(404);
        $request->validate([
            'role_id' => 'required|numeric|exists:roles,id',
            'name' => 'required',
            'phone' => 'nullable|unique:staff,phone,' . $staff->id,
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'password' => 'nullable|min:8|confirmed'
        ]);

        $params = $request->only(['role_id', 'name', 'phone', 'email']);

        if ($request->password) {
            $params['password'] = Hash::make($request->password);
        }

        $res = $staff->update($params);

        if (!$res) return redirect()->back()->withErrors('Unable to update staff');

        return redirect()->back()->withSuccess('Staff updated successfully');

    }

    public function destroy(Staff $staff)

    {
        if (!$staff) return abort(404);
        $res = $staff->delete();

        if (!$res) return redirect()->back()->withErrors('Staff not deleted. May be one or more data exists');

        return redirect()->back()->withSuccess('Staff deleted successfully');

    }
}
