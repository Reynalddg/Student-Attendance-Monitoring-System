@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp

    <div class="container-lg">
        <section class="mt-2">
            @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Students Dashboard</h3>
            @else
                <h3 class="fw-bold"> Students Dashboard</h3>
            @endif
            <hr id="hr">
        </section>

        <br>

      <section class="mt-4 d-flex justify-content-between align-items-center px-4 flex-wrap gap-2">

           @if ($isArchivedRoute)
                 <form method="GET" action="{{ route('students.studentSearch') }}"  class="d-flex align-items-center position-relative" id="searchContainer"> <i class="fa-solid fa-search" id="searchIcon"></i> <input type="text" name="search" id="searchInput" 
                    value="{{ request('search') }}" 
                    class="form-control form-control-sm me-2 py-2 border-dark"
                    placeholder="Search Archived Student" style="text-transform: capitalize;">
                </form>
           @else
                 <form method="GET" action="{{ route('students.studentSearch') }}"  class="d-flex align-items-center position-relative" id="searchContainer"> <i class="fa-solid fa-search" id="searchIcon"></i> <input type="text" name="search" id="searchInput" 
                    value="{{ request('search') }}" 
                    class="form-control form-control-sm me-2 py-2 border-dark"
                    placeholder="Search Student" style="text-transform: capitalize;">
                </form>
           @endif

        @if ($isArchivedRoute)
             
         @else
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fa-solid fa-file-import me-1"></i> Import Students
                </button>

                <button type="button" class="btn btn-primary rounded-start rounded-end" 
                        data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa-solid fa-user-plus me-1"></i> Add Student
                </button>
            </div>

         @endif

        </section>


         <section class="mt-4 px-2 px-md-4"  style="max-height: 450px; overflow-y: auto;">
            <table class="table table-hover mt-3 sticky-header-table">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Full Name</th>
                         <th>LRN</th>
                         <th>Enrollment Status</th>
                         @if ($isArchivedRoute)
                             <th>Date Archived</th>
                         @endif
                         
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    @forelse($students as $student)
                        <tr>

                             <td class="text-truncate" style="max-width:120px;">
                            @if($student->image)
                                <img src="{{ asset('storage/' . $student->image) }}" alt="Student Image" width="60" height="60" style="object-fit: cover; border-radius:15%; margin:2px;">
                            @else
                                No Image
                            @endif
                        </td>

                            <td>
                                {{ $student->first_name }} 
                                {{ $student->middle_name }}
                                {{ $student->last_name }} 
                                {{ $student->suffix }}
                            </td>
                             <td>
                                {{ $student->lrn}}
                            </td>

                            <td>{{ $student->current_status }}</td>

                              @if ($isArchivedRoute)
                                  <td> {{ $student->date_archived }}</td>
                              @endif
                            <td>
                                @if ($isArchivedRoute)
                                        <form action="{{ route('students.restore',$student->student_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                                    </form>
                                    
                                @else
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal-{{ $student->student_id }}"> <i class="fa-solid fa-eye"></i> View </button>

                                @endif
                            </td>
                        </tr>
                        
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('tracks.archived'))
                                    No Archived Student Found
                            @else
                                    No Student Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
          
        </section>

        <div class="mt-4 px-4 flex-wrap fw-bold mb-4">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>

         <section class="d-flex justify-content-end align-items-center px-4 mt-2">
                <div class="d-flex gap-2">
             @if(request()->routeIs('students.archived'))
                <a href="{{ route('students') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Students</a>
            @else
                <a href="{{ route('students.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Students</a>
            @endif
                </div>
        </section>

        

    </div>

        @foreach($students as $student)


        {{-- View Modal --}}
        <div class="modal fade" id="viewModal-{{ $student->student_id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="viewModalLabel-{{ $student->student_id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl"><!-- mas malapad para sa maraming columns -->
            <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h1 class="modal-title fs-5" id="viewModalLabel-{{ $student->student_id }}">Student View</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:70vh; overflow:auto;">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th style="min-width:150px">First Name</th>
                            <th style="min-width:150px">Middle Name</th>
                            <th style="min-width:150px">Last Name</th>
                            <th style="min-width:150px">Suffix</th>
                            <th style="min-width:150px">LRN</th>
                            <th style="min-width:100px">Sex</th>
                            <th style="min-width:300px">Address</th>
                            <th style="min-width:250px">Birth Date</th>
                            <th style="min-width:250px">Religion</th>
                            <th style="min-width:350px">Parents & Guardian</th>
                            <th style="min-width:150px">Remarks</th>
                             <th style="min-width:150px">Grade Level</th>
                            <th style="min-width:250px">Date Created</th>
                            <th  style="min-width:250px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $student->first_name }}</td>
                            <td>{{ $student->middle_name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>{{ $student->suffix ?? '--' }}</td>
                            <td>{{ $student->lrn }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->barangay }} {{ $student->municipality }} {{ $student->province }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($student->birthdate)->format('F d, Y') }}
                            </td>
                            <td> {{ $student->religion }}</td>
                       <td>
                           @php
                                $guardians = $student->guardians ?? collect();
                                $fullName = fn($person) => $person 
                                    ? trim("{$person->first_name} {$person->middle_name} {$person->last_name} {$person->suffix}") 
                                    : '--';

                                $father = $guardians->firstWhere('guardian_type', 'Father');
                                $mother = $guardians->firstWhere('guardian_type', 'Mother');
                                $other = $guardians->firstWhere('guardian_type', 'Guardian');
                            @endphp


                                                    Father: {{ $fullName($father) }} <br>
                            Mother: {{ $fullName($mother) }} <br>
                            Guardian: {{ $fullName($other) }}


                            @if($guardians->isEmpty())
                                <br><span class="text-muted">No guardian info</span>
                            @endif
                        </td>

                             <td>{{ $student->remarks ?? '--' }}</td>
                             
                              <td style="min-width:150px;">{{ $student->grade_level }}</td>
                            <td style="min-width:150px;">{{ $student->date_created }}</td>
                            <td style="min-width:180px;">
                                <button type="button" class="btn btn-sm btn-warning open-update" data-student="{{ $student->student_id }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Update
                                </button>                            
                                <button type="button" class="btn btn-sm btn-danger open-archive" data-student="{{ $student->student_id }}">
                                    <i class="fa-solid fa-box-archive"></i> Archive
                                </button>                        
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            </div>
            </div>
        </div>
        </div>

      {{--Update Modal--}}
        <div class="modal fade"  data-bs-backdrop="static" data-bs-keyboard="false"   id="updateModal-{{ $student->student_id }}" tabindex="-1" aria-labelledby="updateModalLabel-{{ $student->student_id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/student/{{ $student->student_id}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-header bg-warning text-white">
                            <h1 class="modal-title fs-5" id="updateModalLabel-{{ $student->student_id}}">Update Student</h1>
                        </div>
                        <div class="modal-body">

                        <div class="mb-3">
                                <label for="firstName" class="form-label fw-bold">First Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="first_name" id="" class="form-control border-dark" value="{{ $student->first_name }}" style="text-transform: capitalize;" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Middle Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="middle_name" id="" class="form-control border-dark" value="{{ $student->middle_name}}" style="text-transform: capitalize;">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Last Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="last_name" id="" class="form-control border-dark" value="{{ $student->last_name}}" style="text-transform: capitalize;" required >
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Suffix</label>
                                <select name="suffix" id="" class="form-select border-dark">
                                    <option value="" selected disabled>Select Suffix</option>
                                     <option value="">None</option> 
                                    <option value="Jr" {{ $student->suffix == 'Jr' ? 'selected' : ''}}>Jr.</option>
                                    <option value="Sr" {{ $student->suffix == 'Sr' ? 'selected' : ''}}>Sr.</option>
                                    <option value="II" {{ $student->suffix == 'II' ? 'selected' : ''}}>II</option>
                                    <option value="III" {{ $student->suffix == 'III' ? 'selected' : ''}}>III</option>
                                    <option value="IV" {{ $student->suffix == 'IV' ? 'selected' : ''}}>IV</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">LRN <span class="text-danger fw-bold" >*</span></label>
                            <input type="number" name="lrn" class="form-control border-dark" maxlength="12" value="{{$student->lrn}}" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Sex <span class="text-danger fw-bold" >*</span></label>
                                <select name="gender" id="" class="form-select border-dark" required>
                                    <option value="" selected disabled> Select Sex</option>
                                    <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Barangay <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="barangay" id="" class="form-control border-dark" value=" {{ $student->barangay}}" style="text-transform: capitalize;" required>
                            </div>

                             <div class="mb-3">
                                <label for="" class="form-label fw-bold">Municipality <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="municipality" id="" class="form-control border-dark" value=" {{ $student->municipality}}" style="text-transform: capitalize;" required>
                            </div>

                              <div class="mb-3">
                                <label for="" class="form-label fw-bold">Province <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="province" id="" class="form-control border-dark" value=" {{ $student->province}}" style="text-transform: capitalize;" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-clabel fw-bold">Birth Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" name="birthdate" id="" class="form-control border-dark" value="{{ $student->birthdate}}" style="text-transform: capitalize;" required >
                            </div>

                        <div class="mb-3">
                            <label for="religion" class="form-label fw-bold">Religion <span class="text-danger fw-bold">*</span></label>
                            <select name="religion" id="religion" class="form-select border-dark" required>
                                <option value="" disabled>Select Religion</option>
                                <option value="Christianity" {{ $student->religion === 'Christianity' ? 'selected' : '' }}>
                                    Christianity (including Roman Catholic, Born Again, Evangelical, Protestant)
                                </option>
                                <option value="Iglesia ni Cristo" {{ $student->religion === 'Iglesia ni Cristo' ? 'selected' : '' }}>
                                    Iglesia ni Cristo
                                </option>
                                <option value="Ang Dating Daan" {{ $student->religion === 'Ang Dating Daan' ? 'selected' : '' }}>
                                    Ang Dating Daan
                                </option>
                                <option value="Islam / Muslim" {{ $student->religion === 'Islam / Muslim' ? 'selected' : '' }}>
                                    Islam / Muslim
                                </option>
                                <option value="Others" {{ $student->religion === 'Others' ? 'selected' : '' }}>
                                    Others / None
                                </option>
                            </select>
                        </div>

                        

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Remarks</label>
                            <select name="remarks" class="form-control border-dark">
                                    <option value="">Select Remarks</option>
                                    <option value="CCT" {{ $student->remarks === 'CCT' ? 'selected' : '' }} >CCT Recipient</option>
                                    <option value="B/A" {{ $student->remarks === 'B/A' ? 'selected' : '' }} >Balik Aral</option>
                                    <option value="SN"> {{ $student->remarks === 'SN' ? 'selected' : '' }} Special Needs</option>
                                    <option value="ACL" {{ $student->remarks === 'ACL' ? 'selected' : '' }} >Education Accelerated</option>
                                </select>
                            </div>

                              <div class="mb-3">
                                <label for="" class="form-label fw-bold">Grade Level <span class="text-danger fw-bold" >*</span></label>
                                <select name="grade_level" id="" class="form-select border-dark">
                                    <option value=""selected disabled>Select Grade Level</option>
                                    <option value="11"{{ $student->grade_level === '11'? 'selected' : ''}}>11</option>
                                    <option value="12" {{ $student->grade_level === '12' ? 'selected' : ''}}>12</option>
                                </select>
                            </div>



                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Image <span class="text-danger fw-bold" >*</span></label>
                                <input type="file" name="image" id="" class="form-control border-dark" >
                                <small class="text-danger"><i class="fa-solid fa-info-circle me-1"></i> Leave this blank if you don't want to change the image</small>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        {{--archived MOdal--}}
        <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="deleteModal-{{ $student->student_id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $student->student_id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/student/{{ $student->student_id}}" method="post">
                        @csrf
                        @method('delete')
                    <div class="modal-header bg-danger text-white">
                        <h1 class="modal-title fs-5" id="deleteMOdalLabel-{{ $student->student_id}}">Archived Student</h1>
                    </div>
                    <div class="modal-body">
                        <h5>Are you sure you want to archive this student?</h5>
                        <p class="fw-bold mt-3">Name: {{ $student->first_name}} {{ $student->middle_name}} {{$student->last_name}}</p>
                    </div>
                    <div class="modal-footer">
                         <button type="submit" class="btn btn-warning">Archived</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach


    {{--Add Modal--}}
    <div class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" id="addModal" tabindex="-1" aria-labelled>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form action="/add/student" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-5">Add New Student</h5>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="firstName" class="form-label fw-bold">First Name <span class="text-danger fw-bold" >*</span></label>
                            <input type="text" name="first_name" id="" class="form-control border-dark" placeholder="e.g. Juan"  required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="" class="form-clabel fw-bold">Middle Name </label>
                            <input type="text" name="middle_name" id="" class="form-control border-dark" placeholder="e.g. Santos">
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Last Name <span class="text-danger fw-bold" >*</span></label>
                            <input type="text" name="last_name" id="" class="form-control border-dark" placeholder="e.g. Dela Cruz"  required >
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Suffix</label>
                           <select name="suffix" class="form-control border-dark">
                                <option value="">Select Suffix</option>
                                <option value="Jr">Jr.</option>
                                <option value="Sr">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">LRN <span class="text-danger fw-bold" >*</span></label>
                           <input type="number" name="lrn" class="form-control border-dark" maxlength="12" oninput="if(this.value.length > 12) this.value = this.value.slice(0, 12);" placeholder="e.g. 105811080000" required>

                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Sex <span class="text-danger fw-bold" >*</span></label>
                            <select name="gender" id="" class="form-select border-dark" required>
                                <option value="" selected disabled> Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Barangay <span class="text-danger fw-bold" >*</span></label>
                            <input type="text" name="barangay" id="" class="form-control border-dark" placeholder="e.g. Bantug Hacienda" required>
                        </div>

                         <div class="mb-3">
                            <label for="" class="form-label fw-bold">Municipality <span class="text-danger fw-bold" >*</span></label>
                            <input type="text" name="municipality" id="" class="form-control border-dark" placeholder="e.g. Talavera" required>
                        </div>

                         <div class="mb-3">
                            <label for="" class="form-label fw-bold">Province <span class="text-danger fw-bold" >*</span></label>
                            <input type="text" name="province" id="" class="form-control border-dark" placeholder="e.g. Nueva Ecija" required>
                        </div>

                           <div class="mb-3">
                            <label for="birthdate" class="form-label fw-bold" >Birth Date <span class="text-danger fw-bold" >*</span></label>
                            <input 
                                type="date" 
                                name="birthdate" 
                                id="birthdate" 
                                class="form-control border-dark" 
                                required
                                min="{{ \Carbon\Carbon::now()->subYears(25)->format('Y-m-d') }}" 
                                max="{{ \Carbon\Carbon::now()->subYears(15)->format('Y-m-d') }}"
                            >
                        </div>

                         <div class="mb-3">
                            <label for="" class="form-label fw-bold">Religion <span class="text-danger fw-bold">*</span></label>
                            <select name="religion" id="religion" class="form-select border-dark" required>
                                <option value="" disabled selected>Select Religion</option>
                                <option value="Christianity">Christianity (including Roman Catholic, Born Again, Evangelical, Protestant)</option>
                                <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                                <option value="Ang Dating Daan">Ang Dating Daan</option>
                                <option value="Islam / Muslim">Islam / Muslim</option>
                                <option value="Others">Others / None</option>
                            </select>
                        </div>


            


                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Remarks</label>
                           <select name="remarks" class="form-control border-dark">
                                <option value="">Select Remarks</option>
                                <option value="CCT">CCT Recipient</option>
                                <option value="B/A">Balik Aral</option>
                                <option value="SN">Special Needs</option>
                                <option value="ACL">Education Accelerated</option>
                            </select>
                        </div>

                        <div class="mb-3">
                                <label for="" class="form-label fw-bold">Grade Level <span class="text-danger fw-bold" >*</span></label>
                                <select name="grade_level" id="" class="form-select border-dark">
                                    <option value=""selected disabled>Select Grade Level</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Image</span></label>
                            <input type="file" name="image" id="" class="form-control border-dark">
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Student</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{--Import MOdal--}}

    <div class="modal fade" id="importModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="importModalLabel"><i class="fa-solid fa-file-excel me-1"></i> Import Students</h5>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3">
            Upload your Excel file (.xls or .xlsx) following the correct column format.  
            <br>Example columns: <strong>LRN, First Name, Last Name, Sex, etc.</strong>
          </p>

          <div class="mb-3">
            <label for="file" class="form-label fw-bold">Choose File <span class="text-danger">*</span></label>
            <input type="file" name="file" accept=".xls,.xlsx" id="file" class="form-control border-dark" required>
          </div>

          <div class="alert alert-info py-2">
            <i class="fa-solid fa-info-circle me-1"></i> 
            Ensure that duplicate LRN entries will be skipped automatically.
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success"><i class="fa-solid fa-upload me-1"></i> Import</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".open-update").forEach(button => {
        button.addEventListener("click", function() {
            const studentId = this.dataset.student;
            const viewModal = document.querySelector(`#viewModal-${studentId}`);
            const updateModal = document.querySelector(`#updateModal-${studentId}`);

            const bsViewModal = bootstrap.Modal.getInstance(viewModal);
            bsViewModal.hide();

            setTimeout(() => {
                const bsUpdateModal = new bootstrap.Modal(updateModal);
                bsUpdateModal.show();

                updateModal.addEventListener("hidden.bs.modal", () => {
                    const bsViewModal = new bootstrap.Modal(viewModal);
                    bsViewModal.show();
                }, { once: true });
            }, 300);
        });
    });

    document.querySelectorAll(".open-archive").forEach(button => {
        button.addEventListener("click", function() {
            const studentId = this.dataset.student;
            const viewModal = document.querySelector(`#viewModal-${studentId}`);
            const archiveModal = document.querySelector(`#deleteModal-${studentId}`);

            const bsViewModal = bootstrap.Modal.getInstance(viewModal);
            bsViewModal.hide();

            setTimeout(() => {
                const bsArchiveModal = new bootstrap.Modal(archiveModal);
                bsArchiveModal.show();

                archiveModal.addEventListener("hidden.bs.modal", () => {
                    const bsViewModal = new bootstrap.Modal(viewModal);
                    bsViewModal.show();
                }, { once: true });
            }, 300);
        });
    });

    
});

$(document).ready(function() {
            $('#searchInput').on('keyup', function(){
                let query = $(this).val();
                let searchUrl = "{{ $isArchivedRoute ? route('students.archived.search') : route('students.studentSearch') }}";

                $.ajax({
                    url: searchUrl,
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#studentTableBody').html($(response).find('#studentTableBody').html());
                    },
                    error: function() {
                        console.log('Error loading advisers.');
                    }

                });
            });
        });
</script>

@endsection

