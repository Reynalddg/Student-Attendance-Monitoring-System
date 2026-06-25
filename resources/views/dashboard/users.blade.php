@extends('dashboard')
@section('content')
@php
    use Illuminate\Support\Str;
    $isArchivedRoute = Str::contains(Route::currentRouteName(), 'archived');
@endphp

    <div class="container-lg" >
        <section class="mt-2 ">
             @if ($isArchivedRoute)
                <h3 class="fw-bold"> <span class="fw-bold text-danger">Archived</span> Admins Dashboard</h3>
            @else
                <h3 class="fw-bold"> Admins Dashboard</h3>
            @endif
            <hr id="hr" >
        </section>
        
        <br>
         
<section class="mt-4 d-flex justify-content-between align-items-center px-4">

    @if ($isArchivedRoute)
        <form method="GET" action="{{ route('users.search') }}" class="d-flex align-items-center position-relative" id="searchContainer">
            <i class="fa-solid fa-search ms-1" id="searchIcon"></i>
            <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" 
                class="form-control form-control-sm me-4 py-2 border-dark" 
                placeholder="Search Archived Admin" style="text-transform: capitalize;">
    </form>
    @else
        <form method="GET" action="{{ route('users.search') }}" class="d-flex align-items-center position-relative" id="searchContainer">
            <i class="fa-solid fa-search ms-1" id="searchIcon"></i>
            <input type="text" name="search" id="searchInput" value="{{ $search ?? '' }}" 
                class="form-control form-control-sm me-4 py-2 border-dark" 
                placeholder="Search admin" style="text-transform: capitalize;">
    </form>
    @endif

        @if ($isArchivedRoute)
             
         @else
            <button type="button" class="btn btn-primary rounded-start rounded-end" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-user-plus me-1"></i> Add New Admin
            </button>

         @endif


</section>


        <section class="">
            <table class="table table-hover mt-3 ">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        @if ($isArchivedRoute)
                            <th>Date Archived</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    @forelse($users as $user)
                        <tr>

                            <td class="text-truncate" style="max-width:120px;">
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="Admin Image" width="60" height="60" style="object-fit: cover; border-radius:15%; margin:2px;">
                            @else
                                No Image
                            @endif
                        </td>

                            <td> {{$user->first_name}} {{$user->middle_name}} {{$user->last_name}} {{ $user->suffix}}</td>
                            <td>
                                {{$user->email}}
                            </td>
                          
                                @if ($isArchivedRoute)
                                    <td>{{ $user->date_archived }}</td>
                                @endif
                            
                            <td>
                               @if ($isArchivedRoute)
                                    <form action="{{ route('users.restore',$user->user_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-trash-arrow-up "></i> Restore</button>
                                    </form>
                               @else
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal-{{ $user->user_id }}" data-bs-dismiss="modal"><i class="fa-solid fa-pen-to-square"></i> Update</button>
                                <button type="button" class="btn btn-danger btn-sm mt-1" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#archivedModal-{{ $user->user_id }}"><i class="fa-solid fa-box-archive"></i> Archived</button>
                               @endif
                            </td>
                        </tr>
                        
             @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                @if (request()->routeIs('tracks.archived'))
                                    No Archived Admins Found
                            @else
                                    No Admins Found
                            @endif
                        </td>

                        </tr>
                    @endforelse
                </tbody>
            </table>
           {{ $users->links('pagination::bootstrap-5') }}
        </section>
    </div>

  <section class="d-flex justify-content-end align-items-center px-4 mt-5">
                <div class="d-flex gap-2">
             @if(request()->routeIs('users.archived'))
                <a href="{{ route('users') }}" class="btn btn-success"><i class="fa-solid fa-user-check me-1"></i> View Active Admin</a>
            @else
                <a href="{{ route('users.archived') }}" class="btn btn-secondary btn-danger "><i class="fa-solid fa-box-archive me-1"></i> View Archived Admin</a>
            @endif
                </div>
        </section>

    @foreach($users as $user)

        {{--Update Modal--}}
        <div class="modal fade" id="updateModal-{{ $user->user_id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="updateModalLabel-{{ $user->user_id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/user/{{ $user->user_id }}" method="post" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf

                        <div class="modal-header bg-warning text-white">
                            <h1 class="modal-title fs-5" id="updateModalLabel-{{ $user->id }}">Update Admin</h1>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                    <label for="" class="form-label fw-bold">First Name <span class="text-danger fw-bold" >*</span></label>
                                    <input type="text" name="first_name" id="" class="form-control border-dark" required value="{{ $user->first_name }}">
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Middle Name <span class="text-danger fw-bold" >*</span></label>
                                    <input type="text" name="middle_name" id="" class="form-control border-dark" value="{{$user->middle_name}}" >
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Last Name <span class="text-danger fw-bold" >*</span></label>
                                    <input type="text" name="last_name" id="" class="form-control border-dark" value="{{$user->last_name}}" required>
                                </div>

                                  <div class="mb-3">
                                <label for="" class="form-label fw-bold">Suffix</label>
                                <select name="suffix" id="" class="form-select border-dark">
                                    <option value="" selected disabled>Select Suffix</option>
                                     <option value="">None</option> 
                                    <option value="Jr" {{ $user->suffix == 'Jr' ? 'selected' : ''}}>Jr.</option>
                                    <option value="Sr" {{ $user->suffix == 'Sr' ? 'selected' : ''}}>Sr.</option>
                                    <option value="II" {{ $user->suffix == 'II' ? 'selected' : ''}}>II</option>
                                    <option value="III" {{ $user->suffix == 'III' ? 'selected' : ''}}>III</option>
                                    <option value="IV" {{ $user->suffix == 'IV' ? 'selected' : ''}}>IV</option>
                                </select>
                            </div>

                                 <div class="mb-3">
                                    <label for="" class="form-label fw-bold">Phone Number <span class="text-danger fw-bold" >*</span></label>
                                    <input type="tel" name="phone_number" id="" class="form-control border-dark" value="{{$user->phone_number}}" required>
                                </div>

                                
                        
                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Email <span class="text-danger fw-bold" >*</span></label>
                                <input type="email" name="email" id="" class="form-control border-dark" value="{{ $user->email }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Password </label>
                                <input type="password" name="password" id="" class="form-control border-dark">
                                <small class="text-danger">  <i class="fa-solid fa-info-circle me-1"></i> Leave this blank if you don't want to change the password</small>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label fw-bold">Image</label>
                                <input type="file" name="image" id="" class="form-control border-dark">
                                <small class="text-danger "> <i class="fa-solid fa-info-circle me-1"></i>  Leave this blank if you don't want to change the image</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Update</button>
                            <button type="button" class="btn btn-danger"  data-bs-dismiss="modal">Cancel</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    
    {{--Archived Modal--}}

    <div class="modal fade" id="archivedModal-{{ $user->user_id }}" tabindex="-1" data-bs-backdrop="static" aria-labelledby="archivedModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form action="/user/{{$user->user_id}}" method="post">
                    @csrf
                    @method('delete')

                    <div class="modal-header bg-danger text-white">
                        <h1 class="modal-title fs-5" id="archivedModalLabel-{{ $user->id }}"> Archived Admin</h1>
                    </div>

                    <div class="modal-body">
                        <h5>Are you sure you want to archived this admin?</h5>
                        <p class="fw-bold mt-3">Name: {{$user->first_name}} {{$user->last_name}}</p>
                        <p class="fw-bold"> Email: {{ $user->email }}</p>
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


  {{--Add MOdal--}}
    <div class="modal fade" id="addModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/add/user" method="post" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-5">Add New Admin Account</h5>
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
                            <input type="email" name="email" id="" class="form-control border-dark" placeholder="e.g. juandelacruz@gmail.com"  required>
                        </div>

                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Image</label>
                            <input type="file" name="image" id="" class="form-control border-dark">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Admin</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
                let searchUrl = "{{ $isArchivedRoute ? route('users.archived.search') : route('users.search') }}";

                $.ajax({
                    url:searchUrl,
                    type: "GET",
                    data: { search: query },
                    success: function(response) {
                        $('#adminTableBody').html($(response).find('#adminTableBody').html());
                    },
                    error: function() {
                        console.log('Error loading advisers.');
                    }

                });
            });
        });
    </script>
    
@endsection
