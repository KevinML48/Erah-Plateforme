import { Link } from '@inertiajs/react';

export default function HomeDarkTile({
    dateLabel,
    title,
    media = 'orb',
    href = '#',
    imageSrc = '/assets/toycad/turtle.svg',
}) {
    return (
        <article className="home-tile home-tile-dark home-dark-tile">
            <header className="home-dark-header">
                <p>{dateLabel}</p>
                <button type="button" className="home-icon-circle" aria-label="Actions">
                    <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
                        <path d="m8 5 8 7-8 7" />
                    </svg>
                </button>
            </header>

            <h3>{title}</h3>
            <img src={imageSrc} alt="" className="home-dark-media" data-media={media} loading="lazy" />

            <Link href={href} className="home-dark-link" aria-label={`Voir ${title}`}>
                Voir
            </Link>
        </article>
    );
}
