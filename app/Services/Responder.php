<?php

namespace App\Services;

class Responder
{
    /**
     * Return success response with status code
     *
     * @param mixed $data - Can be array, object, null, or string (for message-only responses)
     * @param array|string $extra - Additional fields or message string
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = [], $extra = [], $code = 200)
    {
        // Handle case where first parameter is a string (message-only response)
        if (is_string($data)) {
            $response = [
                'status' => $code,
                'message' => $data
            ];
            return response()->json($response, $code);
        }

        // Handle case where second parameter is a string (message in extra)
        if (is_string($extra)) {
            $response = [
                'status' => $code,
                'message' => $extra
            ];

            // Only include data if it's not null
            if ($data !== null) {
                $response['data'] = $data;
            }

            return response()->json($response, $code);
        }

        // Original behavior for backward compatibility
        $response = [
            'status' => $code,
            'message' => $extra['message'] ?? __('apis.success')
        ];

        // Only include data if it's not null
        if ($data !== null) {
            $response['data'] = $data;
        }

        // Add any additional fields from $extra (except message which we handled above)
        foreach ($extra as $key => $value) {
            if ($key !== 'message') {
                $response[$key] = $value;
            }
        }

        return response()->json($response, $code);
    }

    /**
     * Return error response with status code
     *
     * @param string $message
     * @param array $errors
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message, $errors = [], $code = 422)
    {
        $response = [
            'status' => $code,
            'data' => null,
            'message' => $message
        ];

        // Add errors if provided
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    public static function paginated($resourceCollection)
    {
        if (method_exists($resourceCollection, 'items')) {
            return response()->json([
                'status' => 200,
                'message' => __('apis.success'),
                'data' => $resourceCollection->items(),
                'pagination' => [
                    'current_page' => $resourceCollection->currentPage(),
                    'last_page' => $resourceCollection->lastPage(),
                    'per_page' => $resourceCollection->perPage(),
                    'total' => $resourceCollection->total(),
                    'from' => $resourceCollection->firstItem(),
                    'to' => $resourceCollection->lastItem(),
                ]
            ]);
        }
    
        // Handle raw collection manually BEFORE resource wrapping
        $page = request()->get('page', 1);
        $perPage = request()->get('per_page', 15);
        $offset = ($page - 1) * $perPage;
    
        // Make sure we're working with raw collection
        $total = $resourceCollection->count();
        $paginatedItems = $resourceCollection->slice($offset, $perPage)->values();
    
        return response()->json([
            'status' => 200,
            'message' => __('apis.success'),
            'data' =>$paginatedItems, // ✅ resource after slicing
            'pagination' => [
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => (int) $perPage,
                'total' => $total,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ]
        ]);
    }
        
}
