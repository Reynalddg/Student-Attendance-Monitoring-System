<?php

namespace App\Exports;

use App\Models\StudentEnrollment;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;

class SF1PDFExport
{
    protected $academicYearId, $semesterId, $sectionId;

    public function __construct($academicYearId, $semesterId, $sectionId)
    {
        $this->academicYearId = $academicYearId;
        $this->semesterId = $semesterId;
        $this->sectionId = $sectionId;
    }

    public function download()
    {
       
        $students = StudentEnrollment::with('student')
            ->where('section_id', $this->sectionId)
            ->where('semester_id', $this->semesterId)
            ->whereNull('date_archived')
            ->get()
            ->sortBy(fn($e) => ($e->student->gender === 'Male' ? 1 : 2) . $e->student->last_name);

        $sectionData = Section::with('track_strand', 'adviser')->find($this->sectionId);
        $strandName  = $sectionData->track_strand->strand ?? '';
        $trackName   = $sectionData->track_strand->track ?? '';


        $this->adviserName = $sectionData && $sectionData->adviser
            ? trim("{$sectionData->adviser->first_name} {$sectionData->adviser->middle_name} {$sectionData->adviser->last_name}")
            : 'No Adviser';
         $this->semesterStartDate = Semester::find($this->semesterId)->start_date ?? '';
        $this->semesterEndDate = Semester::find($this->semesterId)->end_date ?? '';

        $data = [
            'students'     => $students,
            'schoolId'     => '305834',
            'schoolName'   => 'Talavera Senior High School',
            'semesterName' => Semester::find($this->semesterId)->name ?? '',
            'section'      => $sectionData->section_name ?? '',
            'gradeLevel'   => $sectionData->grade_level ?? '',
            'strandName'   => $strandName,
            'trackName'    => $trackName,
            'acadName'     => AcademicYear::find($this->academicYearId)->name ?? '',
            'adviserName'  => $this->adviserName,
            'semesterStartDate' => $this->semesterStartDate,
            'semesterEndDate' => $this->semesterEndDate
        ];


        $pdf = Pdf::loadView('exports.sf1_pdf', $data)
                  ->setPaper('legal', 'landscape');

        return $pdf->download('SF1-' . ($sectionData->section_name ?? 'Section') . '.pdf');
    }
    
}
