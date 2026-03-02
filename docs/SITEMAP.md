# SITEMAP - Plateforme ERAH

## Public
- `/` - Landing
- `/login` - Auth login
- `/register` - Auth register
- `/auth/google/redirect`
- `/auth/discord/redirect`

## Authenticated (User)
- `/onboarding`
- `/dashboard`
- `/clips`
- `/clips/{slug}`
- `/clips/favorites`
- `/leaderboards/me`
- `/leaderboards`
- `/leaderboards/{leagueKey}`
- `/notifications`
- `/notifications/preferences`
- `/duels`
- `/duels/create`
- `/profile`
- `/settings`

## Admin
- `/admin/clips`
- `/admin/clips/create`
- `/admin/clips/{clip}/edit`
- `/admin/matches`
- `/admin/matches/create`

## UI/Dev
- `/ui-kit` (local/admin)
- `/status`

## Navigation structure
### Mobile tab bar
1. Dashboard (`/dashboard`)
2. Clips (`/clips`)
3. Classement (`/leaderboards/me`)
4. Notifs (`/notifications`)
5. Duels (`/duels`)

### Desktop sidebar
- Dashboard
- Clips
- Favoris
- Classements
- Notifications
- Duels
- Profil
- Settings
- Admin (section visible admin uniquement)
