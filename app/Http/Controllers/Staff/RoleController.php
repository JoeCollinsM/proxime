<?php

namespace App\Http\Controllers\Staff;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::paginate();
        return view('staff.roles', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'caps' => 'required',
            'caps.*' => 'required'
        ]);
        $role = Role::create($request->only(['name', 'caps']));
        if (!$role) return redirect()->back()->withErrors('Unable to create role');
        return redirect()->back()->withSuccess('Role created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        if (!$role) return abort(404);
        $request->validate([
            'name' => 'required|string|max:199',
            'caps' => 'required',
            'caps.*' => 'required'
        ]);
        $res = $role->update($request->only(['name', 'caps']));
        if (!$res) return redirect()->back()->withErrors('Unable to update role');
        return redirect()->back()->withSuccess('Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Role $role
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function destroy(Role $role)
    {
        if (!$role) return abort(404);
        $res = $role->delete();
        if (!$res) return redirect()->back()->withErrors('Unable to update role. Maybe one or more staff exists in this role');
        return redirect()->back()->withSuccess('Role deleted successfully');
    }
}
