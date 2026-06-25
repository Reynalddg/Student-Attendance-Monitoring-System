<!-- HEADER TABLE (Optimized & Consistent) -->
<table style="width: 100%; padding-left:100px; border-collapse: collapse; margin-bottom: 30px; table-layout: fixed ;">
    <!-- TITLE -->
    <tr>
        <td colspan="15" style="
            font-size: 16px; 
            font-weight: bold; 
            text-align: center; 
            padding: 8px; 
            white-space: normal; 
            word-wrap: break-word;
            border: none;
        ">
           School Form 1 School Register for Senior High School (SF1-SHS)
        </td>
    </tr>

    <tr>

    </tr>

    <!-- FIRST ROW: School ID | School Year | Month -->
    <tr>
        <!-- School ID -->
        <td colspan="1" style="border: none;"></td>

          <td  colspan="" style="padding-left: 12px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;  ">
            School Name 
        </td>
        <td colspan="3" style="
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

       
        <td  colspan="1" style="padding: 6px 4px; font-size:8px; text-align:left; width: 120px;   text-align:right; font-family:sans-serif  ">
            School ID
        </td>
        <td colspan="1" style="
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


            <td  colspan="1" style="padding: 6px 4px; font-size:8px; text-align:left; width: 120px;   text-align:right; font-family:sans-serif ">
            District
            </td>
            <td colspan="1" style="
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


               <td  colspan="" style="padding: 6px 4px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;  ">
                    Division
                </td>
                <td colspan="" style="
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
            {{ $semesterName}}
        </td>


        <!-- School Year -->

<td colspan="1" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
    School Year:
</td>
<td colspan="1" style="
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
    {{ $acadName }} 
</td>

        <!-- School Year -->
        <td colspan="1" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Grade Level
        </td>
        <td colspan="1" style="
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



         <td colspan="1" style="padding: 6px 4px; font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Track and Strand
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


              <td colspan="1" style="padding: 6px 4px;  font-size:8px; text-align:right; width: 120px; font-family:sans-serif;">
            Course for TVL only
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
         
        </td>

         <td colspan="5"></td>
    </tr>

</table>

<!-- Main Table -->
<table style="width: 100%; border-collapse: collapse; font-size: 8px; text-align: center; font-family: sans-serif;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle; width:150px;">LRN</th>
            <th rowspan="2" 
    style="border: 1px solid black; 
           padding: 6px; 
           width: 250px; 
           text-align:center; 
           font-family:sans-serif; 
           font-size:6px; 
           font-weight:bold;  
           vertical-align: middle;">
    NAME <br><small>(Last Name, First Name, Name Extension, Middle Name)</small>
</th>

            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6;  font-weight:bold;  vertical-align: middle; ">Sex (M/F)</th>
            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Birth Date<br>(mm/dd/yyyy)</th>
            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle; width: 50px; word-wrap: break-word;">Age<br>as of 1st Friday of June</th>
            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Religious<br>Affiliation</th>
            <th colspan="4" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">COMPLETE ADDRESS</th>
            <th colspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">PARENTS</th>
            <th colspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">GUARDIAN<br><small>(if learner is not living with Parent)</small></th>
            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Contact Number<br>of Parent or Guardian</th>
            <th rowspan="2" style="border: 1px solid black; padding: 6px; text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Learning<br>Modality</th>
            <th rowspan="2" 
                style="border: 1px solid black; padding: 6px; text-align:center; 
                    font-family:sans-serif; font-size:6; font-weight:bold;  
                    vertical-align: middle;">
                Remarks <br>
                <small>(Please refer to the legend on last page)</small>
            </th>
                    </tr>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">House #/Street/Sitio/Purok</th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Barangay</th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Municipality/City</th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Province</th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;  width: 120px; word-wrap: break-word;">Father's Name <br> (Last Name, First Name, <br> Middle Name)     </th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle; width: 120px; word-wrap: break-word;">Mother's Maiden <br> Name (Last Name, First Name, <br> Middle Name)</th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle; width: 120px; word-wrap: break-word;">Guardian's Name <br>(Last Name, First Name, Name <br> Extension, Middle Name) </th>
            <th style="border: 1px solid black; padding: 6px;  text-align:center; font-family:sans-serif; font-size:6; font-weight:bold;  vertical-align: middle;">Relationship</th>

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

    {{-- Loop per Gender --}}
    @foreach(['M' => 'MALE', 'F' => 'FEMALE'] as $key => $label)
        @php $count = 0; @endphp

        {{-- Display students by gender --}}
        @foreach($grouped->get($key, collect()) as $enrollment)
            @php
                $s = $enrollment->student;
                $age = \Carbon\Carbon::parse($s->birthdate)->diffInYears($cutoff);
                $count++;
                $grandTotal++;
            @endphp
        <tr>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; height:15px; vertical-align: middle;  width:150px;" >{{ "'".$s->lrn }}</td>
            <td style="border: 1px solid black; text-align: left; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ strtoupper("{$s->last_name}, {$s->first_name} {$s->suffix}, {$s->middle_name}") }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:center; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ strtoupper(substr($s->gender, 0, 1)) }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:right; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ \Carbon\Carbon::parse($s->birthdate)->format('m/d/Y') }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:center; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ $age }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ $s->religion }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;"></td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ strtoupper($s->barangay ?? '') }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ strtoupper($s->municipality ?? '') }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ strtoupper($s->province ?? '') }}</td>
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


            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle;">{{ $s->guardian->contact ?? '' }}</td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle; text-align:center;">Face to Face</td>
            <td style="border: 1px solid black; padding: 5px; text-align:left; font-family:sans-serif; font-size:7; vertical-align: middle; text-align:center;"> {{$s->remarks }} </td>
        </tr>
           @endforeach

    <tr>
    <td style="border: 1px solid black; padding:5px; font-family:sans-serif; font-size:7px; width: 30px; text-align:right;">
        {{ $count }}
    </td>
    <td colspan="16" style="border: 1px solid black; padding:5px; font-family:sans-serif; font-size:7px; text-align:left;">
        &lt;===  TOTAL {{ $label }}
    </td>
</tr>
    @endforeach

    {{-- Grand Total --}}
   <tr>
    <td style="border: 1px solid black; padding:5px; font-family:sans-serif; font-size:7px; width: 30px; text-align:right;">
        {{ $grandTotal }}
    </td>
    <td colspan="16" style="border: 1px solid black; padding:5px; font-family:sans-serif; font-size:7px; text-align:left;">
        &lt;===  COMBINED TOTAL
    </td>
</tr>
    </tbody>
</table>


<table>
    
</table>