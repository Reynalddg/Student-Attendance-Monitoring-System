@section('body-style', 'margin:0; padding:0; min-height:100vh; background:#fff;')

@include('partials.header')
@include('partials.modal')

<style>

.hero-btn {
    border-radius: 35px;
    padding: 14px 38px;
    font-weight: 600;
}
.hero-btn-primary {
    background: #ffffff;
    color: #003366;
}
.hero-btn-primary:hover {
    background: #dfe9ff;
}
.hero-btn-outline {
    border: 2px solid white;
    color: white;
}
.hero-btn-outline:hover {
    background: white;
    color: #003366;
}




</style>

<nav class="navbar navbar-expand-lg navbar-dark" >
  <div class="container-fluid">

    <!-- LOGO + SCHOOL NAME -->
    <a class="navbar-brand d-flex align-items-center" href="/">
      <img src="{{ asset('images/logo.webp') }}" class="me-2">

      <div class="d-flex flex-column lh-sm">
          <span class="fw-bold" style="font-size:18px; letter-spacing:0.5px; margin-bottom:3px">TALAVERA SENIOR </span>
          <span class="fw-bold" style="font-size:18px; letter-spacing:0.5px;">HIGH SCHOOL</span>
      </div>
    </a>

    <!-- MOBILE TOGGLER -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- NAVIGATION LINKS -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto fw-semibold me-5">
        <li class="nav-item">
           <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/scan">Scan Attendance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">User Login</a>
        </li>
      </ul>
    </div>

  </div>
</nav>



<div class="hero">
    <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between text-center text-lg-start">

        <div class="hero-content text-white flex-lg-grow-1">
            <h1 class="hero-title fw-bold text-white text-center text-lg-start">
          TSHS Attendance Management System
              </h1>
              <p class="hero-subtitle text-white">
                  QR-Code Based Student Attendance Monitoring System with SMS Notifications
              </p>

            <p class="mb-4 fw-bold">
                <span class="rotate-words">
                      <span>QR Code Attendance</span>
                      <span>Instant SMS Alerts</span>
                      <span>Monthly Attendance Reports</span>
                </span>
            </p>

            <div class="d-flex justify-content-center justify-content-lg-start gap-3 flex-wrap">
                <a href="/scan" class="btn hero-btn hero-btn-primary">Scan Attendance</a>
                <a href="#" class="btn hero-btn hero-btn-outline" data-bs-toggle="modal" data-bs-target="#loginModal">User Login</a>
            </div>
        </div>

        <div class="d-flex justify-content-center justify-content-lg-end flex-lg-shrink-0">
            <img src="{{ asset('images/picture.png') }}" class="scanning-gif img-fluid">
        </div>

    </div>
</div>

<section id="about" class="py-5" style="background:#f7f9fc;">
  <div class="container text-center">
    <h2 class="fw-bold" style="color:#003366;">About the System</h2>
   <p class="mt-3" style="max-width:700px; margin:auto; color:#333;">
        The TSHS Attendance Management System provides fast and secure QR-based student attendance tracking.
        It helps teachers monitor class attendance in real-time and sends notifications instantly to parents based on their child’s attendance status.
        The system also automatically sends alerts when a student has no recorded time-out and generates monthly attendance reports to assist teachers in monitoring and documentation.
        </p>

   <hr class="mt-5 mb-4" style="width: 200px; margin:auto; border:1.5px solid #003366;">
<h5 class="fw-semibold mb-4" style="color:#003366; letter-spacing:0.5px;">System Main Features</h5>


<div class="row g-4 justify-content-center">
  <div class="col-10 col-md-3">
    <div class="feature-card">
      <div class="feature-number">01</div>
      <div class="feature-icon"><i class="bi bi-qr-code-scan"></i></div>
      <h5>QR Code Scanning</h5>
    </div>
  </div>
  <div class="col-10 col-md-3">
    <div class="feature-card">
      <div class="feature-number">02</div>
      <div class="feature-icon"><i class="bi bi-chat-dots"></i></div>
      <h5>SMS Notifications</h5>
    </div>
  </div>
  <div class="col-10 col-md-3">
    <div class="feature-card">
      <div class="feature-number">03</div>
      <div class="feature-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
      <h5>Automated Reports</h5>
    </div>
  </div>
</div>


  </div>
</section>

<!-- LOGIN MODAL -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-white">
        <button type="button" class="btn-close" style="filter: invert(0);" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

      
      <div class="modal-body p-4">
        <div class="text-center mb-3">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>

          <img src="{{ asset('images/logo.webp') }}" alt="Logo" style="max-width: 100px;">
           <h3 class="fw-bold">Admin & Adviser Login</h3>
        </div>

        <form action="/login/process" method="POST">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input type="email" name="email" id="email" class="form-control border-dark" value="{{ old('email') }}" placeholder="Enter Email" required>
          </div>

          <div class="mb-4">
            <label for="password" class="form-label fw-bold">Password</label>
            <input type="password" name="password" id="password" class="form-control border-dark" placeholder="Enter Password" required>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Error Modal (always present) --}}
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered text-center">
    <div class="modal-content border-danger p-4">
      <div class="modal-body">
        <i class="fas fa-times-circle text-danger" style="font-size: 60px;"></i>
        <h5 class="mt-3">Login Failed</h5>
        <p id="errorContent"></p>
        <button type="button" class="btn btn-danger w-25 mt-2" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
@if(session('error') || $errors->any())
<script>
document.addEventListener('DOMContentLoaded', function () {
    var errorModalEl = document.getElementById('errorModal');
    var errorModal = new bootstrap.Modal(errorModalEl);

    // Set the error content
    @if(session('error'))
        document.getElementById('errorContent').innerText = "{{ session('error') }}";
    @elseif($errors->any())
        document.getElementById('errorContent').innerHTML = "{!! implode('<br>', $errors->all()) !!}";
    @endif

    // Show the error modal
    errorModal.show();

    // When error modal closes, show the login modal
    errorModalEl.addEventListener('hidden.bs.modal', function () {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    });
});
</script>
@endif




