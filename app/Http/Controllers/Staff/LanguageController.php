<?php

namespace App\Http\Controllers\Staff;

use App\Models\Language;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTableAbstract;

class LanguageController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Language::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('status', function (Language $language) {
                return $language->status == 1 ? '<span class="badge badge-primary">Enabled</span>' : '<span class="badge badge-warning">Disabled</span>';
            });
            $table->addColumn('actions', function (Language $language) {
                return '<button class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"
                                                            data-id="' . $language->id . '"
                                                            data-name="' . $language->name . '"
                                                            data-code="' . $language->code . '"
                                                            data-status="' . $language->status . '"><i
                                                            class="fa fa-edit"></i></button>
                                                    <button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger"
                                                            data-id="' . $language->id . '"><i
                                                            class="fa fa-trash"></i></button>';
            });
            $table->rawColumns(['status', 'actions']);
            return $table->make(true);
        }
        return view('staff.setting.languages');
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
            'code' => 'required|string|max:10|unique:languages,code',
            'status' => 'nullable|in:0,1'
        ]);
        $params = $request->only(['name', 'code', 'status']);
        if ($request->status != 1) {
            $params['status'] = 0;
        }
        $language = Language::create($params);
        if (!$language) return redirect()->back()->withErrors('Unable to create new language');
        return redirect()->back()->withSuccess('Language created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Language $language
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, Language $language)
    {
        if (!$language) return abort(404);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,' . $language->id,
            'status' => 'nullable|in:0,1'
        ]);
        $params = $request->only(['name', 'code', 'status']);
        if ($request->status != 1) {
            $params['status'] = 0;
        }
        $res = $language->update($params);
        if (!$res) return redirect()->back()->withErrors('Unable to update language');
        return redirect()->back()->withSuccess('Language updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Language $language
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function destroy(Language $language)
    {
        if (!$language) return abort(404);
        $res = $language->delete();
        if (!$res) return redirect()->back()->withErrors('Unable to delete language');
        return redirect()->back()->withSuccess('Language deleted successfully');
    }
}
