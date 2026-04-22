<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\FooterRequest;
use App\Http\Requests\FooterUpdateRequest;
use App\Http\Resources\FooterResource;
use App\Interfaces\FooterRepositoryInterface;
use App\Models\Footer;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class FooterController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(FooterRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Footer = FooterResource::collection($this->crudRepository->all());
            return $Footer->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(FooterRequest $request)
    {
        try {
           $event = $this->crudRepository->create($request->validated());
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }



    public function show(Footer $footer): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new FooterResource($footer)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(FooterUpdateRequest $request, Footer $footer): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $footer->id);
            activity()->performedOn($footer)->withProperties(['attributes' => $footer])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('footers', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Footer::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Footer::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinal(Footer::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

