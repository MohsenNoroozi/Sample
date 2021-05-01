<?php

namespace App\Http\Controllers\CallApi;

use App\Http\Requests\ApiCallRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\DaemonService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;

class CleaningController extends Controller
{
    /**
     * @param ApiCallRequest $request
     * @return JsonResponse
     * @throws RequestException
     */
    public function quick_clean(ApiCallRequest $request): JsonResponse
    {
        $response = DaemonService::quick_clean($request['email'], Auth::id(), $request->ip());
        $response->throw();
        if ($response->successful()) {

            activity("Call API")
                ->withProperties([
                    ['IP' => $request->ip()],
                    ['email' => $request['email']],
                ])
                ->log('Quick Clean');

            return response()->json($response);
        }
        return response()->json([
            "message" => "No Response from server"
        ], 422);
    }

    /**
     * @param ApiCallRequest $request
     * @return JsonResponse
     * @throws RequestException
     */
    public function deep_clean(ApiCallRequest $request): JsonResponse
    {
        $response = DaemonService::deep_clean($request['email'], Auth::id(), $request->ip());
        $response->throw();
        if ($response->successful()) {

            activity("Call API")
                ->withProperties([
                    ['IP' => $request->ip()],
                    ['email' => $request['email']],
                ])
                ->log('Deep Clean');

            return response()->json($response);
        }
        return response()->json([
            "message" => "No Response from server"
        ], 422);
    }
}
