<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class StudentsImport implements ToCollection
{
    public int $importedCount = 0;
    public int $skippedCount = 0;

    public function collection(Collection $rows)
    {
        // Get grade level from a specific cell (optional)
        $gradeLevelCell = $rows[4][8] ?? null;
        $gradeLevel = null;

        if ($gradeLevelCell && preg_match('/\d+/', $gradeLevelCell, $matches)) {
            $gradeLevel = (int)$matches[0];
        }
        $gradeLevel = $gradeLevel ?? 0; // Default to 0 if missing

        foreach ($rows as $index => $row) {
            if ($index < 9) continue; // skip headers

            if (
                (isset($row[0]) && preg_match('/legend|prepared|signature|no students/i', $row[0])) ||
                (isset($row[1]) && preg_match('/legend|prepared|signature|no students/i', $row[1]))
            ) break;

            if (!isset($row[0]) || trim($row[0]) === '' || str_contains($row[1] ?? '', 'TOTAL')) continue;

            $lrn = trim($row[0] ?? '');
            if (!preg_match('/^\d{12}$/', $lrn)) {
                $this->skippedCount++;
                continue;
            }

            $name = trim($row[1] ?? '');
            $gender = strtoupper(trim($row[2] ?? ''));

            if (Student::where('lrn', $lrn)->exists()) {
                $this->skippedCount++;
                continue;
            }

            $gender = match ($gender) {
                'M' => 'Male',
                'F' => 'Female',
                default => ucfirst(strtolower($gender)),
            };

            // Birthdate parsing
            $birthdate = null;
            if (!empty($row[3])) {
                try {
                    $birthdate = is_numeric($row[3])
                        ? Date::excelToDateTimeObject($row[3])->format('Y-m-d')
                        : Carbon::parse($row[3])->format('Y-m-d');
                } catch (\Exception $e) {
                    $birthdate = null;
                }
            }

            $religion = $this->toProperCase($row[5] ?? '');
            $barangay = $this->toProperCase($row[7] ?? '');
            $municipality = $this->toProperCase($row[8] ?? '');
            $province = $this->toProperCase($row[9] ?? '');
            $remarks = trim($row[17] ?? '');

            // Split student name
            [$lastName, $firstName, $middleName, $suffix] = $this->splitStudentName($name);

            // Guardian info
            $fatherName = $row[10] ?? '';
            $motherName = $row[11] ?? '';
            $otherGuardianName = $row[12] ?? '';
            $relation = $row[13] ?? '';
            $phone = trim($row[14] ?? '');

            // Create Student
            $student = Student::create([
                'lrn' => $lrn,
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'suffix' => $suffix,
                'gender' => $gender,
                'birthdate' => $birthdate,
                'religion' => $religion,
                'barangay' => $barangay,
                'municipality' => $municipality,
                'province' => $province,
                'status' => 'Active',
                'grade_level' => $gradeLevel,
                'remarks' => $remarks,
                'image' => '', // default empty string
            ]);

            // Create Guardians
            $guardians = [
                ['type' => 'Father', 'name' => $fatherName, 'relation' => 'Father', 'phone' => null],
                ['type' => 'Mother', 'name' => $motherName, 'relation' => 'Mother', 'phone' => null],
                ['type' => 'Guardian', 'name' => $otherGuardianName, 'relation' => $relation, 'phone' => $phone],
            ];

            foreach ($guardians as $g) {
                if (!empty(trim($g['name']))) {
                    [$first, $middle, $last, $suffix] = $this->splitName($g['name']);
                    Guardian::firstOrCreate(
                        [
                            'student_id' => $student->student_id,
                            'guardian_type' => $g['type']
                        ],
                        [
                            'first_name' => $first,
                            'middle_name' => $middle,
                            'last_name' => $last,
                            'suffix' => $suffix,
                            'relation' => $g['relation'],
                            'phone_number' => $g['phone'],
                        ]
                    );
                }
            }

            $this->importedCount++;

            // Generate QR code safely
            try {
                $fullName = preg_replace('/[^A-Za-z0-9]/', '_', 
                    $student->first_name . '_' . $student->middle_name . '_' . $student->last_name
                );
                $fileName = $fullName . '.png';
                $savePath = 'C:/Users/reyna/capstone/studentQRCode/' . $fileName;

                if (!file_exists(dirname($savePath))) {
                    mkdir(dirname($savePath), 0777, true);
                }

                $options = new QROptions([
                    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                    'eccLevel' => QRCode::ECC_L,
                    'scale' => 8,
                ]);

                $qrcode = new QRCode($options);
                $qrcode->render($student->lrn, $savePath);
            } catch (\Exception $e) {
                // log error or ignore QR generation failure
            }
        }
    }

    private function toProperCase(?string $value): ?string
    {
        $value = trim($value ?? '');
        if ($value === '') return null;
        return ucwords(strtolower($value));
    }

    // Split full name for student: Last, First Middle Suffix
    private function splitStudentName(string $fullName): array
    {
        $parts = explode(',', $fullName);
        $last = isset($parts[0]) ? $this->toProperCase($parts[0]) : '';
        $rest = isset($parts[1]) ? trim($parts[1]) : '';
        $restParts = preg_split('/\s+/', $rest);
        $first = isset($restParts[0]) ? $this->toProperCase($restParts[0]) : '';
        $middle = count($restParts) > 1 ? $this->toProperCase(implode(' ', array_slice($restParts, 1))) : '';
        $suffix = ''; // optional
        return [$last, $first, $middle, $suffix];
    }

    // Split full name for guardian: First Middle Last Suffix
    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName));
        $suffixes = ['Jr', 'Sr', 'II', 'III', 'IV'];
        $suffix = '';
        if (in_array(end($parts), $suffixes)) {
            $suffix = array_pop($parts);
        }
        $first = array_shift($parts);
        $last = array_pop($parts) ?? '';
        $middle = implode(' ', $parts);
        return [$first, $middle, $last, $suffix];
    }
}
