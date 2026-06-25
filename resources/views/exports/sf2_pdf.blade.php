<table style="width:100%; border-collapse:collapse; margin-bottom:5px;">
    <tr>
        <!-- Left Logo -->
        <td style="width:80px;">
            <img src="{{ public_path('images/kagawaran.png') }}" style="height:100px;" alt="Kagawaran">
        </td>

        <!-- Center Title -->
        <td style="text-align:center;">
            <div style="font-size:21px; font-weight:bold; font-family:sans-serif;">
                School Form 2 Daily Attendance Report of Learners For Senior High School (SF2-SHS)
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
        <td colspan="2" style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $semester }}</td>

        <td style="padding: 4px; text-align:right;">School Year</td>
        <td style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ $acadYear, }}</td>

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

        <td style="padding: 4px; text-align:right;">Month of</td>
        <td colspan="2" style="border: 1px solid black; padding: 6px 10px; text-align:center;">{{ \Carbon\Carbon::create()->month($month)->format('F') }}

</td>

        <td colspan="5" style="border: none;"></td>
    </tr>

</table>

<!-- ATTENDANCE TABLE -->
<table style="width: 100%; border-collapse: collapse; border: 1px solid black; font-family: sans-serif; font-size: 8px;">
   <thead>
    <!-- Row 1: No. | Name | DATE colspan | TOTAL | REMARKS -->
    <tr>
        <!-- No. Column -->
        <th rowspan="3" style="text-align: center; vertical-align: middle; padding: 3px; width:25px; border: 1px solid black; font-weight:bold;"> 
            No. 
        </th>

        <!-- Learner's Name -->
        <th rowspan="3" style="text-align: center; vertical-align: middle; padding: 3px; width: 130px; border: 1px solid black; font-weight:bold;">
            LEARNER'S NAME <br> (Last Name, First Name, Middle Name)
        </th>

        <!-- DATE header (covers all school days) -->
        <th colspan="{{ $daysInMonth - collect(range(1,$daysInMonth))->map(fn($d)=>\Carbon\Carbon::create($year,$month,$d)->format('D'))->filter(fn($d)=>in_array($d,['Sat','Sun']))->count() }}" 
            style="text-align: center; border: 1px solid black; padding: 3px; font-weight:bold;">
            DATE
        </th>

        @php
            $schoolDays = 0;

            for ($d = 1; $d <= $daysInMonth; $d++) {

                $date = \Carbon\Carbon::create($year, $month, $d)->format('Y-m-d');
                $dayName = \Carbon\Carbon::create($year, $month, $d)->format('D');

                if (in_array($dayName, ['Sat','Sun'])) {
                    continue;
                }

                if (!empty($holidays) && in_array($date, $holidays)) {
                    continue;
                }

                $schoolDays++;
            }
        @endphp

        <!-- TOTAL FOR THE MONTH -->
        <th colspan="2" rowspan="2" style="text-align: center; border: 1px solid black; padding: 3px; font-weight:bold;">
            TOTAL FOR THE MONTH <br>
            {{$schoolDays}}
        </th>

        <!-- REMARKS -->
        <th colspan="3" rowspan="3" style="text-align: center; vertical-align: middle; padding: 3px; border: 1px solid black; font-size: 7px; width: 90px;">
            REMARKS (If NLS, state reason, please refer to legend number 2. If TRANSFERRED IN/OUT, write the name of School.)
        </th>
    </tr>

    <!-- Row 2: Day numbers -->
    <tr>
        @for ($d = 1; $d <= $daysInMonth; $d++)
            @php $dayName = \Carbon\Carbon::create($year, $month, $d)->format('D'); @endphp
            @if(!in_array($dayName, ['Sat','Sun']))
                <th style="text-align: center; vertical-align: middle; padding: 3px; width: 22px; border: 1px solid black; font-weight:bold;">
                    {{ $d }}
                </th>
            @endif
        @endfor
    </tr>

    <!-- Row 3: Day names -->
        <tr>
            @for ($d = 1; $d <= $daysInMonth; $d++)
                @php 
                    $dayName = \Carbon\Carbon::create($year, $month, $d)->format('D'); 
                    switch ($dayName) {
                        case 'Mon': $dayShort = 'M'; break;
                        case 'Tue': $dayShort = 'T'; break;
                        case 'Wed': $dayShort = 'W'; break;
                        case 'Thu': $dayShort = 'TH'; break;
                        case 'Fri': $dayShort = 'F'; break;
                        default: $dayShort = ''; // skip Sat & Sun
                    }
                @endphp
                @if($dayShort != '')
                    <th style="text-align: center; padding: 3px; width: 22px; border: 1px solid black; font-weight:bold;">
                        {{ $dayShort }}
                    </th>
                @endif
            @endfor

            <!-- Absent -->
            <th style="text-align: center; padding: 3px; width: 35px; border: 1px solid black; font-weight:bold;">
                ABSENT
            </th>

             <!-- Present -->
            <th style="text-align: center; padding: 3px; width: 35px; border: 1px solid black; font-weight:bold;">
                PRESENT
            </th>
        </tr>
</thead>

<tbody>
@php
    $maleNo = 1;
    $femaleNo = 1;
    $maleDailyTotals = [];
    $femaleDailyTotals = [];
    $maleDailyAbsents = [];
    $femaleDailyAbsents = [];
    $holidays = $holidays ?? [];

    $today = \Carbon\Carbon::today('Asia/Manila');

    for ($d = 1; $d <= $daysInMonth; $d++) {
        $date = \Carbon\Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Manila');
        $formattedDate = $date->format('Y-m-d');
        if(!in_array($date->format('D'), ['Sat','Sun'])) {
            $maleDailyTotals[$d] = 0;
            $femaleDailyTotals[$d] = 0;
            $maleDailyAbsents[$d] = 0;
            $femaleDailyAbsents[$d] = 0;
        }
    }
@endphp

{{-- === MALE STUDENTS === --}}
@foreach($students->where('student.gender', 'Male') as $student)
    @php 
        $presentCount = 0; 
        $absentCount = 0; 
        $isActive = $student->status === 'Active'; // Assuming there's a status field
        $remarks = $student->remarks ?? ''; // Assuming there's a remarks field for inactive students
    @endphp
    
    <tr>
        <td align="center" style="border: 1px solid black; padding: 3px;">{{ $maleNo++ }}</td>
        <td style="border: 1px solid black; padding: 3px;">
            {{ strtoupper($student->student->last_name ?? '') }}, {{ strtoupper($student->student->first_name ?? '') }} {{ strtoupper($student->student->middle_name ?? '') }}
        </td>

        @if($isActive)
            {{-- ACTIVE STUDENT - Show normal attendance --}}
            @for ($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $date = \Carbon\Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Manila');
                    if(in_array($date->format('D'), ['Sat','Sun'])) continue;
                    $formattedDate = $date->format('Y-m-d');

                    $dateStr = $date->toDateString();
                    $logEntry = $attendances[$student->enrollment_id][$dateStr] ?? null;
                    $log = is_array($logEntry) ? reset($logEntry) : $logEntry;
                    
                    if(in_array($formattedDate, $holidays)) {
                        $cellValue = ''; // leave cell blank
                    } else {
                        if($date->gt($today)) {
                            $cellValue = '-';
                        } elseif($log) {
                            $cellValue = ''; // present
                            $presentCount++;
                            $maleDailyTotals[$d]++;
                        } else {
                            $cellValue = 'X'; // absent
                            $absentCount++;
                            $maleDailyAbsents[$d]++;
                        }
                    }
                @endphp

                <td align="center" style="border: 1px solid black; padding: 3px;">{{ $cellValue }}</td>
            @endfor

            <td align="center" style="border: 1px solid black; padding: 3px;">{{ $absentCount }}</td>
            <td align="center" style="border: 1px solid black; padding: 3px;">{{ $presentCount }}</td>
            <td colspan="3" style="border: 1px solid black; padding: 3px;"></td>
        @else
            {{-- INACTIVE STUDENT - Merge all attendance cells and show remarks --}}
            @php
                $totalAttendanceColumns = $daysInMonth - collect(range(1,$daysInMonth))->map(fn($d)=>\Carbon\Carbon::create($year,$month,$d)->format('D'))->filter(fn($d)=>in_array($d,['Sat','Sun']))->count();
            @endphp
            
            <td colspan="{{ $totalAttendanceColumns + 2 }}" align="center" style="border: 1px solid black; padding: 3px;  font-size:9px; font-family:sans-serif;">
                 {{ $remarks ?: 'Inactive Student' }}
            </td>
            <td colspan="3" style="border: 1px solid black; padding: 3px;  font-size:9px; font-family:sans-serif; text-align:center;">
                {{ $remarks ?: 'Inactive Student' }}
            </td>
        @endif
    </tr>
@endforeach

{{-- === TOTAL MALE PER DAY === --}}
<tr style="font-weight:bold; ">
    <td colspan="2" align="right" style="border: 1px solid black; padding: 3px; font-weight:bold; background:#f2f2f2;">
        &lt;=== MALE | TOTAL Per Day ===&gt;
    </td>

    @foreach($maleDailyTotals as $d => $total)
        <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">{{ $total == 0 ? '0' : $total }}</td>
    @endforeach
    <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">{{ array_sum($maleDailyAbsents) }}</td>
    <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">{{ array_sum($maleDailyTotals) }}</td>
    <td colspan="3" style="border: 1px solid black; padding: 3px;"></td>
</tr>

{{-- === FEMALE STUDENTS === --}}
@foreach($students->where('student.gender', 'Female') as $student)
    @php 
        $presentCount = 0; 
        $absentCount = 0; 
        $isActive = $student->status === 'Active'; // Assuming there's a status field
        $remarks = $student->remarks ?? ''; // Assuming there's a remarks field for inactive students
    @endphp
    
    <tr>
        <td align="center" style="border: 1px solid black; padding: 3px;">{{ $femaleNo++ }}</td>
        <td style="border: 1px solid black; padding: 3px;">
            {{ strtoupper($student->student->last_name ?? '') }}, {{ strtoupper($student->student->first_name ?? '') }} {{ strtoupper($student->student->middle_name ?? '') }}
        </td>

        @if($isActive)
            {{-- ACTIVE STUDENT - Show normal attendance --}}
            @for ($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $date = \Carbon\Carbon::create($year, $month, $d, 0, 0, 0, 'Asia/Manila');
                    if(in_array($date->format('D'), ['Sat','Sun'])) continue;
                    $formattedDate = $date->format('Y-m-d');

                    $dateStr = $date->toDateString();
                    $logEntry = $attendances[$student->enrollment_id][$dateStr] ?? null;
                    $log = is_array($logEntry) ? reset($logEntry) : $logEntry;
                    
                    if(in_array($formattedDate, $holidays)) {
                        $cellValue = ''; 
                    } else {
                        if($date->gt($today)) {
                            $cellValue = '-';
                        } elseif($log) {
                            $cellValue = ''; 
                            $presentCount++;
                            $femaleDailyTotals[$d]++;
                        } else {
                            $cellValue = 'X'; 
                            $absentCount++;
                            $femaleDailyAbsents[$d]++;
                        }
                    }
                @endphp
               
                <td align="center" style="border: 1px solid black; padding: 3px;">{{ $cellValue }}</td>
            @endfor

            <td align="center" style="border: 1px solid black; padding: 3px;">{{ $absentCount }}</td>
            <td align="center" style="border: 1px solid black; padding: 3px;">{{ $presentCount }}</td>
            <td colspan="3" style="border: 1px solid black; padding: 3px;"></td>
        @else
            {{-- INACTIVE STUDENT - Merge all attendance cells and show remarks --}}
            @php
                $totalAttendanceColumns = $daysInMonth - collect(range(1,$daysInMonth))->map(fn($d)=>\Carbon\Carbon::create($year,$month,$d)->format('D'))->filter(fn($d)=>in_array($d,['Sat','Sun']))->count();
            @endphp
            
            <td colspan="{{ $totalAttendanceColumns + 2 }}" align="center" style="border: 1px solid black; padding: 3px; font-size:9px; font-family:sans-serif;">
                 {{ $remarks ?: 'Inactive Student' }}
            </td>
            <td colspan="3" style="border: 1px solid black; padding: 3px;  font-size:9px; font-family:sans-serif; text-align:center;">
                {{ $remarks ?: 'Inactive Student' }}
            </td>
        @endif
    </tr>
@endforeach

{{-- === TOTAL FEMALE PER DAY === --}}
<tr style="font-weight:bold; ">
    <td colspan="2" align="right" style="border: 1px solid black; padding: 3px; font-weight:bold; background:#f2f2f2;">
        &lt;=== FEMALE | TOTAL Per Day ===&gt;
    </td>
    @foreach($femaleDailyTotals as $d => $total)
        <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">{{ $total == 0 ? '0' : $total }}</td>
    @endforeach
    <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">{{ array_sum($femaleDailyAbsents) }}</td>
    <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">{{ array_sum($femaleDailyTotals) }}</td>
    <td colspan="3" style="border: 1px solid black; padding: 3px;"></td>
</tr>

{{-- === COMBINED TOTAL PER DAY === --}}
<tr style="font-weight:bold;">
    <td colspan="2" align="right" style="border: 1px solid black; padding: 3px; font-weight:bold; background:#f2f2f2;">
        COMBINED TOTAL PER DAY
    </td>
    @foreach($maleDailyTotals as $d => $total)
        <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">
            {{ $total + $femaleDailyTotals[$d] }}
        </td>
    @endforeach
    <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">
        {{ array_sum($maleDailyAbsents) + array_sum($femaleDailyAbsents) }}
    </td>
    <td align="center" style="border: 1px solid black; padding: 3px; background:#f2f2f2;">
        {{ array_sum($maleDailyTotals) + array_sum($femaleDailyTotals) }}
    </td>
    <td colspan="3" style="border: 1px solid black; padding: 3px;"></td>
</tr>
</tbody>
</table>

<!-- GUIDELINES AND LEGENDS SECTION -->
<div style="width: 100%; margin-top: 10px; font-family: sans-serif; font-size: 6px;">
    
    <!-- Three Column Layout -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- LEFT COLUMN: GUIDELINES -->
            <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                <div style="font-weight: bold; margin-bottom: 3px;">GUIDELINES:</div>
                
                <div style="margin-bottom: 2px;">
                    1. The attendance shall be accomplished daily. Refer to the codes for checking learners' attendance.
                </div>
                
                <div style="margin-bottom: 2px;">
                    2. Dates shall be written in the columns after Learner's Name.
                </div>
                
                <div style="margin-bottom: 2px; font-weight: bold;">
                    3. To compute the following:
                </div>
                
                 <!-- Percentage of Enrolment Formula -->
                    <div style="margin-bottom: 5px; font-size: 6px;">
                         &nbsp;&nbsp; a. Percentage of Enrolment =
                        <div style="text-align: center; margin-top: 2px; position: relative; width: 190px; margin-left: auto; margin-right: auto;">
                            <div style="border-bottom: 1px solid black; padding: 0 4px;">
                                Registered Learners as of end of the month
                            </div>
                            <div style="position: absolute; right: -2px; top: 0; font-size: 6px;">
                                × 100
                            </div>
                            <div style="text-align: center; margin-top: 2px;">
                                Enrolment as of 1st Friday of the school year
                            </div>
                        </div>


                
                <!-- Average Daily Attendance Formula -->
                <div style="margin-bottom: 3px;">
                    &nbsp;&nbsp;b. Average Daily Attendance = 
                   <div style="text-align: center; width: 180px; margin: 0 auto; border-bottom: 1px solid black;">
                        Total Daily Attendance
                    </div>

                    <div style=" text-align: center; margin: 1px 0;">
                        Number of School Days in reporting month
                    </div>
                </div>
                
                <!-- Percentage of Attendance Formula -->
                <div style="margin-bottom: 3px;">
                    &nbsp;&nbsp;c. Percentage of Attendance for the month = 
                    <div style="text-align: center; margin-top: 2px; position: relative; width: 180px; margin-left: auto; margin-right: auto;">
                            <div style="border-bottom: 1px solid black; padding: 0 4px;">
                               Average Daily Attendance
                            </div>
                            <div style="position: absolute; right: -2px; top: 0; font-size: 6px;">
                                × 100
                            </div>
                            <div style="text-align: center; margin-top: 2px;">
                                  Registered Learners as of end of the month
                            </div>
                </div>
                <br>
                
                <div style="margin-bottom: 2px;">
                    4. Every end of the month, the class adviser will submit this form to the office of the principal for recording of summary table into the School Form 4. The summary table will be used for submission to the Division Office.
                </div>
                
                <div style="margin-bottom: 2px;">
                    5. The attendance of learners is for the current school year only.
                </div>
                
                <div style="margin-bottom: 2px;">
                    6. * Beginning of School Year cut-off report is every 1st Friday of the school year
                </div>
            </td>
            
            <!-- CENTER COLUMN: CODES AND REASONS -->
            <td style="width: 20%; vertical-align: top; padding: 0 5px; border: 1px solid black;">
                <div style="font-weight: bold; text-align: center; margin-bottom: 3px;">
                    1. CODES FOR CHECKING ATTENDANCE
                </div>
                
                <div style="margin-bottom: 3px;">
                    (blank) = Present; (X) = Absent; Tardy (half<br>
                    shaded Upper for Late Comer, Lower for Cutting)
                </div>
                
                <div style="font-weight: bold; text-align: center; margin: 5px 0 3px 0;">
                    2. REASONS/CAUSES FOR NLS
                </div>
                
                <!-- Domestic-Related Factors -->
                <div style="font-weight: bold; margin-bottom: 1px;">
                    a. Domestic-Related Factors
                </div>
                <div style="margin-left: 5px; margin-bottom: 2px;">
                    a.1. Had to take care of siblings<br>
                    a.2. Early marriage/pregnancy<br>
                    a.3. Parents' attitude toward schooling<br>
                    a.4. Family problems
                </div>
                
                <!-- Individual-Related Factors -->
                <div style="font-weight: bold; margin-bottom: 1px;">
                    b. Individual-Related Factors
                </div>
                <div style="margin-left: 5px; margin-bottom: 2px;">
                    b.1. Illness<br>
                    b.2. Death<br>
                    b.3. Drug abuse<br>
                    b.4. Poor academic performance<br>
                    b.5. Lack of interest/Distractions<br>
                    b.6. Hunger/Malnutrition
                </div>
                
                <!-- School-Related Factors -->
                <div style="font-weight: bold; margin-bottom: 1px;">
                    c. School-Related Factors
                </div>
                <div style="margin-left: 5px; margin-bottom: 2px;">
                    c.1. Teacher factor<br>
                    c.2. Physical condition of classroom<br>
                    c.3. Peer influence
                </div>
                
                <!-- Geographical/Environmental -->
                <div style="font-weight: bold; margin-bottom: 1px;">
                    d. Geographical/Environmental
                </div>
                <div style="margin-left: 5px; margin-bottom: 2px;">
                    d.1. Distance between home and school<br>
                    d.2. Armed conflict (incl. Tribal wars & clan feuds)<br>
                    d.3. Calamities/Disasters
                </div>
                
                <!-- Financial-Related -->
                <div style="font-weight: bold; margin-bottom: 1px;">
                    e. Financial-Related
                </div>
                <div style="margin-left: 5px; margin-bottom: 2px;">
                    e.1. Child labor, work
                </div>
                
                <!-- Others -->
                <div style="font-weight: bold; margin-bottom: 1px;">
                    f. Others (Specify)
                </div>
            </td>
            
          <!-- RIGHT COLUMN: SUMMARY TABLE -->
<td style="width: 25%; vertical-align: top; padding-left: 5px;">
    <!-- Month and Days -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 3px; font-size: 6px;">
        <tr>
            <td style="border: 1px solid black; text-align: center; font-weight: bold; padding: 2px;">Month</td>
            <td style="border: 1px solid black; text-align: center; font-weight: bold; padding: 2px;">No. of Days of<br>Classes</td>
            <td colspan="3" style="border: 1px solid black; text-align: center; font-weight: bold; padding: 2px;">Summary</td>
        </tr>
        <tr>
            <td style="border: 1px solid black; text-align: center; padding: 2px; font-weight: bold;">
                {{ \Carbon\Carbon::create()->month($month)->format('F') }}
            </td>
            <td style="border: 1px solid black; text-align: center; padding: 2px; font-weight: bold;">
                {{ $weekdayCount }}
            </td>
            <td style="border: 1px solid black; text-align: center; padding: 2px; font-weight: bold; width: 15%;">M</td>
            <td style="border: 1px solid black; text-align: center; padding: 2px; font-weight: bold; width: 15%;">F</td>
            <td style="border: 1px solid black; text-align: center; padding: 2px; font-weight: bold; width: 15%;">TOTAL</td>
        </tr>
    </table>
    
    <!-- Summary Data -->
    <table style="width: 100%; border-collapse: collapse; font-size: 5px;">
        @php
            // Compute the summary data (you'll need to pass these variables from your controller)
            $summaryData = [
                "* Enrolment as of (1st Friday of the SY)" => [$maleInitial ?? 0, $femaleInitial ?? 0, $totalInitial ?? 0],
                "Late enrolment during the month\n(beyond cut-off)" => [$lateMale ?? 0, $lateFemale ?? 0, $lateTotal ?? 0],
                "Registered Learners as of end of month" => [$registeredMale ?? 0, $registeredFemale ?? 0, $registeredTotal ?? 0],
                "Percentage of Enrolment as of end of month" => [$percEnrolMale ?? '0%', $percEnrolFemale ?? '0%', $percEnrolTotal ?? '0%'],
                "Average Daily Attendance (%)" => [$avgDailyMale ?? 0, $avgDailyFemale ?? 0, $avgDailyTotal ?? 0],
                "Percentage of Attendance for the month" => [$percMale ?? '0%', $percFemale ?? '0%', $percTotal ?? '0%'],
                "Number of students absent for 5 consecutive days" => [$absent5Male ?? 0, $absent5Female ?? 0, $absent5Total ?? 0],
                "NLS" => [$nlsMale ?? 0, $nlsFemale ?? 0, $nlsTotal ?? 0],
                "Transferred Out" => [$transOutMale ?? 0, $transOutFemale ?? 0, $transOutTotal ?? 0],
                "Transferred In" => [$transInMale ?? 0, $transInFemale ?? 0, $transInTotal ?? 0],
                "Shifted Out" => [$shiftOutMale ?? 0, $shiftOutFemale ?? 0, $shiftOutTotal ?? 0],
                "Shifted In" => [$shiftInMale ?? 0, $shiftInFemale ?? 0, $shiftInTotal ?? 0],
            ];
        @endphp
        
        @foreach($summaryData as $label => $values)
        <tr>
            <td style="border: 1px solid black; padding: 2px; text-align: left; vertical-align: top; width: 55%;">
                {!! str_replace("\n", "<br>", $label) !!}
            </td>
            <td style="border: 1px solid black; padding: 2px; text-align: center; vertical-align: top; width: 15%;">{{ $values[0] }}</td>
            <td style="border: 1px solid black; padding: 2px; text-align: center; vertical-align: top; width: 15%;">{{ $values[1] }}</td>
            <td style="border: 1px solid black; padding: 2px; text-align: center; vertical-align: top; width: 15%;">{{ $values[2] }}</td>
        </tr>
        @endforeach
    </table>
    
    <!-- Certification and Signatures -->
    <div style="margin-top: 5px; font-size: 6px;">
        <div style="font-weight: bold; margin-bottom: 5px;">
            I certify that this is a true and correct report.
        </div>
        
        <!-- Adviser Signature -->
        <div style="text-align: center; margin-bottom: 8px;">
            <div style="border-bottom: 1px solid black; padding-bottom: 1px; margin-bottom: 1px;">
                {{ $adviserName ?? 'Adviser Name' }}
            </div>
            <div style="font-size: 5px;">
                (Signature of Adviser over Printed Name)
            </div>
        </div>
        
        <!-- Attested by -->
        <div style="margin-bottom: 3px;">
            Attested by:
        </div>
        
        <div style="text-align: center;">
            <div style="border-bottom: 1px solid black; padding-bottom: 1px; margin-bottom: 1px;">
                MARY JANE M. ALVAREZ
            </div>
            <div style="font-size: 5px;">
                (Signature of School Head over Printed Name)
            </div>
        </div>
    </div>
</td>
    </table>
</div>