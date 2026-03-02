export function formatDate(value) {
    if (!value) {
        return '-';
    }

    try {
        return new Intl.DateTimeFormat('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(value));
    } catch {
        return String(value);
    }
}

export function formatNumber(value) {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return '0';
    }

    return new Intl.NumberFormat('fr-FR').format(Number(value));
}
