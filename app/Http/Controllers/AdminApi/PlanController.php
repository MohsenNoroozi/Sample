<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequests\PlanCreateRequest;
use App\Http\Requests\AdminRequests\PlanUpdateRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        activity("Admin")
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Get Plans');

        return PlanResource::collection(Plan::all())->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PlanCreateRequest $request
     * @return PlanResource
     */
    public function store(PlanCreateRequest $request): PlanResource
    {
        /* Convert request to array */
        $data = $request->all();

        /* Create plan */
        $plan = Plan::create($data);

        activity("Admin")
            ->withProperties([
                ['IP' => $request->ip()],
                ['plan' => $request->all()],
            ])
            ->log('Store a Plan');

        return new PlanResource($plan);
    }

    /**
     * Display the specified resource.
     *
     * @param Plan $plan
     * @param Request $request
     * @return PlanResource
     */
    public function show(Plan $plan, Request $request): PlanResource
    {
        activity("Admin")
            ->withProperties([
                ['IP' => $request->ip()],
                ['plan' => $request->all()],
            ])
            ->log('Get a Plan data');

        return new PlanResource($plan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PlanUpdateRequest $request
     * @param Plan $plan
     * @return PlanResource
     */
    public function update(PlanUpdateRequest $request, Plan $plan): PlanResource
    {
        /* Convert request to array & Remove null inputs  */
        $data = array_filter($request->all(), function($value) {
            return $value !== null;
        });

        $plan->update($data);

        activity("Admin")
            ->on($plan)
            ->withProperties([
                ['IP' => $request->ip()],
                ['Changes' => $plan->getChanges()],
            ])
            ->log('Update List');

        return new PlanResource($plan);
    }
}
