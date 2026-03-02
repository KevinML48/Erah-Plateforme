export default function HomeCarouselSlide({ slide }) {
    const toneClass = slide.tone === 'light' ? 'home-carousel-slide-light' : 'home-carousel-slide-dark';

    return (
        <article className={`home-carousel-slide ${toneClass}`.trim()}>
            <header className="home-carousel-slide-head">
                {slide.badge ? <span className="home-pill-badge">{slide.badge}</span> : <span />}
                <span className="home-slide-type">{slide.type}</span>
            </header>

            <h4>{slide.title}</h4>
            <p>{slide.meta}</p>

            <div className="home-carousel-thumb" data-thumb={slide.thumb ?? 'default'} aria-hidden="true" />
        </article>
    );
}
