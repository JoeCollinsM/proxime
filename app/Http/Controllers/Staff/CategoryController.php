<?php

namespace App\Http\Controllers\Staff;

use App\Models\Category;
use App\Models\Language;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController
{
    public function index(Request $request)

    {

        $q = Category::query()->with('parent');
        if ($request->ajax()) {
            $categoriesTable = datatables()->of($q)->editColumn('parent.name', function ($category) {
                /* @var Category $category */
                return optional($category->parent)->name;
            })->addColumn('parent_name', function ($category) {
                /* @var Category $category */
                return optional($category->parent)->name;
            })->addColumn('actions', function ($category) {
                /* @var Category $category */
                return '<ul class="list-inline">
                            <li class="list-inline-item">
                                <a href="' . route('staff.catalog.category.edit', $category->id) . '"><span class="ti-pencil"></span></a>
                            <li>
                            <li class="list-inline-item">
                                    <span data-id="' . $category->id . '" class="fa fa-trash btn-delete"></span>
                            </li>
                        </ul>';
            })->rawColumns(['actions'])->filterColumn('parent.name', function ($query, $keyword) {
                $query->whereHas('parent', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            })->make(true);
            return $categoriesTable;
        }
        $categories = $q->get();
        return view('staff.category.index', compact('categories'));

    }

    public function create()

    {
        $languages = Language::query()->where('status', 1)->get();
        $tags = Tag::all();
        $categories = Category::all();
        return view('staff.category.create', compact('languages', 'tags', 'categories'));
    }

    public function store(Request $request)

    {

        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'parent_id' => 'nullable|numeric|exists:categories,id',
            'image' => 'required',
            'status' => 'required|in:0,1'
        ]);
        DB::beginTransaction();
        try {
            /* @var Category $category */
            $category = Category::create($request->only(['name', 'slug', 'parent_id', 'image', 'status']));
            if (!$category) throw new \Exception('Unable to create category');
            if (is_array($request->tags)) {
                $category->tags()->attach($request->tags);
            }
            if (is_array($request->meta)) {
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = [
                        'name' => $name,
                        'content' => $content
                    ];
                }
                $category->metas()->createMany($metas);
            }
            if (is_array($request->lang)) {
                $translations = [];
                foreach ($request->lang as $code => $columns) {
                    foreach ($columns as $column => $content) {
                        $translations[] = [
                            'language' => $code,
                            'column_name' => $column,
                            'content' => $content
                        ];
                    }
                }
                $category->translations()->createMany($translations);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors();
        }
        DB::commit();
        return redirect()->route('staff.catalog.category.index')->withSuccess('Category created successfully');
    }

    public function edit(Category $category)

    {
        if (!$category) return abort(404);
        $metas = $category->metas()->pluck('content', 'name');
        /* @var Collection $translations */
        $translations = $category->translations;
        $translations = $translations->groupBy('language')->map(function ($items) {
            return collect($items)->pluck('content', 'column_name');
        })->toArray();
        $tags = Tag::all();
        $languages = Language::query()->where('status', 1)->get();
        $old_tag_ids = $category->tags()->pluck('tags.id')->toArray();
        $categories = Category::all();
        return view('staff.category.edit', compact('categories', 'tags', 'old_tag_ids', 'category', 'metas', 'translations', 'languages'));
    }

    public function update(Request $request, Category $category)

    {
        if (!$category) return abort(404);
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|numeric|exists:categories,id',
            'image' => 'required',
            'sort' => 'nullable|numeric',
            'status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();
        try {
            $res = $category->update($request->only(['name', 'slug', 'parent_id', 'image', 'sort', 'status']));
            if (!$res) throw new \Exception('Unable to update category');
            $category->tags()->detach();
            if (is_array($request->tags)) {
                $category->tags()->attach($request->tags);
            }
            $category->metas()->delete();
            if (is_array($request->meta)) {
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = [
                        'name' => $name,
                        'content' => $content
                    ];
                }
                $category->metas()->createMany($metas);
            }

            $category->translations()->delete();
            if (is_array($request->lang)) {
                $translations = [];
                foreach ($request->lang as $code => $columns) {
                    foreach ($columns as $column => $content) {
                        $translations[] = [
                            'language' => $code,
                            'column_name' => $column,
                            'content' => $content
                        ];
                    }
                }
                $category->translations()->createMany($translations);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Category updated successfully');
    }

    public function destroy(Category $category)

    {
        if (!$category) return abort(404);

        try {
            $res = $category->delete();

            if (!$res) return redirect()->back()->withErrors('Category not deleted. May be one or more service exists in this category');
        } catch (QueryException $exception) {
            return redirect()->back()->withErrors('Unable to delete a parent category');
        }

        return redirect()->back()->withSuccess('Category deleted successfully');

    }
}
