const copyButtons = document.querySelectorAll('[data-copy-target]');

copyButtons.forEach((button) => {
    button.addEventListener('click', async () => {
        const targetId = button.getAttribute('data-copy-target');
        if (!targetId) {
            return;
        }

        const element = document.getElementById(targetId);
        if (!element) {
            return;
        }

        try {
            await navigator.clipboard.writeText(element.textContent ?? '');
            const previous = button.textContent;
            button.textContent = 'Copied';
            setTimeout(() => {
                button.textContent = previous;
            }, 900);
        } catch (error) {
            // no-op
        }
    });
});

