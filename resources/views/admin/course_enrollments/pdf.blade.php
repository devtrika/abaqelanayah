<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Enrollment Invoice</title>
    <style>
        body {
            font-family: 'dejavu sans', 'sans-serif';
            font-size: 12px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header, .footer {
            text-align: center;
            padding: 10px;
        }
        .header h1 {
            margin: 0;
        }
        .content {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-primary { background-color: #007bff; }
        .badge-dark { background-color: #343a40; }
        .badge-secondary { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('admin.enrollment_details') }}</h1>
            <p>{{ __('admin.enrollment_id') }}: {{ $enrollment->id }}</p>
        </div>

        <div class="content">
            <table>
                <tr>
                    <th colspan="2">{{ __('admin.course_information') }}</th>
                    <th colspan="2">{{ __('admin.client_information') }}</th>
                </tr>
                <tr>
                    <td><strong>{{ __('admin.course_name') }}:</strong></td>
                    <td>{{ $enrollment->course->name ?? '-' }}</td>
                    <td><strong>{{ __('admin.client_name') }}:</strong></td>
                    <td>{{ $enrollment->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('admin.course_provider') }}:</strong></td>
                    <td>{{ $enrollment->course->instructor_name ?? '-' }}</td>
                    <td><strong>{{ __('admin.mobile_number') }}:</strong></td>
                    <td>{{ $enrollment->user->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('admin.enrollment_datetime') }}:</strong></td>
                    <td colspan="3">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : '-' }}</td>
                </tr>
            </table>

            <br>

            <table>
                <tr>
                    <th colspan="4">{{ __('admin.payment_information') }}</th>
                </tr>
                <tr>
                    <td><strong>{{ __('admin.amount_paid') }}:</strong></td>
                    <td>{{ number_format($enrollment->amount_paid, 2) }} {{ __('admin.riyal') }}</td>
                    <td><strong>{{ __('admin.payment_method') }}:</strong></td>
                    <td>
                        @switch($enrollment->payment_method)
                            @case('wallet')
                                <span class="badge badge-info">محفظة</span>
                                @break
                            @case('bank_transfer')
                                <span class="badge badge-warning">تحويل بنكي</span>
                                @break
                            @case('credit_card')
                                <span class="badge badge-primary">بطاقة ائتمان</span>
                                @break
                            @case('mada')
                                <span class="badge badge-success">مدى</span>
                                @break
                            @case('apple_pay')
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
                    <td><strong>{{ __('admin.payment_status') }}:</strong></td>
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
                    <td><strong>{{ __('admin.enrollment_status') }}:</strong></td>
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
                    <td><strong>{{ __('admin.progress_percentage') }}:</strong></td>
                    <td>{{ number_format($enrollment->progress_percentage, 2) }}%</td>
                </tr>
                @if($enrollment->completed_at)
                <tr>
                    <td><strong>{{ __('admin.completion_date') }}:</strong></td>
                    <td colspan="3">{{ $enrollment->completed_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ __('admin.rights_reserved') }}</p>
        </div>
    </div>
</body>
</html> 