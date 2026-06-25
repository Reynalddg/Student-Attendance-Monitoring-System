@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');

    $strands = [
    "Academic Track" => [
        "Accountancy, Business and Management Strand",
        "General Academic Strand",
        "Humanities and Social Sciences Strand"
    ],
    "TVL Track" => [
        "Computer System Servicing Strand",
        "Food and Beverage Services | Bread and Pastry Production Strand",
        "Agricultural Production Strand",
        "Shielded Metal Arc Welding Strand"
    ]
];
@endphp



<div class="container-lg">
    <section class="mt-2">
         @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Tracks and Strands Dashboard</h3>
            @else
                <h3 class="fw-bold"> Tracks and Strands Dashboard</h3>
            @endif
        <hr id="hr">
    </section>

    <section class="mt-5 d-flex justify-content-between align-items-center px-4">
         @if ($isArchivedRoute)
             <form action="{{ route('tracks.search') }}" method="get" class="d-flex align-items-center position-relative">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm me-2 py-2 border-dark"
                    placeholder="Search Archived Track & Strand" style="text-transform: capitalize;">
            </form>
         @else
             <form action="{{ route('tracks.search') }}" method="get" class="d-flex align-items-center position-relative">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm me-2 py-2 border-dark"
                    placeholder="Search Track & Strand" style="text-transform: capitalize;">
            </form>
         @endif

           @if ($isArchivedRoute)
             
         @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa-solid fa-graduation-cap me-1"></i> Add Track & Strand 
        </button>

         @endif

    </section>

    <section>
        <table class="table table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Track</th>
                    <th>Strand</th>
                
                    @if ($isArchivedRoute)
                            <th>Date Archived</th>
                    @endif
                    
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="trackTableBody">
                @forelse ($tracks as $ts)
                <tr>
                    <td>{{ $ts->track }}</td>
                    <td>{{ $ts->strand }}</td>
                  
                    @if ($isArchivedRoute)
                        <td>{{ $ts->date_archived }}</td>
                        
                    @endif

                    <td>
                        @if ($isArchivedRoute)
                             <form action="{{ route('tracks.restore',$ts->track_strand_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $ts->track_strand_id }}"><i class="fa-solid fa-pen-to-square"></i> Update</button>
                        <button type="button" class="btn btn-danger btn-sm mt-1"
                            data-bs-toggle="modal" data-bs-target="#archivedModal-{{ $ts->track_strand_id }}"><i class="fa-solid fa-box-archive"></i> Archive </button>
                        @endif
                    </td>
                </tr>
                  @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('tracks.archived'))
                                    No Archived Tracks Found
                            @else
                                    No Tracks Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
            </tbody>
        </table>
        {{ $tracks->links('pagination::bootstrap-5') }}
    </section>

      <section class="d-flex justify-content-end align-items-center px-4 mt-5">
                <div class="d-flex gap-2">
             @if(request()->routeIs('tracks.archived'))
                <a href="{{ route('tracks') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Tracks</a>
            @else
                <a href="{{ route('tracks.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Tracks</a>
            @endif
                </div>
        </section>

     {{--Update MOdal--}}
        @foreach ($tracks as $track)

<div class="modal fade" id="updateModal-{{ $track->track_strand_id }}" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="updateModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="/track/{{ $track->track_strand_id }}" method="post">
                @method('PUT')
                @csrf
                <div class="modal-header bg-warning text-white">
                    <h3 class="modal-title fs-5" id="updateModalLabel">Update Track & Strand</h3>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Track <span class="text-danger fw-bold" >*</span></label>
                        <select name="track" class="form-select border-dark track-select" required>
                            <option value="">Select Track</option>
                            <option value="Academic Track" {{ $track->track == 'Academic Track' ? 'selected' : '' }}>Academic Track</option>
                            <option value="TVL Track" {{ $track->track == 'TVL Track' ? 'selected' : '' }}>Technical-Vocational-Livelihood Track</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Strand <span class="text-danger fw-bold" >*</span></label>
                       <select name="strand" class="form-select border-dark strand-select" required>
                            <option value="">Select Strand</option>
                            @foreach($strands[$track->track] as $strandOption)
                                <option value="{{ $strandOption }}" {{ $track->strand == $strandOption ? 'selected' : '' }}>
                                    {{ $strandOption }}
                                </option>
                            @endforeach
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

            {{--Archived MOdal--}}
        <div class="modal fade" id="archivedModal-{{ $track->track_strand_id}}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="archivedModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/track/{{ $track->track_strand_id}}" method="post">
                        @method('delete')
                        @csrf
                    
                        <div class="modal-header bg-danger text-white">
                            <h3 class="modal-title fs-5">Archived Strand</h3>
                        </div>
                        <div class="modal-body">
                            <h5>Are you sure you want to archived this track & strand?</h5>
                            <p class="fw-bold mt-3">Track: {{$track->track}}</p>
                            <p class="fw-bold mt-3">Strand: {{$track->strand}}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Archived </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        @endforeach

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/add/track" method="post">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add Track & Strand</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Track <span class="text-danger fw-bold" >*</span></label>
                            <select name="track" id="trackSelect" class="form-select border-dark" required>
                                <option value="">Select Track</option>
                                <option value="Academic Track">Academic Track</option>
                                <option value="TVL Track">Technical-Vocational-Livelihood Track</option>
                              
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Strand <span class="text-danger fw-bold" >*</span></label>
                            <select name="strand" id="strandSelect" class="form-select border-dark" required>
                                <option value="">Select Strand</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Track & Strand</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const strands = {
        "Academic Track": [
            "Accountancy, Business and Management Strand",
            "General Academic Strand",
            "Humanities and Social Sciences Strand"
        ],
        "TVL Track": [
            "Computer System Servicing Strand",
            "Food and Beverage Services | Bread and Pastry Production Strand",
            "Agricultural Production Strand",
            "Shielded Metal Arc Welding Strand"
        ]
    };

   
    const addTrack = document.getElementById('trackSelect');
    const addStrand = document.getElementById('strandSelect');

    if (addTrack && addStrand) {
        addTrack.addEventListener('change', function() {
            const selectedTrack = this.value;
            addStrand.innerHTML = '<option value="">Select Strand</option>';
            if (strands[selectedTrack]) {
                strands[selectedTrack].forEach(strand => {
                    const option = document.createElement('option');
                    option.value = strand;
                    option.textContent = strand;
                    addStrand.appendChild(option);
                });
            }
        });
    }


    document.querySelectorAll('.track-select').forEach(trackSelect => {
        const strandSelect = trackSelect.closest('.modal-content').querySelector('.strand-select');

        trackSelect.addEventListener('change', function() {
            const selectedTrack = this.value;
            strandSelect.innerHTML = '<option value="">Select Strand</option>';
            if (strands[selectedTrack]) {
                strands[selectedTrack].forEach(strand => {
                    const option = document.createElement('option');
                    option.value = strand;
                    option.textContent = strand;
                    strandSelect.appendChild(option);
                });
            }
        });
    });
});

    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            let query = $(this).val();
            let searchUrl = "{{ $isArchivedRoute ? route('tracks.archived.search') : route('tracks.search') }}";

            $.ajax({
                url: searchUrl, 
                type: "GET",
                data: { search: query },
                success: function(response) {
                    $('#trackTableBody').html($(response).find('#trackTableBody').html());
                },
                error: function() {
                    console.log('Error loading track.');
                }
            });
        });
    });
</script>



@endsection
