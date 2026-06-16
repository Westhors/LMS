<?php

namespace App\Http\Controllers;

use App\Helpers\FileUploadAction;
use App\Interfaces\PaymentCodeRepositoryInterface;
use App\Models\PaymentCode;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PaymentCodeController extends BaseController
{
    use HttpResponses;

    protected mixed $crudRepository;

    public function __construct(PaymentCodeRepositoryInterface $pattern)
    {
        $this->crudRepository = $pattern;
    }

    public function generateCodes(Request $request)
    {
        $result = (new FileUploadAction())->checkAssistantPermission('payment-codes', 'create');
            if ($result !== true) {
                return $result;
        }
        $request->validate([
            'type' => 'required|in:wallet,course,semester,lesson',
            'type_code' => 'nullable|in:online,center',
            'count' => 'required|integer|min:1|max:500',
            'amount' => 'required_if:type,wallet',
            'course_id' => 'required_if:type,course',
            'semester_id' => 'required_if:type,semester',
            'course_detail_id' => 'required_if:type,lesson',
        ]);

        $codes = [];

        for ($i = 0; $i < $request->count; $i++) {
            $codes[] = [
                'code' => strtoupper(Str::random(8)),
                'type' => $request->type,
                'type_code' => $request->type_code,

                'amount' => $request->type === 'wallet' ? $request->amount : null,
                'course_id' => $request->type === 'course' ? $request->course_id : null,
                'semester_id' => $request->type === 'semester' ? $request->semester_id : null,
                'course_detail_id' => $request->type === 'lesson' ? $request->course_detail_id : null,

                'teacher_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PaymentCode::insert($codes);

        return response()->json([
            'status' => true,
            'message' => 'Codes generated',
            'count' => count($codes),
            'data' => $codes,
        ]);
    }

    public function index(Request $request)
    {
        $query = PaymentCode::with('student');

        // 🎯 المدرس
        $query->when(auth()->check(), function ($q) {
            $q->where('teacher_id', auth()->id());
        });

        // 🔍 Filters
        $filters = $request->input('filters', []);

        $query->when($filters['amount'] ?? null, function ($q, $value) {
            $q->where('amount', $value);
        });

        $query->when($filters['course_id'] ?? null, function ($q, $value) {
            $q->where('course_id', $value);
        });

        $query->when($filters['semester_id'] ?? null, function ($q, $value) {
            $q->where('semester_id', $value);
        });

        $query->when($filters['course_detail_id'] ?? null, function ($q, $value) {
            $q->where('course_detail_id', $value);
        });

        $query->when($filters['type_code'] ?? null, function ($q, $value) {
            $q->where('type_code', $value);
        });

        // 🔎 Search by code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $codes = $query->get();

        // إضافة اسم الطالب لكل كود
        $codes->each(function ($code) {
            $code->student_name = $code->student?->name;
        });

        return response()->json([
            'status' => true,
            'data' => [

                // 💰 Wallet
                'wallet' => $codes->where('type', 'wallet')
                    ->groupBy('amount')
                    ->map(function ($items, $amount) {
                        return [
                            'amount' => (float) $amount,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),

                // 🎓 Courses
                'courses' => $codes->where('type', 'course')
                    ->groupBy('course_id')
                    ->map(function ($items, $courseId) {
                        return [
                            'course_id' => $courseId,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),

                // 📚 Semesters
                'semesters' => $codes->where('type', 'semester')
                    ->groupBy('semester_id')
                    ->map(function ($items, $semesterId) {
                        return [
                            'semester_id' => $semesterId,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),

                // 📖 Lessons
                'lessons' => $codes->where('type', 'lesson')
                    ->groupBy('course_detail_id')
                    ->map(function ($items, $lessonId) {
                        return [
                            'course_detail_id' => $lessonId,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),
            ]
        ]);
    }



    public function paymentCodesReport()
    {
        $stats = [
            'online_used' => PaymentCode::where('is_used', true)
                ->where('type_code', 'online')
                ->count(),

            'center_used' => PaymentCode::where('is_used', true)
                ->where('type_code', 'center')
                ->count(),

            'total_used' => PaymentCode::where('is_used', true)->count(),

            'total_unused' => PaymentCode::where('is_used', false)->count(),
        ];

        $codes = PaymentCode::with('student:id,name')
            ->where('is_used', true)
            ->latest('used_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Payment Codes Report',
            'data' => [
                'statistics' => $stats,
                'codes' => $codes,
            ],
        ]);
    }


}

