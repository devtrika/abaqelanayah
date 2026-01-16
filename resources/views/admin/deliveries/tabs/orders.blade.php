<div class="tab-pane fade" id="orders">
    @if($row->DeliveryOrders->count() > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card text-center">
                    <div class="card-header header-elements-inline">
                        <h5 class="card-title">{{  __('admin.orders') }}</h5>
                    </div>
                    <div class="d-flex justify-content-center btns">

                    </div>
                    <div class="card-body">
                        <div class="contain-table text-center">
                            <table class="table datatable-button-init-basic text-center">
                                <thead>
                                <tr class="text-center">
                                    <th class="text-center">#</th>
                                    <th class="text-center">{{__('admin.user')}}</th>

                                    <th class="text-center">{{__('admin.order_num')}}</th>

                                    <th class="text-center">{{__('admin.final_total')}}</th>


                                    <th class="text-center">{{__('admin.order_status')}}</th>
                                </tr>
                                </thead>
                                <tbody class="text-center">
                                @forelse($row->DeliveryOrders as $key => $order)
                                    <tr class="delete_row text-center">
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">{{ $order->user->name }}</td>

                                        <td class="text-center">{{ $order->order_num }}</td>
                                        <td class="text-center">{{ $order->final_total }}</td>

                                        <td class="text-center">{{ __('order.'.$order->status) }}</td>
                                    </tr>

                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="d-flex flex-column w-100 align-center mt-4">
            <img src="{{asset('admin/app-assets/images/pages/404.png')}}" alt="">
            <span class="mt-2" style="font-family: cairo">{{__('admin.there_are_no_matches_matching')}}</span>
        </div>
    @endif

</div>
