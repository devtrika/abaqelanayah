<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactUsRequest;
use App\Services\ContactUsService;
use App\Facades\Responder;
use Illuminate\Http\JsonResponse;

class ContactUsController extends Controller
{
    protected $contactUsService;

    public function __construct(ContactUsService $contactUsService)
    {
        $this->contactUsService = $contactUsService;
    }

   public function store(ContactUsRequest $request)
{
    $data = $request->validated();
    $this->contactUsService->store($data);

return Responder::success(null, ['message' => __('apis.message_sent_successfully')]);

}

}
