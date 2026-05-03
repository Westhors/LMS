<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\PaymentCodeRequest;
use App\Http\Resources\PaymentCodeResource;
use App\Interfaces\PaymentCodeRepositoryInterface;
use App\Models\PaymentCode;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class PaymentCodeController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(PaymentCodeRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $PaymentCodes = PaymentCodeResource::collection($this->crudRepository->all(['teacher', 'student'], [], ['*']));
            return $PaymentCodes->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(PaymentCodeRequest $request)
    {
        try {
           $course = $this->crudRepository->create($request->validated());
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(PaymentCode $payment_code): ?\Illuminate\Http\JsonResponse
    {
        try {
            $payment_code->load(['teacher', 'student']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new PaymentCodeResource($payment_code));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(PaymentCodeRequest $request, PaymentCode $payment_code): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $payment_code->id);
            activity()->performedOn($payment_code)->withProperties(['attributes' => $payment_code])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('payment_codes', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(PaymentCode::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = PaymentCode::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(PaymentCode::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

