<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\AssistantTeacherRequest;
use App\Http\Requests\AssistantTeacherUpdateRequest;
use App\Http\Resources\AssistantTeacherResource;
use App\Interfaces\AssistantTeacherRepositoryInterface;
use App\Models\AssistantTeacher;
use App\Models\Teacher;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AssistantTeacherController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(AssistantTeacherRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $AssistantTeacher = AssistantTeacherResource::collection($this->crudRepository->all());
            return $AssistantTeacher->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(AssistantTeacherRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $this->crudRepository->create($data);
            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_ADDED_SUCCESSFULLY)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(AssistantTeacher $assistantTeacher): ?\Illuminate\Http\JsonResponse
    {
        try {
            $assistantTeacher->load(['teacher']);

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new AssistantTeacherResource($assistantTeacher)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(AssistantTeacherUpdateRequest $request, AssistantTeacher $assistantTeacher)
    {
        try {
            $data = $request->validated();

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $this->crudRepository->update($data, $assistantTeacher->id);

            activity()->performedOn($assistantTeacher)->withProperties(['attributes' => $assistantTeacher])->log('update');

            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('assistant_teachers', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(AssistantTeacher::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = AssistantTeacher::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinal(AssistantTeacher::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

