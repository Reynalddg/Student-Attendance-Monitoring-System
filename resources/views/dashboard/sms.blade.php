@extends('dashboard')

@section('content')
<div class="container-lg">
    <section class="mt-2">
        <h3 class="fw-bold">SMS Logs Dashboard</h3>
        <hr id="hr">
    </section>

    <br>

    <section class="mt-4 d-flex justify-content-between align-items-center px-4">
        <div class="d-flex align-items-center position-relative" id="searchContainer">
            {{-- Add search input here if needed --}}
        </div>

        {{-- Send SMS to All --}}
        <form action="{{ url('/send-sms-all') }}" method="GET" class="d-inline">
            <button type="submit" class="btn btn-success ms-2">
                 <i class="fa-solid fa-paper-plane me-1"></i> Send SMS to All
            </button>
        </form>
    </section>

    <section class="mt-4">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Sent By</th>
                    <th>Recipient Adviser</th>
                    <th>Recipient Guardian</th>
                    <th>Phone Number</th>
                    <th>Message</th>
                    <th>Sent At</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($smsLogs as $log)
                <tr>
                        <td>
                            {{ $log->sender 
                                ? $log->sender->first_name . ' ' . ($log->sender->middle_name ? substr($log->sender->middle_name, 0, 1) . '. ' : '') . $log->sender->last_name 
                                : 'Admin' }}
                        </td>
                        <td>
                            {{ $log->recipient 
                                ? $log->recipient->first_name . ' ' . ($log->recipient->middle_name ? substr($log->recipient->middle_name, 0, 1) . '. ' : '') . $log->recipient->last_name 
                                : '-' }}
                        </td>
                        <td>
                            {{ $log->guardian  ? $log->guardian->father_name : '-' }}
                        </td>
                    <td>{{ $log->phone_number }}</td>
                    <td>{{ $log->message }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->sent_at)->format('M d, Y H:i') }}</td>
                    <td>{{ ucfirst($log->status) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No SMS logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $smsLogs->links('pagination::bootstrap-5') }}
    </section>
</div>
@endsection
