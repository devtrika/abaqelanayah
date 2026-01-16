<div class="position-relative">

    <table class="table" id="tab">
        <thead>
            <tr>
                <th>
                    <label class="container-checkbox">
                        <input type="checkbox" value="value1" name="name1" id="checkedAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <th>{{ __('admin.date') }}</th>
                <th>{{ __('admin.number') }}</th>
                <th>{{ __('admin.name') }}</th>
                <th>{{ __('admin.rating') }}</th>
                <th>{{ __('admin.comment') }}</th>
                {{-- <th>{{ __('admin.status') }}</th> --}}
                <th>{{ __(key: 'admin.control') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orderrates as $orderrate)
                <tr class="delete_row">
                    <td class="text-center">
                        <label class="container-checkbox">
                            <input type="checkbox" class="checkSingle" id="{{ $orderrate->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>{{ $orderrate->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($orderrate->order)
                            <a href="{{ route('admin.orders.show', ['id' => $orderrate->order->id]) }}" class="text-primary font-weight-bold" target="_blank">
                                {{ $orderrate->order->order_number }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $orderrate->user?->name }}</td>
                    <td>
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star{{ $i <= $orderrate->rating ? '' : '-o' }}"></i>
                        @endfor
                        ({{ $orderrate->rating }})
                    </td>
                    <td>{{ $orderrate->comment ?? '-' }}</td>
                    {{-- <td>{{ __(key: 'admin.' . $orderrate->status) }}</td> --}}
                    <td class="product-action">
                        <span class="d-none d-md-inline">
                            <a href="{{ route('admin.orderrates.show', ['id' => $orderrate->id]) }}" class="btn btn-warning btn-sm">
                                <i class="feather icon-eye"></i> {{ __('admin.show') }}
                            </a>
                        </span>
                        <span class="actions-dropdown d-md-none">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                    id="actions-menu-{{ $orderrate->id }}" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    {{ __('admin.actions') }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actions-menu-{{ $orderrate->id }}">
                                    <a class="dropdown-item"
                                        href="{{ route('admin.orderrates.show', ['id' => $orderrate->id]) }}">{{ __('admin.show') }}</a>
                                </div>
                            </div>
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- لا توجد بيانات --}}
    @if ($orderrates->count() == 0)
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{ asset('admin/app-assets/images/pages/404.png') }}" alt="">
            <span class="mt-2" style="font-family: cairo">{{ __('admin.there_are_no_matches_matching') }}</span>
        </div>
    @endif

</div>

{{-- روابط الصفحات --}}
@if ($orderrates->count() > 0 && $orderrates instanceof \Illuminate\Pagination\AbstractPaginator)
    <div class="d-flex justify-content-center mt-3">
        {{ $orderrates->links() }}
    </div>
@endif
