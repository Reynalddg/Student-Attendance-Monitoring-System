@include('partials.header')


<nav class="navbar sticky-top d-md-none" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
    <div class="container-fluid">
        <button class="navbar-toggler border-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenuMobile" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
            <span class="navbar-toggler-icon"></span>
        </button>
        <span class="navbar-brand ms-2">Dasboard</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenuMobile" class="offcanvas offcanvas-start d-md-none fw-bold" tabindex="-1" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
           <div class="offcanvas-header">
                <h5 class="offcanvas-header">Menu</h5>
                <button class="btn-close text-reset" type="button" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                @include('partials.teachersidebar-content')
            </div>
        </nav>

        <!-- Desktop Sidebar -->
     <div class="col-md-3 col-lg-2 d-none d-md-block text-dark fw-bold sidebar p-3 min-vh-100" style="background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);">
        @include('partials.teachersidebar-content')
    </div>

    <main class="col-md-9 col-lg-10 px-md-4 mt-4">
        @yield('content')
    </main>
    </div>
</div>

<!-- Change Password Modal -->

<div class="modal fade" id="changePasswordModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg" style="border-radius: 10px;">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="bi bi-key-fill me-2"></i> Change Password
                </h5>
               
            </div>

            <form action="{{ route('users.changePassword') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-bold">New Password <span class="text-danger fw-bold" >*</span></label>
                        <input type="password" class="form-control border-dark @error('new_password') is-invalid @enderror"
                               id="new_password" name="new_password" required minlength="6">
                        @error('new_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label fw-bold">Confirm New Password <span class="text-danger fw-bold" >*</span></label>
                        <input type="password" class="form-control border-dark @error('new_password_confirmation') is-invalid @enderror"
                               id="new_password_confirmation" name="new_password_confirmation" 
                               required minlength="6"
                               oninput="this.setCustomValidity(this.value !== document.getElementById('new_password').value ? 'Passwords do not match' : '')">
                        @error('new_password_confirmation')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning text-white fw-semibold">
                        <i class="bi bi-check2-circle"></i> Update Password
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>



@include('partials.footer')
@include('partials.modal')