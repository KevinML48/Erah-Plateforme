# UI SPEC - Plateforme ERAH

## 1. Vision
Interface dark premium mobile-first, lisible et cohérente sur toutes les pages métier (auth, dashboard, clips, classements, notifications, duels, profil, admin).

## 2. Design Tokens
### Couleurs (CSS variables)
- `--bg: #0B0F14`
- `--surface: #121824`
- `--surface-2: #161F2E`
- `--border: rgba(255,255,255,0.08)`
- `--text: rgba(255,255,255,0.92)`
- `--muted: rgba(255,255,255,0.62)`
- `--muted-2: rgba(255,255,255,0.42)`
- `--danger: #FF4D4D`
- `--success: #29E7A5`
- `--warning: #FFB020`

### Gradients
- `gradient-primary`: vert -> cyan
- `gradient-secondary`: violet -> bleu
- `gradient-warm`: orange -> rose

### Ombres et rayons
- `shadow-soft`: ombre diffuse premium
- `r-card: 20px`
- `r-pill: 9999px`

## 3. Typographie
- Font principale: Inter (fallback system-ui)
- Base: 14-16px
- `h1`: 28-32px
- `h2`: 20-24px
- `h3`: 16-18px
- KPI: 34-44px
- Line-height confortable (`1.4` à `1.6`)

## 4. Grille et spacing
- Mobile first
- Conteneur desktop max: `1200px`
- Padding principal: `16px` mobile, `24px` desktop
- Espacements composants: 8/12/16/24/32

## 5. Composants UI obligatoires
- Layout: `x-app-layout`, `x-topbar`, `x-sidebar`, `x-tabbar`
- Surface: `x-card`, `x-kpi-card`
- Contrôles: `x-button`, `x-input`, `x-textarea`, `x-select`, `x-toggle`, `x-tabs`
- Feedback: `x-alert`, `x-empty-state`, `x-skeleton` (optionnel)
- Data display: `x-badge`, `x-progress`, `x-avatar`, `x-list-item`

## 6. Navigation
- Mobile (`<= md`): bottom tab bar fixe (Dashboard, Clips, Classement, Notifs, Duels)
- Desktop (`>= lg`): sidebar gauche + topbar

## 7. Accessibilité
- Focus visible (`focus-visible:ring`)
- Contraste fort texte/fond
- Cibles tactiles min `44px`
- Labels explicites pour inputs
- États erreurs/validation visibles

## 8. Stratégie de rendu
- Blade server-render en priorité
- Actions via `POST/PUT/DELETE` forms + redirect + flash messages
- JS léger optionnel (amélioration progressive) pour petites interactions non critiques (copy link, toggles visuels)

## 9. États UI obligatoires
- Loading/skeleton sur listes principales
- Empty states dédiés (clips, notifs, duels, favoris, leaderboard)
- Error states (bandeau + message contextualisé)

## 10. Cohérence visuelle
- Même échelle de rayons, bordures, ombres, typographie
- KPI grands + sous-texte muted
- Cartes arrondies avec accent gradient limité aux CTA/points-clés
- Pas de rupture de style entre pages user et admin
