<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\OfferRequest;
use App\Http\Resources\OfferResource;
use App\Interfaces\OfferRepositoryInterface;
use App\Models\Offer;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class OfferController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(OfferRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Offer = OfferResource::collection($this->crudRepository->all());
            return $Offer->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(OfferRequest $request)
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



    public function show(Offer $offer): ?\Illuminate\Http\JsonResponse
    {
        try {
            $offer->load(['teacher']);

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new OfferResource($offer)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(OfferRequest $request, Offer $offer): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $offer->id);
            if ($request->filled('image')) {
                $offer = Offer::find($offer->id);
                $this->crudRepository->AddMediaCollection('image', $offer);
            }
            activity()->performedOn($offer)->withProperties(['attributes' => $offer])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('offers', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Offer::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Offer::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinal(offer::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

