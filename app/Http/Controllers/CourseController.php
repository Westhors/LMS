<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\StageRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StageResource;
use App\Interfaces\CourseRepositoryInterface;
use App\Models\Course;
use App\Models\Stage;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class CourseController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(CourseRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Courses = CourseResource::collection($this->crudRepository->all(['teacher', 'stage', 'subject'], [], ['*']));
            return $Courses->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(CourseRequest $request)
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


    public function show(Course $course): ?\Illuminate\Http\JsonResponse
    {
        try {
            $course->load(['teacher', 'stage', 'subject']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new CourseResource($course));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(CourseRequest $request, Course $course): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $course->id);
            if ($request->filled('image')) {
                $course = Course::find($course->id);
                $this->crudRepository->AddMediaCollection('image', $course);
            }
            activity()->performedOn($course)->withProperties(['attributes' => $course])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('courses', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Course::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Course::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Course::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

