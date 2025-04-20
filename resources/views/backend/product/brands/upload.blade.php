@extends('backend.layouts.app')

@section('title')
{{translate('Upload Brands')}}  
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Upload Brands')}}</h5>
        </div>
        <div class="card-body">
            <div>
                <p>1. {{translate('Download the brands file and fill it with proper data')}}.</p>
                <p>2. {{translate('Once you have downloaded and filled the brands file, upload it in the form below and submit')}}.</p>
            </div>
            <br>
            <div class="">
                <a href="{{ asset('download/brands_demo.xlsx') }}" download><button class="btn btn-info">{{ translate('Download CSV')}}</button></a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><strong>{{translate('Upload Brand File')}}</strong></h5>
        </div>
        <div class="card-body">
            <form class="form-horizontal" action="{{ route('brand_bulk_upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <div class="col-sm-9">
                        <div class="custom-file">
    						<label class="custom-file-label">
    							<input type="file" name="bulk_file" class="custom-file-input" required>
    							<span class="custom-file-name">{{ translate('Choose File')}}</span>
    						</label>
    					</div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-info">{{translate('Upload CSV')}}</button>
                </div>
            </form>
        </div>
    </div>

@endsection
