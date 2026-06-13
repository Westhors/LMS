<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\CenterHourRequest;
use App\Http\Resources\CenterHourResource;
use App\Interfaces\CenterHourRepositoryInterface;
use App\Models\CenterHour;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class CenterHourController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(CenterHourRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $CenterHours = CenterHourResource::collection($this->crudRepository->all(['teacher' , 'subject' , 'stage'], [], ['*']));
            return $CenterHours->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(CenterHourRequest $request)
    {
        try {
           $CenterHour = $this->crudRepository->create($request->validated());
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(CenterHour $center_hour): ?\Illuminate\Http\JsonResponse
    {
        try {
            $center_hour->load(['teacher' , 'subject' ,'stage']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new CenterHourResource($center_hour));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(CenterHourRequest $request, CenterHour $center_hour): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $center_hour->id);
            activity()->performedOn($center_hour)->withProperties(['attributes' => $center_hour])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('center_hours', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(CenterHour::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = CenterHour::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(CenterHour::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

