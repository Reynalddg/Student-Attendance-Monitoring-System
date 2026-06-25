@extends('teacherDashboard')

@section('content')

<div class="container-lg">

    <section class="mt-2">
        <h3 class="fw-bold">Daily Attendance Report</h3>
        <hr id="hr">
    </section>

<section class="mt-5 px-4">
    <div class="row">
        <div class="col-12 d-flex flex-wrap justify-content-end gap-2">
            <button type="button"  class="btn btn-primary w-md-auto"  data-bs-toggle="modal"  data-bs-target="#addModal">
                <i class="fa-solid fa-user-check me-1"></i> Add Attendance
                </button>

            <button type="button" class="btn btn-success w-md-auto" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                <i class="fa-solid fa-file-export me-1"> </i> Export Monthly Attendance
            </button>

            <form method="POST" action="{{ route('sms.sendFromAdviser') }}" class=" w-md-auto">
                @csrf
                <button type="submit" class="btn btn-warning w-100 w-md-auto">
                    <i class="fa-solid fa-paper-plane me-1"></i> Send SMS to Guardians
                </button>
            </form>
        </div>
    </div>
</section>


    <section class="mt-4 px-2 px-md-4"  style="max-height: 500px; overflow-y: auto;">
        
          <table class="table table-hover mt-3 sticky-header-table">
    <thead class="table-dark">
        <tr>
            <th>Image</th>
            <th>Student Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendanceLogs as $log)
        <tr>
            <td class="text-truncate" style="max-width:120px;">
                @if($log->enrollment->student->image)
                    <img src="{{ asset('storage/' . $log->enrollment->student->image) }}" 
                         alt="Student Image" width="60" height="60" 
                         style="object-fit: cover; border-radius:15%; margin:2px;">
                @else
                    No Image
                @endif
            </td>
            <td>
                {{ $log->enrollment->student->first_name ?? 'N/A' }}
                {{ $log->enrollment->student->middle_name ?? '' }}
                {{ $log->enrollment->student->last_name ?? '' }}
            </td>
            <td>{{ \Carbon\Carbon::parse($log->date_time)->format('M d, Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($log->date_time)->format('h:i A') }}</td>            
            <td>{{ $log->status }}</td>
            <td>
                <a href="#" class="btn btn-sm btn-warning mt-1" 
                   data-bs-toggle="modal" data-bs-target="#updateModal-{{ $log->attendance_id }}">
                    <i class="fa-solid fa-pen-to-square"></i> Update
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted">No Daily Attendance Found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

        
    </section>

     <div class="mt-4 px-4 flex-wrap fw-bold">
        {{ $attendanceLogs->links('pagination::bootstrap-5') }}
    </div>
</div>

{{--Export Modal--}}
<div class="modal fade" id="attendanceModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
         <form method="GET" id="sf2Form">          
    <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="attendanceModalLabel">Select Month to Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Academic Year --}}
                <div class="mb-3">
                    <label for="academic_year" class="form-label fw-bold">Academic Year <span class="text-danger fw-bold">*</span></label>
                    <select name="academic_year" id="academic_year" class="form-control border-dark" required>
                        <option value="" disabled selected>Select Academic Year</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->academic_year_id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Semester (empty by default) --}}
                <div class="mb-3">
                    <label for="semester" class="form-label fw-bold">Semester <span class="text-danger fw-bold">*</span></label>
                    <select name="semester" id="semester" class="form-control border-dark" required>
                        <option value="" disabled selected>Select Semester</option>
                    </select>
                </div>

                {{-- Month --}}
                <div class="mb-3">
                    <label for="month" class="form-label fw-bold">Month <span class="text-danger fw-bold">*</span></label>
                    <select name="month" id="month" class="form-control border-dark" required>
                        <option value="" selected disabled>Select Month</option>
                    </select>
                </div>

       
                {{-- Holidays --}}
                <div class="mb-3">
                    <label for="holidays" class="form-label fw-bold">Select Holidays (Optional)</label>
                    <input type="date" name="holidays[]" id="holidays" class="form-control border-dark mb-2">
                    <div id="holidayContainer"></div>
                    <button type="button" id="addHolidayBtn" class="btn btn-sm btn-secondary mt-2">
                        + Add Another Holiday
                    </button>
                </div>

            </div>

                      <div class="modal-footer d-flex justify-content-between">
          <div>
            <!-- Excel -->
            <button type="submit" class="btn btn-success" 
                    formaction="{{ route('teacher.export.attendance') }}">
              <i class="bi bi-file-earmark-excel"></i> Export to Excel
            </button>
            <!-- PDF -->
            <button type="submit" class="btn btn-danger" 
                    formaction="{{ route('sf2.export.pdf') }}">
              <i class="bi bi-file-earmark-pdf"></i> Export to PDF
            </button>
          </div>

          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
        </form>
    </div>
  </div>
</div>



@foreach ($attendanceLogs as $log)
    {{-- Update Modal --}}
    <div class="modal fade" id="updateModal-{{$log->attendance_id}}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/update/studentAtt/{{$log->attendance_id}}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Update Student Attendance</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name </label>
                           <select name="enrollment_id" class="form-select border-dark" disabled >
                                <option value="" disabled>Select Student</option >
                                @foreach ($enrollments as $enrollment)
                                    <option value="{{ $enrollment->enrollment_id }}"
                                        @if($enrollment->enrollment_id == $log->enrollment_id) selected @endif>
                                        {{ $enrollment->student->first_name }} {{ $enrollment->student->middle_name }} {{ $enrollment->student->last_name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Date</label>
                            <input type="date" class="form-control border-dark" name="date" value="{{ $log->date }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Time Out <span class="text-danger fw-bold" >*</span></label>
                            <input type="time" name="time_out" class="form-control border-dark" value="{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '' }}">
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
@endforeach

{{-- Add Modal --}}
<div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="/add/studentAtt" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Student Attendance</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Student <span class="text-danger fw-bold" >*</span></label>
                        <select name="enrollment_id" class="form-select border-dark" required>
                            <option value="" selected disabled>Select Student</option>
                            @foreach ($enrollments as $enrollment)
                                <option value="{{ $enrollment->enrollment_id }}">
                                    {{ $enrollment->student->first_name }} {{ $enrollment->student->middle_name }} {{ $enrollment->student->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                  <div class="mb-3">
                        <label class="form-label fw-bold">Date <span class="text-danger fw-bold">*</span></label>
                       <input type="date" name="date" class="form-control border-dark" 
    value="{{ \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') }}"
    max="{{ \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') }}"
    readonly>

                    </div>

                   

                   <div class="mb-3">
                        <label class="form-label fw-bold">Status <span class="text-danger fw-bold">*</span></label>
                        <select name="status" id="statusSelect" class="form-select border-dark" required>
                            <option value="" selected disabled>Select Status</option>
                            <option value="Excused">Excused</option>
                        </select>
                    </div>

                
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Attendance</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const academicSelect = document.getElementById("academic_year");
    const semesterSelect = document.getElementById("semester");
    const monthSelect = document.getElementById("month");
    const yearSelect = document.getElementById("year");
    const display = document.getElementById('academicYearDisplay');

    // Semesters JSON from Laravel
    const allSemesters = @json($semesters);

    // When Academic Year changes, populate semesters
    academicSelect.addEventListener("change", function() {
        const selectedAcad = this.value;

        // Clear semester dropdown
        semesterSelect.innerHTML = '<option value="" disabled selected>Select Semester</option>';

        allSemesters.forEach(sem => {
            if (sem.academic_year_id == selectedAcad) {
                const option = document.createElement('option');
                option.value = sem.semester_id;
                option.textContent = sem.name;
                option.setAttribute('data-start', new Date(sem.start_date).getMonth() + 1);
                option.setAttribute('data-end', new Date(sem.end_date).getMonth() + 1);
                semesterSelect.appendChild(option);
            }
        });

        // Reset month dropdown
        monthSelect.innerHTML = '<option value="" disabled selected>Select Month</option>';
        display.textContent = `Academic Year: - | Semester: -`;
    });

    // When Semester changes, populate months
    semesterSelect.addEventListener('change', function() {
        const selectedOption = this.selectedOptions[0];
        if (!selectedOption) return;

        const startMonth = parseInt(selectedOption.getAttribute('data-start'));
        const endMonth   = parseInt(selectedOption.getAttribute('data-end'));

        monthSelect.innerHTML = '<option value="" disabled selected>Select Month</option>';

        const months = [
            '', 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        let m = startMonth;
        while (true) {
            const option = document.createElement('option');
            option.value = m;
            option.textContent = months[m];
            monthSelect.appendChild(option);

            if (m === endMonth) break;
            m++;
            if (m > 12) m = 1;
        }

        updateAcademicYearDisplay();
    });

    // Dynamic holidays
    const addBtn = document.getElementById('addHolidayBtn');
   const holidayContainer = document.getElementById('holidayContainer'); 
   addBtn.addEventListener('click', function() { 
    const newInput = document.createElement('input'); 
    newInput.type = 'date'; newInput.name = 'holidays[]'; 
    newInput.classList.add('form-control', 'border-dark', 'mt-2'); 
    holidayContainer.appendChild(newInput); 
});

   

    monthSelect.addEventListener('change', updateAcademicYearDisplay);
    yearSelect.addEventListener('change', updateAcademicYearDisplay);
});

document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    const remarksSelect = document.getElementById('remarksSelect');

    const remarksOptions = {
        'Present': ['Forgot QR Code', 'Scanner Error'],
        'Excused': ['Sick', 'Family Emergency', 'Other Excused']
    };

    statusSelect.addEventListener('change', function() {
        const selectedStatus = this.value;
        
        // Clear existing remarks
        remarksSelect.innerHTML = '<option value="" selected disabled>Select Remarks</option>';

        // Populate remarks based on selected status
        if (remarksOptions[selectedStatus]) {
            remarksOptions[selectedStatus].forEach(function(option) {
                const opt = document.createElement('option');
                opt.value = option;
                opt.textContent = option;
                remarksSelect.appendChild(opt);
            });
        }
    });
});

</script>

{{-- Responsive Styling --}}
<style>
@media (max-width: 768px) {
    /* Make table scrollable */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Center header */
    h3.fw-bold {
        font-size: 1.2rem;
        text-align: center;
    }

    /* Top buttons full width and stacked */
    .row .btn {
        width: 100%;
    }

    /* Reduce table image size */
    table img {
        width: 45px !important;
        height: 45px !important;
    }

    /* Modal text smaller for narrow screens */
    .modal-body p, .modal-body label, .modal-body select, .modal-body input {
        font-size: 0.9rem;
    }
}
</style>

@endsection
