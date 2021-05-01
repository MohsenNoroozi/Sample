<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListDownloadRequest;
use App\Http\Requests\ListUpdateRequest;
use App\Http\Requests\ListUploadRequest;
use App\Http\Resources\EmailListResource;
use App\Models\EmailList;
use App\Models\User;
use App\Services\CreditService;
use Auth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmailListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $lists = User::find(Auth::id())
            ->lists()
            ->where('lists.status', '!=', 'deleted')
            ->where('lists.status', '!=', 'unconfirmed')
            ->orderByDesc('lists.created_at');

        activity()
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Get Lists');

        return EmailListResource::collection($lists->get())->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ListUploadRequest $request
     * @return JsonResponse
     */
    public function store(ListUploadRequest $request): JsonResponse
    {
        // Store uploaded file on storage
        $file = $request->file('file');

        // Delete all users UNCONFIRMED LISTS from database
        auth()->user()->lists()->where('status', 'unconfirmed')->delete();

        // Create new list record
        $list = auth()->user()->lists()->create([
            'name' => $file->getClientOriginalName(),
        ]);

        return response()->json([
            'uuid' => $list->uuid,
            'total_lines' => $total_lines,
            'has_enough_credits' => $has_enough_credits,
            'delimiter' => $delimiter,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param EmailList $emailList
     * @return void
     */
    public function show(EmailList $emailList): void
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmailList $list
     * @param ListUpdateRequest $request
     * @return JsonResponse
     */
    public function update(EmailList $list, ListUpdateRequest $request): JsonResponse
    {
        $creditService = new CreditService();
        // If user has not enough credits, return an 403 error
        if (!$creditService->check_if_has_enough_credits($list->total_lines)['status']) {
            return response()->json([
                'message' => "you don't have enough credit to clean this list.",
                'errors' => [
                    'credits' => 'Not enough credits.'
                ]
            ], 403);
        }

        /* Functionality Removed */

        activity()
            ->on($list)
            ->withProperties([
                ['IP' => $request->ip()],
                ['Changes' => $list->getChanges()],
            ])
            ->log('Update List');

        return response()->json([
            'credits' => $credits,
            'response' => $response,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EmailList $list
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(EmailList $list, Request $request): JsonResponse
    {
        $deleted = $list['name'] ?? "The list";

        activity()
            ->on($list)
            ->withProperties([
                ['IP' => $request->ip()],
            ])
            ->log('Delete a List');

        $list->delete();
        return response()->json([
            "message" => "« " . $deleted . " » has been deleted successfully."
        ]);
    }

    /**
     * @param EmailList $list
     * @param ListDownloadRequest $request
     * @return JsonResponse|BinaryFileResponse
     */
    public function list_download(EmailList $list, ListDownloadRequest $request)
    {

        return response()->download('')->deleteFileAfterSend();
    }


}
