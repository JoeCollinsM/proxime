<?php

namespace App\Http\Controllers\Staff;

use App\Models\ShopCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopCategoryController
{
    public function index(Request $request)

    {

        $q = ShopCategory::query()->with('parent');
        if ($request->ajax()) {
            $categoriesTable = datatables()->of($q)->editColumn('parent.name', function ($category) {
                /* @var ShopCategory $category */
                return optional($category->parent)->name;
            })->addColumn('actions', function ($category) {
                /* @var ShopCategory $category */
                return '<a href="' . route('staff.shop-category.edit', $category->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a><button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger" data-id="' . $category->id . '"><i class="fa fa-trash"></i></button>';
            })->rawColumns(['actions'])->filterColumn('parent.name', function ($query, $keyword) {
                $query->whereHas('parent', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            })->make(true);
            return $categoriesTable;
        }
        $categories = $q->get();
        return view('staff.shop.category.index', compact('categories'));

    }

    public function create()

    {
        $categories = ShopCategory::all();
        return view('staff.shop.category.create', compact('categories'));
    }

    public function store(Request $request)

    {

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:shop_categories,slug',
            'parent_id' => 'nullable|numeric|exists:shop_categories,id',
            'image' => 'required',
            'status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();
        try {
            /* @var ShopCategory $category */
            $category = ShopCategory::create($request->only(['name', 'slug', 'parent_id', 'image', 'status']));
            if (!$category) {
                DB::rollBack();
                return redirect()->back()->withErrors('Unable to create shop category');
            }
            if (is_array($request->meta)) {
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = ['name' => $name, 'content' => $content];
                }
                $category->metas()->createMany($metas);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to create shop category');
        }
        DB::commit();
        return redirect()->route('staff.shop-category.index')->withSuccess('Shop Category created successfully');

    }

    public function edit(ShopCategory $shopCategory)

    {
        if (!$shopCategory) return abort(404);
        $categories = ShopCategory::query()->where('id', '!=', $shopCategory->id)->get();
        $shopCategory->setRelation('meta', $shopCategory->metas()->pluck('content', 'name'));
        return view('staff.shop.category.edit', compact('categories', 'shopCategory'));
    }

    public function update(Request $request, ShopCategory $shopCategory)

    {
        if (!$shopCategory) return abort(404);
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:shop_categories,slug,' . $shopCategory->id,
            'parent_id' => 'nullable|numeric|exists:shop_categories,id',
            'image' => 'required',
            'status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();

        try {
            $res = $shopCategory->update($request->only(['name', 'slug', 'parent_id', 'image', 'status']));
            if (!$res) {
                DB::rollBack();
                return redirect()->back()->withErrors('Unable to update shop category');
            }
            if (is_array($request->meta)) {
                $shopCategory->metas()->delete();
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = ['name' => $name, 'content' => $content];
                }
                $shopCategory->metas()->createMany($metas);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to update shop category');
        }
        DB::commit();
        return redirect()->back()->withSuccess('Shop Category updated successfully');

    }

    public function destroy(ShopCategory $shopCategory)

    {
        if (!$shopCategory) return abort(404);

        DB::beginTransaction();
        try {
            $shopCategory->metas()->delete();
            $res = $shopCategory->delete();
            if (!$res) throw new \Exception();
        } catch (QueryException $queryException) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to delete the shop category because of other relational data');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to delete the shop category');
        }
        DB::commit();
        return redirect()->route('staff.shop.index')->withSuccess('Shop category deleted successfully');
    }
}
