@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp

    <div class="container-lg">

        <section class="mt-2">
             @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Sections Dashboard</h3>
            @else
                <h3 class="fw-bold"> Sections Dashboard</h3>
            @endif
            <hr id="hr">
        </section>

        <section class="mt-5 d-flex justify-content-between align-items-center px-4">
            @if ($isArchivedRoute)
                 <form action="{{ route('sections.search') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                    <i class="fa-solid fa-search" id="searchIcon"></i>
                    <input type="text"  name="search" id="searchInput" value="{{ request('search') }}"
                        class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search Archived Section" style="text-transform: capitalize;">
                </form>
            @else
                 <form action="{{ route('sections.search') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                    <i class="fa-solid fa-search" id="searchIcon"></i>
                    <input type="text"  name="search" id="searchInput" value="{{ request('search') }}"
                        class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search Section" style="text-transform: capitalize;">
                </form>
            @endif

                 @if ($isArchivedRoute)
             
         @else
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                 <i class="fa-solid fa-graduation-cap me-1"> </i> Add Section
            </button>
         @endif

           

        </section>

        <section>
            <table class="table table-hover mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>Grade & Section</th>
                        <th>Adviser</th>
                        <th>Track & Strand</th>
                        @if ($isArchivedRoute)
                            <th>Date Archived</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="sectionTableBody">
                    @forelse ($sections as $section)
                        <tr>
                            <td> {{$section->grade_level}}  {{$section->section_name}}</td>
                            <td> {{$section->adviser?->first_name. ' '. $section->adviser?->middle_name. ' '. $section->adviser?->last_name ?? '-'}}</td>
                            <td> {{ $section->track_strand?->track }} - {{ $section->track_strand?->strand?? '--' }}</td>
                            @if ($isArchivedRoute)
                                <td>{{ $section->date_archived }}</td>
                            @endif
                            <td>
                                @if ($isArchivedRoute)
                                     <form action="{{ route('sections.restore',$section->section_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $section->section_id }}"><i class="fa-solid fa-pen-to-square"></i> Update</button>
                                    <button class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#archivedModal-{{$section->section_id}}"><i class="fa-solid fa-box-archive"></i> Archived</button>
                           
                                @endif
                            </td>
                        </tr>
                      @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('tracks.archived'))
                                    No Archived Section Found
                            @else
                                    No Section Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
             {{ $sections->links('pagination::bootstrap-5')}}
        </section>

         <section class="d-flex justify-content-end align-items-center px-4 mt-5">
                <div class="d-flex gap-2">
             @if(request()->routeIs('sections.archived'))
                <a href="{{ route('sections') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Sections</a>
            @else
                <a href="{{ route('sections.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Sections</a>
            @endif
                </div>
        </section>

        @foreach ($sections as $section)
            
        {{--Update Modal--}}
        <div class="modal fade" id="updateModal-{{$section->section_id}}"  data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/section/{{$section->section_id}}" method="post">
                        @method('put')
                        @csrf

                        <div class="modal-header bg-warning text-white">
                            <h3 class="modal-title fs-5">Update Section</h3>
                        </div>

                        
                        <div class="modal-body">

                             <div class="mb-3">
                                <label for="" class="form-label fw-bold">Grade Level <span class="text-danger fw-bold" >*</span></label>
                                <select name="grade_level" id="" class="form-select border-dark">
                                    <option value="" selected disabled> Select Gender</option>
                                    <option value="11" {{ $section->grade_level == '11' ? 'selected' : '' }}>11</option>
                                    <option value="12" {{ $section->grade_level == '12' ? 'selected' : '' }}>12</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Section Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="section_name" id="" class="form-control border-dark" value="{{ $section->section_name }}">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Adviser <span class="text-danger fw-bold" >*</span></label>
                                <select name="adviser_id" id="" class="form-select border-dark">
                                    <option value="" selected disabled> Select Adviser</option>
                                    @foreach ($advisers as $adviser)
                                   <option value="{{ $adviser->user_id }}" {{ $adviser->user_id == $section->user_id ? 'selected': '' }}> 
                                    {{ $adviser->full_name }}
                                 </option>  
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3"> 
                                <label for="" class="form-label fw-bold">Track & Strand <span class="text-danger fw-bold" >*</span></label> 
                                <select name="track_strand_id" id="" class="form-select border-dark" required> 
                                    <option value="" selected disabled> Select Track & Strand</option> 
                                    @foreach($track_strands as $track_strand) 
                                    <option value=" {{ $track_strand->track_strand_id}}" {{$track_strand->track_strand_id == $section->track_strand_id ? 'selected': ''}}> 
                                        {{ $track_strand->track}} - {{ $track_strand->strand}}
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
        <div class="modal fade" id="archivedModal-{{$section->section_id}}" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/section/{{ $section->section_id}}" method="post">
                        @method('delete')
                        @csrf
                    
                        <div class="modal-header bg-danger text-white">
                            <h3 class="modal-title fs-5">Archived Section</h3>
                        </div>

                        <div class="modal-body">
                                <h5>Are you sure you want to archive this section?</h5>
                                <p class="fw-bold">Grade and Section: {{$section->grade_level}} - {{$section->section_name}}</p>
                                <p class="fw-bold">Adviser: {{$section->adviser?->first_name. ' '. $section->adviser?->middle_name. ' '. $section->adviser?->last_name ?? '-'}}</p>
                                <p class="fw-bold">Strand: {{ $section->track_strand?->track }} - {{ $section->track_strand?->strand?? '--' }}</p>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Archived</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach



        {{--Add Modal--}}
        <div class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addModalLabel">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <form action="/add/section" method="post">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fs-5" id="addModalLabel">Add Section</h5>
                        </div>
                        <div class="modal-body">

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Grade Level <span class="text-danger fw-bold" >*</span></label>
                                <select name="grade_level" id="" class="form-select border-dark">
                                    <option value="" selected disabled>Select Grade Level</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Section Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="section_name" id="" class="form-control border-dark" placeholder="e.g. Magsaysay"  required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adviser_id" class="form-label fw-bold">Adviser <span class="text-danger fw-bold" >*</span></label>
                                <select name="adviser_id" id="" class="form-select border-dark" required>
                                    <option value="" selected disabled> Select Adviser</option>
                                    @foreach($advisers as $adviser)
                                        <option value="{{ $adviser->user_id }}">
                                            {{ $adviser->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Track & Strand <span class="text-danger fw-bold" >*</span></label>
                                <select name="track_strand_id" id="" class="form-select border-dark" required>
                                    <option value="" selected disabled> Select Track & Strand</option>
                                    @foreach($track_strands as $track_strand)
                                        <option value="{{ $track_strand->track_strand_id }}">
                                            {{ $track_strand->track}} - {{ $track_strand->strand}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add Section</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                        </div>
                </div>
            </div>
        </div>


    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function(){
                let query = $(this).val();
                let searchUrl = "{{ $isArchivedRoute ? route('sections.archived.search') : route('sections.search') }}";

                $.ajax({
                    url:searchUrl,
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#sectionTableBody').html($(response).find('#sectionTableBody').html());
                    },
                    error: function() {
                        console.log('Error loading advisers.');
                    }

                });
            });
        });
    </script>

@endsection