@extends('teacherDashboard')
@section('content')

    <div class="container-lg">

        <section class="mt-2">
            <h3 class="fw-bold">Guardians Dashboard</h3>
            <hr id="hr">
        </section>

        <section class="d-flex justify-content-between align-items-center px-4 mt-5">
             <form action="{{ route('guardians.search') }}" method="get" class="d-flex align-items-center position-relative" id="searchContainer">
                <i class="fa-solid fa-search" id="searchIcon"></i>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    class="form-control form-control-sm me-2 py-2 border-dark" placeholder="Search..." style="text-transform: capitalize;">
                <button class="btn btn-primary btn-sm" id="searchButton">Search</button>
            </form>
        </section>

        <section>
            <table class="table table-hover mt-4 sticky-header-table">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Student</th>
                        <th>Gender</th>
                        <th>Phone Number</th>
                        <th>Relation</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($guardians as $guardian)
                        <tr>
                            <td>{{ $guardian->first_name }} {{ $guardian->middle_name }} {{ $guardian->last_name }}</td>
                            <td>
                                @php
                                    $sectionId = session('adviser_section_id');
                                    $studentsInSection = $guardian->studentsInSection($sectionId);
                                @endphp

                                @if($studentsInSection->count() > 0)
                                    @foreach($studentsInSection as $student)
                                        {{ $student->first_name }}        
                                        {{ $student->middle_name ? strtoupper(substr($student->middle_name, 0, 1)) . '.' : '' }}
                                        {{ $student->last_name }} <br>
                                    @endforeach
                                @else
                                    <span class="text-muted">No students</span>
                                @endif
                            </td>
                            <td>{{ $guardian->gender }}</td>
                            <td>{{ $guardian->phone_number }}</td>
                            <td>{{ $guardian->relation }}</td>
                            <td>{{ $guardian->date_created }}</td>
                           
                        </tr>
                    @endforeach
                </tbody>
            </table>
           
        </section>

         <div class="d-flex justify-content-center mt-3 ms-5 mt-5">
            {{ $guardians->links('pagination::bootstrap-5') }}
        </div>

        @foreach ($guardians as $guardian)
            
            {{--Update modal--}}
            <div class="modal fade" id="updateModal-{{ $guardian->guardian_id }}" tabindex="-1">
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
                                    <label for="" class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control border-dark" value="{{ $guardian->first_name }}">
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control border-dark" value="{{ $guardian->middle_name }}">
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control border-dark" value="{{ $guardian->last_name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-clabel">Gender</label>
                                    <select name="gender" class="form-select border-dark" required>
                                        <option value="" selected disabled>Select Gender</option>
                                        <option value="Male" {{ $guardian->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $guardian->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">Phone Number</label>
                                    <input type="tel" name="phone_number" class="form-control border-dark" value="{{ $guardian->phone_number }}" required>
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
            <div class="modal fade" id="archivedModal-{{ $guardian->guardian_id }}" tabindex="-1">
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
                                <p class="fw-bold">Name: {{ $guardian->first_name }} {{ $guardian->middle_name }} {{ $guardian->last_name }}</p>
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
        <div class="modal fade" id="addModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="/add/guardian" method="post">
                        @csrf
                        
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fs-5">
                                Add Guardian
                            </h5>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="" class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control border-dark" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control border-dark">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control border-dark" required>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Gender</label>
                                <select name="gender" class="form-select border-dark" required>
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control border-dark" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection
