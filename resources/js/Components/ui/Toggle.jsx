export default function Toggle({ checked = false, onChange, label, description }) {
    return (
        <label className="flex items-center justify-between gap-3 rounded-hud border border-ui-border/20 bg-ui-surface px-3 py-2.5">
            <span>
                <span className="block text-sm font-semibold">{label}</span>
                {description && <span className="block text-xs text-ui-muted">{description}</span>}
            </span>
            <span
                className={[
                    'relative inline-flex h-6 w-11 shrink-0 rounded-full border transition-all duration-200 ease-hud',
                    checked ? 'border-ui-red/55 bg-ui-red/25' : 'border-ui-border/30 bg-ui-bg',
                ].join(' ')}
            >
                <input
                    type="checkbox"
                    checked={checked}
                    onChange={(event) => onChange?.(event.target.checked)}
                    className="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                />
                <span
                    className={[
                        'pointer-events-none absolute top-0.5 h-5 w-5 rounded-full bg-white transition-all duration-200 ease-hud',
                        checked ? 'left-5' : 'left-0.5',
                    ].join(' ')}
                />
            </span>
        </label>
    );
}

