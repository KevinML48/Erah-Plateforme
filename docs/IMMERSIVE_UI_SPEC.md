# ERAH Immersive UI Spec

## 1. Universe
- Direction: **ERAH Arena / Control Room**.
- Mood: esport premium, tactical, high-contrast, red/black.
- Visual pillars: layered panels, subtle carbon texture, red neon glow, soft depth, modular HUD composition.

## 2. Core Tokens
- `--erah-red`: `#E10613`
- `--erah-red-dark`: `#7A0A10`
- `--bg`: `#07080B`
- `--surface`: `#0E1117`
- `--panel`: `#0B0D12`
- `--border`: `rgba(255,255,255,0.08)`
- `--text`: `rgba(255,255,255,0.92)`
- `--muted`: `rgba(255,255,255,0.60)`

## 3. Gradients
- Red glow radial:
  - `radial-gradient(circle at 20% 10%, rgba(225,6,19,0.28), transparent 55%)`
- Panel sheen:
  - `linear-gradient(135deg, rgba(255,255,255,0.10), transparent 40%)`
- CTA:
  - `linear-gradient(92deg, #ff1f2f 0%, #e10613 54%, #7a0a10 100%)`

## 4. Textures
- Carbon fiber:
  - `repeating-linear-gradient(135deg, rgba(255,255,255,0.03) 0 1px, transparent 1px 12px)`
- Noise:
  - lightweight pseudo-layer via mixed gradients (no heavy image dependency in V1).

## 5. Elevation
- Panel shadow:
  - `0 20px 60px rgba(0,0,0,0.6)`
- Red glow:
  - `0 0 22px rgba(225,6,19,0.25)`
- Radius:
  - panel `28px`
  - controls `16px`
  - pill `9999px`

## 6. Typography
- Base body: `Inter`
- Display headings/KPI: `Rajdhani`
- H1: `30-38`
- H2: `22-30`
- H3: `18-22`
- KPI: `40-56`
- Labels and badges: uppercase with slight tracking.

## 7. Motion
- Duration: `160ms` to `260ms`
- Easing: `cubic-bezier(0.2, 0.8, 0.2, 1)`
- Interactions:
  - button press scale `0.98`
  - card hover lift/glow
  - tab active underline slide
  - desktop tilt max `6deg` on selected panels
- Reduced motion:
  - disables tilt/parallax/pulse/page transitions.

## 8. Layout Patterns
- Mobile:
  - bottom capsule nav (HUD tabbar)
  - stacked large panels
- Desktop:
  - control-room sidebar + topbar
  - wide modular board with 2-column balance where relevant.

## 9. Component System
- Layout: `x-immersive-layout`, `x-hud-sidebar`, `x-hud-tabbar`, `x-hud-topbar`
- Containers: `x-panel`, `x-panel-card`, `x-hero-panel`
- Data: `x-kpi`, `x-progress-hud`, `x-chip`, `x-badge`, `x-hud-list-item`, `x-leaderboard-row`
- Forms: `x-field`, `x-input`, `x-textarea`, `x-select`, `x-toggle`
- Actions: `x-btn`, `x-btn-soft`, `x-btn-ghost`, `x-btn-danger`
- UX states: `x-empty-state`, `x-alert`

## 10. Accessibility Rules
- Focus ring visible and red-contrast on all actionable controls.
- Minimum touch target `44x44`.
- No information conveyed by color alone (badges include text state).
- `prefers-reduced-motion` respected globally.

## 11. Performance Rules
- CSS-first depth and effects.
- One lightweight JS runtime for tilt and tiny interactions.
- No WebGL/Three.js in V1.
- Progressive enhancement: all forms and key flows work without JS.

## 12. Styleboard
- Route: `/styleboard`
- Shows:
  - 3 background variants
  - 3 panel variants
  - primary/soft/ghost buttons
  - badges/chips
  - list rows
  - immersive bottom nav preview

