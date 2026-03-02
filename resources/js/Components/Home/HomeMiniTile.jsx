import { Link } from '@inertiajs/react';

export default function HomeMiniTile({
    label,
    title,
    description,
    href = '#',
    media = 'circle',
    imageSrc = '/assets/toycad/chick.svg',
}) {
    return (
        <article className="home-tile home-tile-light home-mini-tile">
            <header className="home-mini-header">
                <p>{label}</p>
                <button type="button" className="home-kebab" aria-label="Options">
                    <span />
                    <span />
                    <span />
                </button>
            </header>

            <h3>{title}</h3>
            <p className="home-mini-description">{description}</p>

            <img src={imageSrc} alt="" className="home-mini-media" data-media={media} loading="lazy" />

            <Link href={href} className="home-mini-link" aria-label={`Ouvrir ${title}`}>
                Ouvrir
            </Link>
        </article>
    );
}
