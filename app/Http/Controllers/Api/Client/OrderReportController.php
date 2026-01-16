<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\ReportOrderRequest;
use App\Http\Resources\Api\Order\OrderReportResource;
use App\Services\OrderReportService;
use App\Services\Responder;
use App\Traits\ResponseTrait;

class OrderReportController extends Controller
{
    use ResponseTrait;

    /**
     * @var OrderReportService
     */
    protected $orderReportService;

    /**
     * OrderReportController constructor.
     *
     * @param OrderReportService $orderReportService
     */
    public function __construct(OrderReportService $orderReportService)
    {
        $this->orderReportService = $orderReportService;
    }

    /**
     * Report an order
     *
     * @param ReportOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ReportOrderRequest $request)
    {
        try {
            $user = auth()->user();
            $report = $this->orderReportService->reportOrder($user, $request->validated());

            return Responder::success(
                null , 
                ['message' => __('apis.order_reported_successfully')]
            );
        } catch (\Exception $e) {
            return Responder::error($e->getMessage(), [], 422);
        }
    }

   }
