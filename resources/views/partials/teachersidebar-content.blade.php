<div class="text-center mb-4 mt-1">
        <img src="{{ asset('storage/' . auth()->user()->image) }}" 
         class="rounded-circle"
         width="80" 
         height="80" 
         alt="Admin Image">
<div class="mt-2 text-white">
    {{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}
</div>
    <div class="text-success">   <div class="" style="color: rgb(218, 216, 212)">
    {{ auth()->user()->activeSection->grade_level ?? '' }} - {{ auth()->user()->activeSection->section_name ?? '' }} | Adviser
</div></div>

      <button class="btn btn-sm mt-2 w-75 text-white fw-semibold"
        style="background: linear-gradient(90deg, #FF8C00, #FF4500); border: none; border-radius: 8px;"
        data-bs-toggle="modal" data-bs-target="#changePasswordModal">
    <i class="bi bi-key-fill"></i> Change Password
</button>
</div>

<hr>
<ul class="nav flex-column">
    <p class="small  px-2" style="color: rgb(218, 216, 212)">Reports</p>

     <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('teacher.teacherDashboard')}}">
      <i class="fas fa-user-shield me-2"></i>
       Overview Page
    </a>

    <p class="small px-2" style="color: rgb(218, 216, 212)">Managed</p>
     <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('teacher.studentEnrollments')}}">
        <i class="fas fa-users me-2 me-2"></i>
        Student Enrollments
      </a>

        <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('teacher.attendanceView')}}">
      <i class="fas fa-calendar-check me-2"></i>
      Daily Attendance
    </a>
    
      <p class="small px-2" style="color: rgb(218, 216, 212)">View</p>

    

    <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('teacher.studentView')}}">
      <i class="fas fa-user-shield me-2"></i>
      Students
    </a>


    <li class="nav-item" style="margin-bottom:90px">
        <a href="#" class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-right-from-bracket me-2"></i> Log Out
        </a>
        <form id="logout-form" action="/logout" method="POST" class="d-none">
            @csrf

        </form>
    </li>

    <br>
    <br>
       <section class="mt-5 text-center text-white text-align-bottom">
  <p class="d-inline mb-0 text-white">{{ date('F d, Y') }}</p> <br><p class="d-inline mb-0">{{ now()->setTimezone('Asia/Manila')->format('h:i A') }}</p>
</section>

</ul>


