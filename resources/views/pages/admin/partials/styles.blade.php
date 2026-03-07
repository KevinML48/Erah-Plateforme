<style>
    .adm-shell {
        --adm-text: rgba(255, 255, 255, .9);
        --adm-text-soft: rgba(255, 255, 255, .72);
        --adm-border: rgba(255, 255, 255, .16);
        --adm-border-soft: rgba(255, 255, 255, .1);
        --adm-surface-bg: rgba(255, 255, 255, .03);
        --adm-surface-bg-alt: rgba(255, 255, 255, .06);
        --adm-input-bg: rgba(255, 255, 255, .055);
        --adm-hover-bg: rgba(255, 255, 255, .08);
        --adm-hover-text: rgba(14, 18, 27, .94);
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
        --adm-input-bg: rgba(255, 255, 255, .96);
        --adm-hover-bg: rgba(18, 23, 35, .08);
        --adm-hover-text: rgba(15, 19, 28, .94);
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
        border-radius: 22px;
        padding: 24px;
        background:
            radial-gradient(900px 220px at -15% -90%, rgba(255, 255, 255, .07), transparent 55%),
            var(--adm-surface-bg);
        backdrop-filter: blur(6px);
        box-shadow: 0 18px 34px rgba(0, 0, 0, .14);
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
        border-radius: 14px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        min-height: 58px;
        padding: 14px 18px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .04);
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .adm-shell .tt-form-control::placeholder {
        color: var(--adm-text-soft);
    }

    .adm-shell .tt-form-control:focus {
        border-color: rgba(255, 255, 255, .35);
        box-shadow: 0 0 0 4px rgba(225, 11, 11, .08);
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

    .adm-form .tt-form-check {
        margin: 0;
        align-self: center;
    }

    .adm-form .tt-form-check label {
        text-transform: none;
        letter-spacing: 0;
        font-size: 18px;
        line-height: 1.2;
        padding-left: 36px !important;
        display: inline-block;
    }

    .adm-form.tt-form-creative .tt-form-group {
        border: 1px solid var(--adm-border);
        border-radius: 18px;
        padding: 18px 22px 20px;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .03), rgba(255, 255, 255, .015)),
            var(--adm-surface-bg);
        display: grid;
        gap: 12px;
        align-content: start;
        min-height: 136px;
        margin: 0;
        counter-increment: none;
    }

    .adm-form.tt-form-creative .tt-form-group::before {
        display: none !important;
        content: none !important;
    }

    body.tt-lightmode-on .adm-form.tt-form-creative .tt-form-group {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .88), rgba(255, 255, 255, .72)),
            var(--adm-surface-bg);
    }

    .adm-form.tt-form-creative .tt-form-group > label {
        margin: 0;
        padding: 0 !important;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--adm-text-soft);
        display: block;
        line-height: 1.15;
    }

    .adm-form.tt-form-creative .tt-form-control {
        padding: 14px 18px !important;
    }

    .adm-form .tt-form-group .tt-btn {
        align-self: end;
    }

    .adm-form .tt-form-group.adm-form-cta {
        justify-content: flex-start;
        justify-items: start;
        text-align: left;
        min-height: 136px;
    }

    .adm-form-cta-copy {
        margin: 0;
        font-size: 13px;
        color: var(--adm-text-soft);
        max-width: 260px;
        text-align: left;
    }

    .adm-field-help {
        margin: 0;
        font-size: 12px;
        line-height: 1.45;
        color: var(--adm-text-soft);
    }

    .adm-form textarea.tt-form-control {
        min-height: 140px;
        resize: vertical;
    }

    .adm-shell input[type="date"].tt-form-control,
    .adm-shell input[type="datetime-local"].tt-form-control,
    .adm-shell input[type="time"].tt-form-control {
        padding-right: 54px !important;
    }

    .adm-shell input[type="date"]::-webkit-calendar-picker-indicator,
    .adm-shell input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    .adm-shell input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(1) opacity(.72);
        cursor: pointer;
    }

    body.tt-lightmode-on .adm-shell input[type="date"]::-webkit-calendar-picker-indicator,
    body.tt-lightmode-on .adm-shell input[type="datetime-local"]::-webkit-calendar-picker-indicator,
    body.tt-lightmode-on .adm-shell input[type="time"]::-webkit-calendar-picker-indicator {
        filter: none;
    }

    .adm-shell input[type="file"].tt-form-control {
        padding: 10px 12px;
    }

    .adm-shell input[type="file"]::file-selector-button {
        margin-right: 12px;
        border: 1px solid var(--adm-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        color: var(--adm-text);
        font: inherit;
        font-size: 12px;
        letter-spacing: .05em;
        padding: 10px 14px;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease, color .2s ease;
    }

    body.tt-lightmode-on .adm-shell input[type="file"]::file-selector-button {
        background: rgba(18, 23, 35, .08);
        color: rgba(18, 23, 35, .92);
    }

    .adm-shell .tt-form-check {
        min-height: 58px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .adm-shell .tt-form-check input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: #df0b0b;
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
        min-width: 132px;
        padding: 10px 18px;
        border-radius: 999px;
        font-size: 12px;
        letter-spacing: .06em;
        line-height: 1;
        justify-content: center;
    }

    .adm-nav .tt-btn,
    .adm-filter-actions .tt-btn {
        min-width: 126px;
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
        border-radius: 14px;
        background: var(--adm-input-bg);
        color: var(--adm-text);
        font-size: 12px;
        line-height: 1.2;
        padding: 10px 14px;
        min-height: 44px;
    }

    .adm-inline-textarea {
        border-radius: 12px;
        min-height: 72px;
        width: 100%;
    }

    .adm-inline-select {
        min-width: 136px;
    }

    .adm-inline-input::placeholder,
    .adm-inline-textarea::placeholder {
        color: var(--adm-text-soft);
    }

    .adm-table-wrap {
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .adm-table {
        width: 100%;
        min-width: 980px;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .adm-table th,
    .adm-table td {
        padding: 18px 14px;
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
        padding: 0 14px 8px;
        border: none;
    }

    .adm-table tbody td {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .045), rgba(255, 255, 255, .02)),
            var(--adm-surface-bg);
        border-top: 1px solid var(--adm-border-soft);
        border-bottom: 1px solid var(--adm-border-soft);
        transition: background .2s ease, border-color .2s ease, transform .2s ease;
    }

    .adm-table tbody td:first-child {
        border-left: 1px solid var(--adm-border-soft);
        border-radius: 18px 0 0 18px;
    }

    .adm-table tbody td:last-child {
        border-right: 1px solid var(--adm-border-soft);
        border-radius: 0 18px 18px 0;
    }

    .adm-table tbody tr:hover td {
        background: var(--adm-hover-bg);
        border-color: var(--adm-border);
    }

    body.tt-lightmode-on .adm-table tbody tr:hover td {
        background: rgba(18, 23, 35, .06);
    }

    body.tt-lightmode-on .adm-table tbody td {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(255, 255, 255, .78)),
            var(--adm-surface-bg);
    }

    .adm-table td strong {
        color: var(--adm-text);
    }

    .adm-table .adm-row-actions {
        gap: 12px;
        align-items: center;
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
        position: relative;
        display: grid;
        grid-template-columns: minmax(220px, 280px) 1fr;
        gap: 22px;
        margin-bottom: 16px;
        padding: 18px;
        border: 1px solid var(--adm-border-soft);
        border-radius: 22px;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .03), rgba(255, 255, 255, .015)),
            var(--adm-surface-bg);
        transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    }

    .adm-clip-list .blog-list-item:last-child {
        margin-bottom: 0;
    }

    .adm-clip-list .blog-list-item:hover {
        transform: translateY(-4px);
        border-color: var(--adm-border);
        box-shadow: 0 20px 36px rgba(0, 0, 0, .16);
    }

    .adm-clip-list .bli-image-wrap {
        width: 100%;
        align-self: start;
        border-radius: 18px;
        overflow: hidden;
    }

    .adm-clip-list .bli-image {
        margin-bottom: 0;
        border-radius: 18px;
        overflow: hidden;
        height: 176px;
        background: rgba(0, 0, 0, .35);
    }

    .adm-clip-preview {
        position: relative;
        isolation: isolate;
    }

    .adm-clip-image,
    .adm-clip-video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity .22s ease, transform .28s ease, filter .28s ease;
    }

    .adm-clip-video {
        opacity: 0;
        background: #000;
    }

    .adm-clip-live {
        position: absolute;
        right: 14px;
        bottom: 14px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(15, 18, 27, .7);
        color: rgba(255, 255, 255, .92);
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        backdrop-filter: blur(8px);
    }

    .adm-clip-live::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #ec1111;
        box-shadow: 0 0 0 6px rgba(236, 17, 17, .16);
    }

    .adm-clip-list .blog-list-item:hover .adm-clip-image,
    .adm-clip-list .blog-list-item.is-previewing .adm-clip-image {
        opacity: .14;
        transform: scale(1.05);
        filter: blur(1px);
    }

    .adm-clip-list .blog-list-item:hover .adm-clip-video,
    .adm-clip-list .blog-list-item.is-previewing .adm-clip-video {
        opacity: 1;
    }

    .adm-clip-list .bli-title {
        margin: 0;
        font-size: clamp(26px, 3vw, 40px);
        line-height: .95;
    }

    .adm-clip-list .bli-info {
        align-content: start;
    }

    .adm-clip-list .bli-meta,
    .adm-clip-list .bli-categories a {
        font-size: 12px;
    }

    .adm-clip-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .adm-clip-stats .adm-pill {
        padding: 7px 12px;
        background: rgba(255, 255, 255, .03);
    }

    .adm-shell .tt-avlist-item {
        transition: transform .24s ease, color .24s ease;
    }

    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover {
        transform: translateY(-2px);
    }

    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-avlist-title,
    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-avlist-description,
    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-avlist-info,
    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-avlist-count,
    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .adm-pill {
        color: var(--adm-hover-text);
    }

    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .adm-pill {
        border-color: rgba(15, 19, 28, .18);
        background: rgba(15, 19, 28, .05);
    }

    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .adm-pill-live {
        border-color: rgba(184, 35, 35, .35);
        color: rgba(184, 35, 35, .9);
    }

    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-btn-outline {
        box-shadow: inset 0 0 0 2px rgba(15, 19, 28, .48);
    }

    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-btn-outline > *,
    body:not(.is-mobile) .adm-shell .tt-avlist-item:hover .tt-btn-outline > *::after {
        color: var(--adm-hover-text);
    }

    .adm-sub-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        align-items: start;
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
        max-height: 520px;
        overflow-y: auto;
        padding-right: 4px;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
    }

    .adm-user-item {
        border: 1px solid var(--adm-border-soft);
        border-radius: 18px;
        padding: 16px 18px;
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
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .adm-gift-card,
    .adm-mission-card {
        border: 1px solid var(--adm-border);
        border-radius: 20px;
        padding: 16px;
        background: var(--adm-surface-bg);
        display: grid;
        gap: 12px;
    }

    .adm-gift-card {
        grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
        align-items: start;
        column-gap: 22px;
    }

    .adm-gift-media {
        width: 100%;
        aspect-ratio: 4 / 5;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--adm-border-soft);
        background: rgba(0, 0, 0, .18);
        grid-column: 1;
        grid-row: 1 / span 3;
    }

    .adm-gift-media img {
        width: 100%;
        height: 100%;
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

    .adm-gift-copy,
    .adm-gift-meta {
        grid-column: 2;
    }

    .adm-gift-card .adm-advanced {
        grid-column: 1 / -1;
    }

    .adm-gift-meta {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        align-items: start;
    }

    .adm-gift-meta .adm-pill {
        width: 100%;
        min-height: 44px;
        justify-content: center;
        padding: 8px 12px;
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

    .adm-user-directory {
        display: grid;
        gap: 14px;
    }

    .adm-user-card {
        border: 1px solid var(--adm-border-soft);
        border-radius: 22px;
        padding: 18px 20px;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .035), rgba(255, 255, 255, .015)),
            var(--adm-surface-bg);
        display: grid;
        gap: 16px;
    }

    .adm-user-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .adm-user-card-title {
        display: grid;
        gap: 4px;
    }

    .adm-user-card-title strong {
        color: var(--adm-text);
        font-size: 28px;
        line-height: 1;
    }

    .adm-user-card-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .adm-user-stat {
        border: 1px solid var(--adm-border-soft);
        border-radius: 18px;
        padding: 14px 16px;
        background: rgba(255, 255, 255, .025);
        display: grid;
        gap: 8px;
    }

    .adm-user-stat-title {
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--adm-text-soft);
    }

    .adm-user-stat-value {
        color: var(--adm-text);
        line-height: 1.45;
    }

    .adm-user-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
    }

    .adm-user-card-actions .adm-row-actions {
        gap: 12px;
        align-items: center;
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
        .adm-mission-grid {
            grid-template-columns: 1fr;
        }

        .adm-compact-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .adm-gift-card {
            grid-template-columns: 1fr;
        }

        .adm-gift-media,
        .adm-gift-copy,
        .adm-gift-meta,
        .adm-gift-card .adm-advanced {
            grid-column: auto;
            grid-row: auto;
        }

        .adm-gift-media {
            aspect-ratio: 16 / 9;
        }

        .adm-user-card-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .adm-clip-list .blog-list-item {
            grid-template-columns: 1fr;
        }

        .adm-clip-list .bli-image {
            height: 180px;
        }

        .adm-gift-meta {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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

        .adm-user-card-actions {
            align-items: stretch;
        }
    }
</style>

