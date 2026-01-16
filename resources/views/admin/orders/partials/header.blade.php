<!-- order header -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white py-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between w-100">
            <!-- order info + button (right in rtl) -->
            <div>
                <h4 class="card-title mb-2 text-white">
                    <i class="feather icon-file-text mr-2 text-white"></i>
                    <span class="text-white">{{ __('admin.order_details') }}</span>
                    <span class="text-warning font-weight-bold">{{ $order->order_number }}</span>
                </h4>
                <small class="text-white-75 d-block mb-3">
                    {{ __('admin.created_at') }}: {{ $order->created_at->format('d/m/y h:i') }}
                </small>
                <a href="{{ route('admin.reports.payment-report.download-invoice', $order->id) }}" class="btn btn-light btn-sm">
                    <i class="feather icon-download mr-1"></i> {{ __('admin.download_invoice') }}
                </a>
            </div>
    
            <!-- status badge (left in rtl) -->
            <div class="text-left mt-2 mt-md-0">
                @php
                   
                        $statuscolors = [
                            'pending' => 'warning',
                            'new' => 'secondary',
                            'out-for-delivery' => 'primary',
                            'confirmed' => 'primary',
                            'processing' => 'info',
                            'delivered' => 'success',
                            'problem' => 'danger',
                            'cancelled' => 'danger',
                            'request_refund' => 'info',
                            'refunded' => 'success',
                            'request_rejected' => 'danger',
                        ];
                    $color = $statuscolors[$order->status] ?? 'secondary';

            $statusicons = [
                'pending' => 'clock',
                'new' => 'user-plus',
                'out-for-delivery' => 'truck',
                'confirmed' => 'check',
                'processing' => 'refresh-cw',
                'delivered' => 'check-circle-2',
                'problem' => 'alert-triangle',
                'cancelled' => 'x-circle',
                'request_refund' => 'rotate-ccw',
                'refunded' => 'dollar-sign',
                'request_rejected' => 'slash',
            ];
                    $icon = $statusicons[$order->status] ?? 'circle';
                
                @endphp
                
                
                <!-- status change dropdown -->
                <div class="d-flex align-items-center">
                    <div class="dropdown mr-2">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="statusdropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-{{ $icon }} mr-1"></i>
                            {{ __('admin.' . $order->status) }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusdropdown">
                            @php
                                // Define available statuses based on workflow
                                $availablestatuses = [
                                    'pending' => __('admin.pending'),
                                    'new' => __('admin.new'),
                                    'confirmed' => __('admin.confirmed'),
                                    'delivered' => __('admin.delivered'),
                                    'problem' => __('admin.problem'),
                                    'cancelled' => __('admin.cancelled'),
                                    'request_refund' => __('admin.request_refund'),
                                    'refunded' => __('admin.refunded'),
                                ];

                                // For branch managers: only allow specific statuses
                                $admin = auth('admin')->user();
                                if ($admin && (int) $admin->role_id === 2) {
                                    // Branch managers can update to: new, confirmed, delivered, cancelled
                                    $availablestatuses = [
                                        'new' => __('admin.new'),
                                        'confirmed' => __('admin.confirmed'),
                                        'delivered' => __('admin.delivered'),
                                        'cancelled' => __('admin.cancelled'),
                                    ];
                                }
                            @endphp
                            @foreach($availablestatuses as $status => $label)
                                @if($status !== $order->status)
                                    <a class="dropdown-item change-order-status" href="#" data-status="{{ $status }}" data-order-id="{{ $order->id }}">
                                        <i class="feather icon-{{ $statusicons[$status] ?? 'circle' }} mr-2"></i>
                                        {{ $label }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- current status badge -->
                    <span class="badge badge-{{ $color }} px-3 py-2" style="font-size: 0.875rem;">
                        <i class="feather icon-{{ $icon }} mr-1" style="font-size: 0.8rem;"></i>
                        {{ __('admin.' . $order->status) }}
                    </span>
                </div>
    
                @if($order->status === 'cancelled' && $order->cancelreason)
                    <div class="mt-2">
                        <small class="text-white-75 d-block">
                            <i class="feather icon-info mr-1" style="font-size: 0.7rem;"></i>
                            {{ __('admin.cancel_reason') }}:
                        </small>
                        @php
                            $reasondata = $order->cancelreason->reason;
                            $reasontext = $reasondata;
                        @endphp
                        <small class="text-white-50 font-italic">
                            "{{ $reasontext }}"
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
$(document).ready(function() {
    // Listen for status change
    $(document).on('click', '.change-order-status', function(e) {
        var status = $(this).data('status');
        var orderId = $(this).data('order-id');
        if (status === 'new') {
            // Show delivery assignment modal only for new
            $('#assignDeliveryModal').modal('show');
            $('#assignDeliveryModal').data('order-id', orderId);
            // Save status to modal for later use
            $('#assignDeliveryModal').data('assign-status', status);
        } else {
            // Change status directly for other statuses
            $.ajax({
                url: '/admin/orders/' + orderId + '/change-status',
                method: 'POST',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('حدث خطأ أثناء تغيير الحالة');
                }
            });
        }
    });

    // Handle delivery assignment submit
    $('#assignDeliveryBtn').on('click', function() {
        var orderId = $('#assignDeliveryModal').data('order-id');
        var deliveryUserId = $('#delivery_user_id').val();
        if (!deliveryUserId) {
            alert('يرجى اختيار مندوب التوصيل');
            return;
        }
        // Send AJAX to assign delivery user and change status
    var assignStatus = 'new';
        $.ajax({
            url: '/admin/orders/' + orderId + '/assign-delivery',
            method: 'POST',
            data: {
                delivery_user_id: deliveryUserId,
                status: assignStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                console.log(xhr)
                alert('حدث خطأ أثناء تعيين مندوب التوصيل');
            }
        });
    });
});
</script>

<!-- Delivery Assignment Modal -->
<div class="modal fade" id="assignDeliveryModal" tabindex="-1" role="dialog" aria-labelledby="assignDeliveryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignDeliveryModalLabel">تعيين مندوب التوصيل</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <label for="delivery_user_id">اختر مندوب التوصيل المناسب:</label>
                <select id="delivery_user_id" class="form-control">
                    <option value="">-- اختر مندوب --</option>
                    @php $available = 0; @endphp
                    @foreach($deliveryUsers as $user)
                        @if(isset($user->accept_orders) && $user->accept_orders)
                            @php $available++; @endphp
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                    @if($available === 0)
                        <option value="" disabled>{{ __('admin.no_available_delivery_users') ?? 'لا يوجد مندوبين متاحين' }}</option>
                    @endif
                </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="assignDeliveryBtn">تعيين وتغيير الحالة</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
      </div>
    </div>
  </div>
</div>
@endsection
