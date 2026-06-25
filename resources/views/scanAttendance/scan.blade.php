@section('body-style', 'min-height: 100vh; background: rgb(0,51,102);
background: linear-gradient(159deg, rgba(0,51,102,1) 0%, rgba(15,82,186,1) 100%);')

@include('partials.header')

<div class="container-lg min-vh-100 d-flex justify-content-center align-items-center">
    <main class="w-75 w-lg-50 h-auto border-dark border-3 border-end border-bottom bg-white rounded shadow p-3">

        <a href="/homepage" class="btn btn-danger mb-1">Back</a>

        <section class="">
            <h3 class="fw-bold text-center"> Please Scan Your QR Code Here</h3>

            {{-- Input na tatanggap ng QR (lagi naka-focus) --}}
            <form method="POST" action="/add/attendance">
                @csrf
                <input type="text" name="qr_code" id="qr_code" 
                       autofocus tabindex="1"
                       style="opacity:0; position:absolute; left:-9999px;" required>
            </form>

            @if(session('error'))
                <div class="alert alert-danger text-center mb-2">
                    {{ session('error') }}
                </div>
            @endif

@if(session('student'))
    @php
        $student = session('student');

        // dito naka-save yung image field
        $studentImage = $student['image'] ? asset('storage/' . $student['image']) : asset('images/default-student.png');
    @endphp


    <div class="card shadow-sm text-center mb-1 border-2 border-dark">
        <div class="pt-1">
            <h4 class=" ps-2 text-dark fw-bold" style="text-align:left;">Latest Scan:</h4>
            <img src="{{ $studentImage }}" class=" mb-1 border border-1 border-dark" width="120" height="120" alt="Student" style="border-radius:15%">
        </div>

        <div class="card-body w-100 d-flex flex-column align-items-center" style="background-color: #6be9b2; border-radius: 15px;">
            <h5 class="fw-bold mb-1 text-dark">
                {{ $student['first_name'] }} {{ $student['middle_name'] }} {{ $student['last_name'] }}
            </h5>

            {{-- ✅ Display Grade & Section from enrollment --}}
            <small class="d-block fw-bold">
                Grade {{ $student['grade_level'] ?? 'N/A' }} - {{ $student['section_name'] ?? 'N/A' }}
            </small>

            <small class="text-muted fw-bold d-block">Date: {{ $student['date'] }}</small>
            <small class="text-muted fw-bold d-block">Time: {{ $student['time'] }}</small>

            {{-- Status Badge --}}
            @if(isset($student['status']))
                @if($student['status'] === 'On Time')
                    <span class="badge bg-success mt-2">On Time</span>
                @elseif($student['status'] === 'Late')
                    <span class="badge bg-warning mt-2 text-dark">Late</span>
                @elseif($student['status'] === 'Time Out')
                    <span class="badge bg-danger mt-2">Time Out</span>
                @endif
            @endif
        </div>
    </div>
@endif

@php
    $previousScans = array_slice(session('latest_scans', []), 1, 4);
@endphp

@if($previousScans)
    <div class="row  g-3">
        @foreach($previousScans as $scan)
            <div class="col-6">
                <div class="card shadow-sm p-2 d-flex align-items-center text-center border-2 border-dark">
                    <img src="{{ $scan['image'] ? asset('storage/' . $scan['image']) : asset('images/default-student.png') }}" class=" mb-2 border border-1 border-dark" width="80" height="80" alt="Student" style="border-radius:15%">
                    <div class="p-2 w-100" style="background-color: #6baee9; border-radius: 8px;">
                        <strong class="d-block">{{ $scan['first_name'] }} {{ $scan['middle_name'] }} {{ $scan['last_name'] }}</strong>

                        <small class="d-block fw-bold">
                            Grade {{ $scan['grade_level'] ?? 'N/A' }} - {{ $scan['section_name'] ?? 'N/A' }}
                        </small>

                        <small class="text-muted fw-bold d-block">Date: {{ $scan['date'] }}</small>
                        <small class="text-muted fw-bold d-block">Time: {{ $scan['time'] }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
        </section>
    </main>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const qrInput = document.getElementById("qr_code");
    qrInput.focus();

    document.addEventListener("click", () => qrInput.focus());

    qrInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            qrInput.form.submit();
        }
    });
});
</script>

@include('partials.footer')
