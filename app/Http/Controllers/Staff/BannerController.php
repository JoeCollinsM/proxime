<?php

namespace App\Http\Controllers\Staff;

use App\Models\Banner;
use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;

class BannerController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Banner::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('image', function (Banner $banner) {
                return sprintf('<img src="%s" class="img-thumbnail" style="width: 150px;">', $banner->image);
            });
            $table->addColumn('tags', function (Banner $banner) {
                return $banner->tags()->get(['tags.name'])->implode('name', ', ');
            });
            $table->editColumn('shop.name', function (Banner $banner) {
                if (!$banner->shop) return null;
                return sprintf('<a href="%s">%s</a>', route('staff.shop.edit', $banner->shop->id), $banner->shop->name);
            });
            $table->filterColumn('tags', function ($query, $keyword) {
                /* @var Builder $query */
                $query->whereHas('tags', function ($q) use ($keyword) {
                    /* @var Builder $q */
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            });
            $table->addColumn('actions', function (Banner $banner) {
                return '<a href="' . route('staff.setting.banner.edit', $banner->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a><button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger" data-id="' . $banner->id . '"><i class="fa fa-trash"></i></button>';
            });
            $table->rawColumns(['image', 'shop.name', 'actions']);
            return $table->make(true);
        }
        return view('staff.banner.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $tags = Tag::all();
        $shops = Shop::query()->where('status', 1)->get();
        return view('staff.banner.create', compact('tags', 'shops'));
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
            'title' => 'required|max:191',
            'subtitle' => 'nullable|max:191',
            'image' => 'required',
            'shop_id' => 'nullable|exists:shops,id',
            'tags' => 'required'
        ]);
        DB::beginTransaction();
        try {
            /* @var Banner|null $banner */
            $banner = Banner::create($request->only(['title', 'subtitle', 'image', 'shop_id']));
            if (!$banner) throw new \Exception('Unable to create Banner');
            if (is_array($request->tags)) {
                $banner->tags()->attach($request->tags);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('staff.setting.banner.index')->withSuccess('Banner created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Banner $banner
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View|void
     */
    public function edit(Banner $banner)
    {
        if ($banner == null) return abort(404);
        $oldTags = $banner->tags()->pluck('tags.id')->toArray();
        $tags = Tag::all();
        $shops = Shop::query()->where('status', 1)->get();
        return view('staff.banner.edit', compact('banner', 'oldTags', 'tags', 'shops'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Banner $banner
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, Banner $banner)
    {
        if ($banner == null) return abort(404);
        $request->validate([
            'title' => 'required|max:191',
            'subtitle' => 'nullable|max:191',
            'image' => 'required',
            'shop_id' => 'nullable|exists:shops,id',
            'tags' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $res = $banner->update($request->only(['title', 'subtitle', 'image', 'shop_id']));
            if (!$res) throw new \Exception('Unable to update Banner');
            $banner->tags()->detach();
            if (is_array($request->tags)) {
                $banner->tags()->attach($request->tags);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Banner updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Banner $banner
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
        if ($banner == null) return abort(404);
        DB::beginTransaction();
        try {
            $res = $banner->delete();
            if (!$res) throw new \Exception('Unable to delete Banner');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Banner deleted successfully');
    }
}
