@extends('admin.layout.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/extensions/sweetalert2.min.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="content-wrapper">
    

    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ __('admin.enrollment_details') }}</h4>
                        <div>
                            <a href="{{ route('admin.course_enrollments.pdf', $enrollment->id) }}" class="btn btn-success">
                                <i class="la la-download"></i> {{ __('admin.download_pdf') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('admin.enrollment_id') }}:</strong></td>
                                            <td>{{ $enrollment->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.enrollment_datetime') }}:</strong></td>
                                            <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.course_name') }}:</strong></td>
                                            <td>{{ $enrollment->course->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.course') }} ID:</strong></td>
                                            <td>{{ $enrollment->course_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.course_provider') }}:</strong></td>
                                            <td>{{ $enrollment->course->instructor_name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.client_name') }}:</strong></td>
                                            <td>{{ $enrollment->user->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.mobile_number') }}:</strong></td>
                                            <td>{{ $enrollment->user->phone ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('admin.amount_paid') }}:</strong></td>
                                            <td>{{ number_format($enrollment->amount_paid, 2) }} {{ __('admin.riyal') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.payment_method') }}:</strong></td>
                                            <td>
                                                @switch($enrollment->payment_method_id)
                                                    @case(1)
                                                        <span class="badge badge-info">محفظة</span>
                                                        @break
                                                    @case(5)
                                                        <span class="badge badge-warning">تحويل بنكي</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge badge-primary">بطاقة ائتمان</span>
                                                        @break
                                                    @case(3)
                                                        <span class="badge badge-success">مدى</span>
                                                        @break
                                                    @case(4)
                                                        <span class="badge badge-dark">Apple Pay</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $enrollment->payment_method }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('admin.payment_reference') }}:</strong></td>
                                            <td>{{ $enrollment->payment_reference ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>حالة الدفع:</strong></td>
                                            <td>
                                                @switch($enrollment->payment_status)
                                                    @case('pending')
                                                        <span class="badge badge-warning">في الانتظار</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="badge badge-success">مدفوع</span>
                                                        @break
                                                    @case('failed')
                                                        <span class="badge badge-danger">فشل</span>
                                                        @break
                                                    @case('refunded')
                                                        <span class="badge badge-info">مسترد</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $enrollment->payment_status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>حالة الاشتراك:</strong></td>
                                            <td>
                                                @switch($enrollment->status)
                                                    @case('pending_payment')
                                                        <span class="badge badge-warning">في انتظار الدفع</span>
                                                        @break
                                                    @case('active')
                                                        <span class="badge badge-success">نشط</span>
                                                        @break
                                                    @case('suspended')
                                                        <span class="badge badge-danger">معلق</span>
                                                        @break      
                                                    @case('completed')
                                                        <span class="badge badge-info">مكتمل</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-dark">ملغي</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ $enrollment->status }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>نسبة التقدم:</strong></td>
                                            <td>{{ number_format($enrollment->progress_percentage, 2) }}%</td>
                                        </tr>
                                        @if($enrollment->completed_at)
                                        <tr>
                                            <td><strong>تاريخ الإكمال:</strong></td>
                                            <td>{{ $enrollment->completed_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            {{-- Bank Transfer Details Section --}}
                            @if($enrollment->payment_method_id == 5)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="la la-bank"></i> {{ __('admin.bank_transfer_details') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td><strong>اسم البنك المرسل:</strong></td>
                                                            <td>{{ $enrollment->sender_bank_name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>اسم صاحب الحساب:</strong></td>
                                                            <td>{{ $enrollment->sender_account_holder_name ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>رقم الحساب:</strong></td>
                                                            <td>{{ $enrollment->sender_account_number ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>رقم الآيبان:</strong></td>
                                                            <td>{{ $enrollment->sender_iban ?? '-' }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <td><strong>مبلغ التحويل:</strong></td>
                                                            <td>{{ $enrollment->amount_paid ? number_format($enrollment->amount_paid, 2) . ' ' . __('admin.riyal') : '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>مرجع التحويل:</strong></td>
                                                            <td>{{ $enrollment->payment_reference ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>تاريخ التحويل:</strong></td>
                                                            <td>{{ $enrollment->transfer_date ? $enrollment->transfer_date->format('Y-m-d') : '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>حالة التحويل البنكي:</strong></td>
                                                            <td>
                                                                @switch($enrollment->bank_transfer_status)
                                                                    @case('pending')
                                                                        <span class="badge badge-warning">في الانتظار</span>
                                                                        @break
                                                                    @case('verified')
                                                                        <span class="badge badge-success">تم التحقق</span>
                                                                        @break
                                                                    @case('rejected')
                                                                        <span class="badge badge-danger">مرفوض</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge badge-secondary">{{ $enrollment->bank_transfer_status }}</span>
                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            {{-- Receipt Image Section --}}
                                            @if($enrollment->receipt_image_url)
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="card-title mb-0">
                                                                <i class="la la-image"></i> صورة إيصال التحويل
                                                            </h6>
                                                        </div>
                                                        <div class="card-body text-center">
                                                            <img src="{{ $enrollment->receipt_image_preview_url }}"
                                                                 alt="إيصال التحويل"
                                                                 class="img-fluid rounded shadow"
                                                                 style="max-height: 400px; cursor: pointer;"
                                                                 onclick="showImageModal('{{ $enrollment->receipt_image_url }}')">
                                                            <p class="mt-2 text-muted">انقر على الصورة لعرضها بالحجم الكامل</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Admin Verification Section --}}
                                            @if($enrollment->bank_transfer_status == 'verified' && $enrollment->verified_at)
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="alert alert-success">
                                                        <h6><i class="la la-check-circle"></i> تم التحقق من التحويل</h6>
                                                        <p class="mb-1"><strong>تم التحقق بواسطة:</strong> {{ $enrollment->verifiedBy->name ?? '-' }}</p>
                                                        <p class="mb-0"><strong>تاريخ التحقق:</strong> {{ $enrollment->verified_at->format('Y-m-d H:i:s') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if($enrollment->bank_transfer_status == 'rejected' && $enrollment->rejected_at)
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="alert alert-danger">
                                                        <h6><i class="la la-times-circle"></i> تم رفض التحويل</h6>
                                                        <p class="mb-1"><strong>تم الرفض بواسطة:</strong> {{ $enrollment->rejectedBy->name ?? '-' }}</p>
                                                        <p class="mb-0"><strong>تاريخ الرفض:</strong> {{ $enrollment->rejected_at->format('Y-m-d H:i:s') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Admin Action Buttons for Pending Transfers --}}
                                            @if($enrollment->bank_transfer_status == 'pending')
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-success" onclick="verifyTransfer({{ $enrollment->id }})">
                                                            <i class="la la-check"></i> تأكيد التحويل
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="rejectTransfer({{ $enrollment->id }})">
                                                            <i class="la la-times"></i> رفض التحويل
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.course_enrollments.index') }}" class="btn btn-outline-secondary">
                                            <i class="la la-arrow-left"></i> {{ __('admin.back') }}
                                        </a>
                                        {{-- Future: Download Invoice Button --}}
                                        {{-- <a href="#" class="btn btn-outline-success">
                                            <i class="la la-download"></i> {{ __('admin.download_invoice') }}
                                        </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">صورة إيصال التحويل</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="إيصال التحويل" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                <a id="downloadImage" href="" download class="btn btn-primary">تحميل الصورة</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script src="{{asset('admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
    <script>
    function verifyTransfer(enrollmentId) {
        // Create file input for image upload
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.style.display = 'none';
        document.body.appendChild(input);

        Swal.fire({
            title: 'تأكيد التحويل البنكي',
            html: `
                <p>هل أنت متأكد من تأكيد هذا التحويل البنكي؟</p>
                <div class="mt-3">
                    <label class="form-label">رفع صورة إيصال التحويل (اختياري):</label>
                    <input type="file" id="transfer-receipt" class="form-control" accept="image/*">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'تأكيد',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const fileInput = document.getElementById('transfer-receipt');
                const formData = new FormData();

                if (fileInput.files[0]) {
                    formData.append('receipt_image', fileInput.files[0]);
                }

                return fetch(`/admin/course-enrollments/${enrollmentId}/verify-bank-transfer`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (!response.ok) {
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Server error');
                            });
                        } else {
                            return response.text().then(text => {
                                console.error('Server response:', text);
                                throw new Error('Server returned an error page. Check console for details.');
                            });
                        }
                    }

                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        throw new Error('Server did not return JSON response');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.showValidationMessage('Request failed: ' + error.message);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'تم التأكيد!',
                    text: 'تم تأكيد التحويل البنكي بنجاح',
                    icon: 'success',
                    confirmButtonText: 'موافق'
                }).then(() => {
                    location.reload();
                });
            }
        });

        // Clean up
        document.body.removeChild(input);
    }

    function rejectTransfer(enrollmentId) {
        Swal.fire({
            title: 'رفض التحويل البنكي',
            text: 'هل أنت متأكد من رفض هذا التحويل البنكي؟',
            showCancelButton: true,
            confirmButtonText: 'رفض',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch(`/admin/course-enrollments/${enrollmentId}/reject-bank-transfer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (!response.ok) {
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Server error');
                            });
                        } else {
                            return response.text().then(text => {
                                console.error('Server response:', text);
                                throw new Error('Server returned an error page. Check console for details.');
                            });
                        }
                    }

                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        throw new Error('Server did not return JSON response');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    Swal.showValidationMessage('Request failed: ' + error.message);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'تم الرفض!',
                    text: 'تم رفض التحويل البنكي',
                    icon: 'success',
                    confirmButtonText: 'موافق'
                }).then(() => {
                    location.reload();
                });
            }
        });
    }

    function showImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('downloadImage').href = imageUrl;
        $('#imageModal').modal('show');
    }
    </script>
@endsection
