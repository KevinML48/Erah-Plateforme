<style>
    .community-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .community-head h1 {
        margin: 0 0 8px;
        font-size: clamp(40px, 6vw, 76px);
        line-height: .92;
    }

    .community-head p {
        margin: 0;
        max-width: 760px;
        color: rgba(255, 255, 255, .72);
    }

    .community-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .community-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .community-kpi,
    .community-card,
    .community-surface {
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 16px;
        background: linear-gradient(165deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        box-shadow: 0 14px 34px rgba(0, 0, 0, .18);
    }

    .community-kpi {
        padding: 16px 18px;
    }

    .community-kpi strong {
        display: block;
        font-size: 32px;
        line-height: 1;
        margin-bottom: 8px;
    }

    .community-kpi span {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255, 255, 255, .68);
    }

    .community-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .community-card {
        padding: 20px;
        display: grid;
        gap: 14px;
    }

    .community-card h3 {
        margin: 0;
        font-size: 30px;
        line-height: .98;
    }

    .community-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .community-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255, 255, 255, .82);
    }

    .community-surface {
        padding: 24px;
        margin-top: 22px;
    }

    .community-table {
        width: 100%;
        border-collapse: collapse;
    }

    .community-table th,
    .community-table td {
        padding: 14px 10px;
        border-bottom: 1px solid rgba(255, 255, 255, .08);
        text-align: left;
        vertical-align: top;
    }

    .community-table th {
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .62);
    }

    .community-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .community-form-grid .full {
        grid-column: 1 / -1;
    }

    .community-empty {
        padding: 32px;
        text-align: center;
        border: 1px dashed rgba(255, 255, 255, .2);
        border-radius: 16px;
        color: rgba(255, 255, 255, .7);
    }

    body.tt-lightmode-on .community-head p {
        color: rgba(15, 23, 42, .68);
    }

    body.tt-lightmode-on .community-kpi,
    body.tt-lightmode-on .community-card,
    body.tt-lightmode-on .community-surface {
        border-color: rgba(148, 163, 184, .28);
        background: linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(244, 247, 252, .94));
        box-shadow: 0 18px 38px rgba(148, 163, 184, .16);
    }

    body.tt-lightmode-on .community-kpi span,
    body.tt-lightmode-on .community-pill,
    body.tt-lightmode-on .community-table th,
    body.tt-lightmode-on .community-empty {
        color: rgba(51, 65, 85, .74);
    }

    body.tt-lightmode-on .community-pill {
        border-color: rgba(148, 163, 184, .28);
        background: rgba(255, 255, 255, .84);
    }

    body.tt-lightmode-on .community-table th,
    body.tt-lightmode-on .community-table td,
    body.tt-lightmode-on .community-table tr {
        border-color: rgba(148, 163, 184, .22);
    }

    body.tt-lightmode-on .community-empty {
        border-color: rgba(148, 163, 184, .28);
        background: rgba(255, 255, 255, .72);
    }

    @media (max-width: 1199.98px) {
        .community-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .community-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .community-kpis,
        .community-grid,
        .community-form-grid {
            grid-template-columns: 1fr;
        }

        .community-surface {
            padding: 18px;
        }

        .community-table,
        .community-table thead,
        .community-table tbody,
        .community-table tr,
        .community-table th,
        .community-table td {
            display: block;
            width: 100%;
        }

        .community-table thead {
            display: none;
        }

        .community-table tr {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .community-table td {
            border-bottom: 0;
            padding: 6px 0;
        }
    }
</style>
