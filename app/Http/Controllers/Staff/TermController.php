<?php

namespace App\Http\Controllers\Staff;

use App\Models\Attribute;
use App\Models\AttributeTerm;
use App\Helpers\Hex;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = AttributeTerm::query()->where('attribute_id', $request->attribute_id);
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->addColumn('action', function (AttributeTerm $term) {
                $actions = sprintf('<button class="btn btn-sm btn-warning btn-edit mr-2" data-id="%s" data-name="%s" data-slug="%s" data-data="%s">%s</button>', $term->id, $term->name, $term->slug, $term->data, __('Edit'));
                $actions .= sprintf('<button class="btn btn-sm btn-danger btn-delete" data-id="%s">%s</button>', $term->id, __('Delete'));
                return $actions;
            });

            $table->editColumn('name', function (AttributeTerm $term) {
                if ($term->attribute->type == 'image') {
                    return sprintf('<div class="d-flex term-name" data-id="%s"><img src="%s" class="img-thumbnail mr-2" style="width: 50px;height: 50px;"><span>%s</span></div>', $term->id, $term->data, $term->name);
                }
                if ($term->attribute->type == 'color') {
                    return sprintf('<div class="d-flex term-name" data-id="%s"><span class="img-thumbnail mr-2" style="width: 50px;height: 50px;background-color: %s;"></span> <span>%s</span></div>', $term->id, $term->data, $term->name);
                }
                return sprintf('<div class="d-flex term-name" data-id="%s">%s</div>', $term->id, $term->name);
            });
            $table->rawColumns(['name', 'action']);
            return $table->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attribute = Attribute::findOrFail($request->attribute_id);
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191',
            'data' => ($attribute->type == 'color' ? [new Hex] : ['nullable'])
        ]);
        $params = $request->only(['attribute_id', 'name', 'slug', 'data']);
        try {
            DB::beginTransaction();
            /* @var AttributeTerm $term */
            $term = AttributeTerm::create($params);
            if (!$term) throw new \Exception(__('Unable to store term'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Term added successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withSuccess($exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AttributeTerm $term
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request, AttributeTerm $term)
    {
        if (!$term) return abort(404);
        $attribute = $term->attribute;
        $request->validate([
            'name' => 'required|string|max:191',
            'slug' => 'required|string|max:191',
            'data' => ($attribute->type == 'color' ? [new Hex] : ['nullable']),
        ]);
        $params = $request->only(['name', 'slug', 'data']);
        try {
            DB::beginTransaction();
            $res = $term->update($params);
            if (!$res) throw new \Exception(__('Unable to update term'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Term updated successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AttributeTerm $term
     * @return void
     */
    public function destroy(AttributeTerm $term)
    {
        if (!$term) return abort(404);
        try {
            DB::beginTransaction();
            $res = $term->delete();
            if (!$res) throw new \Exception(__('Unable to delete term'));
            DB::commit();
            return redirect()->back()->withSuccess(__('Term deleted successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withSuccess($exception->getMessage());
        }
    }
}
