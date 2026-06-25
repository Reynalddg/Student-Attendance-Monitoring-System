<table style="width:100%; border-collapse:collapse; margin-bottom:5px;">
    <tr>
        <!-- Left Logo -->
        <td style="width:80px;">
            <img src="{{ public_path('images/kagawaran.png') }}" style="height:100px;" alt="Kagawaran">
        </td>

        <!-- Center Title -->
        <td style="text-align:center;">
            <div style="font-size:21px; font-weight:bold; font-family:sans-serif;">
                School Form 1 School Register for Senior High School (SF1-SHS)
            </div>
        </td>

        <!-- Right Logo -->
        <td style="width:80px; text-align:right;">
            <img src="{{ public_path('images/deped.png') }}" style="height:80px;" alt="DepEd">
        </td>
    </tr>
</table>




<table style="width:100%; border-collapse: collapse; font-family: sans-serif; font-size:8px; margin-bottom: 10px;">
    

 
    <!-- FIRST ROW: School Name | School ID | District | Division | Region -->
    <tr>
        <td colspan="1" style="border: none; width: 10px;"></td>

        <td style="padding: 4px; text-align:right;">School Name</td>
        <td colspan="2" style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $schoolName }}</td>

        <td style="padding: 4px; text-align:right;">School ID</td>
        <td style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $schoolId }}</td>

        <td style="padding: 4px; text-align:right;">District</td>
        <td style="border: 1px solid black; padding: 6px 10px; text-align:center;">Talavera North</td>

        <td style="padding: 4px; text-align:right;">Division</td>
        <td style="border: 1px solid black; padding: 6px 10px; text-align:center;">Nueva Ecija</td>

        <td style="padding: 4px; text-align:right;">Region</td>
        <td colspan="2" style="border: 1px solid black; padding: 6px 10px; text-align:center;">Region III</td>

        <td colspan="1" style="border: none;"></td>
    </tr>

    <!-- SPACER ROW -->
    <tr><td colspan="14" style="height: 6px; border: none;"></td></tr>

    <!-- SECOND ROW: Semester | School Year | Grade Level | Track & Strand -->
    <tr>
        <td colspan="1" style="border: none;"></td>

        <td style="padding: 4px; text-align:right;">Semester</td>
        <td colspan="2" style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $semesterName }}</td>

        <td style="padding: 4px; text-align:right;">School Year</td>
        <td style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $acadName }}</td>

        <td style="padding: 4px; text-align:right;">Grade Level</td>
        <td style="border: 1px solid black; padding: 6px 10px; text-align:center;">Grade {{ $gradeLevel }}</td>

        <td style="padding: 4px; text-align:right;">Track & Strand</td>
        <td colspan="3" style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $trackName }} - {{ $strandName }}</td>

        <td colspan="2" style="border: none;"></td>
    </tr>

    <!-- SPACER ROW -->
    <tr><td colspan="14" style="height: 6px; border: none;"></td></tr>

    <!-- THIRD ROW: Section | Course for TVL only -->
    <tr>
        <td colspan="1" style="border: none;"></td>

        <td style="padding: 4px; text-align:right;">Section</td>
        <td colspan="2" style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $section }}</td>

        <td style="border: none;"></td>

        <td style="padding: 4px; text-align:right;">Course (for TVL only)</td>
        <td colspan="3" style="border: 1px solid black; padding: 6px 10px; text-align:center;"></td>

        <td colspan="5" style="border: none;"></td>
    </tr>

</table>

<table style="width:100%; border-collapse:collapse; font-family:sans-serif; font-size:6px; text-align:center;">
    <thead>
        <tr style="background-color:#f2f2f2;">
            <th rowspan="2" style="border:1px solid black; padding:3px; width:60px;">LRN</th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:150px;">NAME<br><small>(Last Name, First Name, Name Extension, Middle Name)</small></th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:25px;">Sex <br> (M/F)</th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:50px;">Birth Date</th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:25px;">Age</th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:50px;">Religion</th>
            <th colspan="4" style="border:1px solid black; padding:3px;">Address</th>
            <th colspan="2" style="border:1px solid black; padding:3px;">Parents</th>
            <th colspan="2" style="border:1px solid black; padding:3px;">Guardian <br> <small>(if learner is not Living with Parent)</small></th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:70px;">Contact Number<br>of Parent or Guardian</th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:50px;">Modality</th>
            <th rowspan="2" style="border:1px solid black; padding:3px; width:50px;">Remarks</th>
        </tr>
        <tr style="background-color:#f2f2f2;">
            <th style="border:1px solid black; padding:3px; width:120px;">House #/Street <br> /Sitio/Purok</th>
            <th style="border:1px solid black; padding:3px; width:60px;">Barangay</th>
            <th style="border:1px solid black; padding:3px; width:60px;">Municipality/City</th>
            <th style="border:1px solid black; padding:3px; width:60px;">Province</th>
            <th style="border:1px solid black; padding:3px; width:80px;">Father's Name <br> (Last Name, First <br> Name, Middle Name)     </th>
            <th style="border:1px solid black; padding:3px; width:80px;">Mother's Maiden <br>Name (Last Name, First <br> Name, Middle Name)</th>
            <th style="border:1px solid black; padding:3px; width:80px;">Name<br>(Last Name, First Name, Name Extension, Middle )</th>
            <th style="border:1px solid black; padding:3px; width:50px;">Relationship</th>
        </tr>
    </thead>
    <tbody>
        @php
        $cutoff = \Carbon\Carbon::createFromDate(now()->year, 6, 7)->startOfWeek();
        $grouped = $students->groupBy(function($enrollment) {
            return strtoupper(substr($enrollment->student->gender, 0, 1));
        });
        $grandTotal = 0;
        @endphp

        @foreach(['M'=>'MALE','F'=>'FEMALE'] as $key=>$label)
            @php $count = 0; @endphp
            @foreach($grouped->get($key, collect()) as $enrollment)
                @php
                    $s = $enrollment->student;
                    $age = \Carbon\Carbon::parse($s->birthdate)->diffInYears($cutoff);
                    $count++;
                    $grandTotal++;
                @endphp
                <tr>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ "".$s->lrn }}</td>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ strtoupper("{$s->last_name}, {$s->first_name} {$s->suffix}, {$s->middle_name}") }}</td>
                    <td style="border:1px solid black; padding:3px;">{{ strtoupper(substr($s->gender,0,1)) }}</td>
                    <td style="border:1px solid black; padding:3px;">{{ \Carbon\Carbon::parse($s->birthdate)->format('m/d/Y') }}</td>
                    <td style="border:1px solid black; padding:3px;">{{ $age }}</td>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ $s->religion }}</td>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ strtoupper($s->street ?? '') }}</td>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ strtoupper($s->barangay ?? '') }}</td>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ strtoupper($s->municipality ?? '') }}</td>
                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ strtoupper($s->province ?? '') }}</td>
@php
$guardians = $s->guardians ?? collect();

$father = $guardians->firstWhere('guardian_type', 'Father');
$mother = $guardians->firstWhere('guardian_type', 'Mother');
$otherGuardian = $guardians->first(function($g){
    return trim(strtolower($g->guardian_type)) === 'guardian';
});

if (!function_exists('formatGuardianName2')) {
    function formatGuardianName2($g) {
        if (!$g) return '';
        $last = strtoupper($g->last_name ?? '');
        $first = strtoupper($g->first_name ?? '');
        $suffix = strtoupper($g->suffix ?? '');
        $middle = strtoupper($g->middle_name ?? '');

        $firstSuffix = trim($first . ' ' . $suffix);
        $parts = [$last, $firstSuffix];
        if (!empty($middle)) {
            $parts[] = $middle;
        }

        return implode(', ', $parts);
    }
}
@endphp

<td>{{ formatGuardianName2($father) }}</td>
<td>{{ formatGuardianName2($mother) }}</td>
<td>{{ formatGuardianName2($otherGuardian) }}</td>
<td>{{ strtoupper($otherGuardian?->relation ?? '') }}</td>




                    <td style="border:1px solid black; padding:3px; text-align:left; font-size:7px;">{{ $s->guardian->contact ?? '' }}</td>
                    <td style="border:1px solid black; padding:3px; font-size:7px;">Face to Face</td>
                    <td style="border:1px solid black; padding:3px; font-size:7px;"> {{$s->remarks }} </td>
                </tr>
            @endforeach
            <tr>
                <td style="border:1px solid black; padding:3px; text-align:right;">{{ $count }}</td>
                <td colspan="16" style="border:1px solid black; padding:3px; text-align:left;">&lt;=== TOTAL {{ $label }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="border:1px solid black; padding:3px; text-align:right;">{{ $grandTotal }}</td>
            <td colspan="16" style="border:1px solid black; padding:3px; text-align:left;">&lt;=== COMBINED TOTAL</td>
        </tr>
    </tbody>
</table>



<!-- === LEGEND + REGISTERED + PREPARED BY (ALL IN ONE ROW) === -->
<table style="width:100%; border-collapse: collapse; font-size: 8px; margin-top: 10px; font-family: sans-serif;">
    <tr>
        <!-- === LEFT COLUMN: LEGEND === -->
        <td style="width:50%; vertical-align: top;">

            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td colspan="6" style="font-weight: bold; border: none; padding: 5px;">
                        Legend: List and Code of Indicators under REMARKS column
                    </td>
                </tr>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid black; padding: 4px;">Indicator</th>
                    <th style="border: 1px solid black; padding: 4px;">Code</th>
                    <th style="border: 1px solid black; padding: 4px;">Required Information</th>
                    <th style="border: 1px solid black; padding: 4px;">Indicator</th>
                    <th style="border: 1px solid black; padding: 4px;">Code</th>
                    <th style="border: 1px solid black; padding: 4px;">Required Information</th>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 4px;">Transferred Out<br>Transferred In</td>
                    <td style="border: 1px solid black; padding: 4px; text-align:center;">T/O<br>T/I</td>
                    <td style="border: 1px solid black; padding: 4px;">
                        Name of School, Date of 1st Attendance, and Date of Last Attendance if Transferred Out
                    </td>
                    <td style="border: 1px solid black; padding: 4px;">CCT Recipient<br>Balik Aral<br>Special Needs Education<br>Accelerated</td>
                    <td style="border: 1px solid black; padding: 4px; text-align:center;">CCT<br>B/A<br>SN<br>ACL</td>
                    <td style="border: 1px solid black; padding: 4px;">
                        CCT Control/reference number & Effectivity Date<br>
                        Name of School last attended & Year<br>
                        Specify Exceptionality of the Learner<br>
                        Specify Level & Effectivity Date
                    </td>
                </tr>
            </table>

        </td>
        <br>
        
        <!-- === CENTER COLUMN: REGISTERED === -->
        <td style="width:25%; vertical-align: top; text-align:center; margin-top:250px; padding-left:50px;">

            <table style="width:100%; border-collapse: collapse;">
                <br>
        <br>
                <tr>
                    <th style="border: 1px solid black; text-align:center;">Registered</th>
                    <th style="border: 1px solid black; text-align:center;">Beginning of Semester</th>
                    <th style="border: 1px solid black; text-align:center;">End of Semester</th>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align:center;">MALE</td>
                    <td style="border: 1px solid black; text-align:center;">
                        {{ $students->where('student.gender', 'Male')->count() }}
                    </td>
                    <td style="border: 1px solid black; text-align:center;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align:center;">FEMALE</td>
                    <td style="border: 1px solid black; text-align:center;">
                        {{ $students->where('student.gender', 'Female')->count() }}
                    </td>
                    <td style="border: 1px solid black; text-align:center;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align:center;">TOTAL</td>
                    <td style="border: 1px solid black; text-align:center;">
                        {{ $students->count() }}
                    </td>
                    <td style="border: 1px solid black; text-align:center;">&nbsp;</td>
                </tr>
            </table>

        
        <!-- === RIGHT COLUMN: PREPARED BY === -->
        <td style="width:25%; vertical-align: top; padding-left:50px; ">
        <br>
        <br>
        
            <b>Prepared by:</b><br><br>
           <div style="text-align:center;">
                <u style="display:inline-block; width:200px; border-bottom:1px solid black;">
                    {{ $adviserName ?? 'No Adviser' }}
                </u><br>
                <small><b>(Signature of Adviser over Printed Name)</b></small>
            </div>


           <div style="margin-top:10px; font-size:8px; display: flex; gap: 10px;">

  <div style="margin-top:10px; font-size:8px; display: flex; gap: 10px; align-items: stretch;">

<div style="font-size:8px; margin-top:10px; width:100%;">
    <!-- Beginning -->
    <div style="display:inline-block; width:48%; text-align:left; vertical-align:top;">
        <strong>Beginning of the Semester Date:</strong><br>
        <div style="border:1px solid black; padding:4px;">
            {{ \Carbon\Carbon::parse($semesterStartDate)->format('d/m/Y') }} 12:00 AM
        </div>
    </div>

    <!-- End -->
    <div style="display:inline-block; width:48%; vertical-align:top; padding-left:7px;">
        <strong>End of the Semester Date:</strong><br>
        <div style="border:1px solid black; padding:4px;">
            {{ \Carbon\Carbon::parse($semesterEndDate)->format('d/m/Y') }} 12:00 AM
        </div>
    </div>
</div>

        </td>
    </tr>
</table>



