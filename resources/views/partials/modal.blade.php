{{-- Success Modal --}}
@if(session('success'))
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered text-center">
    <div class="modal-content border-success p-4">
      <div class="modal-body">
        <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
        <h5 class="mt-3">Success</h5>
        <p>{{ session('success') }}</p>
        <button type="button" class="btn btn-success w-25 mt-2" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Error Modal --}}
@if(session('error') || $errors->any())
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered text-center">
    <div class="modal-content border-danger p-4">
      <div class="modal-body">
        <i class="fas fa-times-circle text-danger" style="font-size: 60px;"></i>
        <h5 class="mt-3">Error</h5>

        @if(session('error'))
          <p>{{ session('error') }}</p>
        @endif

        @if($errors->any())
            @foreach ($errors->all() as $error)
             <p>{{ $error }} </p>
            @endforeach
        @endif

        <button type="button" class="btn btn-danger w-25 mt-2" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endif
