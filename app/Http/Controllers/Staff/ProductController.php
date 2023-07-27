<?php

namespace App\Http\Controllers\Staff;

use App\Models\Attribute;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Currency;
use App\Imports\ProductImporter;
use App\Models\Language;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTableAbstract;

class ProductController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query()->with(['shop', 'category'])->whereNull('parent_id');
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);

            $table->editColumn('title', function (Product $product) {
                return $product->title . '<br>' . $product->rating_html . '<a href="' . route('staff.catalog.review.index', ['type' => 'product', 'type_id' => $product->id]) . '">More...</a>';
            });

            $table->editColumn('shop.name', function (Product $product) {
                return $product->shop->name . '<br>' . $product->shop->rating_html . '<a href="' . route('staff.catalog.review.index', ['type' => 'shop', 'type_id' => $product->shop->id]) . '">More...</a>';
            });

            $table->editColumn('sale_price', function (Product $product) {
                $currency = Currency::getDefaultCurrency();
                return sprintf('<span class="badge badge-info">%s %s <del>%s %s</del>/%s %s</span>', $product->sale_price, $currency->code, $product->general_price, $currency->code, $product->per, $product->unit);
            });

            $table->editColumn('stock', function (Product $product) {
                if ($product->stock == -1) {
                    return sprintf('<span class="badge badge-info">∞ %s</span>', $product->unit);
                }
                return sprintf('<span class="badge badge-info">%s %s</span>', $product->stock, $product->unit);
            });

            $table->editColumn('status', function (Product $product) {
                if ($product->status == 1) {
                    return '<span class="badge badge-success">Enabled</span>';
                } elseif ($product->status == 2) {
                    return '<span class="badge badge-danger">Disabled</span>';
                } else {
                    return '<span class="badge badge-warning">Pending</span>';
                }
            });

            $table->addColumn('actions', function (Product $product) {
                return '<a href="' . route('staff.catalog.product.edit', $product->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a><button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger" data-id="' . $product->id . '"><i class="fa fa-trash"></i></button>';
            });

            $table->filterColumn('shop.name', function ($query, $keyword) {
                /* @var Builder $query */
                $query->whereHas('shop', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%")->orWhere('vendor_name', 'LIKE', "%$keyword%");
                });
            });
            $table->filterColumn('category.name', function ($query, $keyword) {
                /* @var Builder $query */
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            });
            $table->rawColumns(['title', 'shop.name', 'stock', 'status', 'sale_price', 'actions']);
            return $table->make(true);
        }
        return view('staff.product.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::query()->where('status', '!=', 0)->get();
        $shops = Shop::query()->where('status', 1)->get();
        $currency = Currency::getDefaultCurrency();
        $languages = Language::query()->where('status', 1)->get();
        $tags = Tag::all();
        $attributes = Attribute::query()->with('terms')->get();
        return view('staff.product.create', compact('categories', 'attributes', 'shops', 'currency', 'languages', 'tags'));
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
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|max:255|unique:products,slug',
            'category_id' => 'required|numeric|exists:categories,id',
            'shop_id' => 'required|numeric|exists:shops,id',
            'general_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'tax' => 'required|numeric',
            'unit' => 'required|string|max:255',
            'per' => 'required|numeric',
            'sku' => 'nullable|string|max:255',
            'stock' => 'nullable|numeric',
            'delivery_time_type' => 'required|numeric|in:1,2,3,4',
            'delivery_time' => 'required|numeric',
            'image' => 'required|string|max:255',
            'status' => 'required|numeric|in:0,1,2',
            'is_free_shipping' => 'required|numeric|in:0,1',
            'content' => 'nullable',
            'excerpt' => 'nullable',
            'attributes.*.attribute_id' => 'nullable|exists:attributes,id',
            'attributes.*.title' => 'required|string|max:255',
            'attributes.*.name' => 'required|alpha_dash|max:255',
            'attributes.*.content' => 'required|array',
            'variations.*.title' => 'required|string|max:255',
            'variations.*.slug' => 'required|alpha_dash|max:255|unique:products,slug',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.stock' => 'nullable|numeric',
            'variations.*.general_price' => 'required|numeric',
            'variations.*.sale_price' => 'required|numeric',
            'variations.*.image' => 'required|string|max:255',
            'variations.*.attributes' => 'required|array'
        ], [
            'category_id.*' => 'Please select a valid product category',
            'shop_id.*' => 'Please select a valid shop',
            'delivery_time_type.*' => 'Please select a valid delivery time type',
            'status.*' => 'Please select a valid product status',
            'attributes.*' => 'Please add product attributes using proper way',
            'variations.*' => 'Please add product variations using proper way'
        ]);
        DB::beginTransaction();
        try {
            $productParams = $request->only(['title', 'slug', 'category_id', 'shop_id', 'general_price', 'sale_price', 'tax', 'unit', 'per', 'sku', 'stock', 'delivery_time_type', 'delivery_time', 'image', 'status', 'is_free_shipping', 'content', 'excerpt']);
            /* @var Product|null $product */
            $product = Product::create($productParams);
            if (is_array($request->tags)) {
                $product->tags()->attach($request->tags);
            }
            if (is_array($request->meta)) {
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = [
                        'name' => $name,
                        'content' => $content
                    ];
                }
                $product->metas()->createMany($metas);
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
                $product->translations()->createMany($translations);
            }
            if ($request->input('attributes') && is_array($request->input('attributes'))) {
                $product->attrs()->createMany($request->input('attributes'));
            }
            if ($request->input('variations') && is_array($request->input('variations'))) {
                foreach ($request->input('variations') as $variation) {
                    $params = Arr::only($variation, ['title', 'slug', 'general_price', 'sale_price', 'tax', 'sku', 'stock', 'image']);
                    /* @var Product|null $var */
                    $var = $product->variations()->create($params);
                    if (is_array($variation['attributes'])) {
                        $attrs = [];
                        foreach ($variation['attributes'] as $name => $content) {
                            $attrs[] = [
                                'title' => $name,
                                'name' => $name,
                                'content' => $content,
                            ];
                        }
                        $var->attrs()->createMany($attrs);
                    }
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('staff.catalog.product.index')->withSuccess('Product added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Product $product)
    {
        if (!$product) return abort(404);
        $variations = $product->variations()->get()->map(function (Product $variation) {
            $variation->setRelation('attributes', $variation->attrs()->pluck('content', 'name'));
            return $variation;
        });
        $metas = $product->metas()->pluck('content', 'name');
        $attributes = $product->attrs()->with(['attribute' => function ($q) {
            $q->with('terms');
        }])->get();
        /* @var Collection $translations */
        $translations = $product->translations;
        $translations = $translations->groupBy('language')->map(function ($items) {
            return collect($items)->pluck('content', 'column_name');
        })->toArray();
        $tags = Tag::all();
        $product_tag_ids = $product->tags()->pluck('tags.id')->toArray();

        $categories = Category::query()->where('status', '!=', 0)->get();
        $shops = Shop::query()->where('status', 1)->get();
        $currency = Currency::getDefaultCurrency();
        $languages = Language::query()->where('status', 1)->get();
        $all_attributes = Attribute::query()->with('terms')->get();
        return view('staff.product.edit', compact('all_attributes', 'tags', 'product_tag_ids', 'product', 'variations', 'metas', 'translations', 'attributes', 'categories', 'shops', 'currency', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        if (!$product instanceof Product) return abort(404);
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|alpha_dash|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|numeric|exists:categories,id',
            'shop_id' => 'required|numeric|exists:shops,id',
            'general_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'tax' => 'required|numeric',
            'unit' => 'required|string|max:255',
            'per' => 'required|numeric',
            'sku' => 'nullable|string|max:255',
            'stock' => 'nullable|numeric',
            'delivery_time_type' => 'required|numeric|in:1,2,3,4',
            'delivery_time' => 'required|numeric',
            'image' => 'required|string|max:255',
            'status' => 'required|numeric|in:0,1,2',
            'is_free_shipping' => 'required|numeric|in:0,1',
            'content' => 'nullable',
            'excerpt' => 'nullable',
            'attributes.*.attribute_id' => 'nullable|exists:attributes,id',
            'attributes.*.title' => 'required|string|max:255',
            'attributes.*.name' => 'required|alpha_dash|max:255',
            'attributes.*.content' => 'required',
            'variations.*.title' => 'required|string|max:255',
            'variations.*.slug' => 'required|alpha_dash|max:255',
            'variations.*.sku' => 'nullable|string|max:255',
            'variations.*.stock' => 'nullable|numeric',
            'variations.*.general_price' => 'required|numeric',
            'variations.*.sale_price' => 'required|numeric',
            'variations.*.image' => 'required|string|max:255',
            'variations.*.attributes' => 'required|array'
        ], [
            'category_id.*' => 'Please select a valid product category',
            'shop_id.*' => 'Please select a valid shop',
            'delivery_time_type.*' => 'Please select a valid delivery time type',
            'status.*' => 'Please select a valid product status',
            'attributes.*' => 'Please add product attributes using proper way',
            'variations.*' => 'Please add product variations using proper way'
        ]);
        DB::beginTransaction();
        try {
            $productParams = $request->only(['title', 'slug', 'category_id', 'shop_id', 'general_price', 'sale_price', 'tax', 'unit', 'per', 'sku', 'stock', 'delivery_time_type', 'delivery_time', 'image', 'status', 'is_free_shipping', 'content', 'excerpt']);
            $productUpdate = $product->update($productParams);
            $product->tags()->detach();
            if (is_array($request->tags)) {
                $product->tags()->attach($request->tags);
            }
            $product->metas()->delete();
            if (is_array($request->meta)) {
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = [
                        'name' => $name,
                        'content' => $content
                    ];
                }
                $product->metas()->createMany($metas);
            }

            $product->translations()->delete();
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
                $product->translations()->createMany($translations);
            }

            $product->attrs()->delete();
            if ($request->input('attributes') && is_array($request->input('attributes'))) {
                $product->attrs()->createMany($request->input('attributes'));
            }

            if ($request->input('variations') && is_array($request->input('variations'))) {
                foreach ($request->input('variations') as $variation) {
                    $params = Arr::only($variation, ['title', 'slug', 'general_price', 'sale_price', 'tax', 'sku', 'stock', 'image']);
                    /* @var Product|null $var */
                    if (isset($variation['id']) && is_numeric($variation['id']) && ($var = Product::find($variation['id'])) instanceof Product && $var->parent_id == $product->id) {
                        $var->update($params);
                    } else {
                        $var = $product->variations()->create($params);
                    }
                    $var->attrs()->delete();
                    if (is_array($variation['attributes'])) {
                        $attrs = [];
                        foreach ($variation['attributes'] as $name => $content) {
                            $attrs[] = [
                                'title' => $name,
                                'name' => $name,
                                'content' => $content,
                            ];
                        }
                        $var->attrs()->createMany($attrs);
                    }
                }
            }

            CartItem::query()->where('product_id', $product->id)->delete();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('staff.catalog.product.index')->withSuccess('Product updated successfully');
    }

    public function generateAllPossibleVariation(Request $request)
    {
        $response = [
            'count' => 0,
            'variations' => []
        ];
        $limit = is_numeric($request->limit) ? $request->limit : -1;
        $attributes = collect($request->post('attributes'));
        $attributes = $attributes->pluck('content', 'name');
        if (!$attributes->count()) {
            return $response;
        }
        $existing_attributes = [];
        $existing_variations = collect($request->variations);
        foreach ($existing_variations as $existing_variation) {
            $existing_attributes[] = $existing_variation['attributes'];
        }

        $possible_attributes = array_reverse(array_cartesian($attributes->toArray()));
        foreach ($possible_attributes as $possible_attribute) {
            // Allow any order if key/values -- do not use strict mode.
            if (in_array($possible_attribute, $existing_attributes)) {
                continue;
            }

            $response['variations'][] = [
                'title' => $request->title . '-' . implode('-', array_values($possible_attribute)),
                'slug' => $request->slug . '-' . implode('-', array_values($possible_attribute)),
                'sku' => $request->sku ? $request->sku . '-' . implode('-', array_values($possible_attribute)) : '',
                'attributes' => $possible_attribute
            ];

            $response['count']++;

            if ($limit > 0 && $response['count'] >= $limit) {
                break;
            }
        }
        return $response;
    }

    public function destroy(Product $product)
    {
        if (!$product instanceof Product) return abort(404);
        DB::beginTransaction();
        try {
            CartItem::query()->where('product_id', $product->id)->delete();
            $product->attrs()->delete();
            $product->translations()->delete();
            $product->metas()->delete();
            $product->tags()->detach();
            $r = $product->delete();
            if (!$r) throw new \Exception('Unable to delete product');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('staff.catalog.product.index')->withSuccess('Product deleted successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'path' => 'required'
        ]);
        if (!file_exists($request->path)) {
            throw ValidationException::withMessages(['path' => 'Unable to locate excel file']);
        }
        try {
            DB::beginTransaction();
            (new ProductImporter)->import($request->path);
            DB::commit();
            return redirect()->back()->withSuccess('Product Imported Successfully');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $exception) {
            DB::rollBack();
            throw ValidationException::withMessages($exception->errors());
        } catch (\Exception $exception) {
            DB::rollBack();
            throw ValidationException::withMessages(['path' => $exception->getMessage()]);
        }
    }
}
