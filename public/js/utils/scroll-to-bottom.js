function scrollToBottom(element) {
    if (element) {
        element.scrollTop = element.scrollHeight;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tableContainer = document.getElementById('table-container');

    // Sayfa yüklendiğinde en alta kaydır
    scrollToBottom(tableContainer);

    // Yeni veri eklendiğinde otomatik kaydırma
    if (tableContainer) {
        const observer = new MutationObserver(() => {
            scrollToBottom(tableContainer);
        });

        observer.observe(tableContainer, {
            childList: true,
            subtree: true,
        });
    }
});
