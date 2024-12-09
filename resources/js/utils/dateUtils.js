// Tarih işlemleri için yardımcı fonksiyonlar
window.formatDateTime = function (dateString) {
    if (!dateString) return "";

    const date = new Date(dateString);
    // Türkiye saat dilimi için UTC+3
    const userTimezone = 3 * 60; // UTC+3 için dakika cinsinden offset
    const offset = date.getTimezoneOffset() + userTimezone;
    date.setMinutes(date.getMinutes() + offset);
    return date.toISOString().slice(0, 16);
};

window.formatCurrency = function (value) {
    if (!value) return "0,00";
    return parseFloat(value).toLocaleString("tr-TR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

window.normalizeNumber = function (value) {
    if (!value) return "0";
    return value.replace(/\./g, "").replace(/,/g, ".");
};
