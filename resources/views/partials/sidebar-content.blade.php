

<div class="text-center mb-4 mt-1">
    <img src="{{ asset('storage/' . auth()->user()->image) }}" 
         class="rounded-circle"
         width="80" 
         height="80" 
         alt="Admin Image">
<div class="mt-2 text-white">
    {{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}
</div>    <div class="" style="color: rgb(218, 216, 212)">Administrator</div>
</div>

<hr>
<ul class="nav flex-column">
    <p class="small px-2"  style="color: rgb(218, 216, 212)">Reports</p>
    
<a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('dashboard') }}">
  <i class="fas fa-calendar-check me-2"></i>
  Overview
</a>

<a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('attendances') }}">
  <i class="fas fa-calendar-check me-2"></i>
  Attendances
</a>

        
<a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('smsLogs')}}">
  <i class="fas fa-users me-2 me-2"></i>
  SMS Logs
</a>

<hr>
    <p class="small  px-2 " style="color: rgb(218, 216, 212)">Managed</p>

  <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('advisers')}}">
      <i class="fas fa-users me-2 me-2"></i>
      Advisers
    </a>

     <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('tracks')}}">
      <i class="fas fa-graduation-cap me-2 me-2"></i>
      Tracks & Strands
    </a>

    <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('sections')}}">
       <i class="fa-solid fa-graduation-cap me-2"> </i>
      Sections
    </a>

       <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('dashboard.guardians')}}">
      <i class="fas fa-users me-2 me-2"></i>
      Parents & Guardians
    </a>

    <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('students')}}">
      <i class="fas fa-users me-2 me-2"></i>
      Students
    </a>

     <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('academicYears')}}">
      <i class="fas fa-graduation-cap me-2 me-2"></i>
      Academic Years
    </a>

     <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="attendance" href="{{ route('semesters')}}">
      <i class="fas fa-graduation-cap me-2 me-2"></i>
      Semesters
    </a>
    
    @if(auth()->user()->user_id == 1)
    <a class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" 
      id="attendance" 
      href="{{ route('users')}}">
        <i class="fas fa-user-shield me-2"></i>
        Admins
    </a>
    @endif





    <li class="nav-item">
        <a href="#" class="nav-link px-3 py-2 text-white d-flex align-items-center hover-effect" id="logout"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-right-from-bracket me-2"></i> Log Out
        </a>
        <form id="logout-form" action="/logout" method="POST" class="d-none">
            @csrf

        </form>
    </li>



    
       <section class="mt-4 text-center text-white">
  <p class="d-inline mb-0 text-white">{{ date('F d, Y') }}</p> <br><p class="d-inline mb-0">{{ now()->setTimezone('Asia/Manila')->format('h:i A') }}</p>
</section>



</ul>

