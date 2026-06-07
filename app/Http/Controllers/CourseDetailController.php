<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\CourseDetailRequest;
use App\Http\Resources\CourseDetailResource;
use App\Interfaces\CourseDetailRepositoryInterface;
use App\Models\CourseDetail;
use App\Models\CourseDetailAttendance;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseDetailController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(CourseDetailRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index(Request $request)
    {
        try {

            $filters = $request->input('filters', []);
            $orderBy = $request->input('orderBy', 'id');
            $orderByDirection = $request->input('orderByDirection', 'asc');
            $perPage = $request->input('perPage', 10);
            $paginate = $request->input('paginate', 1);
            $delete = $request->input('delete', false);
            /*
            |--------------------------------------------------------------------------
            | Query
            |--------------------------------------------------------------------------
            */
            $query = CourseDetail::with(['course','attendances']);
            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */
            if (!empty($filters['course_id'])) {
                $query->where('course_id', $filters['course_id']);
            }
            /*
            |--------------------------------------------------------------------------
            | Soft Delete
            |--------------------------------------------------------------------------
            */
            if ($delete) {
                $query->onlyTrashed();
            }
            /*
            |--------------------------------------------------------------------------
            | Order
            |--------------------------------------------------------------------------
            */
            $query->orderBy($orderBy, $orderByDirection);

            $courseDetails = $query->get();

            if ($courseDetails->isEmpty()) {
                return JsonResponse::respondError('No course details found');
            }

            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            if ($paginate) {

                $currentPage = Paginator::resolveCurrentPage();

                $currentPageItems = $courseDetails->slice(
                    ($currentPage - 1) * $perPage,
                    $perPage
                )->values();

                $paginatedItems = new LengthAwarePaginator(
                    $currentPageItems,
                    $courseDetails->count(),
                    $perPage,
                    $currentPage,
                    ['path' => Paginator::resolveCurrentPath()]
                );

                return CourseDetailResource::collection($paginatedItems)
                    ->additional([
                        'status' => true,
                        'message' => 'Course details fetched successfully'
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Without Pagination
            |--------------------------------------------------------------------------
            */

            return JsonResponse::respondSuccess(
                'Course details fetched successfully',
                CourseDetailResource::collection($courseDetails)
            );

        } catch (\Exception $e) {

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
            if (request('pdf') !== null) {
                $this->crudRepository->AddMediaCollection('pdf', $course , 'pdf');
           }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(CourseDetail $courseDetail): ?\Illuminate\Http\JsonResponse
    {
        try {

            $courseDetail->load([
                'course',
                'exams',
                'assignments',
                'students.stage',
                'students.teacher',
                'attendances'
            ]);

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new CourseDetailResource($courseDetail)
            );

        } catch (Exception $e) {

            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function markAttendance($lessonId)
    {
        try {

            CourseDetailAttendance::updateOrCreate(

                [
                    'course_detail_id' => $lessonId,
                    'student_id' => auth()->id(),
                ],

                [
                    'attended' => true,
                    'attended_at' => now(),
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Attendance marked successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
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

