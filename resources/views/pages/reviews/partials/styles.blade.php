<style>
    .review-card {
        height: 100%;
        border-radius: 28px;
        overflow: hidden;
    }

    .review-card .tt-stte-card-caption {
        display: grid;
        gap: 18px;
    }

    .review-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .review-card-author {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .review-card-avatar {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, .16);
        background: linear-gradient(180deg, rgba(255, 255, 255, .14), rgba(255, 255, 255, .04));
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .review-card-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .review-card-author-meta {
        min-width: 0;
        display: grid;
        gap: 4px;
    }

    .review-card-name {
        color: #fff;
        font-size: 20px;
        line-height: 1.1;
        font-weight: 600;
    }

    .review-card-name:hover {
        color: #fff;
        opacity: .92;
    }

    .review-card-source {
        color: rgba(255, 255, 255, .62);
        font-size: 13px;
        line-height: 1.35;
    }

    .review-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .review-card-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 32px;
        padding: 7px 12px;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 999px;
        background: rgba(255, 255, 255, .03);
        color: rgba(255, 255, 255, .82);
        font-size: 11px;
        letter-spacing: .06em;
        text-transform: uppercase;
        line-height: 1;
    }

    .review-card-pill--accent {
        border-color: rgba(255, 255, 255, .24);
        color: #fff;
    }

    .review-card-pill--supporter {
        border-color: rgba(255, 87, 87, .45);
        background: rgba(225, 11, 11, .14);
        color: #ffd3d3;
    }

    .review-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px 16px;
        flex-wrap: wrap;
        color: rgba(255, 255, 255, .62);
        font-size: 13px;
    }

    .review-card-footer .tt-link {
        font-size: 13px;
    }

    .review-home-actions {
        display: flex;
        justify-content: center;
        margin-top: 34px;
    }

    .reviews-page-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .reviews-page-hero-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 8px 16px;
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 999px;
        color: rgba(255, 255, 255, .78);
    }

    .reviews-page-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .reviews-page-summary {
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 18px;
        padding: 22px;
        background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
        display: grid;
        gap: 14px;
    }

    .reviews-page-summary strong {
        display: block;
        font-size: clamp(30px, 4vw, 52px);
        line-height: .9;
        color: #fff;
    }

    .reviews-page-summary span,
    .reviews-page-summary p {
        color: rgba(255, 255, 255, .68);
        margin: 0;
    }

    @media (max-width: 991.98px) {
        .reviews-page-grid {
            grid-template-columns: 1fr;
        }

        .review-card-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
