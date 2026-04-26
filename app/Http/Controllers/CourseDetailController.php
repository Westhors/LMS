<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\CourseDetailRequest;
use App\Http\Resources\CourseDetailResource;
use App\Interfaces\CourseDetailRepositoryInterface;
use App\Models\CourseDetail;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class CourseDetailController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(CourseDetailRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $CourseDetail = CourseDetailResource::collection($this->crudRepository->all(['course'], [], ['*']));
            return $CourseDetail->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(CourseDetailRequest $request)
    {
        try {
           $course = $this->crudRepository->create($request->validated());
           if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $course);
           }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(CourseDetail $courseDetail): ?\Illuminate\Http\JsonResponse
    {
        try {
            $courseDetail->load(['course']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new CourseDetailResource($courseDetail));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(CourseDetailRequest $request, CourseDetail $courseDetail): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $courseDetail->id);
            if ($request->filled('image')) {
                $courseDetail = CourseDetail::find($courseDetail->id);
                $this->crudRepository->AddMediaCollection('image', $courseDetail);
            }
            activity()->performedOn($courseDetail)->withProperties(['attributes' => $courseDetail])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('course_details', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(CourseDetail::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = CourseDetail::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(CourseDetail::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

