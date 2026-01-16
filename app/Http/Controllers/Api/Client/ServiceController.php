<?php

namespace App\Http\Controllers\Api\Client;

use App\Facades\Responder;
use Illuminate\Http\Request;
use App\Services\ServiceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Client\ServiceResource;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }


    public function index(Request $request)
    {
        try {
            $filters = [
                'category_id' => $request->input('category_id'), // Filter by product category
                'sort' => $request->input(key: 'sort'), // Add sort filter
            ];

            $products = $this->serviceService->getAllServices($filters);

            return Responder::success(ServiceResource::collection($products));

        } catch (\Exception $e) {
            return Responder::error('Failed to fetch products', ['error' => $e->getMessage()], 500);
        }
    }



    

  

}
