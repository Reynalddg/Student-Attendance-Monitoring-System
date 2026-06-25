@extends('dashboard')
@section('content')

    <div class="container-lg">
        <section class="mt-2">
            <h3 class="fw-bold">
                Strand Dashboard
            </h3>
            <hr id="hr">
        </section>

        <section class="mt-5 d-flex justify-content-between align-items-center px-4">
            <form action="{{ route('strands.search') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    class="form-control form-control-sm me-2 py-2 border-dark"
                    placeholder="Search..." style="text-transform: capitalize;">
                <button class="btn btn-primary btn-sm" id="searchButton">Search</button>
            </form>

        @if ($isArchivedRoute)
             
         @else
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add Strand
                </button>

         @endif

        </section>

        <section>
            <table class="table table-hover mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Strand</th>
                        <th>Track</th>
                        <th>Date Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($strands as $strand)
                        <tr>
                            <td>{{$strand->name}}</td>
                            <td> {{ $strand->track?->name ?? '-'}}</td>
                            <td> {{ $strand->date_created }}</td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $strand->strand_id }}">Update</button>
                                <button class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#archivedModal-{{ $strand->strand_id}}"> Archived</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
              {{ $strands->links('pagination::bootstrap-5') }}

        </section>

        {{--Update Modal--}}
        @foreach ($strands as $strand)
            <div class="modal fade" id="updateModal-{{ $strand->strand_id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="updateModalLabel">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="/strand/{{ $strand->strand_id }}" method="post">
                             @method('PUT')
                            @csrf
                            <div class="modal-header bg-warning text-white">
                                <h3 class="modal-title fs-5" id="updateModalLabel">Update Strand</h3>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="" class="form-label">Name</label>
                                    <input type="text" name="name" id="" class="form-control   border-dark" value=" {{ $strand->name }}">
                                </div>
                    
                             <div class="mb-3"> 
                                <label for="" class="form-label">Track</label> 
                                <select name="track_id" id="" class="form-select border-dark" required> 
                                    <option value="" selected disabled> Select Track</option> 
                                    @foreach($tracks as $track) 
                                    <option value=" {{ $track->track_id}}" {{$track->track_id == $strand->track_id ? 'selected': ''}}> 
                                        {{ $track->name}} 
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
        <div class="modal fade" id="archivedModal-{{ $strand->strand_id}}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="archivedModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/strand/{{$strand->strand_id}}" method="post">
                        @method('delete')
                        @csrf
                    
                        <div class="modal-header bg-danger text-white">
                            <h3 class="modal-title fs-5">Archived Strand</h3>
                        </div>
                        <div class="modal-body">
                            <h5>Are you sure you want to archived this strand?</h5>
                            <p class="fw-bold mt-3">Name: {{$strand->name}}</p>
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
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="/add/strand" method="post">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-5" id="addModalLabel">Add Strand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                            <label for="" class="form-label">Track</label>
                            <select name="track_id" id="" class="form-select border-dark" required>
                                <option value="" selected disabled> Select Track</option>
                                @foreach($tracks as $track)
                                    <option value="{{ $track->track_id }}">
                                        {{ $track->name }}
                                    </option>
                                @endforeach
                                </select>
                        </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control border-dark" style="text-transform: capitalize;" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Strand</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection