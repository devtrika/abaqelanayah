<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use App\Facades\Responder;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService
    ) {}

    public function index(): JsonResponse
    {
        $addresses = $this->addressService->index();
        
        return Responder::success(AddressResource::collection($addresses));
    }

    public function store(AddressRequest $request): JsonResponse
    {
        $address = $this->addressService->store($request->validated());
        
        return Responder::success(null , ['message' => __('apis.created_successfully')]);
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->addressService->show($id);
        
        return match(true) {
            is_null($result) => Responder::error(trans('apis.addresses.not_found'), [], 404),
            $result === false => Responder::error(trans('apis.addresses.unauthorized'), [], 403),
            default => Responder::success([
                'address' => new AddressResource($result)
            ], ['message' => trans('apis.addresses.shown')])
        };
    }

  public function update(AddressRequest $request, string $id): JsonResponse
{
    $result = $this->addressService->update($request->validated(), $id);

    return match(true) {
        is_null($result) => Responder::error(trans('apis.addresses.not_found'), [], 404),
        $result === false => Responder::error(trans('apis.addresses.unauthorized'), [], 403),
        default => Responder::success(null, ['message' => trans('apis.addresses.updated')])
    };
}


    public function destroy(string $id): JsonResponse
    {
        $result = $this->addressService->destroy($id);
        
        return match(true) {
            is_null($result) => Responder::error(trans('apis.addresses.not_found'), [], 404),
            $result === false => Responder::error(trans('apis.addresses.unauthorized'), [], 403),
            default => Responder::success([], ['message' => trans('apis.addresses.deleted')])
        };
    }
}