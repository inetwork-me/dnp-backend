@extends('backend.layouts.app')

@section('title')
    {{ translate('New Post Type') }}
@endsection

@section('content')
    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-9 mt-4">
            <div class="card statistcs_card add_form m-0 mb-3">
                <div class="card-header">
                    <span>{{ translate('Post Information') }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <input type="text" name="title" class="form-control"
                                placeholder="{{ translate('Page title') }}" required>
                        </div>
                        <div class="form-group col-lg-12">
                            <input type="text" name="slug" class="form-control"
                                placeholder="{{ translate('Slug / URL') }}" required>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3">
                            <ul class="list-group" id="block-sidebar">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-bars mr-2"></i> Block 1
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-bars mr-2"></i> Block 2
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-bars mr-2"></i> Block 3
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-bars mr-2"></i> Block 4
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-bars mr-2"></i> Block 5
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-bars mr-2"></i> Block 6
                                </li>
                                <li class="mt-3">
                                    <button class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-plus mr-1"></i> {{ translate('Add Section') }}
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="col-md-9">
                            <div id="blocks-content">
                                @for ($i = 1; $i <= 6; $i++)
                                    <div class="border rounded p-3 mb-3 d-flex justify-content-between align-items-center" style="border:1px dashed #cfcfcf !important;border-radius:10px !important;">
                                        <div style=" font-size: 18px; color: #5d5c5c; font-weight: 500; padding:5px 10px">Block {{ $i }}</div>
                                        <div class="btn-group">
                                            <button class="btn btn-link text-secondary" title="Preview">
                                                <i class="far fa-eye"></i>
                                            </button>
                                            <button class="btn btn-link text-secondary" title="Edit">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <button class="btn btn-link text-danger" title="Delete">
                                                <i class="far fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- Right Column --}}
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Post</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#">Block</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>

                    <div class="form-group border border-secondary rounded p-3 text-center">
                        <p class="text-muted mb-1">Drop your Image here or <a href="#" class="text-primary">browse</a>
                        </p>
                        <small class="text-muted">Maximum size: 5 MB</small>
                    </div>

                    <div class="form-group">
                        <input type="text" name="tags" class="form-control" placeholder="Tags">
                    </div>

                    <div class="form-group">
                        <input type="text" name="categories" class="form-control" placeholder="Categories">
                    </div>

                    <div class="form-group">
                        <input type="date" name="published_at" class="form-control">
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="allowComments">
                        <label class="form-check-label" for="allowComments">Allow Comments</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button class="btn btn-primary">Publish</button>
                        <button class="btn btn-secondary">Save as draft</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
