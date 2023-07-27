<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTableAbstract;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('username', function (User $user) {
                return ('<div class="d-flex">
                            <div class="image mr-2">
                                <img class="img-thumbnail" style="width: 50px;height: 50px;"
                                     src="' . $user->avatar . '"
                                     alt="' . $user->user . '">
                            </div>
                            <div class="meta">
                                <p>
                                    <a href="' . route('staff.catalog.user.edit', $user->id) . '">' . $user->name . '</a>
                                </p>
                                <p class="text-black-50">@' . $user->username . '</p>
                            </div>
                        </div>');
            });
            $table->addColumn('actions', function (User $user) {
                return '<a href="' . route('staff.catalog.user.edit', $user->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a><button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger" data-id="' . $user->id . '"><i class="fa fa-trash"></i></button>';
            });
            $table->editColumn('balance', function (User $user) {
               $currency = Currency::getDefaultCurrency();
               return $user->balance . ' ' . $currency->code;
            });
            $table->editColumn('status', function (User $user) {
                return $user->status == 1 ? '<span class="badge badge-success">Activated</span>' : '<span class="badge badge-danger">Deactivated</span>';
            });
            $table->filterColumn('username', function ($query, $keyword) {
                /* @var Builder $query */
                $query->where(function ($q) use ($keyword) {
                    /* @var Builder $q */
                    $q->where('username', $keyword)->orWhere('name', 'LIKE', "%$keyword%");
                });
            });
            $table->rawColumns(['username', 'status', 'actions']);
            return $table->make(true);
        }
        return view('staff.user.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('staff.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash|max:191|unique:users,username',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'required|numeric|unique:users,phone',
            'name' => 'required|string|max:191',
            'password' => 'required|min:6|max:191|confirmed'
        ]);
        $params = $request->only(['username', 'email', 'phone', 'name', 'avatar', 'password']);
        $params['password'] = Hash::make($params['password']);
        if ($request->email_verification) {
            $params['email_verified_at'] = now();
        } else {
            $params['email_verified_at'] = null;
        }
        if ($request->phone_verification) {
            $params['phone_verified_at'] = now();
        } else {
            $params['phone_verified_at'] = null;
        }
        if ($request->push_notification) {
            $params['push_notification'] = 1;
        } else {
            $params['push_notification'] = 0;
        }
        if ($request->status) {
            $params['status'] = 1;
        } else {
            $params['status'] = 0;
        }
        try {
            $user = User::create($params);
            if (!$user) throw new \Exception('Unable to create new customer');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        return redirect()->route('staff.catalog.user.index')->withSuccess('Customer added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function edit(User $user)
    {
        if ($user == null) return abort(404);
        $currency = Currency::getDefaultCurrency();
        return view('staff.user.edit', compact('user', 'currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, User $user)
    {
        if ($user == null) return abort(404);
        $request->validate([
            'username' => 'required|alpha_dash|max:191|unique:users,username,' . $user->id,
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
            'phone' => 'required|numeric|unique:users,phone,' . $user->id,
            'name' => 'required|string|max:191',
            'password' => 'nullable|min:6|max:191|confirmed'
        ]);
        $params = $request->only(['username', 'email', 'phone', 'name', 'avatar']);
        if ($request->password){
             $params['password'] = Hash::make($request->password);}
        if ($request->email_verification) {
            $params['email_verified_at'] = $user->email_verified_at ?? now();
        } else {
            $params['email_verified_at'] = null;
        }
        if ($request->phone_verification) {
            $params['phone_verified_at'] = $user->phone_verified_at ?? now();
        } else {
            $params['phone_verified_at'] = null;
        }
        if ($request->push_notification) {
            $params['push_notification'] = 1;
        } else {
            $params['push_notification'] = 0;
        }
        if ($request->status) {
            $params['status'] = 1;
        } else {
            $params['status'] = 0;
        }
        try {
            $r = $user->update($params);
            if (!$r) throw new \Exception('Unable to update the customer');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user == null) return abort(404);
        try {
            $r = $user->delete();
            if (!$r) throw new \Exception('Unable to delete customer');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Customer deleted successfully');
    }
}
