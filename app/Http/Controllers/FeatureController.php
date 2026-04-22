<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\FeatureRequest;
use App\Http\Resources\FeatureResource;
use App\Interfaces\FeatureRepositoryInterface;
use App\Models\Feature;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class FeatureController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(FeatureRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Feature = FeatureResource::collection($this->crudRepository->all());
            return $Feature->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(FeatureRequest $request)
    {
        try {
           $event = $this->crudRepository->create($request->validated());
            if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $event);
            }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(Feature $feature): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new FeatureResource($feature)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(FeatureRequest $request, Feature $feature): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $feature->id);
            if ($request->filled('image')) {
                $feature = Feature::find($feature->id);
                $this->crudRepository->AddMediaCollection('image', $feature);
            }
            activity()->performedOn($feature)->withProperties(['attributes' => $feature])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('features', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Feature::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Feature::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinal(Feature::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

