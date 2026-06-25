@section('body-style', 'min-height: 100vh; background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);')

@include('partials.modal')
@include('partials.header')

<div class="container-lg min-vh-100 d-flex justify-content-center align-items-center px-3">

    <main class="login-card w-100 w-md-75 w-lg-50 border-dark border-5 border-end border-bottom rounded shadow bg-white p-2 py-3  " >

        <a href="/homepage" class="btn btn-danger mb-3 px-4">
            Back
        </a>

      
        <img src="{{ asset('images/logo.webp') }}" 
             alt="SNHS Logo"
             class="img-fluid d-block mx-auto mb-3" 
             style="max-width: 150px; height: auto;">

        <!-- Title -->
        <section class="text-center mt-2">
            <h3 class="fw-bold mb-0">Attendance Monitoring System</h3>
            <h3 class="fw-bold">Admin & Adviser Login</h3>
        </section>

        <!-- Login Form -->
        <section class="d-flex flex-column align-items-center mt-3 py-3 px-5">
            <form class="w-100 w-md-75" action="/login/process" method="post">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-bold">Email</label>
                    <input type="email" name="email" id="email" class="form-control border-dark" placeholder="Enter Email" required>
                </div>

                <div class="mb-5">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <input type="password" name="password" id="password" class="form-control border-dark" placeholder="Enter Password" required>
                </div>

                <button type="submit" class="btn btn-primary w-50 w-md-50 py-2 text-center d-block mx-auto mb-3">
                    Login
                </button>
            </form>
        </section>
    </main>

</div>

@include('partials.footer')

<!-- Responsive Styles -->
<style>
/* Default (desktop/tablet) look */
.login-card {
    transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .login-card {
        width: 90% !important;
        padding: 2rem 1.5rem !important;
        border-width: 3px !important;
    }

    .login-card img {
        max-width: 100px !important;
    }

    .login-card h3 {
        font-size: 1.2rem !important;
    }

    .login-card form {
        width: 100% !important;
    }

    .login-card button {
        width: 100% !important;
        font-size: 1rem !important;
        padding: 0.8rem !important;
    }

    .btn-danger {
        font-size: 0.9rem !important;
        padding: 0.4rem 1rem !important;
    }
}

/* Larger screens (desktop wide view) */
@media (min-width: 1200px) {
    .login-card {
        width: 40% !important;
    }
}
</style>
