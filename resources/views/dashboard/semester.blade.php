@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp

<div class="container-lg">
    <section class="mt-2">
        @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Semesters Dashboard</h3>
            @else
                <h3 class="fw-bold"> Semesters Dashboard</h3>
            @endif
        <hr id="hr">
    </section>

    <section class="mt-5 d-flex justify-content-between px-4">
        @if ($isArchivedRoute)
            <form method="GET"  action="{{ route('semesters.search') }}" class="d-flex align-items-center position-relative" id="searchContainer">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control form-control-sm me-2 py-2 border-dark"  placeholder="Search Archived Semester"  style="text-transform: capitalize;">
            </form>
        @else
            <form method="GET"  action="{{ route('semesters.search') }}" class="d-flex align-items-center position-relative" id="searchContainer">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control form-control-sm me-2 py-2 border-dark"  placeholder="Search Semester"  style="text-transform: capitalize;">
            </form>
        @endif


        
        @if ($isArchivedRoute)
             
         @else
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
             <i class="fa-solid fa-graduation-cap me-1"> </i> Add Semester
            </button>
         @endif

      
    </section>

    <section>
        <table class="table table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Academic Year</th>
                    <th>Semester</th>
                    <th>Semester Start</th>
                    <th>Semester End</th>
                    <th>Status</th> 
                    @if ($isArchivedRoute)
                        <th>Date Archived</th>
                    @endif
                    <th>Action</th>
                </tr>
            </thead >
            <tbody id="semesterTableBody">
                @forelse ($semesters as $semester)
                    <tr>
                        <td>{{ $semester->academicYear?->name ?? '-' }} </td>
                        <td>{{ $semester->name }}</td>
                         <td>{{ \Carbon\Carbon::parse($semester->start_date)->format('F d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($semester->end_date)->format('F d, Y') }}</td>
                        <td>
                             @if($semester->status === 'current')
                                    <span class="badge bg-success">Current</span>
                                @elseif($semester->status === 'previous')
                                    <span class="badge bg-secondary">Previous</span>
                                @elseif($semester->status === 'next')
                                    <span class="badge bg-warning text-dark">Upcoming</span>
                                @endif
                        </td>
                            @if ($isArchivedRoute)
                                <td> {{$semester->date_archived}} </td>
                            @endif
                      
                        <td>
                            @if ($isArchivedRoute)
                                 <form action="{{ route('semesters.restore',$semester->semester_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                                    </form>
                            @else
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $semester->semester_id }}"><i class="fa-solid fa-pen-to-square"></i> Update</button>
                            <button class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#archivedModal-{{ $semester->semester_id }}"><i class="fa-solid fa-box-archive"></i> Archived</button>
                            @endif
                        </td>
                    </tr>
                 @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('tracks.archived'))
                                    No Archived Semesters Found
                            @else
                                    No Semesters Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
            </tbody>
        </table>
        {{ $semesters->links('pagination::bootstrap-5') }}
    </section>

     <section class="d-flex justify-content-end align-items-center px-4 mt-5">
                <div class="d-flex gap-2">
             @if(request()->routeIs('semesters.archived'))
                <a href="{{ route('semesters') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Semesters</a>
            @else
                <a href="{{ route('semesters.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Semesters</a>
            @endif
                </div>
        </section>

    {{-- Update Modal --}}
    @foreach ($semesters as $semester)
    <div class="modal fade" id="updateModal-{{ $semester->semester_id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="updateModalLabel">
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content">
                <form action="/semester/{{ $semester->semester_id }}" method="post">
                    @method('PUT')
                    @csrf
                    <div class="modal-header bg-warning text-white">
                        <h3 class="modal-title fs-5" id="updateModalLabel">Update Semester</h3>
                    </div>
                    <div class="modal-body">

                              <div class="mb-3">
                            <label class="form-label fw-bold">Academic Year <span class="text-danger fw-bold" >*</span></label>
                            <select name="academic_year_id" class="form-select border-dark" required>
                                <option value="" disabled>Select Academic Year</option>
                                @foreach($academicYears as $acadYears)
                                    <option value="{{ $acadYears->academic_year_id }}" {{ $acadYears->academic_year_id == $semester->academic_year_id ? 'selected' : '' }}>
                                        {{ $acadYears->name }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Semester <span class="text-danger fw-bold" >*</span></label>
                            <select name="name" class="form-select border-dark" required>
                                <option value="" disabled>Select Semester</option>
                                <option value="First Semester" {{ $semester->name == 'First Semester' ? 'selected' : '' }}>First Semester</option>
                                <option value="Second Semester" {{ $semester->name == 'Second Semester' ? 'selected' : '' }}>Second Semester</option>
                            </select>
                        </div>

                           <div class="mb-3">
                            <label for="year_start" class="form-label fw-bold">Semester Start Date <span class="text-danger fw-bold" >*</span></label>
                            <input type="date" name="start_date" id="year_start" 
                                class="form-control border-dark" 
                                value="{{ old('start_date', $semester->start_date ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label for="year_end" class="form-label fw-bold">Semester End Date <span class="text-danger fw-bold" >*</span></label>
                            <input type="date" name="end_date" id="year_end" 
                                class="form-control border-dark" 
                                value="{{ old('end_date', $semester->end_date ?? '') }}">
                        </div>



                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger fw-bold" >*</span></label>
                            <select name="status" class="form-select border-dark" required>
                                <option value="current" {{ $semester->status === 'current' ? 'selected' : '' }}>Current</option>
                                <option value="previous" {{ $semester->status === 'previous' ? 'selected' : '' }}>Previous</option>
                                <option value="next" {{ $semester->status === 'next' ? 'selected' : '' }}>Next</option>
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

    {{-- Archived Modal --}}
    <div class="modal fade" id="archivedModal-{{ $semester->semester_id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="archivedModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/semester/{{$semester->semester_id}}" method="post">
                    @method('delete')
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h3 class="modal-title fs-5">Archive Semester</h3>
                    </div>
                    <div class="modal-body">
                        <h5>Are you sure you want to archive this semester?</h5>
                        <p class="fw-bold mt-3">Semester: {{ $semester->name }}</p>
                        <p class="fw-bold mt-3">Academic Year: {{ $semester->academicYear?->name ?? '-' }}</p>
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

    {{-- Add Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/add/semester" method="post">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-5" id="addModalLabel">Add Semester</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                         <div class="mb-3">
                            <label class="form-label  fw-bold">Academic Year <span class="text-danger fw-bold" >*</span></label>
                            <select name="academic_year_id" class="form-select border-dark" required>
                                <option value="" selected disabled>Select Academic Year</option>
                                @foreach($academicYears as $acadYears)
                                    <option value="{{ $acadYears->academic_year_id }}">
                                        {{ $acadYears->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label  fw-bold">Semester <span class="text-danger fw-bold" >*</span></label>
                            <select name="name" class="form-select border-dark" required>
                                <option value="" selected disabled>Select Semester</option>
                                <option value="First Semester">First Semester</option>
                                <option value="Second Semester">Second Semester</option>
                            </select>
                        </div>

                            <div class="mb-3">
                                <label for="start_date" class="form-label  fw-bold">Start Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" id="start_date" name="start_date" class="form-control border-dark" required>
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label  fw-bold">End Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" id="end_date" name="end_date" class="form-control border-dark" required>
                            </div>
                       

                        <div class="mb-3">
                            <label class="form-label  fw-bold">Status <span class="text-danger fw-bold" >*</span></label>
                            <select name="status" class="form-select border-dark" required>
                                <option value="current">Current</option>
                                <option value="previous" selected>Previous</option>
                                <option value="next" selected>Next</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Semester</button>
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
                let searchUrl = "{{ $isArchivedRoute ? route('semesters.archived.search') : route('semesters.search') }}";

                $.ajax({
                    url:searchUrl,
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#semesterTableBody').html($(response).find('#semesterTableBody').html());
                    },
                    error: function() {
                        console.log('Error loading advisers.');
                    }

                });
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');

    startInput.addEventListener('change', function () {
        if (!this.value) return;

        const startDate = new Date(this.value);

        // Minimum end date = +4 months
        const minEndDate = new Date(startDate);
        minEndDate.setMonth(minEndDate.getMonth() + 4);

        // Maximum end date = +6 months
        const maxEndDate = new Date(startDate);
        maxEndDate.setMonth(maxEndDate.getMonth() + 7);

        // Apply limits
        endInput.min = minEndDate.toISOString().split('T')[0];
        endInput.max = maxEndDate.toISOString().split('T')[0];

        // Clear invalid value
        if (endInput.value && (endInput.value < endInput.min || endInput.value > endInput.max)) {
            endInput.value = '';
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {

    // LOOP THROUGH ALL UPDATE MODALS
    document.querySelectorAll('[id^="updateModal-"]').forEach(modal => {

        const startInput = modal.querySelector('input[name="start_date"]');
        const endInput = modal.querySelector('input[name="end_date"]');

        if (!startInput || !endInput) return;

        // WHEN START DATE CHANGES
        startInput.addEventListener('change', function () {
            if (!this.value) return;

            const startDate = new Date(this.value);

            // Minimum = start + 4 months
            const minEndDate = new Date(startDate);
            minEndDate.setMonth(minEndDate.getMonth() + 4);

            // Maximum = start + 7 months
            const maxEndDate = new Date(startDate);
            maxEndDate.setMonth(maxEndDate.getMonth() + 7);

            // Apply limits
            endInput.min = minEndDate.toISOString().split('T')[0];
            endInput.max = maxEndDate.toISOString().split('T')[0];

            // Clear invalid end date
            if (endInput.value && (endInput.value < endInput.min || endInput.value > endInput.max)) {
                endInput.value = '';
            }
        });

        // Trigger validation on load (for pre-filled values)
        startInput.dispatchEvent(new Event("change"));
    });

});
    </script>

@endsection
