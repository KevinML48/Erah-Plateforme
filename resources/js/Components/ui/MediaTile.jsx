import StatPill from './StatPill';
import Tile from './Tile';

export default function MediaTile({
    title,
    meta,
    imageUrl,
    badge,
    action,
    className = '',
    imageClassName = '',
    variant = 'dark',
}) {
    return (
        <Tile variant={variant} size="m" className={className} action={action}>
            <div className="space-y-3">
                {badge && <StatPill variant="new">{badge}</StatPill>}
                <div className="flex items-start justify-between gap-3">
                    <div className="min-w-0">
                        <h3 className="line-clamp-2 font-display text-2xl font-bold leading-[0.95]">{title}</h3>
                        {meta && <p className="mt-1 text-xs text-ui-muted">{meta}</p>}
                    </div>
                </div>
                <div className={`overflow-hidden rounded-[1.35rem] border border-ui-border/10 bg-ui-bg/80 ${imageClassName}`}>
                    {imageUrl ? (
                        <img src={imageUrl} alt={title} className="h-36 w-full object-cover" />
                    ) : (
                        <div className="h-36 w-full bg-gradient-to-br from-ui-red/25 via-ui-surface to-ui-panel" />
                    )}
                </div>
            </div>
        </Tile>
    );
}

