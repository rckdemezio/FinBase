(() => {
    document.querySelectorAll('[data-money-input]').forEach((input) => {
        const amount = input.previousElementSibling;

        if (!(amount instanceof HTMLInputElement) || amount.name !== 'amount') {
            return;
        }

        const format = () => {
            const cents = input.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');

            amount.value = cents;

            if (cents === '') {
                input.value = '';

                return;
            }

            const whole = (cents.length > 2 ? cents.slice(0, -2) : '0').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            const decimal = cents.slice(-2).padStart(2, '0');
            input.value = `${whole},${decimal}`;
        };

        input.value = amount.value;
        format();
        input.addEventListener('input', format);
    });
})();
