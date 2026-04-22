<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\AboutRequest;
use App\Http\Requests\AboutUpdateRequest;
use App\Http\Resources\AboutResource;
use App\Interfaces\AboutRepositoryInterface;
use App\Models\About;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class AboutController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(AboutRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $About = AboutResource::collection($this->crudRepository->all());
            return $About->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(AboutRequest $request)
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



    public function show(About $about): ?\Illuminate\Http\JsonResponse
    {
        try {
            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new AboutResource($about)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(AboutUpdateRequest $request, About $about): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $about->id);
            if ($request->filled('image')) {
                $about = About::find($about->id);
                $this->crudRepository->AddMediaCollection('image', $about);
            }
            activity()->performedOn($about)->withProperties(['attributes' => $about])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('abouts', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(About::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = About::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinal(About::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

