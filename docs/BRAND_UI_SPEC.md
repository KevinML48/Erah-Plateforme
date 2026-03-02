# ERAH Brand UI Spec (Red/Black)

## 1) Brand Direction
- Visual identity: esport premium, high contrast, black + red.
- Mood: tactical, metallic, sharp, competitive.
- Do not use cyan/violet/green as primary accent.
- Red must be the only primary interaction color family.
- Logo handling: no ERAH logo asset was found in `public/`; temporary fallback is a monogram `E` badge in layout components.

## 2) Core Palette (Tokenized)
- `--erah-bg`: `#070809` (app background)
- `--erah-surface`: `#0F1216` (cards/default surfaces)
- `--erah-surface-2`: `#171C22` (raised/hover surfaces)
- `--erah-border`: `rgba(255,255,255,0.10)` (subtle lines)
- `--erah-text`: `rgba(255,255,255,0.92)` (primary text)
- `--erah-muted`: `rgba(255,255,255,0.62)` (secondary text)
- `--erah-muted-2`: `rgba(255,255,255,0.46)` (meta text)
- `--erah-red`: `#E11D2F` (primary CTA/active)
- `--erah-red-2`: `#9F1320` (dark red depth)
- `--erah-glow`: `0 16px 42px rgba(225,29,47,0.22)`
- `--success`: `#63D48F`
- `--warning`: `#D39A52`
- `--danger`: `#FF6666`

## 3) Red Gradient Set
- Primary CTA: `linear-gradient(92deg, #FF2B3F 0%, #E11D2F 48%, #BA1324 100%)`
- Secondary dark steel: `linear-gradient(92deg, #292F37 0%, #20262F 100%)`
- Warm red alt: `linear-gradient(92deg, #FF3B4F 0%, #DF1F32 55%, #8D131F 100%)`

## 4) Surfaces / Elevation
- Radius:
  - Card: `20px`
  - Pill: `9999px`
- Shadows:
  - Base card: soft dark shadow
  - Hover card: subtle red glow (`--erah-glow`)
- Surface texture:
  - light internal highlight for top-left depth
  - very subtle carbon diagonal lines in global background

## 5) Typography
- Body: Inter
- Display titles/KPI: Rajdhani
- Headings:
  - H1: 30-36
  - H2: 22-26
  - H3: 17-20
- KPI values: 40-44 with display font
- Labels/badges/buttons: uppercase + slight letter spacing

## 6) Motion Rules
- Timing:
  - quick interactions: 150-220ms
  - page entrance: ~280ms
- Easing: `cubic-bezier(0.22, 0.9, 0.2, 1)`
- Micro interactions:
  - button press: `scale(0.98)`
  - list/card hover border highlight
  - tabs active state with red glow
- 3D tilt:
  - desktop only
  - max rotation: ~6-10 degrees total
  - `requestAnimationFrame` driven
- Reduced motion:
  - disable tilt
  - disable keyframe animations
  - keep instant/simple transitions

## 7) Components & Usage
- `x-app-layout`: global shell, page transition wrapper
- `x-sidebar`: desktop navigation + brand block
- `x-tabbar`: mobile navigation (Dashboard/Clips/Matchs/Classement/Notifs + Duels shortcut)
- `x-card`: default card
- `x-card-3d`: interactive desktop tilt + glow
- `x-kpi-card`: scoreboard numbers, progression
- `x-button`: `primary`, `secondary`, `warm`, `ghost`, `danger`
- `x-badge`: `league`, `status`, `category`, `success`, `warning`, `danger`
- `x-input` / `x-select` / `x-textarea`: dark fields with red focus
- `x-progress`: red gradient progress
- `x-alert`: error/success/warning semantic
- `x-empty-state`: neutral empty state with red icon hint

## 8) Accessibility Rules
- Visible focus ring on all interactive elements
- Min touch target: 44x44
- Contrast preserved on dark surfaces
- Reduced motion support via `prefers-reduced-motion: reduce`

## 9) Performance Rules
- CSS-first effects, no heavy visual libraries
- No WebGL dependency
- Tiny JS for tilt/page effects only
- Progressive enhancement: all flows still work without JS

## 10) Brand Preview
- Route: `/brand-preview` (alias of UI kit preview)
- Existing `/ui-kit` remains available for component checks.
