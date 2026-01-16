<div class="tab-pane fade" id="favorites">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ __('admin.favorites') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div class="contain-table text-center">
                    <table class="table datatable-button-init-basic text-center table-hover">
                        <thead class="thead-light">
                            <tr class="text-center">
                                <th class="text-center">{{ __('admin.item_name') }}</th>
                                <th class="text-center">{{ __('admin.description') }}</th>
                                <th class="text-center">{{ __('admin.category') }}</th>
                                <th class="text-center">{{ __('admin.base_price') }}</th>
                                <th class="text-center">{{ __('admin.discount_percentage') }}</th>
                                <th class="text-center">{{ __('admin.date_added') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($row->favorites as $favorite)
                                @if($favorite->product)
                                    <tr class="text-center">
                                        {{-- اسم المنتج --}}
                                        <td>
                                            <a href="{{ route('admin.products.show', $favorite->product->id) }}">
                                                {{ $favorite->product->name }}
                                            </a>
                                        </td>

                                        {{-- وصف المنتج --}}
                                        <td>{{ $favorite->product->description }}</td>

                                        {{-- الفئة --}}
                                        <td>{{ optional($favorite->product->category)->name }}</td>

                                        {{-- السعر --}}
                                        <td>{{ number_format($favorite->product->base_price, 2) }} {{ __('admin.sar') }}</td>

                                        {{-- الخصم --}}
                                        <td>
                                            {{ $favorite->product->discount_percentage ? $favorite->product->discount_percentage . '%' : '-' }}
                                        </td>

                                        {{-- تاريخ الإضافة --}}
                                        <td>{{ $favorite->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="6" class="text-muted">{{ __('admin.item_deleted') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
