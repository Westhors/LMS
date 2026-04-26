<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\TeacherRequest;
use App\Http\Requests\TeacherUpdateRequest;
use App\Http\Resources\TeacherResource;
use App\Interfaces\TeacherRepositoryInterface;
use App\Models\Teacher;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(TeacherRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $brands = TeacherResource::collection($this->crudRepository->all(
                ['stages.media', 'subjects'],[],['*']
            ));
            return $brands->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function store(TeacherRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $teacher = $this->crudRepository->create($data);
            if ($request->filled('stage')) {
                foreach ($request->stage as $item) {

                    $teacher->stages()->sync($item['stage_id']);

                    if (!empty($item['image'])) {
                        DB::table('mediable')->insert([
                            'model_type' => \App\Models\Stage::class,
                            'model_id'   => $item['stage_id'],
                            'media_id'   => $item['image'],
                            'collection' => 'stage_image',
                            'teacher_id' => $teacher->id
                        ]);
                    }
                }
            }
            if ($request->filled('subject')) {
                foreach ($request->subject as $item) {
                    $teacher->subjects()->sync($item['subject_id']);
                }
            }
            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_ADDED_SUCCESSFULLY)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function show(Teacher $teacher): \Illuminate\Http\JsonResponse
    {
        try {
            $teacher->load([
                'stages',
                'subjects',
                'teacherImage',
                'assistantTeachers',
                'home',
                'features',
                'about',
                'footer',
            ]);

            $teacher->stages->each(function ($stage) use ($teacher) {
                $stage->teacher_image = \DB::table('mediable')
                    ->join('media', 'media.id', '=', 'mediable.media_id')
                    ->where('mediable.teacher_id', $teacher->id)
                    ->where('mediable.model_type', \App\Models\Stage::class)
                    ->where('mediable.model_id', $stage->id)
                    ->where('mediable.collection', 'stage_image')
                    ->first();
            });

            return JsonResponse::respondSuccess(
                'Item Fetched Successfully',
                new TeacherResource($teacher)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function update(TeacherUpdateRequest $request, Teacher $teacher)
    {
        try {
            $data = $request->validated();
            unset($data['stage'], $data['subject']);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $this->crudRepository->update($data, $teacher->id);
            if ($request->filled('stage')) {
                $stageIds = collect($request->stage)
                    ->pluck('stage_id')
                    ->toArray();
                $teacher->stages()->sync($stageIds);
                foreach ($request->stage as $item) {
                    if (!empty($item['image'])) {
                        DB::table('mediable')
                            ->where('model_type', \App\Models\Stage::class)
                            ->where('model_id', $item['stage_id'])
                            ->where('collection', 'stage_image')
                            ->where('teacher_id', $teacher->id)
                            ->delete();
                        DB::table('mediable')->insert([
                            'model_type' => \App\Models\Stage::class,
                            'model_id'   => $item['stage_id'],
                            'media_id'   => $item['image'],
                            'teacher_id' => $teacher->id,
                            'collection' => 'stage_image',
                        ]);
                    }
                }
            }
            if ($request->filled('subject')) {
                $subjectIds = collect($request->subject)
                    ->pluck('subject_id')
                    ->toArray();
                $teacher->subjects()->sync($subjectIds);
            }
            return JsonResponse::respondSuccess(
                trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY)
            );
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->deleteRecords('teachers', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Teacher::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Teacher::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Teacher::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function fetchTeacher(Request $request)
    {
        try {
            $TeacherData = Teacher::get();
            return TeacherResource::collection($TeacherData)->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function register(TeacherRequest $request)
    {

    }



    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $email = $credentials['email'];

            $teacher = Teacher::where('active', 1)
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (!$teacher) {
                $teacher = Teacher::where('active', 1)
                    ->whereRaw('LOWER(secound_email) = ?', [$email])
                    ->first();
            }

            if (!$teacher || !Hash::check($credentials['password'], $teacher->password)) {
                return JsonResponse::respondError('Invalid email or password', 401);
            }

            $token = $teacher->createToken('teacher_token')->plainTextToken;

            return JsonResponse::respondSuccess([
                'message' => 'Login successful',
                'teacher' => new TeacherResource($teacher),
                'token'   => $token,
            ]);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function checkAuth()
    {
        try {
            $teacher = Auth::user();

            if (!$teacher) {
                return JsonResponse::respondError('Unauthenticated', 401);
            }

            return JsonResponse::respondSuccess([
                'message' => 'Authenticated',
                'teacher' => new TeacherResource($teacher->load(['stages', 'subjects'])),
            ]);
        } catch (\Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

}

