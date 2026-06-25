@extends('teacherDashboard')

@section('content')
<div class="container-lg">

    <!-- Header -->
    <section class="mt-2 mb-5">
        <h3 class="fw-bold text-center text-md-start">List Of Students Information</h3>
        <hr id="hr">
    </section>
    
    <section class="mt-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 px-2 px-md-4">
       
        <form method="GET" action="{{ route('students.search') }}"
              class="d-flex align-items-center position-relative w-100 w-md-auto" id="searchContainer">
            <i class="fa-solid fa-search position-absolute" id="searchIcon" style="left:10px; color:gray;"></i>
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control form-control-sm me-2 py-2 ps-4 border-dark"placeholder="Search..." style="text-transform: capitalize;">
        </form>

        <button type="button" class="btn btn-success rounded-start rounded-end w-md-auto flex-shrink-0 mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#sf1Modal" style="white-space: nowrap;">
            <i class="fa-solid fa-file-export me-1"> </i> Export List of Students
        </button>

    </section>


    <section class="mt-4 px-2 px-md-4"  style="max-height: 500px; overflow-y: auto;">
        <div class="">
            <table class="table table-hover align-middle sticky-header-table">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Full Name</th>
                        <th>LRN</th>
                        <th>Sex</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentViewTableBody">
                    @forelse($students as $student)
                        <tr>
                            <td class="text-truncate " style="max-width:120px;">
                                @if($student->image)
                                    <img src="{{ asset('storage/' . $student->image) }}" 
                                         alt="Student Image" 
                                         width="60" height="60"
                                         style="object-fit: cover; border-radius:15%; margin:2px;">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            <td>{{ $student->last_name }}, {{ $student->first_name }} 
                                {{ $student->middle_name ? strtoupper(substr($student->middle_name, 0, 1)) . '.' : '' }}
                            </td>
                            <td>{{ $student->lrn }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>
                                <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal-{{ $student->student_id }}">
                                   <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class=" text-muted py-3">No Students Found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
    </section>

     <div class="mt-4 px-4 flex-wrap fw-bold">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
</div>

<!-- VIEW MODALS -->
@foreach($students as $student)
<div class="modal fade" id="viewModal-{{ $student->student_id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Student View</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-2">
        <div class="table-responsive" style="max-height:60vh; overflow-y:auto; overflow-x:auto;">
          <table class="table table-hover align-middle text-wrap" style="width: 100%; table-layout: auto;">
            <thead class="table-dark">
              <tr>
                <th style="min-width:120px">First Name</th>
                <th style="min-width:120px">Middle Name</th>
                <th style="min-width:120px">Last Name</th>
                <th style="min-width:100px">LRN</th>
                <th style="min-width:80px">Sex</th>
                <th style="min-width:180px">Address</th>
                <th style="min-width:120px">Birth Date</th>
                <th style="min-width:100px">Religion</th>
                <th style="min-width:300px">Parents & Guardian</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ $student->first_name }}</td>
                <td>{{ $student->middle_name }}</td>
                <td>{{ $student->last_name }}</td>
                <td>{{ $student->lrn }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->barangay }} {{ $student->municipality }} {{ $student->province }}</td>
                <td>{{ \Carbon\Carbon::parse($student->birthdate)->format('F d, Y') }}</td>
                <td>{{ $student->religion }}</td>
                <td>
                  @if($student->guardian)
                    Father: {{ $student->guardian->father_name ?? 'N/A' }} <br>
                    Mother: {{ $student->guardian->mother_name ?? 'N/A' }} <br>
                    Guardian: {{ $student->guardian->guardian_name ?? 'N/A' }}
                  @else
                    <span class="text-muted">No guardian info</span>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach


<!-- EXPORT MODAL -->
<div class="modal fade" id="sf1Modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <form method="GET" id="sf1Form">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Export List of Students</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="academic_year" class="form-label fw-bold">Academic Year <span class="text-danger fw-bold" >*</span></label>
            <select name="academic_year" id="academic_year" class="form-control border-dark" required>
              <option value="" disabled selected>Select Academic Year</option>
              @foreach ($academicYears as $year)
                <option value="{{ $year->academic_year_id }}">{{ $year->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Semester -->
          <div class="mb-3">
            <label for="semester" class="form-label fw-bold">Semester <span class="text-danger fw-bold" >*</span></label>
            <select name="semester" id="semester" class="form-control border-dark" required>
              <option value="" disabled selected>Select Semester</option>
              @foreach ($semesters as $sem)
                <option value="{{ $sem->semester_id }}" data-acad="{{ $sem->academic_year_id }}">
                  {{ $sem->name }} ({{ $sem->academicYear->name ?? '' }})
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between">
          <div>
            <!-- Excel -->
            <button type="submit" class="btn btn-success" 
                    formaction="{{ route('sf1.export.excel') }}">
              <i class="bi bi-file-earmark-excel"></i> Export to Excel
            </button>
            <!-- PDF -->
            <button type="submit" class="btn btn-danger" 
                    formaction="{{ route('sf1.export.pdf') }}">
              <i class="bi bi-file-earmark-pdf"></i> Export to PDF
            </button>
          </div>

          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const academicSelect = document.getElementById("academic_year");
    const semesterSelect = document.getElementById("semester");
    const allSemesters = Array.from(semesterSelect.options);

    academicSelect.addEventListener("change", function() {
        const selectedAcad = this.value;
        semesterSelect.innerHTML = '<option value="" disabled selected>Select Semester</option>';

        allSemesters.forEach(opt => {
            const acadId = opt.getAttribute("data-acad");
            if (acadId === selectedAcad) {
                semesterSelect.appendChild(opt);
            }
        });
    });
});

$(document).ready(function() {
            $('#searchInput').on('keyup', function(){
                let query = $(this).val();

                $.ajax({
                    url:"{{route('students.search')}}",
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#studentViewTableBody').html($(response).find('#studentViewTableBody').html());
                    },
                    error: function() {
                        console.log('Error loading advisers.');
                    }

                });
            });
        });

</script>

<style>
@media (max-width: 768px) {
    h3.fw-bold {
        font-size: 1.2rem;
        text-align: center;
    }

    #searchContainer {
        flex: 1 1 100%;
        width: 100%;
        flex-wrap: nowrap;
    }

    #searchInput {
        flex: 1;
        font-size: 0.9rem;
    }

    #searchButton {
        padding: 6px 12px;
    }

    button[data-bs-target="#sf1Modal"] {
        width: 100%;
        margin-top: 8px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table img {
        width: 45px !important;
        height: 45px !important;
    }

    .modal-body {
        font-size: 0.9rem;
        word-break: break-word;
    }

@media (max-width: 768px) {
  .modal-content {
    border-radius: 0; 
  }
  .modal-body {
    padding: 1rem;
  }
  .table th, .table td {
    font-size: 0.85rem;
    white-space: normal !important;
  }
}

@media (min-width: 769px) {
  .modal-dialog {
    max-width: 900px;
  }
  .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }
}

}
</style>
@endsection
