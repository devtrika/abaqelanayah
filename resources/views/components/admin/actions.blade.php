@php
    // Extract route names from URLs
    $showRouteName = null;
    $editRouteName = null;
    $deleteRouteName = null;

    try {
        $showRouteName = app('router')->getRoutes()->match(app('request')->create($showUrl))->getName();
    } catch (\Exception $e) {}

    try {
        $editRouteName = app('router')->getRoutes()->match(app('request')->create($editUrl))->getName();
    } catch (\Exception $e) {}

    try {
        $deleteRouteName = app('router')->getRoutes()->match(app('request')->create($deleteUrl))->getName();
    } catch (\Exception $e) {}

    // Check permissions
    $canShow = $showRouteName ? adminCan($showRouteName) : true;
    $canEdit = $editRouteName ? adminCan($editRouteName) : true;
    $canDelete = $deleteRouteName ? adminCan($deleteRouteName) : true;

    // Check if any action is available
    $hasAnyAction = $canShow || $canEdit || $canDelete;
@endphp

@if($hasAnyAction)
<div class="product-action">
    {{-- Visible on larger screens --}}
    @if($canShow)
        <span class="d-none d-md-inline">
            <a href="{{ $showUrl }}" class="btn btn-warning btn-sm"><i class="feather icon-eye"></i> {{ __('admin.show') }}</a>
        </span>
    @endif
    @if($canEdit)
        <span class="d-none d-md-inline">
            <a href="{{ $editUrl }}" class="btn btn-primary btn-sm"><i class="feather icon-edit"></i>{{ __('admin.edit') }}</a>
        </span>
    @endif
    @if($canDelete)
        <span class="d-none d-md-inline">
            <button class="delete-row btn btn-danger btn-sm" data-url="{{ $deleteUrl }}"><i class="feather icon-trash"></i>{{ __('admin.delete') }}</button>
        </span>
    @endif

    {{-- Dropdown for smaller screens --}}
    <span class="actions-dropdown d-md-none">
        <div class="dropdown">
            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="actions-menu-{{ $id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ __('admin.actions') }}
            </button>
            <div class="dropdown-menu" aria-labelledby="actions-menu-{{ $id }}">
                @if($canShow)
                    <a class="dropdown-item" href="{{ $showUrl }}">{{ __('admin.show') }}</a>
                @endif
                @if($canEdit)
                    <a class="dropdown-item" href="{{ $editUrl }}">{{ __('admin.edit') }}</a>
                @endif
                @if($canDelete)
                    <button class="dropdown-item delete-row" data-url="{{ $deleteUrl }}">{{ __('admin.delete') }}</button>
                @endif
            </div>
        </div>
    </span>
</div>
@else
    <span class="text-muted">--</span>
@endif