<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Success modal
    let successEl = document.getElementById('successModal');
    if (successEl) {
        let successModal = new bootstrap.Modal(successEl);
        successModal.show();
        successEl.addEventListener('hidden.bs.modal', function () {
            successEl.remove();
        });
    }

    // Error modal (validation + exception)
    let errorEl = document.getElementById('errorModal');
    if (errorEl) {
        let errorModal = new bootstrap.Modal(errorEl);
        errorModal.show();
        errorEl.addEventListener('hidden.bs.modal', function () {
            errorEl.remove();
        });
    }
});
</script>
</body>
</html>
