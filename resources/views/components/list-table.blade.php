@props([
    'tableHeaders' => [],
    'tableKeys' => [],
    'translatableKeys' => [],
    'tableData' => [],
    'title' => '',
    'addRoute' => null,
    'showRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'showParams' => fn($item) => [],
    'editParams' => fn($item) => [],
    'deleteParams' => fn($item) => [],
    'customRenderers' => [],
    'othersActions' => [],
    'permissions' => [],
])

<div class="card table-card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="card-title mb-0">{{ $title }}</h5>
            @if ($addRoute != '#')
                <a href="{{ route($addRoute) }}" class="btn btn-outline-secondary btn-sm table-button">
                    {{ translate('Add New') }}
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table  mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        @foreach ($tableHeaders as $header)
                            <th>{{ translate($header) }}</th>
                        @endforeach
                        @if ($editRoute != '#' || $deleteRoute != '#' || $showRoute != '#')
                            <th>{{ translate('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @foreach ($tableKeys as $key)
                                <td>
                                    @php
                                        $value = data_get($item, $key);
                                    @endphp

                                    @if (in_array($key, $translatableKeys ?? []))
                                        {{ $item->getTranslation($key, app()->getLocale()) }}
                                    @elseif (in_array($key, ['logo', 'image', 'photo']) && $value)
                                        <img src="{{ uploaded_asset($value) }}" alt="Image" class="rounded-circle"
                                            width="40" height="40">
                                    @elseif ($key === 'status')
                                        <span class="badge bg-{{ $value ? 'success' : 'secondary' }}">
                                            {{ $value ? translate('Active') : translate('Inactive') }}
                                        </span>
                                    @elseif (isset($customRenderers[$key]))
                                        {!! $customRenderers[$key]($item) !!}
                                    @else
                                        {{ Str::limit($value, 50) }}
                                    @endif
                                </td>
                            @endforeach

                            @if ($editRoute || $deleteRoute || $showRoute)
                                <td class="action-btns">
                                    @if ($showRoute != '#')
                                        <a href="{{ route($showRoute, ($showParams ?? fn($x) => [$x->id])($item)) }}"
                                            class="table_btn" title="View">
                                            <svg fill="#37143e" width="30" height="30" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M21.92 11.6C19.9 6.91 16.1 4 12 4s-7.9 2.91-9.92 7.6a1 1 0 0 0 0 .8C4.1 17.09 7.9 20 12 20s7.9-2.91 9.92-7.6a1 1 0 0 0 0-.8M12 18c-3.17 0-6.17-2.29-7.9-6C5.83 8.29 8.83 6 12 6s6.17 2.29 7.9 6c-1.73 3.71-4.73 6-7.9 6m0-10a4 4 0 1 0 4 4 4 4 0 0 0-4-4m0 6a2 2 0 1 1 2-2 2 2 0 0 1-2 2" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if ($editRoute != '#' && (!isset($permissions['edit']) || auth()->user()->can($permissions['edit'])))
                                        @php
                                            $resolvedEditRoute = is_callable($editRoute)
                                                ? $editRoute($item)
                                                : $editRoute;
                                            $resolvedEditParams = ($editParams ?? fn($x) => ['id' => $x->id])($item);
                                        @endphp

                                        <a href="{{ route($resolvedEditRoute, $resolvedEditParams) }}"
                                            class="table_btn" title="Edit">
                                            <svg fill="#37143e" width="25" height="25" viewBox="0 0 24 24"
                                                data-name="Line Color" xmlns="http://www.w3.org/2000/svg"
                                                class="icon line-color">
                                                <path
                                                    style="fill:none;stroke:#37143e;stroke-linecap:round;stroke-linejoin:round;stroke-width:2"
                                                    d="M21 21H3M19.88 7 11 15.83 7 17l1.17-4 8.88-8.88A2.09 2.09 0 0 1 20 4a2.09 2.09 0 0 1-.12 3" />
                                            </svg>
                                        </a>
                                    @endif

                                    @if (isset($othersActions) && !empty($othersActions))
                                        @foreach ($othersActions as $key => $actionItem)
                                            @if (is_callable($actionItem))
                                                {!! $actionItem($item) !!}
                                            @endif
                                        @endforeach
                                    @endif


                                    @if ($deleteRoute != '#' && (!isset($permissions['delete']) || auth()->user()->can($permissions['delete'])))
                                        <a href="{{ route($deleteRoute, ($deleteParams ?? fn($x) => [$x->id])($item)) }}"
                                            class="table_btn" title="Delete" onclick="return confirm('Are you sure?');">
                                            <svg fill="red" width="25" height="25" viewBox="-3 -2 24 24"
                                                xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin"
                                                class="jam jam-trash">
                                                <path
                                                    d="M6 2V1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1h4a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-.133l-.68 10.2a3 3 0 0 1-2.993 2.8H5.826a3 3 0 0 1-2.993-2.796L2.137 7H2a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm10 2H2v1h14zM4.141 7l.687 10.068a1 1 0 0 0 .998.932h6.368a1 1 0 0 0 .998-.934L13.862 7zM7 8a1 1 0 0 1 1 1v7a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v7a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1" />
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($tableHeaders) + 2 }}" class="text-center py-3">
                                <div class="table_notfound">
                                    <svg fill="#37143e" width="50" height="50" viewBox="0 0 256 256"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M128 20a108 108 0 1 0 108 108A108.12 108.12 0 0 0 128 20m0 192a84 84 0 1 1 84-84 84.095 84.095 0 0 1-84 84m40.485-107.515L144.971 128l23.514 23.515a12 12 0 0 1-16.97 16.97L128 144.971l-23.515 23.514a12 12 0 0 1-16.97-16.97L111.029 128l-23.514-23.515a12 12 0 0 1 16.97-16.97L128 111.029l23.515-23.514a12 12 0 0 1 16.97 16.97" />
                                    </svg>
                                    {{ translate('No data found') }}.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (count($tableData) > 0)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <span class="showing_results">{{ translate('Showing') }}
                        <strong>{{ $tableData->firstItem() }}</strong> {{ translate('to') }}
                        <strong>{{ $tableData->lastItem() }}</strong> {{ translate('of') }}
                        <strong>{{ $tableData->total() }}</strong> {{ translate('Results') }}</span>
                    {{ $tableData->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>
