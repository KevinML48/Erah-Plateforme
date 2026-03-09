@extends('marketing.layouts.template')

@section('title', 'Admin Galerie | ERAH Plateforme')
@section('meta_description', 'Gestion admin de la galerie photos et videos publiques.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-gallery-page {
            display: grid;
            gap: 24px;
        }

        .adm-gallery-page-header .ph-caption-description {
            max-width: 760px;
            line-height: 1.55;
        }

        .adm-gallery-page-header .ph-caption-subtitle {
            letter-spacing: .14em;
        }

        .adm-gallery-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(320px, .8fr);
            gap: 22px;
            padding: 30px;
            background:
                radial-gradient(520px 240px at 0% 0%, rgba(223, 11, 11, .14), transparent 62%),
                radial-gradient(680px 260px at 100% 10%, rgba(255, 255, 255, .06), transparent 60%),
                var(--adm-surface-bg);
        }

        .adm-gallery-hero-copy,
        .adm-gallery-hero-panel,
        .adm-gallery-flashes,
        .adm-gallery-stats-grid,
        .adm-gallery-form-main,
        .adm-gallery-form-side,
        .adm-gallery-field-grid,
        .adm-gallery-results,
        .adm-gallery-grid,
        .adm-gallery-action-row,
        .adm-gallery-order-controls,
        .adm-gallery-order-steps,
        .adm-gallery-form-actions,
        .adm-gallery-edit-action-buttons {
            display: grid;
            gap: 14px;
        }

        .adm-gallery-hero-copy {
            align-content: start;
            gap: 18px;
        }

        .adm-gallery-hero-title {
            margin: 0;
            font-size: clamp(34px, 4.8vw, 64px);
            line-height: .94;
            color: var(--adm-text);
        }

        .adm-gallery-hero-lead {
            margin: 0;
            max-width: 760px;
            color: var(--adm-text-soft);
            font-size: 17px;
            line-height: 1.65;
        }

        .adm-gallery-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .adm-gallery-hero-panel {
            align-content: start;
            border: 1px solid var(--adm-border-soft);
            border-radius: 22px;
            padding: 22px;
            background: rgba(255, 255, 255, .04);
        }

        .adm-gallery-hero-panel h2 {
            margin: 0;
            font-size: 20px;
            color: var(--adm-text);
        }

        .adm-gallery-hero-panel p {
            margin: 0;
            color: var(--adm-text-soft);
        }

        .adm-gallery-hero-points {
            display: grid;
            gap: 12px;
        }

        .adm-gallery-hero-point {
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .03);
        }

        .adm-gallery-hero-point strong {
            display: block;
            margin-bottom: 6px;
            color: var(--adm-text);
            font-size: 16px;
        }

        .adm-gallery-hero-point span {
            display: block;
            color: var(--adm-text-soft);
            line-height: 1.55;
        }

        .adm-gallery-flash {
            border: 1px solid var(--adm-border);
            border-radius: 18px;
            padding: 16px 18px;
            background: var(--adm-surface-bg-alt);
            color: var(--adm-text);
        }

        .adm-gallery-flash strong,
        .adm-gallery-flash p {
            margin: 0;
        }

        .adm-gallery-flash p + p {
            margin-top: 6px;
        }

        .adm-gallery-flash.is-success {
            border-color: rgba(34, 197, 94, .28);
            background: rgba(34, 197, 94, .1);
        }

        .adm-gallery-flash.is-error {
            border-color: rgba(239, 68, 68, .28);
            background: rgba(239, 68, 68, .1);
        }

        .adm-gallery-flash.is-info {
            border-color: rgba(59, 130, 246, .28);
            background: rgba(59, 130, 246, .1);
        }

        .adm-gallery-section-bar,
        .adm-gallery-section-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 14px;
            align-items: start;
        }

        .adm-gallery-section-head {
            align-items: start;
        }

        .adm-gallery-section-eyebrow {
            display: inline-flex;
            margin-bottom: 8px;
            font-size: 11px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--adm-text-soft);
        }

        .adm-gallery-section-bar h2,
        .adm-gallery-section-head h3 {
            margin: 0;
            color: var(--adm-text);
            line-height: .98;
        }

        .adm-gallery-section-bar h2 {
            font-size: clamp(24px, 3.4vw, 38px);
        }

        .adm-gallery-section-head h3 {
            font-size: 24px;
        }

        .adm-gallery-section-bar p,
        .adm-gallery-section-head p {
            margin: 0;
            max-width: 680px;
            color: var(--adm-text-soft);
            font-size: 17px;
            line-height: 1.6;
        }

        .adm-gallery-btn {
            margin: 0;
            white-space: nowrap;
        }

        .adm-gallery-btn > span {
            white-space: nowrap;
        }

        .adm-gallery-hero-actions .adm-gallery-btn {
            margin: 0;
        }

        .adm-gallery-stats-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin-top: 20px;
        }

        .adm-gallery-stat-card {
            border: 1px solid var(--adm-border-soft);
            border-radius: 20px;
            padding: 18px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .04), rgba(255, 255, 255, .015)),
                rgba(255, 255, 255, .03);
        }

        .adm-gallery-stat-card strong {
            display: block;
            margin-bottom: 10px;
            color: var(--adm-text);
            font-size: 34px;
            line-height: .95;
        }

        .adm-gallery-stat-card span {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--adm-text-soft);
        }

        .adm-gallery-stat-card p {
            margin: 0;
            color: var(--adm-text-soft);
            line-height: 1.55;
        }

        .adm-gallery-compose {
            display: grid;
            gap: 18px;
        }

        .adm-gallery-compose summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
        }

        .adm-gallery-compose summary::-webkit-details-marker {
            display: none;
        }

        .adm-gallery-compose summary > div {
            max-width: 760px;
        }

        .adm-gallery-compose summary h2 {
            margin: 0;
            color: var(--adm-text);
            font-size: clamp(24px, 3vw, 36px);
            line-height: 1;
        }

        .adm-gallery-compose summary p {
            margin: 10px 0 0;
            color: var(--adm-text-soft);
            line-height: 1.6;
        }

        .adm-gallery-compose-indicator {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 180px;
            min-height: 48px;
            padding: 10px 18px;
            border-radius: 999px;
            border: 1px solid var(--adm-border);
            background: rgba(255, 255, 255, .04);
            color: var(--adm-text);
            text-align: center;
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .adm-gallery-compose summary:hover .adm-gallery-compose-indicator,
        .adm-gallery-compose[open] .adm-gallery-compose-indicator {
            border-color: rgba(223, 11, 11, .36);
            background: rgba(223, 11, 11, .1);
        }

        .adm-gallery-compose-body {
            border-top: 1px solid var(--adm-border-soft);
            padding-top: 22px;
        }

        .adm-gallery-form-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
            gap: 18px;
            align-items: start;
        }

        .adm-gallery-form-main,
        .adm-gallery-form-side {
            align-content: start;
        }

        .adm-gallery-section-card,
        .adm-gallery-field-card,
        .adm-gallery-toolbar-card,
        .adm-gallery-card,
        .adm-gallery-empty-state {
            border: 1px solid var(--adm-border);
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .035), rgba(255, 255, 255, .015)),
                var(--adm-surface-bg);
        }

        .adm-gallery-section-card {
            padding: 22px;
        }

        .adm-gallery-field-grid {
            grid-template-columns: 1fr;
            margin-top: 18px;
        }

        .adm-gallery-field-grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .adm-gallery-field-card {
            padding: 16px 18px;
            min-width: 0;
        }

        .adm-gallery-field-card.adm-span-2 {
            grid-column: 1 / -1;
        }

        .adm-gallery-form-label {
            display: block;
            margin: 0 0 10px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--adm-text-soft);
        }

        .adm-gallery-upload-block .tt-form-control,
        .adm-gallery-field-card .tt-form-control {
            min-height: 58px;
        }

        .adm-gallery-field-card textarea.tt-form-control,
        .adm-gallery-edit-form textarea.tt-form-control {
            min-height: 152px;
        }

        .adm-gallery-inline-help {
            margin: 8px 0 0;
            color: var(--adm-text-soft);
            font-size: 12px;
            line-height: 1.5;
        }

        .adm-gallery-error {
            margin: 8px 0 0;
            color: #fecaca;
            font-size: 12px;
            line-height: 1.45;
        }

        body.tt-lightmode-on .adm-gallery-error {
            color: #b91c1c;
        }

        .adm-gallery-upload-preview {
            overflow: hidden;
            border: 1px dashed var(--adm-border);
            border-radius: 20px;
            min-height: 280px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .04), rgba(255, 255, 255, .015)),
                rgba(255, 255, 255, .02);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .adm-gallery-upload-preview img,
        .adm-gallery-upload-preview video {
            width: 100%;
            height: 100%;
            min-height: 280px;
            object-fit: cover;
            display: block;
        }

        .adm-gallery-upload-placeholder {
            padding: 26px;
            text-align: center;
            max-width: 340px;
        }

        .adm-gallery-upload-placeholder-tag {
            display: inline-flex;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(223, 11, 11, .12);
            color: var(--adm-text);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .adm-gallery-upload-placeholder strong,
        .adm-gallery-preview-caption strong {
            display: block;
            color: var(--adm-text);
        }

        .adm-gallery-upload-placeholder strong {
            margin-bottom: 8px;
            font-size: 20px;
        }

        .adm-gallery-upload-placeholder p,
        .adm-gallery-preview-caption span {
            margin: 0;
            color: var(--adm-text-soft);
            line-height: 1.55;
        }

        .adm-gallery-preview-caption {
            display: grid;
            gap: 6px;
        }

        .adm-gallery-toggle-card {
            margin-top: 18px;
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 16px 18px;
            background: rgba(255, 255, 255, .03);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .adm-gallery-toggle-card strong {
            display: block;
            margin: 4px 0 6px;
            color: var(--adm-text);
            font-size: 17px;
        }

        .adm-gallery-toggle-card p {
            margin: 0;
            color: var(--adm-text-soft);
            line-height: 1.5;
        }

        .adm-gallery-switch {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--adm-border);
            background: rgba(255, 255, 255, .04);
            color: var(--adm-text);
            cursor: pointer;
        }

        .adm-gallery-switch input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #df0b0b;
        }

        .adm-gallery-form-actions,
        .adm-gallery-edit-action-buttons {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .adm-gallery-form-actions .adm-gallery-btn,
        .adm-gallery-edit-action-buttons .adm-gallery-btn {
            width: 100%;
            justify-content: center;
        }

        .adm-gallery-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) repeat(3, minmax(240px, .5fr)) minmax(190px, .38fr) minmax(190px, .38fr);
            gap: 14px;
            align-items: end;
            margin-top: 20px;
        }

        .adm-gallery-toolbar-card {
            padding: 18px 20px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .055), rgba(255, 255, 255, .025)),
                rgba(255, 255, 255, .035);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .03);
        }

        .adm-gallery-toolbar .tt-form-control {
            min-height: 64px;
            font-size: 15px;
            font-weight: 500;
        }

        .adm-gallery-toolbar select.tt-form-control {
            padding-right: 54px !important;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .adm-gallery-toolbar .tt-form-control::placeholder {
            opacity: .92;
        }

        .adm-gallery-toolbar .adm-gallery-btn {
            width: 100%;
            justify-content: center;
        }

        .adm-gallery-results {
            grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr);
            align-items: start;
            margin-top: 18px;
            gap: 16px;
        }

        .adm-gallery-results-copy strong {
            display: block;
            color: var(--adm-text);
            font-size: clamp(28px, 2.4vw, 36px);
            line-height: 1;
            margin-bottom: 10px;
        }

        .adm-gallery-results-copy span {
            color: var(--adm-text-soft);
            font-size: 17px;
            line-height: 1.55;
        }

        .adm-gallery-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-start;
        }

        .adm-gallery-results .adm-gallery-chip-row {
            justify-content: flex-end;
        }

        .adm-gallery-results .adm-pill {
            padding: 10px 16px;
            font-size: 12px;
            letter-spacing: .06em;
            background: rgba(255, 255, 255, .04);
            border-color: var(--adm-border);
            color: var(--adm-text);
        }

        .adm-gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 26px;
        }

        .adm-gallery-card {
            overflow: hidden;
            display: grid;
            align-content: start;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .055), rgba(255, 255, 255, .02)),
                rgba(255, 255, 255, .025);
        }

        .adm-gallery-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(0, 0, 0, .14);
        }

        .adm-gallery-card-media {
            position: relative;
            min-height: 320px;
            aspect-ratio: 5 / 4;
            overflow: hidden;
            background: rgba(255, 255, 255, .04);
        }

        .adm-gallery-card-media img,
        .adm-gallery-card-media video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            transition: transform .35s ease, opacity .25s ease;
        }

        .adm-gallery-card:hover .adm-gallery-card-media img,
        .adm-gallery-card.is-previewing .adm-gallery-card-media video {
            transform: scale(1.03);
        }

        .adm-gallery-card-media-empty {
            display: grid;
            place-items: center;
            height: 100%;
            color: var(--adm-text-soft);
            padding: 20px;
            text-align: center;
        }

        .adm-gallery-card-badges,
        .adm-gallery-card-order {
            position: absolute;
            z-index: 2;
        }

        .adm-gallery-card-badges {
            top: 14px;
            left: 14px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            max-width: calc(100% - 28px);
        }

        .adm-gallery-card-order {
            top: 14px;
            right: 14px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .18);
            background: rgba(11, 11, 13, .68);
            color: rgba(255, 255, 255, .92);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }

        body.tt-lightmode-on .adm-gallery-card-order {
            border-color: rgba(18, 23, 35, .12);
            background: rgba(255, 255, 255, .9);
            color: rgba(18, 23, 35, .9);
        }

        .adm-gallery-card-media-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(10, 10, 14, .08) 0%, rgba(10, 10, 14, .08) 48%, rgba(10, 10, 14, .72) 100%);
            pointer-events: none;
        }

        .adm-gallery-media-cta {
            position: absolute;
            left: 14px;
            bottom: 14px;
            z-index: 2;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            padding: 10px 16px;
            background: rgba(11, 11, 13, .72);
            color: rgba(255, 255, 255, .96);
            font: inherit;
            font-size: 13px;
            cursor: pointer;
            transition: transform .2s ease, background .2s ease;
            backdrop-filter: blur(8px);
        }

        .adm-gallery-media-cta:hover,
        .adm-gallery-media-cta:focus-visible {
            transform: translateY(-1px);
            background: rgba(223, 11, 11, .74);
        }

        body.tt-lightmode-on .adm-gallery-media-cta {
            border-color: rgba(18, 23, 35, .12);
            background: rgba(255, 255, 255, .9);
            color: rgba(18, 23, 35, .92);
        }

        .adm-gallery-state-pill {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(11, 11, 13, .66);
            color: rgba(255, 255, 255, .92);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }

        .adm-gallery-state-pill.is-active {
            background: rgba(34, 197, 94, .22);
        }

        .adm-gallery-state-pill.is-inactive {
            background: rgba(239, 68, 68, .22);
        }

        .adm-gallery-state-pill.is-scheduled {
            background: rgba(59, 130, 246, .22);
        }

        body.tt-lightmode-on .adm-gallery-state-pill {
            border-color: rgba(18, 23, 35, .12);
            background: rgba(255, 255, 255, .88);
            color: rgba(18, 23, 35, .92);
        }

        .adm-gallery-card-body {
            display: grid;
            gap: 18px;
            padding: 24px;
        }

        .adm-gallery-card-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            align-items: start;
        }

        .adm-gallery-card-eyebrow {
            margin: 0 0 8px;
            color: rgba(255, 255, 255, .76);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        body.tt-lightmode-on .adm-gallery-card-eyebrow {
            color: rgba(18, 23, 35, .68);
        }

        .adm-gallery-card-head h3 {
            margin: 0;
            color: var(--adm-text);
            font-size: 30px;
            line-height: 1.02;
        }

        .adm-gallery-card-summary {
            margin: 0;
            color: rgba(255, 255, 255, .9);
            font-size: 15px;
            line-height: 1.65;
        }

        body.tt-lightmode-on .adm-gallery-card-summary {
            color: rgba(18, 23, 35, .84);
        }

        .adm-gallery-card .adm-pill {
            padding: 9px 13px;
            font-size: 12px;
            letter-spacing: .04em;
            background: rgba(255, 255, 255, .045);
            border-color: var(--adm-border);
            color: var(--adm-text);
        }

        .adm-gallery-meta-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin: 0;
        }

        .adm-gallery-meta-item {
            margin: 0;
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .045);
            min-width: 0;
        }

        .adm-gallery-meta-item dt {
            margin: 0 0 8px;
            color: var(--adm-text-soft);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .adm-gallery-meta-item dd {
            margin: 0;
            color: var(--adm-text);
            font-size: 15px;
            line-height: 1.55;
            word-break: break-word;
        }

        .adm-gallery-order-panel {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 14px;
            padding: 18px 20px;
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            background: rgba(255, 255, 255, .045);
        }

        .adm-gallery-order-panel p {
            margin: 6px 0 0;
        }

        .adm-gallery-order-controls {
            min-width: min(100%, 340px);
            justify-items: end;
        }

        .adm-gallery-order-form {
            display: grid;
            grid-template-columns: minmax(120px, 1fr) minmax(132px, .72fr);
            gap: 10px;
            width: 100%;
        }

        .adm-gallery-order-form .tt-form-control {
            min-height: 52px;
            font-size: 17px;
        }

        .adm-gallery-order-form .adm-gallery-btn,
        .adm-gallery-action-row .adm-gallery-btn {
            width: 100%;
            justify-content: center;
        }

        .adm-gallery-order-steps {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            width: 100%;
        }

        .adm-gallery-order-steps form {
            margin: 0;
        }

        .adm-gallery-icon-btn {
            width: 100%;
            min-height: 46px;
            border: 1px solid var(--adm-border);
            border-radius: 999px;
            background: rgba(255, 255, 255, .04);
            color: var(--adm-text);
            font: inherit;
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: border-color .2s ease, background .2s ease, transform .2s ease;
        }

        .adm-gallery-icon-btn:hover,
        .adm-gallery-icon-btn:focus-visible {
            border-color: rgba(223, 11, 11, .34);
            background: rgba(223, 11, 11, .08);
            transform: translateY(-1px);
        }

        .adm-gallery-action-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .adm-gallery-action-row form {
            margin: 0;
        }

        .adm-gallery-edit {
            border-top: 1px solid var(--adm-border-soft);
            padding-top: 18px;
        }

        .adm-gallery-edit summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            color: var(--adm-text);
        }

        .adm-gallery-edit summary::-webkit-details-marker {
            display: none;
        }

        .adm-gallery-edit summary span {
            font-size: 18px;
            font-weight: 600;
        }

        .adm-gallery-edit summary small {
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
        }

        body.tt-lightmode-on .adm-gallery-edit summary small {
            color: rgba(18, 23, 35, .72);
        }

        .adm-gallery-edit-form {
            display: grid;
            gap: 16px;
            margin-top: 16px;
        }

        .adm-gallery-edit-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            border-top: 1px solid var(--adm-border-soft);
            padding-top: 16px;
        }

        .adm-gallery-empty-state {
            padding: 36px 30px;
            text-align: center;
            margin-top: 22px;
        }

        .adm-gallery-empty-state h3 {
            margin: 0 0 10px;
            color: var(--adm-text);
            font-size: 28px;
        }

        .adm-gallery-empty-state p {
            margin: 0 auto;
            max-width: 620px;
            color: var(--adm-text-soft);
            line-height: 1.65;
        }

        .adm-gallery-empty-state .adm-gallery-hero-actions {
            justify-content: center;
            margin-top: 20px;
        }

        .adm-gallery-modal[hidden] {
            display: none;
        }

        .adm-gallery-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .adm-gallery-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(7, 10, 16, .74);
            backdrop-filter: blur(8px);
        }

        .adm-gallery-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(1040px, 100%);
            max-height: calc(100vh - 56px);
            overflow: auto;
            border: 1px solid var(--adm-border);
            border-radius: 26px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .02)),
                rgba(8, 11, 18, .96);
            box-shadow: 0 30px 80px rgba(0, 0, 0, .42);
        }

        body.tt-lightmode-on .adm-gallery-modal-dialog {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .98), rgba(255, 255, 255, .96)),
                rgba(255, 255, 255, .96);
        }

        .adm-gallery-modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            z-index: 2;
            border: 1px solid var(--adm-border);
            border-radius: 999px;
            padding: 10px 16px;
            background: rgba(255, 255, 255, .06);
            color: var(--adm-text);
            font: inherit;
            cursor: pointer;
        }

        .adm-gallery-modal-media {
            min-height: 420px;
            background: #05070c;
        }

        .adm-gallery-modal-media img,
        .adm-gallery-modal-media video {
            width: 100%;
            display: block;
            max-height: 72vh;
            object-fit: contain;
            background: #05070c;
        }

        .adm-gallery-modal-copy {
            padding: 20px 22px 24px;
        }

        .adm-gallery-modal-copy h2 {
            margin: 0 0 8px;
            color: var(--adm-text);
            font-size: 28px;
        }

        .adm-gallery-modal-copy p {
            margin: 0;
            color: var(--adm-text-soft);
            line-height: 1.6;
        }

        body.adm-gallery-modal-open {
            overflow: hidden;
        }

        @media (max-width: 1500px) {
            .adm-gallery-toolbar {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .adm-gallery-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 1180px) {
            .adm-gallery-hero,
            .adm-gallery-form-layout,
            .adm-gallery-results,
            .adm-gallery-section-bar,
            .adm-gallery-section-head,
            .adm-gallery-toggle-card {
                grid-template-columns: 1fr;
            }

            .adm-gallery-section-bar,
            .adm-gallery-section-head,
            .adm-gallery-toggle-card {
                display: grid;
            }

            .adm-gallery-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .adm-gallery-action-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .adm-gallery-field-grid-2,
            .adm-gallery-meta-list,
            .adm-gallery-form-actions,
            .adm-gallery-edit-action-buttons,
            .adm-gallery-action-row,
            .adm-gallery-stats-grid,
            .adm-gallery-order-form,
            .adm-gallery-order-steps {
                grid-template-columns: 1fr;
            }

            .adm-gallery-hero,
            .adm-gallery-section-card,
            .adm-gallery-card-body,
            .adm-gallery-empty-state,
            .adm-gallery-modal-dialog {
                padding-left: 18px;
                padding-right: 18px;
            }

            .adm-gallery-card-media {
                min-height: 240px;
            }

            .adm-gallery-order-controls {
                min-width: 100%;
                justify-items: stretch;
            }

            .adm-gallery-chip-row {
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .adm-gallery-hero-actions,
            .adm-gallery-empty-state .adm-gallery-hero-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .adm-gallery-hero-actions .adm-gallery-btn,
            .adm-gallery-empty-state .adm-gallery-hero-actions .adm-gallery-btn {
                width: 100%;
                justify-content: center;
            }

            .adm-gallery-modal {
                padding: 14px;
            }

            .adm-gallery-modal-media {
                min-height: 240px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $photos = $photos ?? collect();
        $stats = array_merge([
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'images' => 0,
            'videos' => 0,
            'scheduled' => 0,
        ], $stats ?? []);
        $filters = array_merge([
            'q' => '',
            'status' => 'all',
            'type' => 'all',
            'sort' => 'manual',
        ], $filters ?? []);
        $sortOptions = $sortOptions ?? ['manual' => 'Ordre manuel'];
        $hasFilters = filled($filters['q'])
            || (($filters['status'] ?? 'all') !== 'all')
            || (($filters['type'] ?? 'all') !== 'all')
            || (($filters['sort'] ?? 'manual') !== 'manual');
        $composeOpen = $errors->any()
            || filled(old('title'))
            || filled(old('description'))
            || filled(old('filter_key'))
            || old('sort_order') !== null
            || old('published_at') !== null;
        $statusOptions = [
            'all' => 'Tous',
            'active' => 'Actifs',
            'inactive' => 'Inactifs',
            'scheduled' => 'Planifies',
        ];
        $typeOptions = [
            'all' => 'Tout',
            'image' => 'Images',
            'video' => 'Videos',
        ];
        $activeFilterPills = [];

        if (filled($filters['q'])) {
            $activeFilterPills[] = 'Recherche: '.$filters['q'];
        }

        if (($filters['status'] ?? 'all') !== 'all') {
            $activeFilterPills[] = 'Statut: '.($statusOptions[$filters['status']] ?? ucfirst((string) $filters['status']));
        }

        if (($filters['type'] ?? 'all') !== 'all') {
            $activeFilterPills[] = 'Type: '.($typeOptions[$filters['type']] ?? ucfirst((string) $filters['type']));
        }

        if (($filters['sort'] ?? 'manual') !== 'manual') {
            $activeFilterPills[] = 'Tri: '.($sortOptions[$filters['sort']] ?? $filters['sort']);
        }
    @endphp

    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax adm-gallery-page-header">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Admin galerie</h2>
                    <h1 class="ph-caption-title">Photos</h1>
                    <div class="ph-caption-description max-width-700">
                        Ajoutez, previsualisez, classez et publiez les medias publics depuis une interface galerie plus nette et plus rapide a piloter.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Control Center</h2>
                        <h1 class="ph-caption-title">Pilotage</h1>
                        <div class="ph-caption-description max-width-700">
                            Tri, statut, edition rapide et ordre public de la galerie dans une seule zone admin.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs>
                        <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                    </defs>
                    <text dy="30">
                        <textPath xlink:href="#textcircle">Admin Galerie - Trier Publier Organiser -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-40 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell adm-gallery-page">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="adm-gallery-section-bar">
                            <div>
                                <span class="adm-gallery-section-eyebrow">Actions rapides</span>
                                <h2>Gestion de la galerie photo</h2>
                            </div>
                            <p>Le hero reprend le langage de la galerie publique, puis l'interface admin bascule sur un pilotage plus operationnel juste en dessous.</p>
                        </div>

                            <div class="adm-gallery-hero-actions">
                                <a href="#gallery-compose" class="tt-btn tt-btn-primary tt-magnetic-item adm-gallery-btn" data-gallery-compose-open>
                                    <span>Ajouter une photo</span>
                                </a>

                                <a href="{{ route('marketing.gallery-photos') }}" class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn" target="_blank" rel="noopener">
                                    <span>Voir la galerie publique</span>
                                </a>
                            </div>
                    </section>

                    <div class="adm-gallery-flashes">
                        @if(session('success'))
                            <div class="adm-gallery-flash is-success">
                                <strong>Operation terminee</strong>
                                <p>{{ session('success') }}</p>
                            </div>
                        @endif

                        @if(($autoImportedCount ?? 0) > 0)
                            <div class="adm-gallery-flash is-info">
                                <strong>Import automatique</strong>
                                <p>{{ (int) $autoImportedCount }} media(s) importes depuis la source historique locale pour initialiser la galerie.</p>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="adm-gallery-flash is-error">
                                <strong>Validation a corriger</strong>
                                <p>{{ $errors->first() }}</p>
                            </div>
                        @endif
                    </div>

                    <section class="adm-surface">
                        <div class="adm-gallery-section-bar">
                            <div>
                                <span class="adm-gallery-section-eyebrow">Vue d'ensemble</span>
                                <h2>Statistiques galerie</h2>
                            </div>
                            <p>Les compteurs permettent de voir en un coup d'oeil le volume disponible, le stock actif et les medias en attente de publication.</p>
                        </div>

                        <div class="adm-gallery-stats-grid">
                            <div class="adm-gallery-stat-card">
                                <span>Total</span>
                                <strong>{{ (int) $stats['total'] }}</strong>
                                <p>Media actuellement en base.</p>
                            </div>

                            <div class="adm-gallery-stat-card">
                                <span>Actifs</span>
                                <strong>{{ (int) $stats['active'] }}</strong>
                                <p>Disponibles des maintenant si la date le permet.</p>
                            </div>

                            <div class="adm-gallery-stat-card">
                                <span>Inactifs</span>
                                <strong>{{ (int) $stats['inactive'] }}</strong>
                                <p>Prets a etre retravailles avant diffusion.</p>
                            </div>

                            <div class="adm-gallery-stat-card">
                                <span>Videos</span>
                                <strong>{{ (int) $stats['videos'] }}</strong>
                                <p>Clips et sequences mixees dans la bibliotheque.</p>
                            </div>

                            <div class="adm-gallery-stat-card">
                                <span>Planifies</span>
                                <strong>{{ (int) $stats['scheduled'] }}</strong>
                                <p>En attente de leur date de mise en ligne.</p>
                            </div>
                        </div>
                    </section>

                    <section class="adm-surface" id="gallery-compose-section">
                        <details class="adm-gallery-compose" id="gallery-compose" @if($composeOpen) open @endif>
                            <summary>
                                <div>
                                    <span class="adm-gallery-section-eyebrow">Creation</span>
                                    <h2>Ajouter une nouvelle photo</h2>
                                    <p>Le formulaire reste complet mais mieux structure : upload, infos publiques, preview, statut et publication dans un seul panneau.</p>
                                </div>

                                <span class="adm-gallery-compose-indicator">Ouvrir le formulaire</span>
                            </summary>

                            <div class="adm-gallery-compose-body">
                                @include('pages.admin.gallery-photos.partials.create-form')
                            </div>
                        </details>
                    </section>

                    <section class="adm-surface" id="gallery-library">
                        <div class="adm-gallery-section-bar">
                            <div>
                                <span class="adm-gallery-section-eyebrow">Bibliotheque</span>
                                <h2>Piloter la galerie existante</h2>
                            </div>
                            <p>Recherche, filtres, tri et cartes medias pour travailler plus vite quand la galerie devient dense.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.gallery-photos.index') }}" class="adm-gallery-toolbar">
                            <div class="adm-gallery-toolbar-card">
                                <label class="adm-gallery-form-label" for="gallery_q">Recherche</label>
                                <input class="tt-form-control" id="gallery_q" name="q" type="text" value="{{ $filters['q'] }}" placeholder="Titre, description, categorie ou filtre">
                            </div>

                            <div class="adm-gallery-toolbar-card">
                                <label class="adm-gallery-form-label" for="gallery_status">Statut</label>
                                <select class="tt-form-control" id="gallery_status" name="status">
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="adm-gallery-toolbar-card">
                                <label class="adm-gallery-form-label" for="gallery_type">Type</label>
                                <select class="tt-form-control" id="gallery_type" name="type">
                                    @foreach($typeOptions as $value => $label)
                                        <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="adm-gallery-toolbar-card">
                                <label class="adm-gallery-form-label" for="gallery_sort">Tri</label>
                                <select class="tt-form-control" id="gallery_sort" name="sort">
                                    @foreach($sortOptions as $value => $label)
                                        <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="tt-btn tt-btn-primary tt-magnetic-item adm-gallery-btn" type="submit">
                                <span>Appliquer</span>
                            </button>

                            <a href="{{ route('admin.gallery-photos.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn">
                                <span>Reinitialiser</span>
                            </a>
                        </form>

                        <div class="adm-gallery-results">
                            <div class="adm-gallery-results-copy">
                                <strong>{{ $photos->total() }} resultat(s)</strong>
                                <span>Sur {{ (int) $stats['total'] }} media(s) au total. Tri courant : {{ $sortOptions[$filters['sort']] ?? 'Ordre manuel' }}.</span>
                            </div>

                            <div class="adm-gallery-chip-row">
                                @if($hasFilters)
                                    @foreach($activeFilterPills as $pill)
                                        <span class="adm-pill">{{ $pill }}</span>
                                    @endforeach
                                @else
                                    <span class="adm-pill">Vue globale</span>
                                    <span class="adm-pill">Cartes admin avec miniatures</span>
                                    <span class="adm-pill">Edition rapide integree</span>
                                @endif
                            </div>
                        </div>

                        @if($photos->count())
                            <div class="adm-gallery-grid">
                                @foreach($photos as $photo)
                                    @include('pages.admin.gallery-photos.partials.card', ['photo' => $photo])
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $photos->onEachSide(1)->links('vendor.pagination.admin') }}</div>
                        @else
                            <div class="adm-gallery-empty-state">
                                <h3>{{ $hasFilters ? 'Aucun resultat pour ce filtre' : 'La galerie est encore vide' }}</h3>
                                <p>
                                    {{ $hasFilters
                                        ? 'Ajustez les filtres ou reinitialisez la barre d outils pour retrouver vos medias.'
                                        : 'Ajoutez le premier media depuis le formulaire de creation pour lancer une vraie bibliotheque admin exploitable.' }}
                                </p>

                                <div class="adm-gallery-hero-actions">
                                    @if($hasFilters)
                                        <a href="{{ route('admin.gallery-photos.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn">
                                            <span>Reinitialiser les filtres</span>
                                        </a>
                                    @endif

                                    <a href="#gallery-compose" class="tt-btn tt-btn-primary tt-magnetic-item adm-gallery-btn" data-gallery-compose-open>
                                        <span>Ajouter une photo</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>

    @include('pages.admin.gallery-photos.partials.preview-modal')
@endsection

@section('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var composePanel = document.getElementById('gallery-compose');

            var openComposePanel = function () {
                if (!composePanel) {
                    return;
                }

                composePanel.open = true;
                composePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });

                var firstField = composePanel.querySelector('input:not([type="hidden"]), textarea, select');
                if (firstField) {
                    window.setTimeout(function () {
                        firstField.focus();
                    }, 160);
                }
            };

            document.querySelectorAll('[data-gallery-compose-open]').forEach(function (trigger) {
                trigger.addEventListener('click', function (event) {
                    event.preventDefault();
                    openComposePanel();
                });
            });

            if (window.location.hash === '#gallery-compose') {
                openComposePanel();
            }

            document.querySelectorAll('[data-gallery-close-details]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var details = button.closest('details');
                    if (details) {
                        details.open = false;
                    }
                });
            });

            var uploadInput = document.querySelector('[data-gallery-upload-input]');
            var uploadPreview = document.querySelector('[data-gallery-upload-preview]');
            var uploadName = document.querySelector('[data-gallery-upload-name]');
            var uploadMeta = document.querySelector('[data-gallery-upload-meta]');
            var emptyPreviewMarkup = uploadPreview ? uploadPreview.innerHTML : '';
            var currentObjectUrl = null;

            var formatBytes = function (bytes) {
                if (!bytes) {
                    return '0 Ko';
                }

                if (bytes >= 1048576) {
                    return (bytes / 1048576).toFixed(1).replace('.', ',') + ' Mo';
                }

                return Math.max(1, Math.round(bytes / 1024)) + ' Ko';
            };

            var releaseObjectUrl = function () {
                if (currentObjectUrl) {
                    URL.revokeObjectURL(currentObjectUrl);
                    currentObjectUrl = null;
                }
            };

            if (uploadInput && uploadPreview && uploadName && uploadMeta) {
                uploadInput.addEventListener('change', function () {
                    var file = uploadInput.files && uploadInput.files[0] ? uploadInput.files[0] : null;

                    releaseObjectUrl();

                    if (!file) {
                        uploadPreview.innerHTML = emptyPreviewMarkup;
                        uploadName.textContent = 'Aucun fichier selectionne';
                        uploadMeta.textContent = 'JPG, PNG, WebP, AVIF, GIF, MP4, WebM';
                        return;
                    }

                    currentObjectUrl = URL.createObjectURL(file);
                    uploadPreview.innerHTML = '';

                    var previewElement;
                    if ((file.type || '').indexOf('video/') === 0) {
                        previewElement = document.createElement('video');
                        previewElement.src = currentObjectUrl;
                        previewElement.controls = true;
                        previewElement.muted = true;
                        previewElement.playsInline = true;
                    } else {
                        previewElement = document.createElement('img');
                        previewElement.src = currentObjectUrl;
                        previewElement.alt = file.name;
                    }

                    uploadPreview.appendChild(previewElement);
                    uploadName.textContent = file.name;
                    uploadMeta.textContent = [file.type || 'type inconnu', formatBytes(file.size || 0)].join(' - ');
                });
            }

            var modal = document.querySelector('[data-gallery-modal]');
            if (modal) {
                var modalMedia = modal.querySelector('[data-gallery-modal-media]');
                var modalTitle = modal.querySelector('[data-gallery-modal-title]');
                var modalCaption = modal.querySelector('[data-gallery-modal-caption]');

                var closeModal = function () {
                    modal.hidden = true;
                    document.body.classList.remove('adm-gallery-modal-open');
                    if (modalMedia) {
                        modalMedia.innerHTML = '';
                    }
                };

                var openModal = function (dataset) {
                    if (!modalMedia || !modalTitle || !modalCaption) {
                        return;
                    }

                    modalMedia.innerHTML = '';

                    if (dataset.previewType === 'video') {
                        var video = document.createElement('video');
                        video.controls = true;
                        video.autoplay = true;
                        video.playsInline = true;
                        video.preload = 'metadata';

                        var source = document.createElement('source');
                        source.src = dataset.previewSrc || '';
                        source.type = dataset.previewMime || 'video/mp4';
                        video.appendChild(source);
                        modalMedia.appendChild(video);
                    } else {
                        var image = document.createElement('img');
                        image.src = dataset.previewSrc || '';
                        image.alt = dataset.previewTitle || 'Previsualisation galerie';
                        modalMedia.appendChild(image);
                    }

                    modalTitle.textContent = dataset.previewTitle || 'Previsualisation';
                    modalCaption.textContent = dataset.previewCaption || 'Controle rapide du media sans quitter la page.';
                    modal.hidden = false;
                    document.body.classList.add('adm-gallery-modal-open');
                };

                document.querySelectorAll('[data-gallery-preview-trigger]').forEach(function (trigger) {
                    trigger.addEventListener('click', function () {
                        openModal(trigger.dataset);
                    });
                });

                modal.querySelectorAll('[data-gallery-modal-close]').forEach(function (button) {
                    button.addEventListener('click', closeModal);
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && !modal.hidden) {
                        closeModal();
                    }
                });
            }

            if (!document.body.classList.contains('is-mobile')) {
                document.querySelectorAll('[data-gallery-card]').forEach(function (card) {
                    var video = card.querySelector('[data-gallery-card-video]');
                    if (!video) {
                        return;
                    }

                    var startPreview = function () {
                        card.classList.add('is-previewing');
                        var playPromise = video.play();
                        if (playPromise && typeof playPromise.catch === 'function') {
                            playPromise.catch(function () {});
                        }
                    };

                    var stopPreview = function () {
                        card.classList.remove('is-previewing');
                        video.pause();
                        video.currentTime = 0;
                    };

                    card.addEventListener('mouseenter', startPreview);
                    card.addEventListener('mouseleave', stopPreview);
                    card.addEventListener('focusin', startPreview);
                    card.addEventListener('focusout', stopPreview);
                });
            }
        });
    </script>
    @include('pages.admin.partials.theme-scripts')
@endsection
