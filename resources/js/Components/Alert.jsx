const variants = {
    info: 'border-blue-700/45 bg-blue-900/30 text-blue-200',
    success: 'border-emerald-700/45 bg-emerald-900/30 text-emerald-200',
    error: 'border-red-700/45 bg-red-900/30 text-red-200',
    warning: 'border-amber-700/45 bg-amber-900/30 text-amber-200',
};

export default function Alert({ variant = 'info', message }) {
    if (!message) {
        return null;
    }

    return (
        <div className={`rounded-hud border px-3 py-2 text-sm ${variants[variant] ?? variants.info}`}>
            {message}
        </div>
    );
}
