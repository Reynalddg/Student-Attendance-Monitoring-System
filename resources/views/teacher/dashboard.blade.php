@extends('teacherDashboard')

@section('content')
<div class="container-lg">

    {{-- Summary Cards --}}
<div class="row g-3 text-center">
    <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="small text-muted">Total Students</div>
                <div class="display-6 fw-bold text-primary">{{ $totalStudents }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm border-0 bg-success text-white">
            <div class="card-body">
                <div class="small">Present Today</div>
                <div class="display-6 fw-bold">{{ $presentToday }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm border-0 bg-danger text-white">
            <div class="card-body">
                <div class="small">Absent Today</div>
                <div class="display-6 fw-bold">{{ $absentToday }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100 shadow-sm border-0 bg-warning">
            <div class="card-body">
                <div class="small">Late Arrivals</div>
                <div class="display-6 fw-bold text-dark">{{ $lateToday }}</div>
            </div>
        </div>
    </div>
</div>


    {{-- Charts --}}
    <div class="row g-3 mt-4">
        {{-- Attendance Trends --}}
        @if($hasAttendance)
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">📈 Attendance Trends (Last 7 Days)</h6>
                    <canvas id="trendChart" height="160"></canvas>
                </div>
            </div>
        </div>
        @else
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-5">
                    <h6 class="fw-bold text-muted">📅 No attendance data available.</h6>
                </div>
            </div>
        </div>
        @endif


        {{-- Top 5 Most Absent Students --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">🚨 Top 5 Most Absent Students</h6>
                    <canvas id="topAbsentChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

   {{-- Today's Attendance Table --}}
<div class="card mt-4 shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
<h6 class="fw-bold mb-0 text-danger">🚨 Absent Today</h6>
        </div>

        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light position-sticky top-0" style="z-index: 5;">
                    <tr>
                        <th style="width: 35%">Student Name</th>
                        <th style="width: 20%">Section</th>
                        <th style="width: 20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                   @forelse($todayAttendance->where('status', 'Absent') as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row->last_name }}, {{ $row->first_name }}</td>
                        <td>{{ $row->section_name }}</td>
                        <td><span class="badge bg-danger px-3 py-2">Absent</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No students absent today.</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

  

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Attendance %',
                data: @json($trendRates),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true, position: 'top' } },
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } },
                x: { ticks: { color: '#6c757d' } }
            }
        }
    });

    // Top 5 Most Absent Students Chart
      const absentCtx = document.getElementById('topAbsentChart').getContext('2d');
    new Chart(absentCtx, {
        type: 'bar',
        data: {
            labels: @json($topAbsentLabels), // Dynamic na names galing sa DB
            datasets: [{
                label: 'Total Absences',
                data: @json($topAbsentCounts), // Dynamic na counts galing sa DB
                backgroundColor: [
                    "rgba(255, 99, 132, 0.7)",   // Red
                    "rgba(255, 159, 64, 0.7)",   // Orange
                    "rgba(255, 205, 86, 0.7)",   // Yellow
                    "rgba(75, 192, 192, 0.7)",   // Teal
                    "rgba(54, 162, 235, 0.7)"    // Blue
                ],
                borderColor: [
                    "rgba(255, 99, 132, 1)",
                    "rgba(255, 159, 64, 1)",
                    "rgba(255, 205, 86, 1)",
                    "rgba(75, 192, 192, 1)",
                    "rgba(54, 162, 235, 1)"
                ],
                borderWidth: 2,
                borderRadius: 10 // Rounded edges para soft look
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#333",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    padding: 10,
                    borderColor: "#ddd",
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        color: "#444",
                        font: { size: 12, weight: "bold" }
                    },
                    grid: { drawBorder: false }
                },
                x: {
                    ticks: {
                        color: "#444",
                        font: { size: 12, weight: "bold" }
                    },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
