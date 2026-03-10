<style>
    .erah-help-shell { position: relative; }
    .erah-help-overlap { position: relative; z-index: 4; margin-top: -88px; }
    .erah-help-surface, .erah-help-card, .erah-help-band, .erah-help-video, .erah-help-glossary, .erah-help-highlight, .erah-help-starter, .erah-help-faq-panel, .erah-help-chat-shell, .erah-help-chat-panel, .erah-help-chat-sidebar {
        position: relative; overflow: hidden; padding: 34px; border: 1px solid rgba(255,255,255,.11); border-radius: 28px;
        background: linear-gradient(145deg, rgba(255,255,255,.045), rgba(255,255,255,.018)); box-shadow: 0 20px 60px rgba(0,0,0,.18);
    }
    .erah-help-surface::before, .erah-help-card::before, .erah-help-band::before, .erah-help-video::before, .erah-help-highlight::before, .erah-help-starter::before, .erah-help-faq-panel::before, .erah-help-chat-shell::before, .erah-help-chat-panel::before, .erah-help-chat-sidebar::before {
        content: ""; position: absolute; inset: 0; background: radial-gradient(circle at top left, rgba(216,7,7,.15), transparent 45%); pointer-events: none;
    }
    .erah-help-search-grid, .erah-help-split, .erah-help-footer-grid, .erah-help-chat-grid { display: grid; gap: 26px; align-items: start; }
    .erah-help-search-grid { grid-template-columns: minmax(0,1.25fr) minmax(320px,.75fr); }
    .erah-help-split { grid-template-columns: minmax(0,.9fr) minmax(0,1.1fr); }
    .erah-help-footer-grid { grid-template-columns: minmax(0,1.15fr) minmax(320px,.85fr); }
    .erah-help-chat-grid { grid-template-columns: minmax(300px,.78fr) minmax(0,1.22fr); }
    .erah-help-overline { display: inline-flex; align-items: center; gap: 10px; margin-bottom: 16px; letter-spacing: .22em; text-transform: uppercase; font-size: 12px; color: rgba(255,255,255,.58); }
    .erah-help-overline::before { content: ""; width: 28px; height: 1px; background: rgba(216,7,7,.85); }
    .erah-help-anchor-row, .erah-help-pill-row, .erah-help-mini-links, .erah-help-meta-row, .erah-help-search-actions, .erah-help-chat-prompts { display: flex; flex-wrap: wrap; gap: 12px; }
    .erah-help-anchor-row a, .erah-help-meta-pill, .erah-help-mini-link, .erah-help-chip-button {
        display: inline-flex; align-items: center; min-height: 42px; padding: 0 18px; border: 1px solid rgba(255,255,255,.12); border-radius: 999px;
        color: rgba(255,255,255,.82); background: rgba(255,255,255,.03); font-size: 12px; letter-spacing: .12em; text-transform: uppercase;
    }
    .erah-help-anchor-row a:hover, .erah-help-mini-link:hover, .erah-help-chip-button:hover { border-color: rgba(216,7,7,.55); color: #fff; }
    .erah-help-search-form .tt-form-group { margin-bottom: 0; }
    .erah-help-search-actions { margin-top: 22px; }
    .erah-help-stat-grid, .erah-help-highlight-grid, .erah-help-starter-grid, .erah-help-quick-grid, .erah-help-category-grid { display: grid; gap: 16px; }
    .erah-help-stat-grid { grid-template-columns: repeat(2, minmax(0,1fr)); margin-top: 26px; }
    .erah-help-highlight-grid, .erah-help-quick-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
    .erah-help-category-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
    .erah-help-starter-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
    .erah-help-stat { padding: 18px 18px 16px; border-radius: 20px; background: rgba(255,255,255,.028); border: 1px solid rgba(255,255,255,.1); }
    .erah-help-stat-label { display: block; margin-bottom: 8px; font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: rgba(255,255,255,.46); }
    .erah-help-stat-value { font-family: "Big Shoulders Display", sans-serif; font-size: 52px; line-height: .9; color: #fff; }
    .erah-help-lead { max-width: 860px; font-size: 20px; line-height: 1.7; color: rgba(255,255,255,.8); }
    .erah-help-band { height: 100%; }
    .erah-help-band-count { display: inline-flex; align-items: center; justify-content: center; min-width: 48px; height: 48px; margin-bottom: 18px; border-radius: 16px; background: rgba(216,7,7,.14); color: #fff; font-family: "Big Shoulders Display", sans-serif; font-size: 28px; }
    .erah-help-list { margin: 20px 0 0; padding: 0; list-style: none; }
    .erah-help-list li { position: relative; padding-left: 18px; margin-bottom: 10px; color: rgba(255,255,255,.72); }
    .erah-help-list li::before { content: ""; position: absolute; top: 10px; left: 0; width: 7px; height: 7px; border-radius: 50%; background: rgba(216,7,7,.85); }
    .erah-help-video-frame { position: relative; overflow: hidden; border-radius: 22px; border: 1px solid rgba(255,255,255,.12); background: radial-gradient(circle at top left, rgba(216,7,7,.2), transparent 40%), linear-gradient(135deg, rgba(255,255,255,.04), rgba(255,255,255,.015)); aspect-ratio: 16 / 9; }
    .erah-help-video-frame iframe { width: 100%; height: 100%; border: 0; }
    .erah-help-video-empty { display: flex; flex-direction: column; justify-content: flex-end; height: 100%; padding: 28px; }
    .erah-help-badge { display: inline-flex; margin-bottom: 14px; padding: 8px 12px; border-radius: 999px; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1); font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.64); }
    .erah-help-category-preview { display: grid; gap: 12px; margin-top: 22px; }
    .erah-help-category-preview-item { padding: 14px 16px; border-radius: 18px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); color: rgba(255,255,255,.76); }
    .erah-help-tour .tt-accordion-content { padding-top: 10px; }
    .erah-help-step-visual { margin: 18px 0 22px; padding: 18px 20px; border-radius: 22px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); }
    .erah-help-step-progress { display: inline-flex; align-items: center; justify-content: center; min-width: 64px; height: 64px; margin-bottom: 14px; border-radius: 18px; background: rgba(216,7,7,.14); color: #fff; font-family: "Big Shoulders Display", sans-serif; font-size: 34px; line-height: 1; }
    .erah-help-faq-layout { display: grid; gap: 24px; grid-template-columns: minmax(300px,.75fr) minmax(0,1.25fr); align-items: start; }
    .erah-help-faq-item-meta, .erah-help-source-list { display: flex; flex-wrap: wrap; gap: 10px; }
    .erah-help-faq-item-panel { margin-top: 18px; padding: 18px 20px; border-radius: 22px; background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08); }
    .erah-help-chat-log { display: grid; gap: 18px; max-height: 620px; padding-right: 4px; overflow-y: auto; }
    .erah-help-chat-message { max-width: 92%; padding: 20px 22px; border-radius: 24px; border: 1px solid rgba(255,255,255,.1); background: rgba(255,255,255,.03); }
    .erah-help-chat-message--assistant { justify-self: start; border-top-left-radius: 10px; }
    .erah-help-chat-message--user { justify-self: end; background: rgba(216,7,7,.16); border-color: rgba(216,7,7,.4); border-top-right-radius: 10px; }
    .erah-help-chat-message p:last-child, .erah-help-chat-message ul:last-child { margin-bottom: 0; }
    .erah-help-chat-meta { margin-top: 14px; font-size: 12px; line-height: 1.8; color: rgba(255,255,255,.58); }
    .erah-help-chat-composer textarea {
        width: 100%; min-height: 124px; padding: 18px 20px; border-radius: 24px; border: 1px solid rgba(255,255,255,.12);
        background: rgba(0,0,0,.24); color: #fff; resize: vertical;
    }
    .erah-help-chat-composer textarea::placeholder { color: rgba(255,255,255,.4); }
    .erah-help-chat-empty { padding: 24px; border-radius: 22px; border: 1px dashed rgba(255,255,255,.16); background: rgba(255,255,255,.025); color: rgba(255,255,255,.68); }
    @media (max-width: 1399.98px) { .erah-help-search-grid, .erah-help-split, .erah-help-footer-grid, .erah-help-chat-grid, .erah-help-faq-layout { grid-template-columns: 1fr; } }
    @media (max-width: 1199.98px) { .erah-help-category-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (max-width: 1024px) { .erah-help-overlap { margin-top: -34px; } .erah-help-highlight-grid, .erah-help-starter-grid, .erah-help-quick-grid { grid-template-columns: 1fr; } }
    @media (max-width: 767.98px) {
        .erah-help-surface, .erah-help-card, .erah-help-band, .erah-help-video, .erah-help-glossary, .erah-help-highlight, .erah-help-starter, .erah-help-faq-panel, .erah-help-chat-shell, .erah-help-chat-panel, .erah-help-chat-sidebar { padding: 24px; border-radius: 24px; }
        .erah-help-stat-grid, .erah-help-category-grid { grid-template-columns: 1fr 1fr; }
        .erah-help-stat-value { font-size: 42px; }
        .erah-help-lead { font-size: 17px; }
    }
    @media (max-width: 575.98px) {
        .erah-help-stat-grid, .erah-help-category-grid { grid-template-columns: 1fr; }
        .erah-help-chat-message { max-width: 100%; }
    }
</style>
