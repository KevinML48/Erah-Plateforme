export default function KebabMenu({ onClick, className = '', label = 'Menu' }) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-label={label}
            className={`inline-flex h-9 w-9 items-center justify-center rounded-full border border-ui-border/25 bg-ui-panel/95 text-ui-muted transition hover:border-ui-red/35 hover:text-white ${className}`}
        >
            <svg viewBox="0 0 24 24" className="h-4 w-4" fill="currentColor">
                <circle cx="12" cy="5.5" r="1.7" />
                <circle cx="12" cy="12" r="1.7" />
                <circle cx="12" cy="18.5" r="1.7" />
            </svg>
        </button>
    );
}

