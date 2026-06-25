@extends('dashboard')

@section('content')
<div class="container-lg">
    <section class="mt-2">
        <h3 class="fw-bold">Attendance Dashboard</h3>
        <hr id="hr">
    </section>

    <br>

    {{-- Filter and Add Button --}}
    <section class="mt-4 d-flex justify-content-between align-items-center px-4">
        <form method="GET" action="{{ route('attendance.filter') }}" class="d-flex align-items-center">
           <select name="section" class="form-select border-dark me-2">
                <option value="" disabled selected>Select Section</option>
                @foreach($sections as $section)
                    <option value="{{ $section->section_id }}"
                        {{ (isset($selectedSection) && $selectedSection == $section->section_id) ?  : '' }}>
                        Grade {{ $section->grade_level }} - {{ $section->section_name }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-primary">Filter</button>
        </form>

       
    </section>


    <section  class="mt-4 px-2 px-md-4 "  style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover mt-3 table-striped sticky-header-table">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Student Name</th>
                    <th>Section</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceLogs as $log)
                    <tr>
                        <td class="text-truncate" style="max-width:120px;">
                            @if($log->enrollment->student->image)
                                <img src="{{ asset('storage/' . $log->enrollment->student->image) }}" alt="Student Image" width="60" height="60" style="object-fit: cover; border-radius:15%; margin:2px;">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $log->enrollment->student->first_name }} {{ $log->enrollment->student->middle_name }} {{ $log->enrollment->student->last_name }}</td>
                        <td>
                            Grade {{ $log->enrollment->section->grade_level ?? '-' }} - {{ $log->enrollment->section->section_name ?? '-' }}
                        </td>
                       <td>{{ \Carbon\Carbon::parse($log->date_time)->format('M d, Y') }}</td> 
                        <td>{{ \Carbon\Carbon::parse($log->date_time)->format('h:i A') }}</td>  
                        <td>{{ $log->status }}</td>                                            
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No daily attendance found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

      
    </section>

      <div class="mt-4 px-4 flex-wrap fw-bold mb-4">
            {{ $attendanceLogs->links('pagination::bootstrap-5') }}
        </div>
</div>



@endsection
