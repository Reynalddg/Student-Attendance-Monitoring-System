@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp

<div class="container-lg">
    <section class="mt-2">
        @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Academic Years Dashboard</h3>
            @else
                <h3 class="fw-bold"> Academic Years Dashboard</h3>
            @endif
        <hr id="hr">
    </section>

    <section class="mt-5 d-flex justify-content-between px-4">

         @if ($isArchivedRoute)
              <form method="GET"  action="{{ route('academicYears.search') }}" class="d-flex align-items-center position-relative" id="searchContainer">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control form-control-sm me-2 py-2 border-dark"  placeholder="Search Archived A.Y"  style="text-transform: capitalize;">
            </form>
         @else
              <form method="GET"  action="{{ route('academicYears.search') }}" class="d-flex align-items-center position-relative" id="searchContainer">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control form-control-sm me-2 py-2 border-dark"  placeholder="Search Academic Year"  style="text-transform: capitalize;">
            </form>
         @endif

         @if ($isArchivedRoute)
             
         @else
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-graduation-cap me-1"> </i> Add Academic Year
            </button>

         @endif
</section>

    <section>
        <table class="table table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th> Academic Year</th>
                    <th>School Year Start</th>
                    <th>School Year End</th>
                    <th>Status</th> 
                    @if ($isArchivedRoute)
                        <th>Date Archived</th>
                    @endif
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="academicYearTableBody">
                @forelse ($academicYears as $acadYears)
                    <tr>
                        <td> {{ $acadYears->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($acadYears->start_date)->format('F d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($acadYears->end_date)->format('F d, Y') }}</td>

                        <td>
                           @if($acadYears->status === 'current')
                                    <span class="badge bg-success">Current</span>
                                @elseif($acadYears->status === 'previous')
                                    <span class="badge bg-secondary">Previous</span>
                                @elseif($acadYears->status === 'next')
                                    <span class="badge bg-warning text-dark">Upcoming</span>
                                @endif
                        </td>
                        @if ($isArchivedRoute)
                            <td>
                                {{ $acadYears->date_archived }}
                            </td>
                        @endif
                        <td>
                            @if ($isArchivedRoute)
                                  <form action="{{ route('academicYears.restore',$acadYears->academic_year_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                                    </form>
                            @else
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $acadYears->academic_year_id }}"><i class="fa-solid fa-pen-to-square"></i> Update</button>
                            <button class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#archivedModal-{{ $acadYears->academic_year_id}}"><i class="fa-solid fa-box-archive"></i> Archived</button>
                            @endif
                        </td>
                    </tr>
                 @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('tracks.archived'))
                                    No Archived Academic Year Found
                            @else
                                    No Academic Year Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
            </tbody>
        </table>
        {{ $academicYears->links('pagination::bootstrap-5') }}
    </section>

     <section class="d-flex justify-content-end align-items-center px-4 mt-5">
                <div class="d-flex gap-2">
             @if(request()->routeIs('academicYears.archived'))
                <a href="{{ route('academicYears') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Academic Year</a>
            @else
                <a href="{{ route('academicYears.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Academic Year</a>
            @endif
                </div>
        </section>

        {{-- Update Modal --}}
        @foreach ($academicYears as $acadYears)
        <div class="modal fade" id="updateModal-{{ $acadYears->academic_year_id }}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="updateModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/academicYear/{{ $acadYears->academic_year_id }}" method="post">
                        @method('PUT')
                        @csrf
                        <div class="modal-header bg-warning text-white">
                            <h3 class="modal-title fs-5" id="updateModalLabel">Update Academic Year</h3>
                        </div>
                        <div class="modal-body">

                        <div class="mb-3">
                                <label for="year_start" class="form-label fw-bold">Academic Year Start Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" name="start_date" id="year_start" 
                                    class="form-control border-dark" 
                                    value="{{ old('start_date', $acadYears->start_date ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="year_end" class="form-label fw-bold">Academic Year End Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" name="end_date" id="year_end" 
                                    class="form-control border-dark" 
                                    value="{{ old('end_date', $acadYears->end_date ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Status <span class="text-danger fw-bold" >*</span></label>
                                <select name="status" id="status" class="form-select border-dark">
                                    <option value="current" {{ $acadYears->status === 'current' ? 'selected' : '' }}>Current</option>
                                    <option value="previous" {{ $acadYears->status === 'previous' ? 'selected' : '' }}>Previous</option>
                                    <option value="next" {{ $acadYears->status === 'next' ? 'selected' : '' }}>Next</option>
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
    <div class="modal fade" id="archivedModal-{{ $acadYears->academic_year_id }}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="archivedModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/academicYear/{{$acadYears->academic_year_id}}" method="post">
                    @method('delete')
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h3 class="modal-title fs-5">Archive Academic Year</h3>
                    </div>
                    <div class="modal-body">
                        <h5>Are you sure you want to archive this academic year?</h5>
                        <p class="fw-bold mt-3">Academic Year: {{$acadYears->name}}</p>
                      
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
    <div class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/add/academicYear" method="post">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-5" id="addModalLabel">Add Academic Year</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                          <div class="mb-3">
                            <label for="start_date" class="form-label fw-bold">Academic Year Start Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" id="start_date" name="start_date" class="form-control border-dark" required>
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label fw-bold">Academic Year End Date <span class="text-danger fw-bold" >*</span></label>
                                <input type="date" id="end_date" name="end_date" class="form-control border-dark" required>
                            </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status <span class="text-danger fw-bold" >*</span></label>
                            <select name="status" class="form-select border-dark" required>
                                <option value="current">Current</option>
                                <option value="previous" selected>Previous</option>
                                <option value="next" selected>Next</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Academic Year</button>
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
        let searchUrl = "{{ $isArchivedRoute ? route('academicYears.archived.search') : route('academicYears.search') }}";

        $.ajax({
            url:searchUrl,
            type: "GET",
            data: { search: query },
            success: function(response) {
                $('#academicYearTableBody').html($(response).find('#academicYearTableBody').html());
            },
            error: function() {
                console.log('Error loading advisers.');
            }
        });
    });
});

// ===== ADD MODAL MIN 8 MONTHS RULE =====
document.addEventListener("DOMContentLoaded", function () {
    const MIN_MONTHS = 8;
    const startInput = document.getElementById("start_date");
    const endInput = document.getElementById("end_date");
    const currentYear = new Date().getFullYear();

    if (startInput && endInput) {
        startInput.min = `${currentYear}-01-01`;

        startInput.addEventListener("change", function () {
            if (!this.value) return;

            const startDate = new Date(this.value);
            const minAllowed = new Date(`${currentYear}-01-01`);

            if (startDate < minAllowed) {
                alert(`Start date cannot be earlier than ${currentYear}`);
                this.value = "";
                endInput.value = "";
                return;
            }

            // Set end date min = start date + 8 months
            const minEnd = new Date(startDate);
            minEnd.setMonth(minEnd.getMonth() + MIN_MONTHS);
            endInput.min = minEnd.toISOString().split("T")[0];

            // Max end date = start date + 1 year
            const maxEnd = new Date(startDate);
            maxEnd.setFullYear(maxEnd.getFullYear() + 1);
            endInput.max = maxEnd.toISOString().split("T")[0];

            endInput.value = "";
        });

        endInput.addEventListener("change", function() {
            if (!startInput.value || !endInput.value) return;

            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);

            const monthsDiff = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());

            if (monthsDiff < MIN_MONTHS) {
                alert(`End date must be at least ${MIN_MONTHS} months after start date.`);
                endInput.value = "";
            }
        });
    }

    // ===== UPDATE MODALS MIN 8 MONTHS RULE =====
    document.querySelectorAll('[id^="updateModal-"]').forEach(modal => {
        const startInput = modal.querySelector("input[name='start_date']");
        const endInput = modal.querySelector("input[name='end_date']");
        if (!startInput || !endInput) return;

        startInput.min = `${currentYear}-01-01`;

        startInput.addEventListener("change", function () {
            if (!this.value) return;

            const startDate = new Date(this.value);
            const minAllowed = new Date(`${currentYear}-01-01`);

            if (startDate < minAllowed) {
                alert(`Start date cannot be earlier than ${currentYear}`);
                this.value = "";
                endInput.value = "";
                return;
            }

            // Set end date min = start date + 8 months
            const minEnd = new Date(startDate);
            minEnd.setMonth(minEnd.getMonth() + MIN_MONTHS);
            endInput.min = minEnd.toISOString().split("T")[0];

            // Max end date = start date + 1 year
            const maxEnd = new Date(startDate);
            maxEnd.setFullYear(maxEnd.getFullYear() + 1);
            endInput.max = maxEnd.toISOString().split("T")[0];

            endInput.value = "";
        });

        endInput.addEventListener("change", function () {
            if (!startInput.value || !endInput.value) return;

            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);

            const monthsDiff = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth());

            if (monthsDiff < MIN_MONTHS) {
                alert(`End date must be at least ${MIN_MONTHS} months after start date.`);
                endInput.value = "";
            }
        });
    });
});
</script>

@endsection
