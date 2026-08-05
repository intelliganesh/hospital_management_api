<?php

namespace App\Services;

use AutoIdGenerate;
use App\Models\Payment;
use App\Enums\ServiceType;
use Illuminate\Http\Request;
use App\Traits\PaymentValidation;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    use PaymentValidation;

    private $paymentColumns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(
        CheckValidation $checkValidationService
    ) {
        $this->paymentColumns = Payment::$paymentColumns;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request|array $request
     * @throws \Symfony\Component\HttpKernel\Exception\ValidationException
     * @return void
     */
    public function create(Request|array $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        // $paymentData = ['payment_number' => AutoIdGenerate::generateId(ServiceType::Payment)];
        // Payment::create(array_merge($request->only($this->paymentColumns), $paymentData));
        // Payment::create(array_merge($request, $paymentData));
        // if (!empty($request['discount_percentage']) && isset($request['discount_percentage'])) {
        //     $request['discount_amount'] = $request['amount'] - ($request['amount'] * ($request['discount_percentage'] / 100));
        // }
        if (!empty($request['discount_percentage']) && isset($request['discount_percentage'])) {
            $discountPercentage = $request['discount_percentage'];
            $amount = $request['amount'];
            $discountAmount = $amount * ($discountPercentage / 100);
            $request['discount_amount'] = $discountAmount;
        }

        $check=Payment::where('consultation_id',$request['consultation_id'])->where('amount_for',$request['amount_for'])->first();
        if(!$check){
            Payment::create($request);   

            $invoice=\App\Models\Invoice::where('consultation_id',$request['consultation_id'])->first();
            if($invoice){
                if($invoice->discount_percentage>0){
                    $discountAmount=$request['amount'] * ($invoice->discount_percentage / 100);
                    $invoice->update(['balanced_amount'=>$invoice->balanced_amount+$request['amount']-$discountAmount]);
                }else{
                    $invoice->update(['balanced_amount'=>$invoice->balanced_amount+$request['amount']]);
                }
            }
            
        }else{
            // Create a validator instance with the error message
            $validator = Validator::make([], []); // Empty validator
            $validator->errors()->add('amount_for', 'Test has already been added');
            
            // Throw ValidationException with the validator instance
            throw new ValidationException($validator);
        }
    }


    /**
     * Summary of getByColumnNameDynamic
     * @param mixed $columnName
     * @param mixed $columnValue
     * @return \Illuminate\Database\Eloquent\Collection<int, Payment>
     */
    public function getByColumnNameDynamic($columnName, $columnValue)
    {
        $paymentData = Payment::where($columnName, $columnValue)->get();
        $consultationCost = $paymentData->firstWhere('amount_for', 'Consultation Cost');

        $remainingPayments = $paymentData->reject(function ($payment) {
            return $payment->amount_for === 'Consultation Cost';
        });

        if (isset($consultationCost) && !empty($consultationCost)) {
            return collect([$consultationCost])->merge($remainingPayments);
        } else {
            return collect($remainingPayments);
        }

    }


    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        $payment = Payment::findOrFail($id);

        if (!$payment) {
            throw new NotFoundHttpException('Payment data not found.');
        }

        $payment->update($request->all());
    }

    /**
     * Summary of updateByColumnName
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @param string $columnName
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function updateByColumnName(Request|array $request, string $id, string $columnName = 'id')
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $payment = Payment::where($columnName, $id)->first();
        if (!$payment) {
            throw new NotFoundHttpException('Payment data not found.');
        }

        $payment->update($request->all());

    }


    /**
     * Summary of updateByColumns
     * @param array $data
     * @param array $condition
     * @return void
     */
    public function updateByColumns(array $data, array $condition)
    {
        $this->checkValidationService->checkValidation($this->validate($data));
        $payment = Payment::where($condition)->first();
        if (!$payment) {
            // $paymentData = ['payment_number' => AutoIdGenerate::generateId(ServiceType::Payment)];
            Payment::create($data);
            return;
        }
        Payment::where($condition)->update($data);
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Payment
     */
    public function get(string $id)
    {
        $payment = Payment::findOrFail($id);
        if (!$payment) {
            throw new NotFoundHttpException('Payment data not found.');
        }
        return $payment;
    }

    /**
     * Summary of getByColumnName
     * @param string $id
     * @param string $columnName
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Payment
     */
    public function getByColumnName(string $id, string $columnName = 'id')
    {
        $payment = Payment::where($columnName, $id)->first();
        if (!$payment) {
            throw new NotFoundHttpException('Payment data not found.');
        }
        return $payment;
    }


    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id)
    {
        $payment = Payment::findOrFail($id);
        if (!$payment) {
            throw new NotFoundHttpException('Payment data not found.');
        }
        $payment->delete();
    }


    /**
     * Summary of dynamicByColumnNameDelete
     * @param string $id
     * @param string $columnName
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function dynamicByColumnNameDelete(string $id, string $columnName = 'id')
    {
        $payment = Payment::where($columnName, $id)->first();
        if (!$payment) {
            throw new NotFoundHttpException('Payment data not found.');
        }
        $payment->delete();
    }

    /**
     * Summary of dynamicByMultipleColumnNameDelete
     * @param string $columnName The column to filter on
     * @param array $values Array of values to match against the column
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function dynamicByMultipleColumnNameDelete(string $columnName, array $values)
    {
        $payments = Payment::whereIn($columnName, $values)->get();
        
        if ($payments->isEmpty()) {
            throw new NotFoundHttpException('Payment data not found.');
        }
        
        // Delete all matching records
        Payment::whereIn($columnName, $values)->delete();
    }
    
    /**
     * Delete all payments for a specific consultation
     * 
     * @param string $consultationId The consultation ID
     * @param array $excludeTypes Optional array of payment types to exclude from deletion
     * @return int Number of records deleted
     */
    public function deletePaymentsByConsultationId(string $consultationId, array $excludeTypes = [])
    {
        $query = Payment::where('consultation_id', $consultationId);
        
        // If there are payment types to exclude
        if (!empty($excludeTypes)) {
            $query->whereNotIn('amount_for', $excludeTypes);
        }
        
        // Get count before deletion for return value
        $count = $query->count();
        
        // Delete the records
        $query->delete();
        
        return $count;
    }



}