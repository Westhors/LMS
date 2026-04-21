<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\HomeRequest;
use App\Http\Requests\HomeUpdateRequest;
use App\Http\Resources\HomeResource;
use App\Interfaces\HomeRepositoryInterface;
use App\Models\Home;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(HomeRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Home = HomeResource::collection($this->crudRepository->all());
            return $Home->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(HomeRequest $request)
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



    public function show(Home $home): ?\Illuminate\Http\JsonResponse
    {
        try {
            $home->load(['teacher']);

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new HomeResource($home)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(HomeUpdateRequest $request, Home $home): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $home->id);
            if ($request->filled('image')) {
                $home = Home::find($home->id);
                $this->crudRepository->AddMediaCollection('image', $home);
            }
            activity()->performedOn($home)->withProperties(['attributes' => $home])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('homes', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Home::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Home::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinal(Home::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

