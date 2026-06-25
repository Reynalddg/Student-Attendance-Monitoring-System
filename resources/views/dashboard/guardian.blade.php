@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp

    <div class="container-lg">

        <section class="mt-2">
             @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Parents and Guardians Dashboard</h3>
            @else
                <h3 class="fw-bold"> Parents and Guardians Dashboard</h3>
            @endif
            <hr id="hr">
        </section>

        <section class="d-flex justify-content-between align-items-center px-4 mt-5">
             
            @if ($isArchivedRoute)
                 <form action="{{ route('guardians.guardianSearch') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                        <i class="fa-solid fa-search" id="searchIcon"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search Archived Parent & Guardian" style="text-transform: capitalize;">
                </form>
            @else
                 <form action="{{ route('guardians.guardianSearch') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                        <i class="fa-solid fa-search" id="searchIcon"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search Parent & Guardian" style="text-transform: capitalize;">
                </form>
            @endif

        @if ($isArchivedRoute)
             
         @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa-solid fa-user-plus me-1"></i> Add Parent & Guardian
                </button>
         @endif
            

        
        </section>

        <section class="mt-4 px-2 px-md-4 "  style="max-height: 450px; overflow-y: auto;">
            <table class="table table-hover mt-4 sticky-header-table">
                <thead class="table-dark">
                    <tr>
                        <th>Student</th>
                        <th>Type</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Relation</th>
                        <th>Phone Number</th>
                        @if ($isArchivedRoute)
                            <th>Date Archived</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="guardianTableBody">
                    @forelse ($guardians as $guardian)
                        <tr>
                            <td>
                                @if($guardian->student)
                                    {{ $guardian->student->first_name }}
                                    {{ $guardian->student->middle_name ? strtoupper(substr($guardian->student->middle_name, 0, 1)) . '.' : '' }}
                                    {{ $guardian->student->last_name }}
                                @else
                                    <span class="text-muted">No student</span>
                                @endif
                            </td>
                            <td>{{ $guardian->guardian_type }}</td>
                            <td>{{ $guardian->first_name }}</td>
                            <td>{{ $guardian->middle_name ?? '--' }}</td>
                            <td>{{ $guardian->last_name }}</td>
                            <td>{{ $guardian->suffix ?? '--' }}</td>
                            <td>{{ $guardian->relation ?? '--' }}</td>
                            <td>{{ $guardian->phone_number ?? '--' }}</td>

                            @if ($isArchivedRoute)
                                <td>{{ $guardian->date_archived ?? '--' }}</td>
                            @endif

                            <td>
                                @if ($isArchivedRoute)
                                    <form action="{{ route('guardians.restore', $guardian->guardian_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up"></i> Restore</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $guardian->guardian_id }}"><i class="fa-solid fa-pen-to-square"></i> Update</button>
                                    <button type="button" class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#archivedModal-{{ $guardian->guardian_id }}"><i class="fa-solid fa-box-archive"></i> Archive</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                @if (request()->routeIs('guardians.archived'))
                                    No Archived Guardians Found
                                @else
                                    No Guardian Found
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>


            </table>
        </section>

          <div class="mt-4 px-4 flex-wrap fw-bold mb-4">
             {{ $guardians->links('pagination::bootstrap-5') }}
        </div>

        <section class="d-flex justify-content-end align-items-center px-4 mt-2">
                <div class="d-flex gap-2">
             @if(request()->routeIs('guardians.archived'))
                <a href="{{ route('dashboard.guardians') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Guardians</a>
            @else
                <a href="{{ route('guardians.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Guardians</a>
            @endif
                </div>
        </section>

        @foreach ($guardians as $guardian)
            
            {{--Update modal--}}
            <div class="modal fade" id="updateModal-{{ $guardian->guardian_id }}" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="/guardian/{{ $guardian->guardian_id }}" method="post">
                            @method('put')
                            @csrf

                            <div class="modal-header bg-warning text-white">
                                <h5 class="fw-bold fs-5">
                                    Update Guardian
                                </h5>
                            </div>

                        <div class="modal-body">

                               <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Student <span class="text-danger fw-bold" >*</span></label>
                                  <select name="guardian_type" class="form-control border-dark guardianType" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="Father" {{ $guardian->guardian_type == 'Father' ? 'selected' : '' }}>Father</option>
                                    <option value="Mother" {{ $guardian->guardian_type == 'Mother' ? 'selected' : '' }}>Mother</option>
                                    <option value="Guardian" {{ $guardian->guardian_type == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                                </select>
                                </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Guardian Type <span class="text-danger">*</span></label>
                                <select id="guardianType" name="guardian_type" class="form-control border-dark" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="Father" {{ $guardian->guardian_type == 'Father' ? 'selected' : ''}}>Father</option>
                                    <option value="Mother" {{ $guardian->guardian_type == 'Mother' ? 'selected' : ''}}>Mother</option>
                                    <option value="Guardian" {{ $guardian->guardian_type == 'Guardian' ? 'selected' : ''}}>Guardian</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ $guardian->first_name}}" class="form-control border-dark" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ $guardian->middle_name}}" class="form-control border-dark">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ $guardian->last_name}}" class="form-control border-dark" required>
                            </div>

                             <div class="mb-3">
                                <label for="" class="form-label fw-bold">Suffix</label>
                                <select name="suffix" id="" class="form-select border-dark">
                                    <option value="" selected disabled>Select Suffix</option>
                                     <option value="">None</option> 
                                    <option value="Jr" {{ $guardian->suffix == 'Jr' ? 'selected' : ''}}>Jr.</option>
                                    <option value="Sr" {{ $guardian->suffix == 'Sr' ? 'selected' : ''}}>Sr.</option>
                                    <option value="II" {{ $guardian->suffix == 'II' ? 'selected' : ''}}>II</option>
                                    <option value="III" {{ $guardian->suffix == 'III' ? 'selected' : ''}}>III</option>
                                    <option value="IV" {{ $guardian->suffix == 'IV' ? 'selected' : ''}}>IV</option>
                                </select>
                            </div>

                       <div class="mb-3 relationContainer" style="{{ $guardian->guardian_type === 'Guardian' ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label fw-bold">Relationship</label>
                            <select name="relation" class="form-control border-dark">
                                <option value="" selected>Select Relationship</option>
                                <option value="Relative" {{ $guardian->relation == 'Relative' ? 'selected' : '' }}>Relative</option>
                                <option value="Guardian" {{ $guardian->relation == 'Guardian' ? 'selected' : '' }}>Guardian (Non-Relative)</option>
                            </select>
                        </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone_number" value="{{$guardian->phone_number}}" class="form-control border-dark" placeholder="e.g. 09971234567" required>
                                <small class="text-danger">  <i class="fa-solid fa-info-circle me-1"></i> Add the phone number if this guardian is the main contact person</small>

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

            {{--Archived modal--}}
            <div class="modal fade" id="archivedModal-{{ $guardian->guardian_id }}" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="/guardian/{{ $guardian->guardian_id }}" method="post">
                            @method('delete')
                            @csrf
                        
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fs-5">
                                    Archived Guardian
                                </h5>
                            </div>
                            <div class="modal-body">
                                <h5>Are you sure you want to archive this guardian?</h5>
                                <p class="fw-bold">Father's Name: {{ $guardian->first_name }} </p>
                                <p class="fw-bold">Mother's Name: {{ $guardian->middle_name }}</p>
                                <p class="fw-bold">Guardian's Name: {{ $guardian->last_name ?? "N/A"}}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">Archive</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endforeach

       {{--Add Modal--}}
        <div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/add/guardian" method="post">
                        @csrf
                        
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fs-5">Add Parent & Guardian</h5>
                        </div>

                        <div class="modal-body">

                               <div class="mb-3">
                                <label class="form-label fw-bold">Student <span class="text-danger">*</span></label>
                                <select name="student_id" class="form-control border-dark" required>
                                    <option value="" disabled selected>Select Student</option>
                                  @foreach($students as $student)
                                        <option value="{{ $student->student_id }}">
                                            {{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Guardian Type <span class="text-danger">*</span></label>
                              <select name="guardian_type" class="form-control border-dark guardianType" required>
                                <option value="" disabled selected>Select Type</option>
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Guardian">Guardian</option>
                            </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" placeholder="e.g. Juan" class="form-control border-dark" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Middle Name</label>
                                <input type="text" name="middle_name" placeholder="e.g. Santos" class="form-control border-dark">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" placeholder="e.g. Dela Cruz" class="form-control border-dark" required>
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

                     <div class="mb-3 relationContainer" style="display:none;">
                        <label class="form-label fw-bold">Relationship</label>
                        <select name="relation" class="form-control border-dark">
                            <option value="" selected>Select Relationship</option>
                            <option value="Relative">Relative</option>
                            <option value="Guardian">Guardian (Non-Relative)</option>
                        </select>
                    </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone_number" class="form-control border-dark" placeholder="e.g. 09971234567" >
                                <small class="text-danger">  <i class="fa-solid fa-info-circle me-1"></i> Add the phone number if this guardian is the main contact person</small>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Parent/Guardian</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

       <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function(){
                let query = $(this).val();
                let searchUrl = "{{ $isArchivedRoute ? route('guardians.archived.search') : route('guardians.guardianSearch') }}";

                $.ajax({
                    url:searchUrl,
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#guardianTableBody').html($(response).find('#guardianTableBody').html());
                    },
                    error: function() {
                        console.log('Error loading advisers.');
                    }

                });
            });
        });

document.addEventListener('DOMContentLoaded', function () {
    const guardianTypeDropdowns = document.querySelectorAll('.guardianType');

    guardianTypeDropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const relationContainer = this.closest('form').querySelector('.relationContainer');
            if (relationContainer) {
                relationContainer.style.display = this.value === 'Guardian' ? 'block' : 'none';
            }
        });

        // trigger change on page load
        dropdown.dispatchEvent(new Event('change'));
    });
});

    </script>

@endsection
