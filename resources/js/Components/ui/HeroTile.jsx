import PillButton from './PillButton';
import Tile from './Tile';
import { Link } from '@inertiajs/react';

export default function HeroTile({
    title,
    description,
    ctaLabel,
    onCta,
    ctaHref,
    ctaVariant = 'primary',
    secondaryAction,
    className = '',
    children,
    variant = 'light',
}) {
    return (
        <Tile variant={variant} size="l" className={className}>
            <div className="flex h-full flex-col justify-between gap-4">
                <div>
                    <h1 className="font-display text-4xl font-bold leading-[0.95] sm:text-5xl">{title}</h1>
                    {description && <p className="mt-3 max-w-xl text-sm text-ui-muted">{description}</p>}
                </div>

                {children}

                <div className="flex flex-wrap gap-2">
                    {ctaLabel && (ctaHref ? (
                        <Link href={ctaHref}>
                            <PillButton variant={ctaVariant}>
                                {ctaLabel}
                            </PillButton>
                        </Link>
                    ) : (
                        <PillButton variant={ctaVariant} onClick={onCta}>
                            {ctaLabel}
                        </PillButton>
                    ))}
                    {secondaryAction}
                </div>
            </div>
        </Tile>
    );
}
