@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp
    <div class="container-lg">

        <section class="mt-2">
            @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Advisers Dashboard</h3>
            @else
                <h3 class="fw-bold"> Advisers Dashboard</h3>
            @endif
            <hr id="hr">
        </section>

        <section class="d-flex justify-content-between align-items-center px-4 mt-5">
            @if ($isArchivedRoute)
                 <form action="{{ route('advisers.search') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                    <i class="fa-solid fa-search" id="searchIcon"></i>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search Archived Adviser" style="text-transform: capitalize;">
                </form>
            @else
                 <form action="{{ route('advisers.search') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                    <i class="fa-solid fa-search" id="searchIcon"></i>
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search Adviser" style="text-transform: capitalize;">
                </form>
            @endif

              @if ($isArchivedRoute)
             
         @else
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-user-plus me-1"></i> Add Adviser
            </button>

         @endif

           
            
        </section>

        <section>
            <table class="table table-hover mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Date Created</th>
                         @if($isArchivedRoute)
                        <th>Date Archived</th>
                    @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="adviserTableBody">
                    @forelse ($advisers as $adviser)
                        <tr>
                            <td class="text-truncate" style="max-width:120px;">
                                @if($adviser->image)
                                    <img src="{{ asset('storage/' . $adviser->image) }}" alt="Adviser Image" width="60" height="60" style="object-fit: cover; border-radius:15%; margin:2px;">
                                @else
                                    No Image
                                @endif
                            </td>
                            <td>{{ $adviser->first_name }} {{ $adviser->middle_name }} {{ $adviser->last_name }} {{ $adviser->suffix}}</td>
                            <td>{{ $adviser->phone_number }}</td>
                            <td>{{ $adviser->email }}</td>
                            <td>{{ $adviser->date_created }}</td>

                            @if($isArchivedRoute)
                                <td>{{ $adviser->date_archived }}</td>
                            @endif

                            <td>
                                @if($isArchivedRoute)
                                    <form action="{{ route('advisers.restore', $adviser->user_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $adviser->user_id }}"> <i class="fa-solid fa-pen-to-square"></i> Update</button>
                                    <button type="button" class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#archivedModal-{{ $adviser->user_id }}"><i class="fa-solid fa-box-archive"></i> Archive</button>
                                @endif
                            </td>
                        </tr>
                     @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('advisers.archived'))
                                    No Archived Advisers Found
                            @else
                                    No Advisers Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $advisers->links('pagination::bootstrap-5') }}
        </section>


        <section class="d-flex justify-content-end align-items-center px-4 mt-5">
                <div class="d-flex gap-2">
             @if(request()->routeIs('advisers.archived'))
                <a href="{{ route('advisers') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Advisers</a>
            @else
                <a href="{{ route('advisers.archived') }}" class="btn btn-secondary btn-danger"><i class="fa-solid fa-box-archive me-1"></i> View Archived Advisers</a>
            @endif
                </div>
        </section>

        @foreach ($advisers as $adviser)

            {{--Update Modal--}}
            <div class="modal fade" id="updateModal-{{ $adviser->user_id}}" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="/adviser/{{$adviser->user_id}}" method="post" enctype="multipart/form-data">
                            @method('put')
                            @csrf

                            <div class="modal-header bg-warning text-white">
                                <h5 class="fw-bold fs-5">Update Adviser</h5>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="" class="form-label fw-bold">First Name  <span class="text-danger fw-bold" >*</span></label>
                                    <input type="text" name="first_name" id="" class="form-control border-dark " required value="{{ $adviser->first_name }}">
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Middle Name</label>
                                    <input type="text" name="middle_name" id="" class="form-control border-dark" value="{{$adviser->middle_name}}" >
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Last Name  <span class="text-danger fw-bold" >*</span></label>
                                    <input type="text" name="last_name" id="" class="form-control border-dark" value="{{$adviser->last_name}}" required>
                                </div>

                                     <div class="mb-3">
                                <label for="" class="form-label fw-bold">Suffix</label>
                                <select name="suffix" id="" class="form-select border-dark">
                                    <option value="" selected disabled>Select Suffix</option>
                                     <option value="">None</option> 
                                    <option value="Jr" {{ $adviser->suffix == 'Jr' ? 'selected' : ''}}>Jr.</option>
                                    <option value="Sr" {{ $adviser->suffix == 'Sr' ? 'selected' : ''}}>Sr.</option>
                                    <option value="II" {{ $adviser->suffix == 'II' ? 'selected' : ''}}>II</option>
                                    <option value="III" {{ $adviser->suffix == 'III' ? 'selected' : ''}}>III</option>
                                    <option value="IV" {{ $adviser->suffix == 'IV' ? 'selected' : ''}}>IV</option>
                                </select>
                            </div>

                                 <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Phone Number  <span class="text-danger fw-bold" >*</span></label>
                                    <input type="tel" name="phone_number" id="" class="form-control border-dark" value="{{$adviser->phone_number}}" required>
                                </div>

                                
                        
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Email  <span class="text-danger fw-bold" >*</span></label>
                                <input type="email" name="email" id="" class="form-control border-dark" value="{{ $adviser->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Password </label>
                                <input type="password" name="password" id="" class="form-control border-dark">
                                <small class="text-danger">  <i class="fa-solid fa-info-circle me-1"></i> Leave this blank if you don't want to change the password</small>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Image</label>
                                <input type="file" name="image" id="" class="form-control border-dark">
                                <small class="text-danger ">  <i class="fa-solid fa-info-circle me-1"></i> Leave this blank if you don't want to change the image</small>
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

            {{--archived Modal--}}
            <div class="modal fade" data-bs-backdrop="static" id="archivedModal-{{$adviser->user_id}}">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="/adviser/{{$adviser->user_id}}" method="post">
                            @method('delete')
                            @csrf
                        
                            <div class="modal-header bg-danger text-white">
                                <h5 class="fw-bold fs-5">Archived Adviser</h5>
                            </div>

                            <div class="modal-body">
                                <h5>Are you sure you want to archived this adviser? </h5>
                                <p class="fw-bold"> Name: {{$adviser->first_name}} {{$adviser->middle_name}} {{$adviser->last_name}}</p>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-warning">Archived</button>
                                <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        @endforeach

        {{--Add mOdal--}}
        <div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/add/adviser" method="post" enctype="multipart/form-data">
                        @csrf
                    
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fs-5">
                                Add Adviser
                            </h5>
                        </div>

                        <div class="modal-body">
                             <div class="mb-3">
                                <label for="" class="form-label fw-bold">First Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="first_name" id="" class="form-control border-dark" placeholder="e.g. Juan" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Middle Name </label>
                                <input type="text" name="middle_name" id="" class="form-control border-dark" placeholder="e.g. Santos">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Last Name <span class="text-danger fw-bold" >*</span></label>
                                <input type="text" name="last_name" id="" class="form-control border-dark" placeholder="e.g. Dela Cruz" required>
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
                                <label for="" class="form-label fw-bold">Phone Number <span class="text-danger fw-bold" >*</span></label>
                                <input type="tel" name="phone_number" id="" class="form-control border-dark" placeholder="e.g. 09971234567" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Email <span class="text-danger fw-bold" >*</span></label>
                                <input type="email" name="email" id="" class="form-control border-dark" placeholder="e.g. juandelacruz@gmail.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Image</label>
                                <input type="file" name="image" id="" class="form-control border-dark">
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add Adviser</button>
                            <button type="modal" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        



    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        let query = $(this).val();
        let searchUrl = "{{ $isArchivedRoute ? route('advisers.archived.search') : route('advisers.search') }}";

        $.ajax({
            url: searchUrl,
            type: "GET",
            data: { search: query },
            success: function(response) {
                $('#adviserTableBody').html($(response).find('#adviserTableBody').html());
            },
            error: function() {
                console.log('Error loading advisers.');
            }
        });
    });
});
        </script>
@endsection