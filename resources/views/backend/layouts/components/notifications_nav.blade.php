@php
    $unreadNotifications = auth()->user()->unreadNotifications;
    $userType = auth()->user()->user_type;
@endphp

<li class="dropdown nav-item">
    <a class="nav-link dropdown-toggle dropdown-notification p-0 mt-2" href="#" data-toggle="dropdown">
        <i class="ft-bell font-medium-3"></i>
        @if ($unreadNotifications->count())
            <span class="notification badge badge-pill badge-danger">{{ $unreadNotifications->count() }}</span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right notification-dropdown m-0 overflow-hidden">
        <li class="dropdown-menu-header bg-primary white px-3 py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="ft-bell font-medium-3 mr-2"></i>
                    <span class="noti-title">
                        {{ $unreadNotifications->count() }} {{ translate('New Notification') }}
                    </span>
                </div>
                <span class="text-bold-400 cursor-pointer" onclick="markAllNotificationsAsRead()">
                    {{ translate('Mark all as read') }}
                </span>
            </div>
        </li>

        <li class="scrollable-container">
            @forelse($unreadNotifications as $notification)
                @php
                    $type = class_basename($notification->type);
                    $data = $notification->data;
                    $createdAt = \Carbon\Carbon::parse($notification->created_at)->format('F j, Y g:i A');
                @endphp

                <a class="d-flex justify-content-between" href="javascript:void(0)">
                    <div class="media d-flex align-items-center">
                        <div class="media-left mr-3">
                            <svg fill="#37143e" width="35" height="35" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><path class="clr-i-solid clr-i-solid-path-1" d="m32.85 28.13-.34-.3A14.4 14.4 0 0 1 30 24.9a12.6 12.6 0 0 1-1.35-4.81v-4.94A10.81 10.81 0 0 0 19.21 4.4V3.11a1.33 1.33 0 1 0-2.67 0v1.31a10.81 10.81 0 0 0-9.33 10.73v4.94a12.6 12.6 0 0 1-1.35 4.81 14.4 14.4 0 0 1-2.47 2.93l-.34.3v2.82h29.8Z"/><path class="clr-i-solid clr-i-solid-path-2" d="M15.32 32a2.65 2.65 0 0 0 5.25 0Z"/><path fill="none" d="M0 0h36v36H0z"/></svg>
                        </div>
                        <div class="media-body">
                            @switch($type)
                                @case('OrderNotification')
                                    <h6 class="m-0">
                                        {{ translate('Order code:') }}
                                        <small class="grey lighten-1 font-italic float-right">{{ $createdAt }}</small>
                                    </h6>
                                    <small class="noti-text">{{ $data['order_code'] ?? '' }}</small>
                                    <h6 class="noti-text font-small-3 m-0">
                                        {{ translate('has been') . ' ' . ucfirst(str_replace('_', ' ', $data['status'] ?? '')) }}
                                    </h6>
                                    @break

                                @case('ShopVerificationNotification')
                                    <h6 class="m-0">
                                        <span>
                                            {{ $userType === 'admin' ? ($data['name'] ?? '') . ':' : translate('Your') }}
                                        </span>
                                        <small class="grey lighten-1 font-italic float-right">{{ $createdAt }}</small>
                                    </h6>
                                    <h6 class="noti-text font-small-3 m-0">
                                        {{ translate('verification request has been') . ' ' . ($data['status'] ?? '') }}
                                    </h6>
                                    @break

                                @case('ShopProductNotification')
                                    <h6 class="m-0">
                                        {{ translate('Product:') }}
                                        <small class="grey lighten-1 font-italic float-right">{{ $createdAt }}</small>
                                    </h6>
                                    <small class="noti-text">{{ $data['name'] ?? '' }}</small>
                                    <h6 class="noti-text font-small-3 m-0">
                                        {{ translate('is') . ' ' . ($data['status'] ?? '') }}
                                    </h6>
                                    @break

                                @case('PayoutNotification')
                                    <h6 class="m-0">
                                        {{ translate('Payout') }}
                                        <small class="grey lighten-1 font-italic float-right">{{ $createdAt }}</small>
                                    </h6>
                                    <h6 class="noti-text font-small-3 m-0">
                                        {{ translate($data['message'] ?? 'You have a payout update.') }}
                                    </h6>
                                    @break

                                @default
                                    <h6 class="m-0">
                                        {{ translate('Notification') }}
                                        <small class="grey lighten-1 font-italic float-right">{{ $createdAt }}</small>
                                    </h6>
                                    <h6 class="noti-text font-small-3 m-0">
                                        {{ translate('You have a new notification.') }}
                                    </h6>
                            @endswitch
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-3">{{ translate('No new notifications.') }}</div>
            @endforelse
        </li>
        <li class="dropdown-menu-footer">
            <div class="noti-footer text-center cursor-pointer primary border-top text-bold-400 py-1">
                <a href="{{ route('admin.all-notification') }}" class="text-reset d-block py-2">
                    {{ translate('View All Notifications') }}
                </a>
            </div>
        </li>
    </ul>
</li>