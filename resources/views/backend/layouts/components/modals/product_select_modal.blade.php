<div class="modal fade" id="fq-bought-product-select-modal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Add Products')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="min-height: 300px;">
                <div class="row gutters-5">
                    <div class="col-md-6">
                        <select class="form-control aiz-selectpicker" name="fq_brough_category" onchange="filterFqBoughtProduct()" data-placeholder="{{ translate('Select a Category')}}" data-live-search="true">
                            <option value="">{{ translate('Choose Category') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                @foreach ($category->childrenCategories as $childCategory)
                                    @include('categories.child_category', ['child_category' => $childCategory])
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search_keyword" onkeyup="filterFqBoughtProduct()" placeholder="{{ translate('Search by Product Name') }}">
                    </div>
                </div>
                <div class="mt-3" id="product-list"></div>
            </div>

            <div class="modal-footer">
                <button 
                    type="button"
                    class="mx-2 btn btn-primary btn-sm rounded-2 fs-14 fw-700 shadow-success action-btn"
                    onclick="addFqBoughtProduct()"
                >{{ translate('Add') }}</button>
            </div>
        </div>
    </div>
</div>