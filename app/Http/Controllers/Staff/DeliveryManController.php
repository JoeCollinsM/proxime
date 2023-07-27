<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTableAbstract;

class DeliveryManController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DeliveryMan::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('username', function (DeliveryMan $user) {
                return ('<div class="d-flex">
                            <div class="image mr-2">
                                <img class="img-thumbnail" style="width: 50px;height: 50px;"
                                     src="' . $user->avatar . '"
                                     alt="' . $user->user . '">
                            </div>
                            <div class="meta">
                                <p>
                                    <a href="' . route('staff.catalog.delivery-man.edit', $user->id) . '">' . $user->name . '</a>
                                </p>
                                <p class="text-black-50">@' . $user->username . '</p>
                                <p class="text-black-50">' . $user->rating_html . '<a href="' . route('staff.catalog.review.index', ['type' => 'delivery_man', 'type_id' => $user->id]) . '">More...</a></p>
                            </div>
                        </div>');
            });
            $table->addColumn('actions', function (DeliveryMan $user) {
                return '<a href="' . route('staff.catalog.delivery-man.edit', $user->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a><button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger" data-id="' . $user->id . '"><i class="fa fa-trash"></i></button>';
            });
            $table->editColumn('balance', function (DeliveryMan $user) {
                $currency = Currency::getDefaultCurrency();
                return $user->balance . ' ' . $currency->code;
            });
            $table->editColumn('status', function (DeliveryMan $user) {
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
        return view('staff.delivery-man.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('staff.delivery-man.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash|max:191|unique:delivery_men,username',
            'email' => 'required|email|max:191|unique:delivery_men,email',
            'phone' => 'required|numeric|unique:delivery_men,phone',
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
        if ($request->status) {
            $params['status'] = 1;
        } else {
            $params['status'] = 0;
        }
        try {
            $delivery_man = DeliveryMan::create($params);
            if (!$delivery_man) throw new \Exception('Unable to create new delivery man');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        return redirect()->route('staff.catalog.delivery-man.index')->withSuccess('Delivery man added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DeliveryMan  $deliveryMan
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(DeliveryMan $deliveryMan)
    {
        if ($deliveryMan == null) return abort(404);
        $currency = Currency::getDefaultCurrency();
        return view('staff.delivery-man.edit', ['currency' => $currency, 'user' => $deliveryMan]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DeliveryMan  $deliveryMan
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request, DeliveryMan $deliveryMan)
    {
        if ($deliveryMan == null) return abort(404);
        $request->validate([
            'username' => 'required|alpha_dash|max:191|unique:delivery_men,username,' . $deliveryMan->id,
            'email' => 'required|email|max:191|unique:delivery_men,email,' . $deliveryMan->id,
            'phone' => 'required|numeric|unique:delivery_men,phone,' . $deliveryMan->id,
            'name' => 'required|string|max:191',
            'password' => 'nullable|min:6|max:191|confirmed'
        ]);
        $params = $request->only(['username', 'email', 'phone', 'name', 'avatar']);
        if ($request->password) $params['password'] = Hash::make($request->password);
        if ($request->email_verification) {
            $params['email_verified_at'] = $deliveryMan->email_verified_at ?? now();
        } else {
            $params['email_verified_at'] = null;
        }
        if ($request->phone_verification) {
            $params['phone_verified_at'] = $deliveryMan->phone_verified_at ?? now();
        } else {
            $params['phone_verified_at'] = null;
        }
        if ($request->status) {
            $params['status'] = 1;
        } else {
            $params['status'] = 0;
        }
        try {
            $r = $deliveryMan->update($params);
            if (!$r) throw new \Exception('Unable to update the delivery man');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Delivery man updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DeliveryMan  $deliveryMan
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function destroy(DeliveryMan $deliveryMan)
    {
        if ($deliveryMan == null) return abort(404);
        try {
            $r = $deliveryMan->delete();
            if (!$r) throw new \Exception('Unable to delete delivery man');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Delivery man deleted successfully');
    }
}
