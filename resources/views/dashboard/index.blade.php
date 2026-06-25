@extends('dashboard')

@section('content')

<div class="container-lg">

    <section class="mt-2">
        <h3 class="fw-bold">Admin Dashboard Overview</h3>
        <hr id="hr">
    </section>

    <div class="row mt-4">

        <!-- Total Enrolled Students -->
        <div class="col-md-4 mb-3" style="margin-top:30px;">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $totalStudents ?? 0 }}</h3>
                    <p>Total Enrolled ({{ $activeSemester ?? 'Sem' }} - {{ $activeYear ?? 'Year' }})</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>

        <!-- Attendance Percentage This Month -->
        <div class="col-md-4 mb-3" style="margin-top:30px;">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>{{ $attendanceRate ?? 0 }}%</h3>
                    <p>Attendance This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            
            </div>
        </div>

        <!-- Present Today -->
        <div class="col-md-4 mb-3" style="margin-top:30px;">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>{{ $presentToday ?? 0 }}</h3>
                    <p>Present Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
               
            </div>
        </div>

        <!-- Absent Today -->
        <div class="col-md-4 mb-3">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $absentToday ?? 0 }}</h3>
                    <p>Absent Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
               
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $lateToday ?? 0 }}</h3>
                    <p>Late Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-clock"></i>
                </div>
               
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $smsSentToday ?? 0 }}</h3>
                    <p>SMS Sent Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
               
            </div>
        </div>

    </div>
</div>

@endsection
