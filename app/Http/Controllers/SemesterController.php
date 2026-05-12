<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\BookRequest;
use App\Http\Requests\SemesterRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\SemesterResource;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\SemesterRepositoryInterface;
use App\Models\Book;
use App\Models\Semester;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

class SemesterController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(SemesterRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Semesters = SemesterResource::collection($this->crudRepository->all(['teacher', 'courses'], [], ['*']));
            return $Semesters->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(SemesterRequest $request)
    {
        try {
           $Semesters = $this->crudRepository->create($request->validated());
            if (request('image') !== null) {
                    $this->crudRepository->AddMediaCollection('image', $Semesters);
            }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Semester $semester): ?\Illuminate\Http\JsonResponse
    {
        try {

            $semester->load([
                'teacher',
                'courses.details',
                'students.stage',
                'students.teacher',
                'subject'
            ]);

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new SemesterResource($semester)
            );

        } catch (Exception $e) {

            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(SemesterRequest $request, Semester $semester): \Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->update($request->validated(), $semester->id);
            if ($request->filled('image')) {
                $semester = Semester::find($semester->id);
                $this->crudRepository->AddMediaCollection('image', $semester);
            }
            activity()->performedOn($semester)->withProperties(['attributes' => $semester])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('semesters', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Semester::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Semester::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Semester::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
}

