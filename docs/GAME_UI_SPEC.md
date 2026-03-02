# GAME UI SPEC - TOYCAD x ERAH (React + Inertia)

## 1) Direction Artistique
- Style cible: playful premium UI type ToyCAD (board en tuiles, gros arrondis, pill tabs, bottom pill bar, bouton `+` central).
- Adaptation branding ERAH: noir/rouge en UI principale, couleurs vives reservees au fond geometrique decoratif.
- Rendu attendu: mobile-first, immersion type app-jeu, lisible et actionnable.

## 2) Tokens Couleur
- `--ui-bg`: `#0A0A0D`
- `--ui-panel`: `#111114`
- `--ui-surface`: `#17171C`
- `--ui-text`: `rgba(255,255,255,0.92)`
- `--ui-muted`: `rgba(255,255,255,0.70)`
- `--ui-red`: `#E10613`
- `--ui-red-dark`: `#7A0A10`
- `--ui-border`: `rgba(255,255,255,0.10)`
- `--ui-geo-green`: `#8BAF2C`
- `--ui-geo-yellow`: `#C9B723`
- `--ui-geo-orange`: `#C06F1A`

## 3) Fond Geometrique (ToyCAD-like)
- `bg-geo-home`: polygones verts/jaunes/orange + voile noir/rouge.
- `bg-geo-library`: contraste plus fort pour pages medias (clips/matchs).
- `bg-geo-profile`: geometrie plus sobre pour settings/profile/admin.
- Implementation: uniquement CSS gradients/pseudo-elements, sans asset lourd.

## 4) Composants de Base
- Layout:
  - `AppShell`
  - `PillTabBar` (bottom nav persistante)
  - `PlusDrawer` (slide-up menu)
- Tuiles:
  - `Tile`, `HeroTile`, `MediaTile`, `EmptyTile`
  - `StatPill`, `KebabMenu`, `FloatingAction`
  - `PillTabs`
- Form:
  - `PillButton`, `PillInput`, `Toggle`, `Modal`

## 5) Radius / Spacing / Ombres
- Tile radius: `32px`
- Small tiles: `24px`
- Pill radius: `9999px`
- Spacing principal: `16px -> 28px`
- Ombres: diffuses et douces
  - `0 18px 50px rgba(0,0,0,0.45)`
  - glow rouge subtil au hover/focus

## 6) Motion & Accessibility
- Hover desktop: lift `2px`.
- Press mobile: scale `0.98`.
- Tabs/Drawer: transitions courtes (`160-220ms`).
- `prefers-reduced-motion`: desactivation transitions non essentielles.
- Focus ring visible rouge/blanc.

## 7) Navigation
- Bottom nav (toutes tailles): `Dashboard`, `Clips`, `Matchs`, `Classement`, `+`.
- Notifs via topbar (icone cloche).
- `PlusDrawer`: `Paris`, `Wallet`, `Duels`, `Missions`, `Cadeaux`, `Notifs`, `Settings`, `Admin` (si admin).
