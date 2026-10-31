// Global front-end helpers.
// Auto-format the card number field on the payment page (visual only).
document.addEventListener('input', function (e) {
    if (e.target && e.target.name === 'card_number') {
        let v = e.target.value.replace(/\D/g, '').slice(0, 16);
        e.target.value = v.replace(/(.{4})/g, '$1 ').trim();
    }
});
