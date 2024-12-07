@extends('layouts.app')

@section('title','Products')

@section('contents')

    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Products</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Products</li>
            </ul>
        </div>

        <form action="{{ route('products.add-basic-info') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <!-- Nav tabs -->
                            @include('admin.products.nav-tabs')

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active p-3" id="basicinfo" role="tabpanel">
                                    <div class="card-body">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Name</label>
                                            <div>
                                                <input data-parsley-type="text" type="text" class="form-control" required placeholder="Enter Product Title" name="product_name">
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Sort Description</label>
                                            <textarea class="editor" name="sort_description" id=""></textarea>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Long Description</label>
                                            <textarea class="editor" name="long_description" id=""></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-light">
                            <div class="d-flex flex-wrap">
                                <span class="me-2">Category</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="category-tree" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                @if (!empty($categorys))
                                    @foreach ($categorys as $category)
                                        <!-- Only display top-level categories -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category{{ $category->id }}" {{ isset($selectedCategories) && in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category{{ $category->id }}"> {{ $category->name }} </label>
                                        </div>
                                        @include('admin.products.subcategory', [
                                            'subcategories' => $category->children,
                                            'parent_id' => $category->id,
                                            'margin' => 20,
                                            'selectedCategories' => isset($selectedCategories) ? $selectedCategories : [],
                                        ])
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header bg-primary text-light">
                            Publish
                        </div>
                        <div class="card-body">
                            {{-- <div class="mb-3">
                                <label class="form-label mb-3 d-flex">Product Type</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline1" name="product_type" class="form-check-input" value="simple" checked>
                                    <label class="form-check-label" for="customRadioInline1">Simple</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline2" name="product_type" class="form-check-input" value="attribute">
                                    <label class="form-check-label" for="customRadioInline2">Attribute</label>
                                </div>
                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label mb-3 d-flex">Veg Or Non Veg</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline1" name="veg_non_veg" class="form-check-input" value="1" checked>
                                    <label class="form-check-label" for="customRadioInline1">Veg</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline2" name="veg_non_veg" class="form-check-input" value="0">
                                    <label class="form-check-label" for="customRadioInline2">Non Veg</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label mb-3 d-flex">Product Availability</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline1" name="is_available" class="form-check-input" value="1" checked>
                                    <label class="form-check-label" for="customRadioInline1">Avaliable</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline2" name="is_available" class="form-check-input" value="0">
                                    <label class="form-check-label" for="customRadioInline2">Not Avaliable</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label mb-3 d-flex">Special Product</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline1" name="is_special" class="form-check-input" value="1">
                                    <label class="form-check-label" for="customRadioInline1">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline2" name="is_special" class="form-check-input" value="0" checked>
                                    <label class="form-check-label" for="customRadioInline2">No</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label mb-3 d-flex">Visiblity</label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline1" name="is_visible" class="form-check-input" value="1" checked>
                                    <label class="form-check-label" for="customRadioInline1">Show</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="customRadioInline2" name="is_visible" class="form-check-input" value="0">
                                    <label class="form-check-label" for="customRadioInline2">Hide</label>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                        Save & Next
                                    </button>
                                    <button type="reset" class="btn btn-secondary waves-effect">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection