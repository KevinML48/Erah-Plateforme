export default function Chip({ active = false, children, onClick, type = 'button' }) {
    return (
        <button
            type={type}
            onClick={onClick}
            className={[
                'inline-flex items-center rounded-pill border px-3 py-1.5 text-xs font-semibold transition-all duration-200 ease-hud',
                active
                    ? 'border-ui-red/50 bg-ui-red/20 text-red-100'
                    : 'border-ui-border/20 bg-ui-surface text-ui-muted hover:border-ui-red/35 hover:text-white',
            ].join(' ')}
        >
            {children}
        </button>
    );
}
