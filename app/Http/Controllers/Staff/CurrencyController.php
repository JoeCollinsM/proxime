<?php

namespace App\Http\Controllers\Staff;

use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTableAbstract;

class CurrencyController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Currency::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('is_default', function (Currency $currency) {
                return $currency->is_default == 1 ? '<span class="badge badge-primary">Yes</span>' : '<span class="badge badge-warning">No</span>';
            });
            $table->editColumn('status', function (Currency $currency) {
                return $currency->status == 1 ? '<span class="badge badge-primary">Enabled</span>' : '<span class="badge badge-warning">Disabled</span>';
            });
            $table->addColumn('actions', function (Currency $currency) {
                return '<button class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"
                                                            data-id="' . $currency->id . '"
                                                            data-name="' . $currency->name . '"
                                                            data-code="' . $currency->code . '"
                                                            data-symbol="' . $currency->symbol . '"
                                                            data-rate="' . $currency->rate . '"
                                                            data-is_default="' . $currency->is_default . '"
                                                            data-status="' . $currency->status . '"><i
                                                            class="fa fa-edit"></i></button>
                                                    <button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger"
                                                            data-id="' . $currency->id . '"><i
                                                            class="fa fa-trash"></i></button>';
            });
            $table->rawColumns(['is_default', 'status', 'actions']);
            return $table->make(true);
        }
        $default = Currency::getDefaultCurrency();
        return view('staff.setting.currencies', compact('default'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|alpha_dash|max:3|unique:currencies,code',
            'symbol' => 'nullable|max:10',
            'rate' => 'required|numeric|gt:0',
            'is_default' => 'nullable|in:0,1',
            'status' => 'nullable|in:0,1',
        ]);
        $old = Currency::query()->count();
        if (!$old) {
            if ($request->is_default != 1) {
                return redirect()->back()->withErrors('You must set your first currency as default');
            }
        }

        $params = $request->only(['name', 'code', 'symbol', 'rate', 'is_default', 'status']);

        if ($request->status != 1) {
            $params['status'] = 0;
        }

        if ($request->is_default == 1) {
            $params['rate'] = 1;
            $params['status'] = 1;
        } else {
            $params['is_default'] = 0;
        }

        DB::beginTransaction();
        try {
            $currency = Currency::create($params);
            if (!$currency) throw new \Exception('Unable to create currency');
            if ($currency->is_default == 1 && $old) {
                $res = Currency::query()->where('id', '!=', $currency->id)->update([
                    'is_default' => 0
                ]);
                if (!$res) throw new \Exception('Unable to update all currency');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Currency created successfully');

    }

    public function refresh(Request $request)
    {
        $default = Currency::getDefaultCurrency();
        if (!$default instanceof Currency) return redirect()->back()->withErrors('Unable to update currency rates');
        $query = Currency::query()->where('id', '!=', $default->id);
        $count = (clone $query)->count();
        if (!$count) return redirect()->back()->withSuccess('Currency rates updated successfully');
        $currencies = (clone $query)->pluck('code')->implode(',');
        try {

            $URL = 'http://api.currencylayer.com/live?access_key='.config('services.currencylayer.access_key').'&currencies='.$currencies.'&format=1&source='.$default->code.'';
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $URL);
            $contents = curl_exec($c);
            curl_close($c);
            // $response = file_get_contents(
            //     sprintf(
            //         "http://api.currencylayer.com/live?access_key=%s&currencies=%s&format=1&source=%s",
            //         config('services.currencylayer.access_key'),
            //         $currencies,
            //         $default->code
            //     )
            // );
            $response = json_decode($contents, true);
            if ($response['success'] && $response['quotes'] && is_array($response['quotes'])) {
                $currencies = (clone $query)->get();
                foreach ($currencies as $currency) {
                    $key = $default->code . $currency->code;
                    if (!isset($response['quotes'][$key])) continue;
                    $currency->update([
                        'rate' => $response['quotes'][$key]
                    ]);
                }
            } elseif (isset($response['error'])) {
                throw new \Exception($response['error']);
            } else {
                throw new \Exception('Unable to update currency rates');
            }
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Currency Rates updated Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Currency $currency
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, Currency $currency)
    {
        if (!$currency) return abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|alpha_dash|max:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'nullable|max:3',
            'rate' => 'required|numeric|gt:0',
            'is_default' => 'nullable|in:0,1',
            'status' => 'nullable|in:0,1',
        ]);
        $default = Currency::getDefaultCurrency();

        $another = Currency::query()->where('id', '!=', $currency->id)->count();
        if ($default->id == $currency->id) {
            if ($request->is_default != 1) {
                return redirect()->back()->withErrors('You must set this currency as default');
            }
        }

        $params = $request->only(['name', 'code', 'symbol', 'rate', 'is_default', 'status']);

        if ($request->status != 1) {
            $params['status'] = 0;
        }

        if ($request->is_default == 1) {
            $params['rate'] = 1;
            $params['status'] = 1;
        } else {
            $params['is_default'] = 0;
        }

        DB::beginTransaction();
        try {
            $res = $currency->update($params);
            if (!$res) throw new \Exception('Unable to update currency');
            if ($params['is_default'] == 1 && $another) {
                $res = Currency::query()->where('id', '!=', $currency->id)->update([
                    'is_default' => 0
                ]);
                if (!$res) throw new \Exception('Unable to update all currency');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        notify()->success('Welcome to Laravel Notify ⚡️');
        return redirect()->back()->withSuccess('Currency updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Currency $currency
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function destroy(Currency $currency)
    {
        if (!$currency) return abort(404);
        $default = Currency::getDefaultCurrency();
        if ($default->id == $currency->id) return redirect()->back()->withErrors('You must not delete your default currency');
        $res = $currency->delete();
        if (!$res) return redirect()->back()->withErrors('Unable to delete currency');
        return redirect()->back()->withSuccess('Currency deleted successfully');
    }
}
