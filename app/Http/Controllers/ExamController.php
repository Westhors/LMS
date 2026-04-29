<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Requests\ExamRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\QuestionResource;
use App\Interfaces\ExamRepositoryInterface;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamQuestion;
use App\Models\QuestionOption;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;

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
            $exam->load(['questions', 'answers', 'courseDetail', 'stage', 'teacher']);
            return JsonResponse::respondSuccess('Item Fetched Successfully', new ExamResource($exam));
        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }


    public function update(ExamRequest $request, Exam $exam): \Illuminate\Http\JsonResponse
    {
        try {
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
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'questions' => 'required|array'
        ]);

        foreach ($request->questions as $q) {

            $question = ExamQuestion::create([
                'exam_id' => $request->exam_id,
                'question_type' => $q['question_type'],
                'question' => $q['question'],
                'mark' => $q['mark'] ?? 1,
                'correct_answer' => $q['correct_answer'] ?? null,
            ]);

            // MCQ Options
            if ($q['question_type'] === 'multiple_choice' && isset($q['options'])) {
                foreach ($q['options'] as $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt['option_text'],
                        'is_correct' => $opt['is_correct'] ?? false,
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Questions added successfully'
        ]);
    }

//////////////////////////////////////////// submitExam ////////////////////////////////////
    public function getQuestions($examId)
    {
        $exam = Exam::with('questions.options')
            ->findOrFail($examId);

        return response()->json([
            'status' => true,
            'exam_id' => $exam->id,
            'exam_title' => $exam->title,
            'questions_count' => $exam->questions->count(),
            'data' => QuestionResource::collection($exam->questions)
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

            // ESSAY (manual correction)
            if ($question->question_type === 'essay') {
                $mark = null;
                $isCorrect = null;
                $auto = false;
            }

            ExamAnswer::create([
                'exam_id' => $request->exam_id,
                'student_id' => $request->student_id,
                'question_id' => $question->id,
                'answer' => $answer,
                'mark' => $mark,
                'is_auto_corrected' => $auto,
                'is_correct' => $isCorrect
            ]);
        }

        return response()->json([
            'status' => true,
            'auto_score' => $totalScore,
            'message' => 'Exam submitted successfully'
        ]);
    }

/////////////////////////////////////////////////////// gradeEssay ///////////////////////////////

    public function gradeEssay(Request $request)
    {
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

/////////////////////////////////////////////////////// result ///////////////////////////////

    public function result($examId, $studentId)
    {
        $answers = ExamAnswer::with('question.options')
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        return response()->json([
            'status' => true,
            'total' => $answers->sum('mark'),
            'data' => AnswerResource::collection($answers)
        ]);
    }


}

