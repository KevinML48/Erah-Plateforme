<style>
    .adm-shell {
        --adm-text: rgba(255, 255, 255, .9);
        --adm-text-soft: rgba(255, 255, 255, .72);
        --adm-border: rgba(255, 255, 255, .16);
        --adm-border-soft: rgba(255, 255, 255, .1);
        --adm-surface-bg: rgba(255, 255, 255, .025);
        --adm-surface-bg-alt: rgba(255, 255, 255, .045);
        --adm-input-bg: rgba(0, 0, 0, .28);
        display: grid;
        gap: 22px;
    }

    body.tt-lightmode-on .adm-shell {
        --adm-text: rgba(22, 24, 31, .94);
        --adm-text-soft: rgba(22, 24, 31, .72);
        --adm-border: rgba(18, 23, 35, .22);
        --adm-border-soft: rgba(18, 23, 35, .14);
        --adm-surface-bg: rgba(255, 255, 255, .72);
        --adm-surface-bg-alt: rgba(255, 255, 255, .88);
        --adm-input-bg: rgba(255, 255, 255, .78);
    }

    .adm-nav,
    .adm-filter-actions,
    .adm-row-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .adm-surface {
        border: 1px solid var(--adm-border);
        border-radius: 16px;
        padding: 20px;
        background:
            radial-gradient(900px 220px at -15% -90%, rgba(255, 255, 255, .07), transparent 55%),
            var(--adm-surface-bg);
        backdrop-filter: blur(6px);
    }

    body.tt-lightmode-on .adm-surface {
        box-shadow: 0 10px 30px rgba(18, 23, 35, .06);
    }

    .adm-surface-title {
        margin: 0 0 14px;
        font-size: clamp(26px, 3.2vw, 42px);
        line-height: .95;
        color: var(--adm-text);
    }

    .adm-meta {
        margin: 0;
        color: var(--adm-text-soft);
    }

    .adm-shell .tt-heading-title,
    .adm-shell h1,
    .adm-shell h2,
    .adm-shell h3,
    .adm-shell h4,
    .adm-shell h5,
    .adm-shell h6 {
        color: var(--adm-text);
    }

    .adm-shell p,
    .adm-shell label,
    .adm-shell .text-gray,
    .adm-shell .tt-avlist-info {
        color: var(--adm-text-soft);
    }

    .adm-shell .tt-form-control,
    .adm-shell input.tt-form-control,
    .adm-shell select.tt-form-control,
    .adm-shell textarea.tt-form-control {
        color: var(--adm-text);
        border-color: var(--adm-border);
        background: var(--adm-input-bg);
    }

    .adm-shell .tt-form-control::placeholder {
        color: var(--adm-text-soft);
    }

    .adm-shell .tt-form-control:focus {
        border-color: rgba(255, 255, 255, .35);
    }

    body.tt-lightmode-on .adm-shell .tt-form-control,
    body.tt-lightmode-on .adm-shell input.tt-form-control,
    body.tt-lightmode-on .adm-shell select.tt-form-control,
    body.tt-lightmode-on .adm-shell textarea.tt-form-control {
        color: rgba(16, 18, 24, .95);
        border-color: rgba(20, 24, 34, .24);
        background: rgba(255, 255, 255, .9);
    }

    body.tt-lightmode-on .adm-shell .tt-form-control::placeholder {
        color: rgba(20, 24, 34, .58);
    }

    body.tt-lightmode-on .adm-shell .tt-form-control:focus {
        border-color: rgba(20, 24, 34, .45);
    }

    .adm-shell .tt-form-check label {
        color: var(--adm-text);
    }

    .adm-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .adm-kpi-card,
    .adm-compact-kpi {
        border: 1px solid var(--adm-border);
        border-radius: 14px;
        padding: 14px 16px;
        background: linear-gradient(160deg, var(--adm-surface-bg-alt), var(--adm-surface-bg));
    }

    .adm-kpi-card strong,
    .adm-compact-kpi strong {
        display: block;
        font-size: 32px;
        line-height: 1;
        margin-bottom: 6px;
        font-weight: 700;
        color: var(--adm-text);
    }

    .adm-kpi-card span,
    .adm-compact-kpi span {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--adm-text-soft);
    }

    .adm-empty {
        border: 1px dashed var(--adm-border);
        border-radius: 14px;
        padding: 24px;
        text-align: center;
        color: var(--adm-text-soft);
    }

    .adm-row-actions form {
        margin: 0;
    }

    .adm-row-actions .tt-btn,
    .adm-nav .tt-btn,
    .adm-filter-actions .tt-btn {
        min-height: 46px;
        padding: 10px 18px;
        border-radius: 999px;
        font-size: 12px;
        letter-spacing: .06em;
        line-height: 1;
    }

    .adm-nav .tt-btn,
    .adm-filter-actions .tt-btn {
        min-width: 126px;
        justify-content: center;
    }

    .adm-nav .tt-btn > span,
    .adm-filter-actions .tt-btn > span,
    .adm-row-actions .tt-btn > span {
        white-space: nowrap;
    }

    body.tt-lightmode-on .adm-shell .tt-btn-outline {
        box-shadow: inset 0 0 0 2px rgba(19, 22, 31, .55);
    }

    body.tt-lightmode-on .adm-shell .tt-btn-outline > *,
    body.tt-lightmode-on .adm-shell .tt-btn-outline > *::after {
        color: rgba(19, 22, 31, .9);
    }

    .adm-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--adm-border);
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--adm-text-soft);
    }

    .adm-pill-live {
        border-color: rgba(255, 79, 79, .5);
        color: #ffb6b6;
    }

    body.tt-lightmode-on .adm-pill-live {
        border-color: rgba(184, 35, 35, .5);
        color: rgba(184, 35, 35, .9);
    }

    .adm-form {
        display: grid;
        gap: 14px;
    }

    .adm-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .adm-form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .adm-form-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .adm-col-span-2 { grid-column: span 2; }
    .adm-col-span-3 { grid-column: span 3; }
    .adm-col-span-4 { grid-column: span 4; }

    .adm-inline-form {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .adm-inline-input,
    .adm-inline-select,
    .adm-inline-textarea {
        border: 1px solid var(--adm-border);
        border-radius: 999px;
        background: var(--adm-input-bg);
        color: var(--adm-text);
        font-size: 12px;
        line-height: 1.2;
        padding: 8px 12px;
        min-height: 38px;
    }

    .adm-inline-textarea {
        border-radius: 12px;
        min-height: 72px;
        width: 100%;
    }

    .adm-inline-select {
        min-width: 120px;
    }

    .adm-inline-input::placeholder,
    .adm-inline-textarea::placeholder {
        color: var(--adm-text-soft);
    }

    .adm-table-wrap {
        overflow-x: auto;
        border: 1px solid var(--adm-border-soft);
        border-radius: 14px;
    }

    .adm-table {
        width: 100%;
        min-width: 920px;
        border-collapse: collapse;
    }

    .adm-table th,
    .adm-table td {
        padding: 14px 12px;
        border-bottom: 1px solid var(--adm-border-soft);
        vertical-align: top;
        text-align: left;
        color: var(--adm-text);
    }

    .adm-table thead th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--adm-text-soft);
        font-weight: 600;
    }

    .adm-table tbody tr:last-child td {
        border-bottom: none;
    }

    .adm-table tbody tr:hover td {
        background: rgba(255, 255, 255, .03);
    }

    body.tt-lightmode-on .adm-table tbody tr:hover td {
        background: rgba(18, 23, 35, .06);
    }

    .adm-pagin {
        margin-top: 16px;
    }

    .adm-pagin nav,
    .adm-pagin span,
    .adm-pagin a {
        color: var(--adm-text);
    }

    .adm-blog-list .bli-info {
        display: grid;
        gap: 10px;
    }

    .adm-blog-list .bli-title,
    .adm-blog-list .bli-title a {
        color: var(--adm-text);
    }

    .adm-blog-list .bli-categories a,
    .adm-blog-list .bli-meta {
        color: var(--adm-text-soft);
    }

    .adm-blog-list .bli-desc {
        color: var(--adm-text-soft);
    }

    .adm-clip-list .blog-list-item {
        display: grid;
        grid-template-columns: minmax(180px, 220px) 1fr;
        gap: 18px;
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--adm-border-soft);
    }

    .adm-clip-list .blog-list-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .adm-clip-list .bli-image-wrap {
        width: 100%;
        align-self: start;
    }

    .adm-clip-list .bli-image {
        margin-bottom: 0;
        border-radius: 12px;
        overflow: hidden;
        height: 138px;
    }

    .adm-clip-list .bli-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .adm-clip-list .bli-title {
        margin: 0;
        font-size: clamp(26px, 3vw, 40px);
        line-height: .95;
    }

    .adm-clip-list .bli-meta,
    .adm-clip-list .bli-categories a {
        font-size: 12px;
    }

    .adm-sub-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .adm-sub-stack {
        display: grid;
        gap: 16px;
    }

    .adm-compact-kpis {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .adm-legacy-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .adm-user-list {
        display: grid;
        gap: 10px;
        max-height: 380px;
        overflow: auto;
        padding-right: 4px;
    }

    .adm-user-item {
        border: 1px solid var(--adm-border-soft);
        border-radius: 12px;
        padding: 10px 12px;
        background: var(--adm-surface-bg);
    }

    .adm-user-item strong {
        display: block;
        color: var(--adm-text);
    }

    .adm-user-item small {
        color: var(--adm-text-soft);
    }

    .adm-gift-grid,
    .adm-mission-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .adm-gift-card,
    .adm-mission-card {
        border: 1px solid var(--adm-border);
        border-radius: 14px;
        padding: 14px;
        background: var(--adm-surface-bg);
        display: grid;
        gap: 12px;
    }

    .adm-gift-media {
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--adm-border-soft);
        background: rgba(0, 0, 0, .18);
    }

    .adm-gift-media img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .adm-gift-title,
    .adm-mission-title {
        margin: 0;
        font-size: clamp(22px, 2.5vw, 34px);
        line-height: .95;
        color: var(--adm-text);
    }

    .adm-mission-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .adm-advanced {
        border: 1px solid var(--adm-border-soft);
        border-radius: 12px;
        padding: 10px 12px;
        background: var(--adm-surface-bg-alt);
    }

    .adm-advanced > summary {
        cursor: pointer;
        color: var(--adm-text);
        font-weight: 600;
        list-style: none;
    }

    .adm-advanced > summary::-webkit-details-marker {
        display: none;
    }

    .adm-advanced > summary::after {
        content: '+';
        float: right;
        color: var(--adm-text-soft);
    }

    .adm-advanced[open] > summary::after {
        content: '-';
    }

    .adm-advanced-body {
        margin-top: 12px;
        display: grid;
        gap: 12px;
    }

    @media (max-width: 1399.98px) {
        .adm-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .adm-form-grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 1199.98px) {
        .adm-sub-grid,
        .adm-gift-grid,
        .adm-mission-grid {
            grid-template-columns: 1fr;
        }

        .adm-compact-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .adm-clip-list .blog-list-item {
            grid-template-columns: 1fr;
        }

        .adm-clip-list .bli-image {
            height: 180px;
        }

        .adm-nav .tt-btn,
        .adm-filter-actions .tt-btn {
            min-width: 110px;
        }
    }

    @media (max-width: 767.98px) {
        .adm-form-grid,
        .adm-form-grid-3,
        .adm-form-grid-4,
        .adm-kpi-grid,
        .adm-compact-kpis {
            grid-template-columns: 1fr;
        }

        .adm-col-span-2,
        .adm-col-span-3,
        .adm-col-span-4 {
            grid-column: auto;
        }

        .adm-surface {
            padding: 14px;
        }

        .adm-row-actions .tt-btn,
        .adm-nav .tt-btn,
        .adm-filter-actions .tt-btn {
            width: 100%;
            justify-content: center;
            min-width: 0;
        }

        .adm-inline-form {
            align-items: stretch;
        }

        .adm-inline-input,
        .adm-inline-select {
            width: 100%;
            border-radius: 12px;
        }
    }
</style>

