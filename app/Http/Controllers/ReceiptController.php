<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReceiptController extends Controller
{
    use ResponseTrait;

    public function __construct(private ReceiptService $receiptService)
    {
    }

    /** GET /receipt_download/{receipt_id} -> {url} of the receipt PDF */
    public function download(string $receipt_id)
    {
        try {
            return $this->successResponse(['url' => $this->receiptService->download($receipt_id)]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(new NotFoundHttpException($e->getMessage()));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /** GET /receipt_download_by_invoice/{invoice_id} -> {url} of the latest payment's receipt */
    public function downloadByInvoice(string $invoice_id)
    {
        try {
            return $this->successResponse(['url' => $this->receiptService->downloadLatestForInvoice($invoice_id)]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(new NotFoundHttpException($e->getMessage()));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /** GET /receipt_download_by_external_appointment/{id} -> {url} (online appointment, after payment verification) */
    public function downloadByExternalAppointment(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->receiptService->downloadForExternalAppointment($id)]);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(new NotFoundHttpException($e->getMessage()));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
