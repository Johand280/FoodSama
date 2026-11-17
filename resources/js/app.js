document.addEventListener('DOMContentLoaded', () => {
    const orderForm = document.querySelector('[data-order-form]');

    if (orderForm) {
        const currency = orderForm.dataset.currency || 'USD';
        const quantityInputs = orderForm.querySelectorAll('[data-order-quantity]');
        const totalElement = orderForm.querySelector('[data-order-total]');
        const countElement = orderForm.querySelector('[data-order-count]');
        const formatter = new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency,
            minimumFractionDigits: 2,
        });

        const updateSummary = () => {
            let total = 0;
            let units = 0;

            quantityInputs.forEach((input) => {
                const quantity = Math.max(0, Number.parseInt(input.value, 10) || 0);
                const price = Number.parseFloat(input.dataset.price || '0');

                total += quantity * price;
                units += quantity;
            });

            if (totalElement) {
                totalElement.textContent = formatter.format(total);
            }

            if (countElement) {
                countElement.textContent = `${units} ${units === 1 ? 'producto' : 'productos'}`;
            }
        };

        quantityInputs.forEach((input) => input.addEventListener('input', updateSummary));
        updateSummary();
    }

    document.querySelectorAll('[data-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || '¿Estás seguro?')) {
                event.preventDefault();
            }
        });
    });
});
