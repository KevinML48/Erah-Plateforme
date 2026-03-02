import KebabMenu from './KebabMenu';

const sizeMap = {
    auto: '',
    s: 'min-h-[120px]',
    m: 'min-h-[170px]',
    l: 'min-h-[240px]',
};

export default function Tile({
    title,
    subtitle,
    action,
    menuAction,
    variant = 'dark',
    size = 'auto',
    className = '',
    children,
}) {
    const tone = variant === 'light' ? 'toy-tile-light' : 'toy-tile-dark';

    return (
        <section className={`toy-tile ${tone} ${sizeMap[size] ?? ''} ${className}`.trim()}>
            {(title || subtitle || action || menuAction) && (
                <header className="relative z-[1] flex items-start justify-between gap-2 px-4 pt-4 sm:px-5 sm:pt-5">
                    <div className="min-w-0">
                        {subtitle && <p className="text-xs font-semibold uppercase tracking-[0.12em] text-ui-muted">{subtitle}</p>}
                        {title && <h2 className="truncate font-display text-xl font-bold">{title}</h2>}
                    </div>
                    <div className="flex items-center gap-2">
                        {action}
                        {menuAction && <KebabMenu onClick={menuAction} />}
                    </div>
                </header>
            )}
            <div className="relative z-[1] p-4 sm:p-5">{children}</div>
        </section>
    );
}

