@extends('shop.layouts.app')

@section('page_title', 'Edit Product')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }

        .accordion {
            min-height: unset;
            cursor: pointer;
        }

        .bootstrap-tagsinput {
            width: 100%;
        }

        .vw-100 {
            width: 100vw !important;
        }

        .ovx {
            overflow-x: scroll;
        }

        .cursor-pointer {
            cursor: pointer;
            float: right;
        }
    </style>
@endsection

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Products</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.catalog.product.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
            </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    <div id="app" v-cloak>
            
            <form action="{{ route('shop.catalog.product.update', $product->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="card">
                    <div
                            class="card-header d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            General Info
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg btn-round">Update
                            </button>
                            <button type="reset" class="btn btn-danger btn-lg btn-round">Reset</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Title</label>
                                        <input type="text" name="title"
                                               class="form-control" v-model="title" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Slug</label>
                                        <input type="text" name="slug"
                                               class="form-control" v-model="slug" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Category</label>
                                        <select2 name="category_id" class="form-control" v-model="category_id" required>
                                            @foreach($categories as $category)
                                                <option
                                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select2>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tags" class="control-label">Tags</label>
                                        <select name="tags[]" class="form-control" id="tags" multiple>
                                            @foreach($tags as $tag)
                                                <option
                                                        value="{{ $tag->id }}" {{ (is_array($product_tag_ids) && in_array($tag->id, $product_tag_ids))?'selected':'' }}>{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">General Price</label>
                                        <div class="input-group">
                                            <input type="number" min="0" step="0.01" name="general_price"
                                                   v-model="general_price"
                                                   class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ $currency->code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Sale Price</label>
                                        <div class="input-group">
                                            <input type="number" min="0" step="0.01" name="sale_price"
                                                   v-model="sale_price"
                                                   class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ $currency->code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Tax</label>
                                        <div class="input-group">
                                            <input type="number" min="0" step="0.01" name="tax" v-model="tax"
                                                   class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">{{ $currency->code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Unit</label>
                                        <input type="text" name="unit" v-model="unit"
                                               class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Per x Unit</label>
                                        <div class="input-group">
                                            <input type="text" name="per" value="{{ $product->per??1 }}"
                                                   class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">@{{ unit }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">SKU (Optional)</label>
                                        <input type="text" name="sku" v-model="sku" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Stock (Optional)</label>
                                        <div class="input-group">
                                            <input type="text" name="stock" value="{{ $product->stock??-1 }}"
                                                   class="form-control">
                                            <div class="input-group-append">
                                                <span class="input-group-text">@{{ unit }}</span>
                                            </div>
                                        </div>
                                        <p class="text-info">-1 for unlimited</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Delivery Time Type</label>
                                        <select name="delivery_time_type" v-model="delivery_time_type"
                                                class="form-control" required>
                                            <option value="1">Hour</option>
                                            <option value="2">Day</option>
                                            <option value="3">Week</option>
                                            <option value="4">Month</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Delivery Time</label>
                                        <div class="input-group">
                                            <input type="text" name="delivery_time"
                                                   value="{{ $product->delivery_time??1 }}"
                                                   class="form-control" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                      v-if="delivery_time_type == '1'">Hour(s)</span>
                                                <span class="input-group-text" v-else-if="delivery_time_type == '2'">Day(s)</span>
                                                <span class="input-group-text" v-else-if="delivery_time_type == '3'">Week(s)</span>
                                                <span class="input-group-text" v-else-if="delivery_time_type == '4'">Month(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <img :src="image"
                                     class="img-fluid">
                                <input type="hidden" name="image" v-model="image">
                                <button class="btn btn-success cag-btn"
                                        @click.prevent="selectImage">
                                    Select Image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card accordion py-2 my-3">
                    <div
                            class="card-header mb-2 d-flex justify-content-between align-content-center"
                            data-toggle="collapse" data-target="#details" aria-expanded="false" aria-controls="details">
                        <div class="pull-left">
                            Product Details
                        </div>
                    </div>
                    <div class="card-body mt-3 collapse" id="details">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea name="content" id="content"
                                          class="form-control editor">{!! clean($product->content) !!}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Excerpt</label>
                                <textarea name="excerpt" id="excerpt"
                                          class="form-control">{{ $product->excerpt }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card accordion py-2 my-3">
                    <div
                            class="card-header mb-2 d-flex justify-content-between align-content-center"
                            data-toggle="collapse" data-target="#attributes" aria-expanded="false"
                            aria-controls="attributes">
                        <div class="pull-left">
                            Attributes
                        </div>
                    </div>
                    <div class="card-body mt-3 collapse" id="attributes">
                        <div class="row align-items-center justify-content-between"
                             v-for="(attribute, index) in attributes" :key="index">
                            <input type="hidden" :id="'attribute-id-' + index"
                                   :name="'attributes[' + index + '][attribute_id]'" class="form-control"
                                   v-model="attribute.attribute_id">
                            <template v-if="attribute.attribute_id == null">
                                <div class="col-md-5">
                                    <attribute-component :attribute="attribute" :index="index"/>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label :for="'attribute-content-' + index">Value (Separated By |)</label>
                                        <select2 tags="true" :id="'attribute-content-' + index"
                                                 :name="'attributes[' + index + '][content][]'" class="form-control"
                                                 v-model="attribute.content" placeholder="S|M|L|XL|XXL"
                                                 :options="attribute.options" multiple></select2>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-danger btn-block"
                                            @click.prevent="removeAttr(index)"><i class="fa fa-times"></i></button>
                                </div>
                            </template>
                            <template v-else>
                                <div class="col-md-5">
                                    @{{ attribute.title }}
                                    <input type="hidden" :id="'attribute-title-' + index"
                                           :name="'attributes[' + index + '][title]'" class="form-control"
                                           v-model="attribute.title">
                                    <input type="hidden" :id="'attribute-name-' + index"
                                           :name="'attributes[' + index + '][name]'" class="form-control"
                                           v-model="attribute.name">
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label :for="'attribute-content-' + index">Terms</label>
                                        <select2 :id="'attribute-content-' + index"
                                                 :name="'attributes[' + index + '][content][]'" class="form-control"
                                                 v-model="attribute.content" multiple>
                                            <option v-for="(term, termIndex) in attribute.attribute.terms"
                                                    :key="termIndex" :value="term.slug">@{{ term.name }}
                                            </option>
                                        </select2>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-danger btn-block "
                                            @click.prevent="removeAttr(index)"><i class="fa fa-times"></i></button>
                                </div>
                            </template>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <select class="form-control form-control-lg" v-model="add_attribute_id">
                                        <option value="-1">Custom Attribute</option>
                                        <option v-for="(attr, attrIndex) in all_attributes" :key="attrIndex"
                                                :value="attrIndex" :disabled="attr.disabled">@{{ attr.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group text-right">
                                    <button class="btn btn-primary btn-block"
                                            @click.prevent="addAttr"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card accordion py-2 my-3">
                    <div
                            class=" card-header mb-2 d-flex justify-content-between align-content-center"
                            data-toggle="collapse" data-target="#variations" aria-expanded="false"
                            aria-controls="variations">
                        <div class="pull-left">
                            <h5>Variations</h5>
                        </div>
                    </div>
                    <div class="card-body mt-3 collapse" id="variations">
                        <div class="form-group text-right">
                            <button class="btn btn-sm btn-primary"
                                    @click.prevent="generateVariations">Regenerate Variations
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="nav flex-column nav-pills" id="v-variations-tab" role="tablist"
                                     aria-orientation="vertical">
                                    <a v-for="(v, i) in variations" :key="i" :class="{active: i === 0}" class="nav-link"
                                       :id="'variation-tab-' + i" data-toggle="pill" :href="'#variation-content-' + i"
                                       role="tab" :aria-controls="'variation-content-' + i" :aria-selected="i === 0">Variation
                                        @{{ i+1 }}
                                        <i class="fa fa-times cursor-pointer text-danger"
                                           @click.prevent="removeVariation(variation_index)"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content" id="v-variations-tabContent">
                                    <div v-for="(variation, variation_index) in variations" :key="variation_index"
                                         :class="{show: variation_index === 0, active: variation_index === 0}"
                                         class="tab-pane fade" :id="'variation-content-' + variation_index"
                                         role="tabpanel"
                                         :aria-labelledby="'variation-tab-' + variation_index">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label
                                                            :for="'variation-' + variation_index + '-title'">Title</label>
                                                    <input :id="'variation-' + variation_index + '-title'" type="text"
                                                           class="form-control" v-model="variation.title"
                                                           :name="'variations[' + variation_index + '][title]'">
                                                    <input type="hidden" v-model="variation.slug"
                                                           :name="'variations[' + variation_index + '][slug]'">
                                                    <input type="hidden" v-model="variation.id"
                                                           :name="'variations[' + variation_index + '][id]'">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="some" style="visibility: hidden;">Remove
                                                        Variation</label>

                                                </div>
                                            </div>
                                            <div class="col-md-6" v-for="(attribute, attribute_index) in attributes"
                                                 :key="attribute_index">
                                                <div class="form-group">
                                                    <label
                                                            :for="'variation-' + variation_index + '-attribute-' + attribute_index">@{{
                                                        attribute.title }}</label>
                                                    <select
                                                            v-model="variation.attributes[attribute.name]"
                                                            :id="'variation-' + variation_index + '-attribute-' + attribute_index"
                                                            class="form-control"
                                                            :name="'variations[' + variation_index + '][attributes][' + attribute.name + ']'">
                                                        <option v-for="(v, i) in splitAttributeOptions(attribute)"
                                                                :key="i"
                                                                :value="v">
                                                            @{{ v }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label :for="'variation-' + variation_index + '-sku'">SKU</label>
                                                    <input type="text" v-model="variation.sku"
                                                           :name="'variations[' + variation_index + '][sku]'"
                                                           :id="'variation-' + variation_index + '-sku'"
                                                           class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label
                                                            :for="'variation-' + variation_index + '-stock'">Stock</label>
                                                    <div class="input-group">
                                                        <input type="text" v-model="variation.stock"
                                                               :name="'variations[' + variation_index + '][stock]'"
                                                               :id="'variation-' + variation_index + '-stock'"
                                                               class="form-control">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">@{{ unit }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label :for="'variation-' + variation_index + '-general-price'">General
                                                        Price</label>
                                                    <div class="input-group">
                                                        <input type="number" min="0" step="0.01"
                                                               v-model="variation.general_price"
                                                               :name="'variations[' + variation_index + '][general_price]'"
                                                               :id="'variation-' + variation_index + '-general-price'"
                                                               class="form-control">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $currency->code }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label :for="'variation-' + variation_index + '-sale-price'">Sale
                                                        Price</label>
                                                    <div class="input-group">
                                                        <input type="number" min="0" step="0.01"
                                                               :name="'variations[' + variation_index + '][sale_price]'"
                                                               v-model="variation.sale_price"
                                                               :id="'variation-' + variation_index + '-sale-price'"
                                                               class="form-control">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $currency->code }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label :for="'variation-' + variation_index + '-tax'">Tax</label>
                                                    <div class="input-group">
                                                        <input type="number" min="0" step="0.01"
                                                               :name="'variations[' + variation_index + '][tax]'"
                                                               v-model="variation.tax"
                                                               :id="'variation-' + variation_index + '-tax'"
                                                               class="form-control">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">{{ $currency->code }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <img :src="variation.image" class="img-fluid">
                                                    <input type="hidden"
                                                           :name="'variations[' + variation_index + '][image]'"
                                                           v-model="variation.image">
                                                    <button class="btn btn-success cag-btn"
                                                            @click.prevent="selectVariationImage(variation_index)">
                                                        Select Image
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card accordion py-2 my-3">
                    <div
                            class=" card-header mb-2 d-flex justify-content-between align-content-center"
                            data-toggle="collapse" data-target="#meta-info" aria-expanded="false"
                            aria-controls="meta-info">
                        <div class="pull-left">
                            <h5>Meta Info</h5>
                        </div>
                    </div>
                    <div class=" card-body mt-3 collapse" id="meta-info">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="control-label">Meta Title</label>
                                <input type="text" name="meta[title]"
                                       value="{{ isset($metas['title'])?$metas['title']:null }}"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Meta Description</label>
                                <textarea name="meta[description]"
                                          class="form-control">{{ isset($metas['description'])?$metas['description']:null }}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Meta Keywords (separate by comma)</label>
                                <input type="text" name="meta[keywords]"
                                       value="{{ isset($metas['keywords'])?$metas['keywords']:null }}"
                                       class="form-control tags">
                            </div>
                            <div class="form-group form-og-wrap">
                                <img src="{{ isset($metas['og_image'])?$metas['og_image']:'//via.placeholder.com/500x200' }}"
                                     id="og-image-preview" class="w-100">
                                <input type="hidden" id="og-image-input" name="meta[og_image]"
                                       value="{{ isset($metas['og_image'])?$metas['og_image']:null }}"
                                       class="form-control">
                                <button class="btn btn-success btn-block btn-select"
                                        data-input="#og-image-input" data-preview="#og-image-preview"
                                        data-prop="url"
                                        data-title="Select Image">Select Image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($languages as $language)
                    <div class=" card accordion py-2 my-3">
                        <div
                                class=" card-header mb-2 d-flex justify-content-between align-content-center"
                                data-toggle="collapse" data-target="#translation-{{ $language->code }}"
                                aria-expanded="false" aria-controls="translation-{{ $language->code }}">
                            <div class="pull-left">
                                <h5>Translation ({{ $language->code }})</h5>
                            </div>
                        </div>
                        <div class=" card-body mt-3 collapse" id="translation-{{ $language->code }}">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">Title</label>
                                    <input type="text" name="lang[{{ $language->code }}][title]"
                                           value="{{ (is_array($translations) && isset($translations[$language->code]) && is_array($translations[$language->code]))?$translations[$language->code]['title']:'' }}"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea name="lang[{{ $language->code }}][content]"
                                              class="form-control editor">{!! clean((is_array($translations) && isset($translations[$language->code]) && is_array($translations[$language->code]))?$translations[$language->code]['content']:'') !!}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Excerpt</label>
                                    <textarea name="lang[{{ $language->code }}][excerpt]"
                                              class="form-control">{{ (is_array($translations) && isset($translations[$language->code]) && is_array($translations[$language->code]))?$translations[$language->code]['excerpt']:'' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </form>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/js/vue.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('staff/vendors/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('staff/js/select2.vue.js') }}"></script>
@endsection

@section('js')
    <script>
        var editor_config = {
            path_absolute: "{{ url('/') }}/",
            selector: ".editor",
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
            relative_urls: false,
            file_browser_callback: function (field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + 'shop/media?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                    var title = 'Select Images'
                } else {
                    cmsURL = cmsURL + "&type=Files";
                    var title = 'Select FIles'
                }

                tinyMCE.activeEditor.windowManager.open({
                    file: cmsURL,
                    title: title,
                    width: x * 0.8,
                    height: y * 0.8,
                    resizable: "yes",
                    close_previous: "no"
                });
            }
        };

        tinymce.init(editor_config);
    </script>
    <script>
        (function ($) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $(document).ready(function () {
                // $('.select2').select2();
                $('#tags').select2({
                    tags: true,
                    tokenSeparators: [','],
                    createTag: function (params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: term,
                            text: term
                        }
                    }
                }).on('change', function (e) {
                    var isNew = $(this).find('[data-select2-tag="true"]');
                    if (isNew.length && $.inArray(isNew.val(), $(this).val()) !== -1) {
                        $.post('{{ route('shop.catalog.tag.store') }}', {
                            _token: '{{ csrf_token() }}',
                            name: isNew.val()
                        }).done(tag => {
                            isNew.replaceWith('<option selected value="' + tag.id + '">' + tag.name + '</option>');
                        })
                    }
                })
            })
            $(document).on('click', '.btn-select', function (e) {
                e.preventDefault()
                var input = $($(this).data('input'))
                var preview = $($(this).data('preview'))
                var title = $(this).data('title')
                var prop = $(this).data('prop')
                openMediaManager(items => {
                    if (items[0] && items[0].hasOwnProperty(prop)) {
                        preview.attr('src', items[0][prop])
                        input.val(items[0][prop])
                    }
                }, 'image', title || 'Select Icon')
            })
        })(jQuery)
    </script>
    <script>
        window.makeSlug = function (str) {
            if (!str) return;
            const a = 'àáäâãåăæçèéëêǵḧìíïîḿńǹñòóöôœøṕŕßśșțùúüûǘẃẍÿź·/_,:;'
            const b = 'aaaaaaaaceeeeghiiiimnnnooooooprssstuuuuuwxyz------'
            const p = new RegExp(a.split('').join('|'), 'g')

            return str.toString().toLowerCase()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
                .replace(/&/g, '-and-') // Replace & with ‘and’
                .replace(/[^\w\-]+/g, '') // Remove all non-word characters
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, '') // Trim - from end of text
        }
        Vue.component('attribute-component', {
            props: ["attribute", "index"],
            template: `
        <div class="form-group">
            <label :for="'attribute-title-' + index">Attribute Title</label>
            <input type="text" :id="'attribute-title-' + index"
                   :name="'attributes[' + index + '][title]'" class="form-control"
                   v-model="attribute.title" placeholder="Size">
            <input type="hidden" :name="'attributes[' + index + '][name]'" v-model="attribute.name">
        </div>
`,
            watch: {
                'attribute.title': {
                    handler: function (newValue) {
                        this.attribute.name = window.makeSlug(newValue)
                    },
                    deep: true
                }
            }
        });
        window.app = new Vue({
            el: '#app',
            components: {
                select2Vue
            },
            data: {
                title: '{{ $product->title??'' }}',
                category_id: '{{ $product->category_id??'' }}',
                shop_id: '{{ $product->shop_id??'' }}',
                image: '{{ $product->image??'https://via.placeholder.com/200' }}',
                slug: '{{ $product->slug??'' }}',
                sku: '{{ $product->sku??'' }}',
                general_price: '{{ $product->general_price??0 }}',
                sale_price: '{{ $product->sale_price??0 }}',
                tax: '{{ $product->tax??0 }}',
                unit: '{{ $product->unit??'kg' }}',
                delivery_time_type: '{{ $product->delivery_time_type??2 }}',
                attributes: @json($attributes??[]),
                variations: @json($variations??[]),
                add_attribute_id: -1,
                all_attributes: @json($all_attributes),
            },
            beforeMount() {
                for (const i in this.attributes) {
                    const attr = this.attributes[i]
                    if (attr.attribute_id != null || attr.attribute_id !== '') {
                        const realAttr = this.all_attributes.filter(r => {
                            return parseInt(r.id) === parseInt(attr.attribute_id)
                        })
                        if (realAttr.length) {
                            attr.attribute = realAttr[0]
                            attr.attribute.disabled = true
                        }
                    }
                    if (typeof attr.content) {
                        attr.options = attr.content.map(sl => {
                            return {id: sl, text: sl}
                        })
                    }
                }
            },
            methods: {
                addAttr() {
                    if (parseInt(this.add_attribute_id) == -1) {
                        this.attributes.push({
                            title: null,
                            name: null,
                            content: null,
                            attribute_id: null,
                            attribute: null
                        })
                    } else {
                        const attr = this.all_attributes[parseInt(this.add_attribute_id)]
                        this.attributes.push({
                            title: attr.name,
                            name: attr.slug,
                            content: [],
                            attribute_id: attr.id,
                            attribute: attr
                        })
                        this.all_attributes[parseInt(this.add_attribute_id)].disabled = true
                        this.add_attribute_id = -1
                    }
                },
                removeAttr(index) {
                    const attr = this.attributes[index]
                    if (attr.attribute) {
                        attr.attribute.disabled = false
                    }
                    this.add_attribute_id = -1
                    this.attributes.splice(index, 1);
                },
                splitAttributeOptions(attribute) {
                    if (!attribute) return []
                    if (!attribute.hasOwnProperty('content')) return []
                    if (!attribute.content) return []
                    return attribute.content
                },
                makeSlug(str) {
                    return window.makeSlug(str)
                },
                removeVariation(index) {
                    this.variations.splice(index, 1)
                },
                selectImage() {
                    openMediaManager(items => {
                        if (items[0] && items[0].hasOwnProperty('thumb_url')) {
                            this.image = items[0].thumb_url
                        }
                    }, 'image', 'Select Image')
                },
                selectVariationImage(index) {
                    openMediaManager(items => {
                        if (items[0] && items[0].hasOwnProperty('thumb_url')) {
                            this.variations[index].image = items[0].thumb_url
                        }
                    }, 'image', 'Select Image')
                },
                generateVariations() {
                    _app = this
                    $.post("{{ route('shop.catalog.product.variations') }}", {
                        _token: "{{ csrf_token() }}",
                        attributes: this.attributes,
                        variations: this.variations,
                        title: this.title,
                        slug: this.slug,
                        sku: this.sku
                    }).done(response => {
                        const count = response.count || 0
                        const variations = response.variations
                        if (count) {
                            variations.forEach(variation => {
                                variation.image = _app.image
                                variation.general_price = _app.general_price
                                variation.sale_price = _app.sale_price
                                variation.tax = _app.tax
                                variation.stock = -1
                                _app.variations.push(variation)
                            })
                        }
                    })
                }
            },
            watch: {
                title: {
                    handler: function (newValue) {
                        this.slug = window.makeSlug(newValue)
                    },
                    deep: true
                }
            }
        })
    </script>
@endsection
