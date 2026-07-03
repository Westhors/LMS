<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseDetail extends BaseModel
{
    protected $table = 'course_details';

    use HasFactory;

    use HasMedia;

    protected $with = [
        'media',
    ];
    protected $casts = [
        'titles' => 'array',
        'titles_ar' => 'array',
        'link_video' => 'array',
    ];
    protected $guarded = ['id'];
    public function views()
    {
        return $this->hasMany(CourseDetailView::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class)->withDefault();
    }

    public function exams()
    {
        return $this->hasMany(Exam::class)
            ->where('type', 'exam');
    }

    public function assignments()
    {
        return $this->hasMany(Exam::class)
            ->where('type', 'assignment');
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'enrollments',
            'course_detail_id',
            'student_id'
        )
        ->where('type', 'lesson');
    }

    public function checkStudentPassedExam()
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user instanceof Teacher) {
            return true;
        }

        if (!($user instanceof Student)) {
            return false;
        }

        $exams = $this->exams()->orderBy('id')->get();

        if ($exams->isEmpty()) {
            return true;
        }

        foreach ($exams as $exam) {

            $answers = ExamAnswer::where('exam_id', $exam->id)
                ->where('student_id', $user->id)
                ->get();

            if ($answers->isEmpty()) {
                continue;
            }

            $studentMark = $answers->sum('mark');

            if ($studentMark >= $exam->total_must_pass_marks) {
                return true;
            }
        }

        return false;
    }


    public function attendances()
    {
        return $this->hasMany(
            CourseDetailAttendance::class
        );
    }

}
