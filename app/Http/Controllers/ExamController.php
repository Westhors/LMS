<?php

namespace App\Http\Controllers;

use App\Helpers\FileUploadAction;
use App\Helpers\JsonResponse;
use App\Http\Requests\ExamRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionBankResource;
use App\Http\Resources\QuestionResource;
use App\Interfaces\ExamRepositoryInterface;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use App\Models\QuestionBank;
use App\Models\QuestionBankOption;
use App\Models\QuestionOption;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
class ExamController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(ExamRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function index()
    {
        try {
            $Exams = ExamResource::collection($this->crudRepository->all(['questions', 'answers', 'courseDetail', 'stage', 'teacher'], [], ['*']));
            return $Exams->additional(JsonResponse::success());
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }
    public function store(ExamRequest $request)
    {
        try {
            $result = (new FileUploadAction())->checkAssistantPermission('exams', 'create');
            if ($result !== true) {
                return $result;
            }
           $exam = $this->crudRepository->create($request->validated());
           if (request('image') !== null) {
                $this->crudRepository->AddMediaCollection('image', $exam);
           }
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_ADDED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function show(Exam $exam): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exam->load(['questions', 'answers.student','courseDetail', 'stage', 'teacher']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new ExamResource($exam));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(ExamRequest $request, Exam $exam): \Illuminate\Http\JsonResponse
    {
        try {
            $result = (new FileUploadAction())->checkAssistantPermission('exams', 'update');
            if ($result !== true) {
                return $result;
            }
            $this->crudRepository->update($request->validated(), $exam->id);
            if ($request->filled('image')) {
                $exam = Exam::find($exam->id);
                $this->crudRepository->AddMediaCollection('image', $exam);
            }
            activity()->performedOn($exam)->withProperties(['attributes' => $exam])->log('update');
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_UPDATED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function destroy(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $result = (new FileUploadAction())->checkAssistantPermission('exams', 'delete');
            if ($result !== true) {
                return $result;
            }
            $this->crudRepository->deleteRecords('exams', $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function restore(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $this->crudRepository->restoreItem(Exam::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_RESTORED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function forceDelete(Request $request): ?\Illuminate\Http\JsonResponse
    {
        try {
            $exists = Exam::whereIn('id', $request['items'])->exists();
            if (!$exists) {
                return JsonResponse::respondError("One or more records do not exist. Please refresh the page.");
            }
            $this->crudRepository->deleteRecordsFinial(Exam::class, $request['items']);
            return JsonResponse::respondSuccess(trans(JsonResponse::MSG_FORCE_DELETED_SUCCESSFULLY));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }




/////////////////////////////////////////// addQuestions //////////////////////////////////////////////
    public function addQuestions(Request $request)
    {
        $result = (new FileUploadAction())->checkAssistantPermission('questions', 'create');
                if ($result !== true) {
                    return $result;
        }
        $request->validate([
            'exam_id'   => 'required|exists:exams,id',
            'questions' => 'required|array'
        ]);

        DB::beginTransaction();

        try {

            $exam = Exam::findOrFail($request->exam_id);

            $createdQuestions = [];

            foreach ($request->questions as $q) {

                /*
                |--------------------------------------------------------------------------
                | Create Exam Question
                |--------------------------------------------------------------------------
                */

                $question = ExamQuestion::create([
                    'exam_id'        => $request->exam_id,
                    'question_type'  => $q['question_type'],
                    'question'       => $q['question'],
                    'mark'           => $q['mark'] ?? 1,
                    'correct_answer' => $q['correct_answer'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Save Question Image
                |--------------------------------------------------------------------------
                */

                if (!empty($q['image'])) {

                    DB::table('mediable')->insert([
                        'model_type' => \App\Models\ExamQuestion::class,
                        'model_id'   => $question->id,
                        'media_id'   => $q['image'],
                        'collection' => 'question_image',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Save To Question Bank
                |--------------------------------------------------------------------------
                */

                $bankQuestion = QuestionBank::create([
                    'teacher_id'     => $exam->teacher_id,
                    'stage_id'       => $exam->stage_id,
                    'subject_id'     => $exam->courseDetail?->course?->subject_id,
                    'question_type'  => $q['question_type'],
                    'question'       => $q['question'],
                    'mark'           => $q['mark'] ?? 1,
                    'correct_answer' => $q['correct_answer'] ?? null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Save Bank Question Image
                |--------------------------------------------------------------------------
                */

                if (!empty($q['image'])) {

                    DB::table('mediable')->insert([
                        'model_type' => \App\Models\QuestionBank::class,
                        'model_id'   => $bankQuestion->id,
                        'media_id'   => $q['image'],
                        'collection' => 'question_bank_image',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Save MCQ Options
                |--------------------------------------------------------------------------
                */

                if (
                    $q['question_type'] === 'multiple_choice'
                    && isset($q['options'])
                ) {

                    foreach ($q['options'] as $opt) {

                        // exam question options
                        $option = QuestionOption::create([
                            'question_id'      => $question->id,
                            'question_bank_id' => $bankQuestion->id,
                            'option_text'      => $opt['option_text'],
                            'is_correct'       => $opt['is_correct'] ?? false,
                        ]);

                        // حفظ صورة الاختيار
                        if (!empty($opt['image'])) {

                            DB::table('mediable')->insert([
                                'model_type' => \App\Models\QuestionOption::class,
                                'model_id'   => $option->id,
                                'media_id'   => $opt['image'],
                                'collection' => 'option_image',
                            ]);
                        }

                        // question bank options
                        $bankOption = QuestionBankOption::create([
                            'question_bank_id' => $bankQuestion->id,
                            'option_text'      => $opt['option_text'],
                            'is_correct'       => $opt['is_correct'] ?? false,
                        ]);

                        // حفظ صورة اختيار بنك الأسئلة
                        if (!empty($opt['image'])) {

                            DB::table('mediable')->insert([
                                'model_type' => \App\Models\QuestionBankOption::class,
                                'model_id'   => $bankOption->id,
                                'media_id'   => $opt['image'],
                                'collection' => 'option_bank_image',
                            ]);
                        }
                    }
                }

                $createdQuestions[] = [
                    'exam_question_id' => $question->id,
                    'question_bank_id' => $bankQuestion->id,
                    'question'         => $question->question,
                ];
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Questions added successfully and saved to question bank',
                'data'    => $createdQuestions
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
//////////////////////////////////////////// submitExam ////////////////////////////////////
    public function getQuestions($examId)
    {
        $exam = Exam::with([
            'questions.options',
            'questions.question_image'
        ])->findOrFail($examId);

        $questions = $exam->questions;
        /*
        |--------------------------------------------------------------------------
        | Random Questions
        |--------------------------------------------------------------------------
        */
        if ($exam->random_questions) {
            $questions = $questions->shuffle()->values();
        }
        /*
        |--------------------------------------------------------------------------
        | Random Answers
        |--------------------------------------------------------------------------
        */
        if ($exam->random_answers) {

            $questions->transform(function ($question) {
                if ($question->relationLoaded('options')) {
                    $question->setRelation(
                        'options',
                        $question->options->shuffle()->values()
                    );
                }
                return $question;
            });
        }
        return response()->json([
            'status' => true,
            'exam_id' => $exam->id,
            'exam_title' => $exam->title,
            'random_questions' => (bool) $exam->random_questions,
            'random_answers' => (bool) $exam->random_answers,
            'questions_count' => $questions->count(),
            'data' => QuestionResource::collection($questions)
        ]);
    }
//////////////////////////////////////////// getQuestions ////////////////////////////////////

    public function submitExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
            'answers' => 'required|array'
        ]);

        DB::beginTransaction();

        try {

            $totalScore = 0;

            foreach ($request->answers as $item) {

                $question = ExamQuestion::findOrFail($item['question_id']);
                $answer = $item['answer'];

                $mark = 0;
                $isCorrect = null;
                $auto = false;

                // TRUE / FALSE
                if ($question->question_type === 'true_false') {

                    $auto = true;

                    if ($answer == $question->correct_answer) {
                        $mark = $question->mark;
                        $isCorrect = true;
                    } else {
                        $isCorrect = false;
                    }

                    $totalScore += $mark;
                }

                // MCQ
                if ($question->question_type === 'multiple_choice') {

                    $auto = true;

                    $correctOption = $question->options()
                        ->where('is_correct', 1)
                        ->first();

                    if ($correctOption && $correctOption->option_text == $answer) {
                        $mark = $question->mark;
                        $isCorrect = true;
                    } else {
                        $isCorrect = false;
                    }

                    $totalScore += $mark;
                }

                // ESSAY
                if ($question->question_type === 'essay') {
                    $mark = null;
                    $isCorrect = null;
                    $auto = false;
                }

                $examAnswer = ExamAnswer::create([
                    'exam_id' => $request->exam_id,
                    'student_id' => $request->student_id,
                    'question_id' => $question->id,
                    'answer' => $answer,
                    'mark' => $mark,
                    'is_auto_corrected' => $auto,
                    'is_correct' => $isCorrect
                ]);

                /*
                |--------------------------------------------------------------------------
                | Save Answer Image
                |--------------------------------------------------------------------------
                */

                if (!empty($item['image'])) {

                    DB::table('mediable')->insert([
                        'model_type' => \App\Models\ExamAnswer::class,
                        'model_id'   => $examAnswer->id,
                        'media_id'   => $item['image'],
                        'collection' => 'answer_image',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                // 'auto_score' => $totalScore,
                'message' => 'Exam submitted successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

/////////////////////////////////////////////////////// gradeEssay ///////////////////////////////

    public function gradeEssay(Request $request)
    {
        $result = (new FileUploadAction())->checkAssistantPermission('correct-answers', 'create');
            if ($result !== true) {
                return $result;
        }
        $request->validate([
            'answer_id' => 'required|exists:exam_answers,id',
            'mark' => 'required|numeric'
        ]);

        $answer = ExamAnswer::findOrFail($request->answer_id);

        $answer->update([
            'mark' => $request->mark,
            'is_correct' => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Essay graded successfully'
        ]);
    }

/////////////////////////////////////////////////////// result //////////////////////////////////////

    public function result($examId, $studentId)
    {
        $exam = Exam::findOrFail($examId);

        $answers = ExamAnswer::with('question.options')
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        $total = $answers->sum('mark');

        $passed = $total >= $exam->total_must_pass_marks;

        if (!$exam->show_result) {
            return response()->json([
                'status' => false,
                'message' => 'Result is hidden for this exam.',
                'passed' => $passed,
            ], 403);
        }

        return response()->json([
            'status' => true,
            'exam_id' => $exam->id,
            'exam_title' => $exam->title,
            'total' => $total,
            'passed' => $passed,
            'data' => AnswerResource::collection($answers)
        ]);
    }


    public function bankIndex(Request $request)
    {
        try {
            $filters = $request->input('filters', []);
            $orderBy = $request->input('orderBy', 'id');
            $orderByDirection = $request->input('orderByDirection', 'desc');
            $perPage = $request->input('perPage', 10);
            $paginate = $request->input('paginate', 1);
            $delete = $request->input('delete', false);
            $query = QuestionBank::with([
                'options',
                'teacher',
                'stage',
                'subject',
                'question_image'
            ]);
            if (!empty($filters['teacher_id'])) {
                $query->where('teacher_id', $filters['teacher_id']);
            }

            if (!empty($filters['stage_id'])) {
                $query->where('stage_id', $filters['stage_id']);
            }

            if (!empty($filters['subject_id'])) {
                $query->where('subject_id', $filters['subject_id']);
            }

            if (!empty($filters['question_type'])) {
                $query->where(
                    'question_type',
                    $filters['question_type']
                );
            }
            if ($delete) {
                $query->onlyTrashed();
            }
            $query->orderBy($orderBy, $orderByDirection);
            $questions = $query->get();
            if ($questions->isEmpty()) {
                return JsonResponse::respondError(
                    'No questions found in question bank'
                );
            }
            if ($paginate) {
                $currentPage = Paginator::resolveCurrentPage();
                $currentPageItems = $questions->slice(
                    ($currentPage - 1) * $perPage,
                    $perPage
                )->values();
                $paginatedItems = new LengthAwarePaginator(
                    $currentPageItems,
                    $questions->count(),
                    $perPage,
                    $currentPage,
                    ['path' => Paginator::resolveCurrentPath()]
                );
                return QuestionBankResource::collection($paginatedItems)
                    ->additional([
                        'status' => true,
                        'message' => 'Question bank fetched successfully'
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Without Pagination
            |--------------------------------------------------------------------------
            */

            return JsonResponse::respondSuccess(
                'Question bank fetched successfully',
                QuestionBankResource::collection($questions)
            );

        } catch (\Exception $e) {

            return JsonResponse::respondError($e->getMessage());
        }
    }

}

