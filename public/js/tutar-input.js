// Tutar input modülü
document.addEventListener('DOMContentLoaded', function () {
    // Tüm tutar inputlarını seç
    const tutarInputs = document.querySelectorAll('.tutar-input');

    // Binlik ayraç ve ondalık formatlama
    function formatNumber(value) {
        value = value.replace(/[^\d,]/g, ''); // Sadece rakam ve virgüle izin ver
        const parts = value.split(',');
        if (parts.length > 1) {
            parts[1] = parts[1].substring(0, 2); // Virgülden sonra maksimum iki basamak
        }
        const numberParts = parts[0].split('');
        for (let i = numberParts.length - 3; i > 0; i -= 3) {
            numberParts.splice(i, 0, '.'); // Binlik ayracı ekle
        }
        parts[0] = numberParts.join('');
        return parts.join(',');
    }

    // Binlik ayraçları kaldır ve sayıyı normalize et
    function normalizeNumber(value) {
        return value.replace(/\./g, '').replace(/,/g, '.'); // Binlik ayracı kaldır, virgülü noktaya çevir
    }

    // Her bir input için event listener ekle
    tutarInputs.forEach(function (input) {
        // Kullanıcı yazarken formatla
        input.addEventListener('input', function () {
            input.value = formatNumber(input.value);
        });

        // Form gönderilirken normalize edilmiş değeri ekle
        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function (event) {
                // Input değerini normalize et
                input.value = normalizeNumber(input.value);

                // Ekstra: Gönderim sırasında sorunları engellemek için boşsa sıfır yap
                if (input.value === '') {
                    input.value = '0';
                }
            });
        }
    });
});
