<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\MasterExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function __construct()
    {
        // Only clean the buffer if one exists
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();
    }

    public function master($export, Request $request)
    {
        $data =  $this->$export($request);
        return $this->masterExport($export, $data['cols'], $data['values']);
    }

    public function User()
    {
        return [
            'cols' => ['#', __('admin.name'), __('admin.email'), __('admin.phone')],
            'values' =>  ['id', 'name', 'email', 'phone']
        ];
    }

    public function Category()
    {
        return [
            'cols' => ['#', __('admin.name'), __('admin.followed_category')] ,
            'values' => ['id', 'name', 'followed_category'] ,
        ] ;
    }

    public function CourseEnrollment()
    {
        return [
            'cols' => [
                __('admin.serial_number'),
                __('admin.enrollment_id'),
                __('admin.enrollment_datetime'),
                __('admin.course_name'),
                __('admin.course') . ' ID',
                __('admin.course_provider'),
                __('admin.client_name'),
                __('admin.mobile_number'),
                __('admin.amount_paid'),
                __('admin.payment_method'),
                __('admin.payment_reference')
            ],
            'values' => [
                'id',
                'id',
                'enrolled_at',
                'course.name',
                'course_id',
                'course.instructor_name',
                'user.name',
                'user.phone',
                'amount_paid',
                'payment_method',
                'payment_reference'
            ]
        ];
    }


    public function LoyalityPoints()
    {
        return [
            'cols' => [
                __('admin.name'),
                __('admin.phone'),
                __('admin.points'),
                __('admin.value'),
            
            ],
            'values' => [
                'name',
                'phone',
                'loyality_points',
                'user.loyalty_points_value',
    
            ]
        ];
    }
    public function Country()
    {
        return [
            'cols' => ['#', __('admin.name'), __('admin.key')] ,
            'values' => ['id', 'name', 'key' ] ,
        ] ;
    }
    public function Admin()
    {
        return [
            'cols' => ['#', __('admin.name'), __('admin.email') , __('admin.phone')] ,
            'values' => ['id', 'name', 'email' , 'phone'] ,
        ] ;
    }
    public function Region()
    {
        return [
            'cols' => ['#', __('admin.name'), __('admin.country')] ,
            'values' => ['id', 'name', 'country.name'] ,
        ] ;
    }
    public function City()
    {
        return [
            'cols' => ['#', __('admin.name'), __('admin.region')] ,
            'values' => ['id', 'name', 'region.name'] ,
        ] ;
    }

    public function RevenueReport()
    {
        return [
            'cols' => [
                __('admin.serial_number'),
                __('admin.order_number'),
                __('admin.user'),
                __('admin.final_total'),
                __('admin.created_at'),
            ],
            'values' => [
                'id',
                'order_number',
                'user.name',
                'total',
                'created_at',
            ]
        ];
    }

    public function masterExport($model, $cols, $values, Request $request = null)
    {
        $folderNmae = strtolower(Str::plural(class_basename($model)));
        $modelClass = app("App\\Models\\$model");

        // Special handling for CourseEnrollment to include relationships
        if ($model === 'CourseEnrollment') {
            $records = $modelClass::with(['user', 'course'])->latest('enrolled_at')->get();
            } elseif ($model === 'Order' || $model === 'RevenueReport') {
            $query = $modelClass::query();
            if ($request && $request->has('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request && $request->has('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            $records = $query->latest()->get();
        } else {
            $records = $modelClass::latest()->get();
        }

        return Excel::download(new MasterExport($records, 'master-excel', ['cols' => $cols, 'values' => $values]), $folderNmae.'-reports-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }
}
