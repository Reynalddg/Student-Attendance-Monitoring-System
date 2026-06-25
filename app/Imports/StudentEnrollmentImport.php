<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Semester;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentEnrollmentImport implements ToCollection
{
    public int $importedCount = 0;
    public int $skippedCount = 0;

    private $sectionId;
    private $semesterId;

    public function __construct($sectionId, $semesterId)
    {
        $this->sectionId = $sectionId;
        $this->semesterId = $semesterId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
              if ($index < 9) continue;

            if (
                isset($row[0]) && preg_match('/legend|prepared|signature|no students/i', $row[0]) ||
                isset($row[1]) && preg_match('/legend|prepared|signature|no students/i', $row[1])
            ) break;

            if (!isset($row[0]) || trim($row[0]) === '' || str_contains($row[1] ?? '', 'TOTAL')) continue;

            $lrn = trim($row[0] ?? '');
            if (!$lrn) continue;

            $student = Student::where('lrn', $lrn)->first();
            if (!$student) {
                $this->skippedCount++;
                continue;
            }

            $alreadyEnrolled = StudentEnrollment::where('student_id', $student->student_id)
                ->where('semester_id', $this->semesterId)
                ->exists();

            if ($alreadyEnrolled) {
                $this->skippedCount++;
                continue;
            }

            StudentEnrollment::create([
                'student_id' => $student->student_id,
                'section_id' => $this->sectionId,
                'semester_id' => $this->semesterId,
                'status' => 'Active',
            ]);

            $this->importedCount++;
        }
    }
}
