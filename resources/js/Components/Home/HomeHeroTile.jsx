import { Link } from '@inertiajs/react';

export default function HomeHeroTile({ title, subtitle, leagueName, points, href = '/app/matches', imageSrc = '/assets/toycad/hero-hand.svg' }) {
    return (
        <article className="home-tile home-tile-light home-hero-tile">
            <div className="home-hero-copy">
                <p className="home-hero-eyebrow">ERAH Arena</p>
                <h2>{title}</h2>
                <p>{subtitle}</p>
            </div>

            <div className="home-hero-meta">
                <span className="home-chip">{leagueName}</span>
                <span className="home-chip home-chip-muted">{points} pts</span>
            </div>

            <img src={imageSrc} alt="" className="home-hero-image" loading="lazy" />

            <div className="home-hero-action">
                <Link href={href} className="home-round-action" aria-label="Ouvrir les matchs">
                    <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2.1">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </Link>
            </div>
        </article>
    );
}
