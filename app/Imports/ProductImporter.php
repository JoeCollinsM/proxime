<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class ProductImporter implements OnEachRow, WithValidation, WithHeadingRow
{
    use Importable;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        /* @var Product $product */
        $product = Product::find($row['id']);
        if (!$product) {
            $product = new Product;
        }
        $product->category_id = $row['category_id'];
        $product->shop_id = $row['shop_id'];
        $product->title = $row['title'];
        $product->slug = $product->slug ?? Str::slug($row['title'] . '-' . uniqid());
        $product->excerpt = $row['excerpt'];
        $product->content = $row['description'];
        $product->image = $row['image'];
        $product->per = $row['per_x_unit'];
        $product->unit = $row['unit'];
        $product->sale_price = $row['sale_price'];
        $product->general_price = $row['general_price'];
        $product->tax = $row['tax'];
        $product->sku = $row['sku'];
        $product->stock = $row['stock'];
        $product->delivery_time = $row['delivery_time'];
        if ($row['delivery_time_type'] == 'hour') {
            $product->delivery_time_type = 1;
        } elseif ($row['delivery_time_type'] == 'day') {
            $product->delivery_time_type = 2;
        } elseif ($row['delivery_time_type'] == 'week') {
            $product->delivery_time_type = 3;
        } elseif ($row['delivery_time_type'] == 'month') {
            $product->delivery_time_type = 4;
        }
        $product->is_free_shipping = $row['is_free_shipping'];
        $product->status = $row['status'];
        if ($row['tags']) {
            $tagNames = explode(',', $row['tags']);
            $tagIds = [];
            foreach ($tagNames as $n) {
                $tag = Tag::query()->updateOrCreate([
                    'name' => $n
                ]);
                $tagIds[] = $tag->id;
            }
            $product->tags()->detach();
            $product->tags()->attach($tagIds);
        }
        $product->metas()->updateOrCreate(['name' => 'title'], ['content' => $row['meta_title'] ?? $product->title]);
        $product->metas()->updateOrCreate(['name' => 'description'], ['content' => $row['meta_description'] ?? $product->excerpt]);
        $product->metas()->updateOrCreate(['name' => 'keywords'], ['content' => $row['meta_keywords'] ?? $row['tags']]);
        $product->metas()->updateOrCreate(['name' => 'og_image'], ['content' => $row['meta_image'] ?? $row['image']]);
        return $product;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'nullable|numeric|exists:products,id',
            '*.id' => 'nullable|numeric|exists:products,id',
            'category_id' => 'required|numeric|exists:categories,id',
            '*.category_id' => 'required|numeric|exists:categories,id',
            'shop_id' => 'required|numeric|exists:shops,id',
            '*.shop_id' => 'required|numeric|exists:shops,id',
            'title' => 'required|max:191',
            '*.title' => 'required|max:191',
            'excerpt' => 'nullable',
            '*.excerpt' => 'nullable',
            'description' => 'nullable',
            '*.description' => 'nullable',
            'image' => 'required|max:191',
            '*.image' => 'required|max:191',
            'per_x_unit' => 'required|numeric',
            '*.per_x_unit' => 'required|numeric',
            'unit' => 'required|string|max:191',
            '*.unit' => 'required|string|max:191',
            'general_price' => 'required|numeric',
            '*.general_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            '*.sale_price' => 'required|numeric',
            'tax' => 'required|numeric',
            '*.tax' => 'required|numeric',
            'sku' => 'nullable|string|max:191',
            '*.sku' => 'nullable|string|max:191',
            'stock' => 'nullable|numeric',
            '*.stock' => 'nullable|numeric',
            'delivery_time_type' => 'required|in:hour,day,week,month',
            '*.delivery_time_type' => 'required|in:hour,day,week,month',
            'delivery_time' => 'required|numeric',
            '*.delivery_time' => 'required|numeric',
            'status' => 'required|numeric|in:0,1,2',
            '*.status' => 'required|numeric|in:0,1,2',
            'is_free_shipping' => 'required|numeric|in:0,1',
            '*.is_free_shipping' => 'required|numeric|in:0,1',
            'parent_id' => 'nullable|exists:products,id'
        ];
    }

    /**
     * @param Row $rowData
     * @return mixed
     */
    public function onRow(Row $rowData)
    {
        $rowIndex = $rowData->getIndex();
        $row = $rowData->toArray();

        /* @var Product $product */
        $product = Product::find($row['id']);
        if (!$product) {
            $product = new Product;
        }
        $product->parent_id = $row['parent_id']??null;
        $product->category_id = $row['category_id'];
        $product->shop_id = $row['shop_id'];
        $product->title = $row['title'];
        $product->slug = $product->slug ?? Str::slug($row['title'] . '-' . uniqid());
        $product->excerpt = $row['excerpt'];
        $product->content = $row['description'];
        $product->image = $row['image'];
        $product->per = $row['per_x_unit'];
        $product->unit = $row['unit'];
        $product->sale_price = $row['sale_price'];
        $product->general_price = $row['general_price'];
        $product->tax = $row['tax'];
        $product->sku = $row['sku'];
        $product->stock = $row['stock'];
        $product->delivery_time = $row['delivery_time'];
        if ($row['delivery_time_type'] == 'hour') {
            $product->delivery_time_type = 1;
        } elseif ($row['delivery_time_type'] == 'day') {
            $product->delivery_time_type = 2;
        } elseif ($row['delivery_time_type'] == 'week') {
            $product->delivery_time_type = 3;
        } elseif ($row['delivery_time_type'] == 'month') {
            $product->delivery_time_type = 4;
        }
        $product->is_free_shipping = $row['is_free_shipping'];
        $product->status = $row['status'];

        $r = $product->save();
        if (!$r) throw ValidationException::withMessages(['Product not saving on row ' . ($rowIndex+1)]);
        $product->refresh();

        $attributes = [];
        if (isset($row['attributes'])) {
            $attributes = explode(',', $row['attributes']);
        }

        if (count($attributes)) {
            foreach ($attributes as $attributeKey) {
                $attributeValue = $row[$attributeKey];
                if (!$row['parent_id']) {
                    $attributeValue = str_replace(',', '|', $attributeValue);
                }
                $product->attrs()->updateOrCreate(['name' => Str::slug($attributeKey)], ['title' => $attributeKey, 'content' => $attributeValue]);
            }
        }

        if ($row['tags']) {
            $tagNames = explode(',', $row['tags']);
            $tagIds = [];
            foreach ($tagNames as $n) {
                $tag = Tag::query()->updateOrCreate([
                    'name' => $n
                ]);
                $tagIds[] = $tag->id;
            }
            $product->tags()->detach();
            $product->tags()->attach($tagIds);
        }
        $product->metas()->updateOrCreate(['name' => 'title'], ['content' => $row['meta_title'] ?? $product->title]);
        $product->metas()->updateOrCreate(['name' => 'description'], ['content' => $row['meta_description'] ?? $product->excerpt]);
        $product->metas()->updateOrCreate(['name' => 'keywords'], ['content' => $row['meta_keywords'] ?? $row['tags']]);
        $product->metas()->updateOrCreate(['name' => 'og_image'], ['content' => $row['meta_image'] ?? $row['image']]);
    }
}
