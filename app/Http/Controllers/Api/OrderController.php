<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function invoice_pdf($id, Request $request): JsonResponse
    {
        if ($id) {
            /* Todo: Get invoice data */
            $invoice = [
                "sale_id" => $id,
            ];
            $user = auth()->user();

            $pdf = PDF::loadView('pdf.invoice', compact('invoice', 'user'));

            activity()
                ->withProperties([
                    ['IP' => $request->ip()],
                    ['id' => $id],
                ])
                ->log('Get Invoice');

            return $pdf->output();
        } else {
            return response()->json([
                'message' => "This invoice ID is invalid.",
                'errors' => 'id'
            ],404);
        }
    }
}
