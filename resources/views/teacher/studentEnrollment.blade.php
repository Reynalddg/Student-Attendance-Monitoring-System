@extends('teacherDashboard')

@section('content')
<div class="container-lg">
    <section class="mt-2">
        <h3 class="fw-bold"> Student Enrollment Dashboard </h3>
        <hr id="hr">
    </section>

    <br>

<!-- Search + Enroll -->
<section class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 px-2 px-md-4">

    <form method="GET" action="{{ route('studentEnrollment.search') }}"
              class="d-flex align-items-center position-relative w-100 w-md-auto" id="searchContainer">
            <i class="fa-solid fa-search position-absolute" id="searchIcon" style="left:10px; color:gray;"></i>
            <input type="text" name="search" id="searchInput" 
                   value="{{ request('search') }}" 
                   class="form-control form-control-sm me-2 py-2 ps-4 border-dark"
                   placeholder="Search..." style="text-transform: capitalize;">
        </form>
    
    <button type="button"  class="btn btn-success rounded-start rounded-end w-md-auto flex-shrink-0 mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#importEnrollmentModal">
        <i class="fa-solid fa-file-excel me-1"></i> Bulk Enroll
    </button>

    <button type="button" class="btn btn-success rounded-start rounded-end w-md-auto flex-shrink-0 mt-2 mt-md-0"  id="openSearchLRN">
         <i class="fa-solid fa-user-plus me-1"></i> Enroll Student
    </button>

</section>

    <section class="mt-3 table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-hover align-middle  sticky-header-table">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Full Name</th>
                    <th>LRN</th>
                    <th>Grade Level</th>
                    <th>Section</th>
                    <th>Academic Year & Semester</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Date Enrolled</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="enrollmentTableBody">
                @forelse($studentEnrollments as $studentEnroll)
                <tr>
                    <td class="text-truncate" style="max-width:120px;">
                        @if($studentEnroll->student->image)
                            <img src="{{ asset('storage/' . $studentEnroll->student->image) }}" alt="Student Image" width="60" height="60" style="object-fit: cover; border-radius:15%; margin:2px;">
                        @else
                            No Image
                        @endif
                    </td>
                    <td>{{ $studentEnroll->student->last_name }}, {{ $studentEnroll->student->first_name }} {{ $studentEnroll->student->middle_name ? strtoupper(substr($studentEnroll->student->middle_name, 0, 1)) . '.' : '' }}</td>
                    <td>{{ $studentEnroll->student->lrn }}</td>
                    <td>Grade {{ $studentEnroll->section->grade_level ?? 'No Grade Level' }}</td>
                    <td>{{ $studentEnroll->section->grade_level ?? 'No Grade Level'}} - {{ $studentEnroll->section->section_name ?? 'No Section' }}</td>
                    <td>A.Y. {{ $studentEnroll->semester->academicYear->name }}  | {{ $studentEnroll->semester->name ?? 'No Semester' }}</td>
                    <td> {{ $studentEnroll->status }}</td>
                     <td> {{ $studentEnroll->remarks ?? '--' }}</td>
                    <td>{{ \Carbon\Carbon::parse($studentEnroll->date_created)->format('F d, Y') }}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-warning mt-1" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $studentEnroll->enrollment_id }}">  <i class="fa-solid fa-pen-to-square"></i> Update</a>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No Enrolled Students Found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
       
    </section>
    <div class="mt-4 px-4 flex-wrap fw-bold">
        {{ $studentEnrollments->links('pagination::bootstrap-5') }}
    </div>
</div>

@foreach ($studentEnrollments as $studentEnroll)
    {{-- Update Modal --}}
    <div class="modal fade" id="updateModal-{{ $studentEnroll->enrollment_id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/studentEnrollment/{{ $studentEnroll->enrollment_id }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">Update Student Enrollment</h5>
                    </div>

                    <div class="modal-body">

                        <p class="fs-5 fw-bold">
                            Student Name: 
                            {{ $studentEnroll->student->last_name }},
                            {{ $studentEnroll->student->first_name }}
                            {{ $studentEnroll->student->middle_name ? strtoupper(substr($studentEnroll->student->middle_name, 0, 1)) . '.' : '' }}
                        </p>

                        {{-- STATUS --}}
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                Status <span class="text-danger fw-bold">*</span>
                            </label>
                            <select name="status" id="status-{{ $studentEnroll->enrollment_id }}" 
                                class="form-select border-dark status-dropdown" 
                                data-target="{{ $studentEnroll->enrollment_id }}" required>
                                
                                <option value="" disabled>Select Status</option>
                                <option value="Active" {{ $studentEnroll->status == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="NLS" {{ $studentEnroll->status == 'NLS' ? 'selected' : '' }}>
                                    No Longer in School (NLS)
                                </option>
                            </select>
                        </div>

                        {{-- REMARKS (Only show if NLS) --}}
                        <div class="mb-3 remarks-section" id="remarks-section-{{ $studentEnroll->enrollment_id }}" 
                            style="display: none;">
                            
                            <label class="form-label fw-bold">
                                Remarks <span class="text-danger fw-bold">*</span>
                            </label>

                            <select name="remarks" class="form-select border-dark">
                                <option value="" disabled selected>Select Remarks</option>
                                <option value="Domestic-Related Factors">Domestic-Related Factors</option>
                                <option value="Individual-Related Factors">Individual-Related Factors</option>
                                <option value="School-Related Factors">School-Related Factors</option>
                                <option value="Geographical/Environmental">Geographical/Environmental</option>
                                <option value="Financial-Related">Financial-Related</option>
                            </select>
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



<!-- Search LRN Modal -->
<div class="modal fade" id="searchLRNModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Search Student by LRN</h5>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                    <label class="form-label fw-bold">Select Student <span class="text-danger fw-bold">*</span></label>
                    <input type="text" id="lrnInput" class="form-control border-dark" placeholder="Type LRN or Name">
                    <div id="lrnSuggestions" class="list-group position-absolute w-100" style="z-index: 1055;">
                    </div>
            </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="searchLRNBtn">Search</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md modal-fullscreen-sm-down">
        <div class="modal-content">
            <form action="/add/studentEnrollment" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Student Info</h5>
                </div>
               <div class="modal-body">
                    <input type="hidden" name="student_id" id="enrollStudentId">
                    <input type="hidden" name="semester_id" value="{{ $activeSemester->semester_id ?? '' }}">

                     <p><strong>Full Name:</strong> <span id="enrollStudentName"></span></p>
                    <p><strong>LRN:</strong> <span id="enrollStudentLRN"></span></p>
                    <p><strong>Gender:</strong> <span id="enrollStudentGender"></span></p>
                    <p><strong>Birthdate:</strong> <span id="enrollStudentBirthdate"></span></p>
                    <p><strong>Address:</strong> <span id="enrollStudentAddress"></span></p>
                    <p><strong>Religion:</strong> <span id="enrollStudentReligion"></span></p>
                    <p><strong>Father Name:</strong> <span id="enrollStudentFather"></span></p>
                    <p><strong>Mother Name:</strong> <span id="enrollStudentMother"></span></p>
                    <p><strong>Guardian Name:</strong> <span id="enrollStudentGuardian"></span></p>
                    <p><strong>Grade Level:</strong> <span id="enrollStudentGradeLevel"></span></p>
                    <p><strong>Academic Year:</strong> <span id="academicYearSpan">N/A</span></p>
                    <p><strong>Semester:</strong> <span id="semesterSpan">N/A</span></p>

                </div>

                <div class="modal-footer flex-column flex-sm-row">
                    <button type="submit" class="btn btn-success w-sm-auto mb-2 mb-sm-0">Enroll Student</button>
                    <button type="button" class="btn btn-danger  w-sm-auto" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import Enrollment Modal --}}
<div class="modal fade" id="importEnrollmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="importEnrollmentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('studentEnrollment.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="importEnrollmentModalLabel">
            <i class="fa-solid fa-file-excel me-1"></i> Bulk Enroll Students
          </h5>
        </div>
        <div class="modal-body">
         
          <div class="mb-3">
            <label for="file" class="form-label fw-bold">Choose File <span class="text-danger">*</span></label>
            <input type="file" name="file" accept=".xls,.xlsx" id="file" class="form-control border-dark" required>
          </div>

          <div class="alert alert-info py-2">
            <i class="fa-solid fa-info-circle me-1"></i> 
            Duplicate enrollments (same LRN + semester) will be skipped automatically.
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            <i class="fa-solid fa-upload me-1"></i> Import Enrollments
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchModalEl = document.getElementById('searchLRNModal');
    const searchModal = new bootstrap.Modal(searchModalEl);

    const enrollModalEl = document.getElementById('enrollStudentModal');
    const enrollModal = new bootstrap.Modal(enrollModalEl);

    const lrnInput = document.getElementById('lrnInput');
    const suggestionsContainer = document.getElementById('lrnSuggestions');

    document.getElementById('openSearchLRN').addEventListener('click', function() {
        searchModal.show();
        lrnInput.value = '';
        suggestionsContainer.innerHTML = '';
        lrnInput.focus();
    });

 lrnInput.addEventListener('input', function() {
    const query = lrnInput.value.trim();
    if (!query) {
        suggestionsContainer.innerHTML = '';
        return;
    }

    fetch(`/studentEnrollment/searchByLRN?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            suggestionsContainer.innerHTML = '';
            if (data.success && data.students?.length) {
                data.students.forEach(student => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.innerHTML = `
                        <strong>${student.lrn}</strong><br>
                        <small class="text-muted">${student.last_name}, ${student.first_name} ${student.middle_name ?? ''}</small>
                    `;
                    item.addEventListener('click', function() {
                        // Use exact LRN to fetch full details
                        fetch(`/studentEnrollment/searchByLRN?lrn=${student.lrn}`)
                            .then(res => res.json())
                            .then(data => {
                                if(data.success){
                                    document.getElementById('enrollStudentId').value = data.student.student_id;
                                    document.getElementById('enrollStudentName').innerText = `${data.student.first_name} ${data.student.middle_name ?? ''} ${data.student.last_name}`;
                                    document.getElementById('enrollStudentLRN').innerText = data.student.lrn;
                                    document.getElementById('enrollStudentGradeLevel').innerText = data.grade_level ?? 'N/A';
                                    document.getElementById('enrollStudentGender').innerText = data.student.gender ?? 'N/A';
                                    if(data.student.birthdate){
                                        const birthdate = new Date(data.student.birthdate);
                                        const options = { year: 'numeric', month: 'long', day: 'numeric' };
                                        document.getElementById('enrollStudentBirthdate').innerText = birthdate.toLocaleDateString('en-US', options);
                                    } else {
                                        document.getElementById('enrollStudentBirthdate').innerText = 'N/A';
                                    }
                                    document.getElementById('enrollStudentAddress').innerText = `${data.student.barangay}, ${data.student.municipality}, ${data.student.province}`;
                                    document.getElementById('enrollStudentReligion').innerText = data.student.religion ?? 'N/A';
                                   document.getElementById('enrollStudentFather').innerText = data.guardians.father ?? 'N/A';
                                    document.getElementById('enrollStudentMother').innerText = data.guardians.mother ?? 'N/A';
                                    document.getElementById('enrollStudentGuardian').innerText = data.guardians.guardian ?? 'N/A';

                                    document.querySelector('input[name="semester_id"]').value = data.semester_id;
                                    document.getElementById('academicYearSpan').innerText = data.academic_year ?? 'N/A';
                                    document.getElementById('semesterSpan').innerText = data.semester ?? 'N/A';

                                    suggestionsContainer.innerHTML = '';
                                    searchModal.hide();
                                    enrollModal.show();
                                }
                            });
                    });
                    suggestionsContainer.appendChild(item);
                });
            } else {
                suggestionsContainer.innerHTML = '<div class="list-group-item disabled">No students found</div>';
            }
        });
});


    document.addEventListener('click', function(e) {
        if (!lrnInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.innerHTML = '';
        }
    });
});


 document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.status-dropdown').forEach(function (dropdown) {
            
            function checkStatus(dd) {
                let id = dd.getAttribute('data-target');
                let remarksDiv = document.getElementById('remarks-section-' + id);

                if (dd.value === 'NLS') {
                    remarksDiv.style.display = 'block';
                } else {
                    remarksDiv.style.display = 'none';
                }
            }

            // When dropdown changes
            dropdown.addEventListener('change', function () {
                checkStatus(this);
            });

            // Trigger on load (for edit mode)
            checkStatus(dropdown);
        });
    });


        $(document).ready(function() {
            $('#searchInput').on('keyup', function(){
                let query = $(this).val();

                $.ajax({
                    url:"{{route('studentEnrollment.search')}}",
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#enrollmentTableBody').html($(response).find('#enrollmentTableBody').html());
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
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

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

    #openSearchLRN {
        width: 100%;
    }

    .modal-body p {
        font-size: 0.9rem;
        word-break: break-word;
    }

    table img {
        width: 45px !important;
        height: 45px !important;
    }

    
}
</style>
@endsection