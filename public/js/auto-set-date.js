// Tarih girişlerini otomatik olarak bugüne ayarlayan modül
document.addEventListener('DOMContentLoaded', function () {
    // Bugünün tarihini alın
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; // YYYY-MM-DD formatı

    // Modal açıldığında tarih alanlarını otomatik doldur
    const modals = document.querySelectorAll('.modal');

    modals.forEach(function (modal) {
        modal.addEventListener('show.bs.modal', function () {
            const dateInputs = modal.querySelectorAll('input[type="date"]');
            dateInputs.forEach(function (input) {
                input.value = formattedDate; // Tarih girişine bugünün tarihini ata
            });
        });
    });
});
