<?php

namespace App\Http\Controllers\Staff;

use App\Models\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Attribute::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('type', function (Attribute $attribute) {
                if ($attribute->type == 'dropdown') return __('Dropdown');
                if ($attribute->type == 'button') return __('Button');
                if ($attribute->type == 'color') return __('Color');
                if ($attribute->type == 'image') return __('Image');
            });
            $table->addColumn('action', function (Attribute $attribute) {
                $actions = sprintf('<a href="%s" class="btn btn-sm btn-success mr-2">%s</a>', route('staff.catalog.attribute.show', $attribute->id), __('Terms'));
                $actions .= sprintf('<button class="btn btn-sm btn-warning btn-edit mr-2" data-id="%s" data-name="%s" data-slug="%s" data-type="%s">%s</button>', $attribute->id, $attribute->name, $attribute->slug, $attribute->type, __('Edit'));
                $actions .= sprintf('<button class="btn btn-sm btn-danger btn-delete" data-id="%s">%s</button>', $attribute->id, __('Delete'));
                return $actions;
            });
            return $table->make(true);
        }
        return view('staff.attribute.index');
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
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:attributes,slug',
            'type' => 'required|in:color,image,button,dropdown'
        ]);
        $params = $request->only(['name', 'slug', 'type']);
        try {
            DB::beginTransaction();
            /* @var Attribute $attribute */
            $attribute = Attribute::create($params);
            if (!$attribute) throw new \Exception(__('Unable to store attribute'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Attribute added successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Attribute $attribute
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function show(Attribute $attribute)
    {
        if (!$attribute) return abort(404);
        return view('staff.attribute.terms', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Attribute $attribute
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        if (!$attribute) return abort(404);
        $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191|unique:attributes,slug,' . $attribute->id,
            'type' => 'required|in:color,image,button,dropdown'
        ]);
        $params = $request->only(['name', 'slug', 'type']);
        try {
            DB::beginTransaction();
            $res = $attribute->update($params);
            if (!$res) throw new \Exception(__('Unable to update attribute'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Attribute updated successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Attribute $attribute
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function destroy(Attribute $attribute)
    {
        if (!$attribute) return abort(404);
        try {
            DB::beginTransaction();
            $res = $attribute->delete();
            if (!$res) throw new \Exception(__('Unable to delete attribute'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Attribute deleted successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }
}
