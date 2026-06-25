<!-- HEADER TABLE (Optimized & Consistent) -->
<table style="width: 100%; padding-left:100px; border-collapse: collapse; margin-bottom: 30px; table-layout: fixed ;">
    <!-- TITLE -->
    <tr>
        <td colspan="30" style="
            font-size: 16px; 
            font-weight: bold; 
            text-align: center; 
            padding: 8px; 
            white-space: normal; 
            word-wrap: break-word;
            border: none;
        ">
            School Form 2 Daily Attendance Report of Learners For Senior High School (SF2-SHS)
        </td>
    </tr>

    <tr>

    </tr>

    <!-- FIRST ROW: School ID | School Year | Month -->
    <tr>
        <!-- School ID -->
        <td colspan="1" style="border: none;"></td>

          <td  colspan="1" style="padding-left: 12px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;  ">
            School Name 
        </td>
        <td colspan="5" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            font-family:sans-serif;
            word-wrap: break-word; 
            white-space: normal;
        ">
            {{ $schoolName }}
        </td>

         <td colspan="1" style="border: none;"></td>



        <td  colspan="2" style="padding: 6px 4px; font-size:8px; text-align:left; width: 120px;   text-align:right; font-family:sans-serif  ">
            School ID
        </td>
        <td colspan="2" style="
            border: 1px solid black; 
            padding: 6px 16px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family:sans-serif;
        ">
            {{ $schoolId }}
        </td>

          <td colspan="1" style="border: none;"></td>

            <td  colspan="2" style="padding: 6px 4px; font-size:8px; text-align:left; width: 120px;   text-align:right; font-family:sans-serif ">
            District
            </td>
            <td colspan="4" style="
                border: 1px solid black; 
                padding: 6px 12px; 
                min-width: 120px; 
                max-width: 150px;
                font-size:7px; 
                text-align:center; 
                word-wrap: break-word; 
                white-space: normal;
            ">
                Talavera North
            </td>

           <td colspan="1" style="border: none;"></td>

               <td  colspan="2" style="padding: 6px 4px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;  ">
                    Division
                </td>
                <td colspan="3" style="
                    border: 1px solid black; 
                    padding: 6px 12px; 
                    min-width: 120px; 
                    max-width: 150px;
                    font-size:7px; 
                    text-align:center; 
                    word-wrap: break-word; 
                    white-space: normal;
                    font-family:sans-serif;
                ">
                    Nueva Ecija
                </td>

            <td colspan="1" style="border: none;"></td>

            
               <td    style="padding: 6px 4px; font-size:8px; text-align:left; width: 120px;   text-align:right; font-family:sans-serif;  ">
                    Region
                </td>
                <td colspan="2" style="
                    border: 1px solid black; 
                    padding: 7px 12px; 
                    min-width: 120px; 
                    max-width: 150px;
                    font-size:7px; 
                    text-align:center; 
                    word-wrap: break-word; 
                    white-space: normal;
                    font-family:sans-serif;
                ">
                    Region III
                </td>

            <td colspan="1" style="border: none;"></td>

    </tr>
    <tr>

    </tr>
    
    <tr>
                <td colspan="1" style="border: none;"></td>




        <td colspan="1" style="padding: 6px 4px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Semester
        </td>
        <td colspan="3" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family:sans-serif;

        ">
            {{ $semester}}
        </td>

         <td colspan="1" style="border: none;"></td>

        <!-- School Year -->

<td colspan="3" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
    School Year:
</td>
<td colspan="3" style="
    border: 1px solid black; 
    padding: 6px 12px; 
    min-width: 120px; 
    max-width: 150px;
    font-size:7px; 
    text-align:center; 
    word-wrap: break-word; 
    white-space: normal;
    font-family:sans-serif;
">
    {{ $academicYear }}
</td>
           <td colspan="1" style="border: none;"></td>

        <!-- School Year -->
        <td colspan="3" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Grade Level
        </td>
        <td colspan="2" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family: sans-serif;
        ">
            Grade {{$gradeLevel}}
        </td>

            <td colspan="1" style="border: none;"></td>


         <td colspan="3" style="padding: 6px 4px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Track and Strand
        </td>
        <td colspan="8" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family:sans-serif;
        ">
          {{$trackName}} - {{$strandName}}
        </td>


    </tr>

   <tr>

   </tr>

    <tr >

    <td colspan="2" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Section 
    </td>
        <td colspan="2" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family:sans-serif;
        ">
          {{$section}}
        </td>

        <td colspan="1" style="border: none;"></td>


              <td colspan="4" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Course for TVL only
        </td>
        <td colspan="5" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family:sans-serif;
        ">
         
        </td>

        <td colspan="1" style="border: none;"></td>

               <td colspan="2" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Month of 
        </td>

        <td colspan="4" style="
            border: 1px solid black; 
            padding: 6px 12px; 
            min-width: 120px; 
            max-width: 150px;
            font-size:7px; 
            text-align:center; 
            word-wrap: break-word; 
            white-space: normal;
            font-family:sans-serif;
        ">
            {{$monthName}}  {{$year}}
        </td>
         <td colspan="5"></td>
    </tr>

</table>



<!-- ATTENDANCE TABLE -->
<table style="width: 100%; border-collapse: collapse; ">
   <thead>
    <!-- Row 1: No. | Name | DATE colspan | TOTAL | REMARKS -->
    <tr>
        <!-- No. Column -->
        <th rowspan="3" style="text-align: center; vertical-align: middle; padding: 6px; width:26px; border: 1px solid black; font-family:sans-serif; font-size:6px; font-weight:bold;"> 
            No. 
        </th>

        <!-- Learner's Name -->
        <th rowspan="3" style="text-align: center; vertical-align: middle; padding: 6px; width: 170px; border: 1px solid black; font-family:sans-serif; font-size:6px; font-weight:bold;">
            LEARNER'S NAME <br> (Last Name, First Name, Middle Name)
        </th>

        <!-- DATE header (covers all school days) -->
        <th colspan="{{ $daysInMonth - collect(range(1,$daysInMonth))->map(fn($d)=>\Carbon\Carbon::create($year,$month,$d)->format('D'))->filter(fn($d)=>in_array($d,['Sat','Sun']))->count() }}" 
            style="text-align: center; border: 1px solid black; padding: 6px; font-family:sans-serif; font-size:6px;">
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
        <th colspan="2" rowspan="2" style="text-align: center; border: 1px solid black; padding: 6px; font-size: 8px; font-weight:bold; font-family:sans-serif;">
            TOTAL FOR THE MONTH <br>
                    {{$schoolDays}}

        </th>

        <!-- REMARKS -->
        <th colspan="3" rowspan="3" style="text-align: center; vertical-align: middle; padding: 2px; border: 1px solid black; font-size: 5px; font-weight:bold; font-family:sans-serif;">
            REMARKS (If NLS, state reason, please <br>
             refer to legend number 2. If TRANSFERRED <br> 
            IN/OUT, write the name of School.)
        </th>
    </tr>

    <!-- Row 2: Day numbers -->
    <tr>
        @for ($d = 1; $d <= $daysInMonth; $d++)
            @php $dayName = \Carbon\Carbon::create($year, $month, $d)->format('D'); @endphp
            @if(!in_array($dayName, ['Sat','Sun']))
                <th style="text-align: center; vertical-align: middle; padding: 6px; min-width: 35px; border: 1px solid black; font-family:sans-serif; font-size:9px;">
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
                    <th style="text-align: center;  padding: 7  px; width:30px; border: 1px solid black; font-family:sans-serif; font-size:6px;">
                        {{ $dayShort }}
                    </th>
                @endif
            @endfor

            <!-- Absent -->
            <th style="text-align: center;  padding: 6px; min-width: 60px; border: 1px solid black; font-family:sans-serif; font-size:5px;">
                ABSENT
            </th>

             <!-- Present -->
            <th style="text-align: center; padding: 6px; min-width: 60px; border: 1px solid black; font-family:sans-serif; font-size:5px;">
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
<tr>
    <td align="center">{{ $maleNo++ }}</td>
    <td>
        {{ strtoupper($student->student->last_name ?? '') }},
        {{ strtoupper($student->student->first_name ?? '') }}
        {{ strtoupper($student->student->middle_name ?? '') }}
    </td>

    @php 
        $presentCount = 0; 
        $absentCount = 0; 
        $isActive = $student->status === 'Active';
        $remarks = $student->remarks ?? '';
    @endphp

    @if($isActive)

        {{-- ACTIVE STUDENT: Attendance Cells --}}
        @for ($d = 1; $d <= $daysInMonth; $d++)
            @php
                $date = \Carbon\Carbon::create($year, $month, $d);
                if(in_array($date->format('D'), ['Sat','Sun'])) continue;

                $formattedDate = $date->format('Y-m-d');
                $dateStr = $date->toDateString();

                $logEntry = $attendances[$student->enrollment_id][$dateStr] ?? null;
                $log = is_array($logEntry) ? reset($logEntry) : $logEntry;

                if(in_array($formattedDate, $holidays)) {
                    $cellValue = '';
                } elseif($date->gt($today)) {
                    $cellValue = '-';
                } elseif($log) {
                    $cellValue = '';
                    $presentCount++;
                    $maleDailyTotals[$d]++;
                } else {
                    $cellValue = 'X';
                    $absentCount++;
                    $maleDailyAbsents[$d]++;
                }
            @endphp

            <td align="center">{{ $cellValue }}</td>
        @endfor

        {{-- TOTAL ABSENT / PRESENT + REMARKS --}}
        <td align="center">{{ $absentCount }}</td>
        <td align="center">{{ $presentCount }}</td>
        <td colspan="3" style="border:1px solid black;"></td>

    @else

        {{-- INACTIVE STUDENT --}}
        @php
            $totalAttendanceColumns = $daysInMonth
                - collect(range(1,$daysInMonth))
                    ->map(fn($d)=>\Carbon\Carbon::create($year,$month,$d)->format('D'))
                    ->filter(fn($d)=>in_array($d,['Sat','Sun']))
                    ->count();
        @endphp

        <td colspan="{{ $totalAttendanceColumns + 2 }}"
            align="center"
            style="border:1px solid black; background:#f8f8f8;  font-size:7px; font-family:sans-serif;">
            {{ $remarks ?: 'Inactive Student' }}
        </td>

        <td colspan="3" style="border:1px solid black;  font-size:7px; font-family:sans-serif; text-align:center;">
            {{ $remarks ?: 'Inactive Student' }}
        </td>

    @endif
</tr>
@endforeach

{{-- === TOTAL MALE PER DAY === --}}
<tr style="font-weight:bold; font-size:7px;">
    <td colspan="2" align="right" 
        style="border: 1px solid black; font-weight:bold;">
        &lt;=== MALE | TOTAL Per Day ===&gt;
    </td>

    @foreach($maleDailyTotals as $d => $total)
        <td align="center" style="border: 1px solid black;">{{ $total == 0 ? '0' : $total }}</td>
    @endforeach
    <td align="center" style="border: 1px solid black;">{{ array_sum($maleDailyAbsents) }}</td>
    <td align="center" style="border: 1px solid black;">{{ array_sum($maleDailyTotals) }}</td>
    <td colspan="3" style="border: 1px solid black;"></td>
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
        <td align="center">{{ $femaleNo++ }}</td>
      <td>
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
               
        <td align="center" >{{ $cellValue }}</td>
            @endfor
        <td align="center">{{ $absentCount }}</td>
        <td align="center">{{ $presentCount }}</td>
        <td colspan="3" style="border:1px solid black;"></td>
     @else
            {{-- INACTIVE STUDENT - Merge all attendance cells and show remarks --}}
            @php
                $totalAttendanceColumns = $daysInMonth - collect(range(1,$daysInMonth))->map(fn($d)=>\Carbon\Carbon::create($year,$month,$d)->format('D'))->filter(fn($d)=>in_array($d,['Sat','Sun']))->count();
            @endphp
            
            <td colspan="{{ $totalAttendanceColumns + 2 }}" align="center" style="border: 1px solid black; padding: 3px; background-color: #f8f8f8;  font-size:7px; font-family:sans-serif;">
                 {{ $remarks ?: 'Inactive Student' }}
            </td>
            <td colspan="3" style="border: 1px solid black; padding: 3px;  font-size:7px; font-family:sans-serif; text-align:center;">
                {{ $remarks ?: 'Inactive Student' }}
            </td>
        @endif
    </tr>
@endforeach

{{-- === TOTAL FEMALE PER DAY === --}}
<tr style="font-weight:bold; font-size:7px;">
    <td colspan="2" align="right" style="border: 1px solid black; padding:3px; font-weight:bold;">
        &lt;=== FEMALE | TOTAL Per Day ===&gt;
    </td>
    @foreach($femaleDailyTotals as $d => $total)
        <td align="center" style="border: 1px solid black;">{{ $total == 0 ? '0' : $total }}</td>
    @endforeach
    <td align="center" style="border: 1px solid black;">{{ array_sum($femaleDailyAbsents) }}</td>
    <td align="center" style="border: 1px solid black;">{{ array_sum($femaleDailyTotals) }}</td>
    <td colspan="3" style="border: 1px solid black;"></td>
</tr>

{{-- === COMBINED TOTAL PER DAY === --}}
<tr style="font-weight:bold; font-size:7px; background:#f2f2f2;">
    <td colspan="2" align="right" style="border: 1px solid black; padding:3px; font-weight:bold;">
        COMBINED TOTAL PER DAY
    </td>
    @foreach($maleDailyTotals as $d => $total)
        <td align="center" style="border: 1px solid black;">
            {{ $total + $femaleDailyTotals[$d] }}
        </td>
    @endforeach
    <td align="center" style="border: 1px solid black;">
        {{ array_sum($maleDailyAbsents) + array_sum($femaleDailyAbsents) }}
    </td>
    <td align="center" style="border: 1px solid black;">
        {{ array_sum($maleDailyTotals) + array_sum($femaleDailyTotals) }}
    </td>
    <td colspan="3" style="border: 1px solid black;"></td>
</tr>
</tbody>

</table>
