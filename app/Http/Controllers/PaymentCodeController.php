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

        // Teacher
        if (auth()->check()) {
            $query->where('teacher_id', auth()->id());
        }

        // Filters
        $filters = $request->input('filters', []);

        foreach (
            [
                'amount',
                'course_id',
                'semester_id',
                'course_detail_id',
                'type_code',
            ] as $filter
        ) {
            if (isset($filters[$filter]) && $filters[$filter] !== '') {
                $query->where($filter, $filters[$filter]);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', "%{$search}%");
        }

        $codes = $query->get();

        // Add student name
        $codes->each(function ($code) {
            $code->student_name = $code->student?->name;
        });

        // Group by type once
        $grouped = $codes->groupBy('type');

        return response()->json([
            'status' => true,
            'data' => [

                'wallet' => $grouped->get('wallet', collect())
                    ->groupBy('amount')
                    ->map(function ($items, $amount) {
                        return [
                            'amount' => (float) $amount,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),

                'courses' => $grouped->get('course', collect())
                    ->groupBy('course_id')
                    ->map(function ($items, $courseId) {
                        return [
                            'course_id' => $courseId,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),

                'semesters' => $grouped->get('semester', collect())
                    ->groupBy('semester_id')
                    ->map(function ($items, $semesterId) {
                        return [
                            'semester_id' => $semesterId,
                            'count' => $items->count(),
                            'codes' => $items->values(),
                        ];
                    })->values(),

                'lessons' => $grouped->get('lesson', collect())
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



    public function delete(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|integer|exists:payment_codes,id',
        ]);

        $query = PaymentCode::whereIn('id', $request->items);

        if (auth()->check()) {
            $query->where('teacher_id', auth()->id());
        }

        $deleted = $query->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment codes deleted successfully',
            'deleted_count' => $deleted,
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
