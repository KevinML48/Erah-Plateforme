import { useEffect, useMemo, useRef, useState } from 'react';

import HomeCarouselSlide from './HomeCarouselSlide';

function clampIndex(index, length) {
    if (!length) {
        return 0;
    }

    if (index < 0) {
        return 0;
    }

    if (index >= length) {
        return length - 1;
    }

    return index;
}

export default function HomeCarousel({ slides = [], label = 'Highlights', collapsed = false }) {
    const [activeIndex, setActiveIndex] = useState(0);
    const trackRef = useRef(null);
    const frameRef = useRef(null);

    const resolvedSlides = useMemo(() => slides.filter(Boolean), [slides]);

    useEffect(() => {
        setActiveIndex((current) => clampIndex(current, resolvedSlides.length));
    }, [resolvedSlides.length]);

    useEffect(() => {
        const track = trackRef.current;
        if (!track) {
            return undefined;
        }

        const updateActiveFromScroll = () => {
            const children = Array.from(track.children);
            if (!children.length) {
                return;
            }

            const rect = track.getBoundingClientRect();
            const center = rect.left + rect.width / 2;

            let winner = 0;
            let winnerDistance = Number.POSITIVE_INFINITY;

            children.forEach((child, index) => {
                const childRect = child.getBoundingClientRect();
                const childCenter = childRect.left + childRect.width / 2;
                const distance = Math.abs(center - childCenter);

                if (distance < winnerDistance) {
                    winnerDistance = distance;
                    winner = index;
                }
            });

            setActiveIndex(winner);
        };

        const onScroll = () => {
            if (frameRef.current) {
                return;
            }

            frameRef.current = requestAnimationFrame(() => {
                frameRef.current = null;
                updateActiveFromScroll();
            });
        };

        track.addEventListener('scroll', onScroll, { passive: true });
        updateActiveFromScroll();

        return () => {
            track.removeEventListener('scroll', onScroll);
            if (frameRef.current) {
                cancelAnimationFrame(frameRef.current);
                frameRef.current = null;
            }
        };
    }, [resolvedSlides.length]);

    const goTo = (index) => {
        const track = trackRef.current;
        const node = track?.children?.[index];
        if (!node) {
            return;
        }

        node.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        setActiveIndex(index);
    };

    const goPrev = () => {
        goTo(clampIndex(activeIndex - 1, resolvedSlides.length));
    };

    const goNext = () => {
        goTo(clampIndex(activeIndex + 1, resolvedSlides.length));
    };

    const onKeyDown = (event) => {
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            goTo(clampIndex(activeIndex + 1, resolvedSlides.length));
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goTo(clampIndex(activeIndex - 1, resolvedSlides.length));
        }
    };

    if (!resolvedSlides.length) {
        return null;
    }

    return (
        <section className={`home-carousel-wrap ${collapsed ? 'is-collapsed' : ''}`.trim()} aria-label={label}>
            <div className="home-carousel-viewport">
                <button
                    type="button"
                    className="home-carousel-nav home-carousel-nav-left"
                    onClick={goPrev}
                    aria-label="Slide precedent"
                >
                    <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2.1">
                        <path d="m15 5-7 7 7 7" />
                    </svg>
                </button>

                <div
                    ref={trackRef}
                    className={`home-carousel-track ${collapsed ? 'is-collapsed' : ''}`.trim()}
                    tabIndex={0}
                    onKeyDown={onKeyDown}
                    aria-label="Carousel highlights"
                >
                    {resolvedSlides.map((slide) => (
                        <HomeCarouselSlide key={slide.id} slide={slide} />
                    ))}
                </div>

                <button
                    type="button"
                    className="home-carousel-nav home-carousel-nav-right"
                    onClick={goNext}
                    aria-label="Slide suivant"
                >
                    <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2.1">
                        <path d="m9 5 7 7-7 7" />
                    </svg>
                </button>
            </div>

            <div className="home-carousel-dots" role="tablist" aria-label="Pagination carousel">
                {resolvedSlides.map((slide, index) => {
                    const isActive = index === activeIndex;
                    return (
                        <button
                            key={`${slide.id}-dot`}
                            type="button"
                            role="tab"
                            aria-selected={isActive}
                            aria-label={`Slide ${index + 1}`}
                            className={`home-carousel-dot ${isActive ? 'is-active' : ''}`.trim()}
                            onClick={() => goTo(index)}
                        />
                    );
                })}
            </div>
        </section>
    );
}
