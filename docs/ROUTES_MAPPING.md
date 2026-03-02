# ROUTES MAPPING - Pages -> Data -> Endpoints/Actions

## 1) Landing (`/`)
- Data:
  - Clips récents publiés
  - Mini leaderboard de la ligue Bronze (fallback première ligue active)
- Sources:
  - Query models `Clip::published()`
  - `LeaderboardQuery` (ou fallback Eloquent sur `user_progress`)
- Actions:
  - CTA vers login/register/social redirect

## 2) Auth Login/Register (`/login`, `/register`)
- Data:
  - Erreurs de validation + old input
- Sources:
  - Forms web (POST) vers API auth existante ou contrôleur web proxy
- Endpoints métiers existants:
  - `POST /api/login`
  - `POST /api/register`
  - `GET /auth/google/redirect`
  - `GET /auth/discord/redirect`

## 3) Onboarding (`/onboarding`)
- Data:
  - Règles progression + préférences notifications user
- Sources:
  - `UserNotificationChannel`, `NotificationPreference`
- Actions:
  - Form POST vers web action update prefs (réutilise `UpdateNotificationPreferencesAction`)

## 4) Dashboard (`/dashboard`)
- Data:
  - User profile, progress, ligue actuelle
  - Position classement ligue
  - Duels pending
  - Notifications récentes
  - Clips récents
- Sources:
  - `EnsureUserProgressAction`
  - `LeaderboardQuery`
  - `Duel::forUser()`
  - `Notification`
  - `Clip::published()`
- Actions:
  - Likes/favoris/comments/share clips (forms)
  - Accept/refuse duel
  - Mark notification read

## 5) Clips (`/clips`, `/clips/{slug}`, `/clips/favorites`)
- Data:
  - Feed récent/populaire, détail clip, commentaires, favoris user
- Sources:
  - `Clip::published()`, `ClipFavorite`, `ClipComment`
- Endpoints/actions existants:
  - `POST/DELETE /api/clips/{id}/like`
  - `POST/DELETE /api/clips/{id}/favorite`
  - `POST /api/clips/{id}/comments`
  - `DELETE /api/clips/{clipId}/comments/{commentId}`
  - `POST /api/clips/{id}/share`

## 6) Classements (`/leaderboards/me`, `/leaderboards`, `/leaderboards/{leagueKey}`)
- Data:
  - Ma ligue + progression
  - Toutes ligues actives
  - Leaderboard par ligue
- Sources:
  - `League`, `UserProgress`, `LeaderboardQuery`
- Endpoints existants:
  - `GET /api/leagues`
  - `GET /api/leagues/{key}/leaderboard`
  - `GET /api/me/progress`

## 7) Notifications (`/notifications`, `/notifications/preferences`)
- Data:
  - Notifications user + état read
  - Préférences globales + catégories
- Sources:
  - `Notification`, `UserNotificationChannel`, `NotificationPreference`
- Endpoints/actions existants:
  - `GET /api/notifications`
  - `POST /api/notifications/{id}/read`
  - `GET/PUT /api/me/notification-preferences`

## 8) Duels (`/duels`, `/duels/create`)
- Data:
  - Duels user filtrés par status
  - Liste utilisateurs (sélecteur challenger)
- Sources:
  - `Duel::forUser()`, `User`
- Endpoints/actions existants:
  - `POST /api/duels`
  - `POST /api/duels/{id}/accept`
  - `POST /api/duels/{id}/refuse`
  - `GET /api/duels`

## 9) Profil (`/profile`)
- Data:
  - User + ligue + progress
  - Stats (likes/comments/duels/bets)
  - Historique points transactions
- Sources:
  - `User`, `UserProgress`, `PointsTransaction`, `ClipLike`, `ClipComment`, `Duel`, `Bet`

## 10) Admin Clips (`/admin/clips*`)
- Data:
  - Liste clips, filtres publié/brouillon
  - Form create/edit avec preview slug
- Sources:
  - `Clip`
- Endpoints/actions existants:
  - `POST /api/admin/clips`
  - `PUT /api/admin/clips/{id}`
  - `DELETE /api/admin/clips/{id}`
  - `POST /api/admin/clips/{id}/publish`

## 11) Matches/Bets pages (`/admin/matches`, `/matches`, `/bets/me`)
- Data:
  - Matches publics
  - Bets user
  - Admin settlement
- Sources:
  - `EsportMatch`, `Bet`, `MatchSettlement`
- Endpoints/actions existants:
  - `GET /api/matches`
  - `POST /api/bets`
  - `GET /api/bets/me`
  - `POST /api/admin/matches`
  - `POST /api/admin/matches/{id}/settle`

## Rendu/Action Strategy (globale)
- Pages: Blade server render
- Actions: forms `POST`/`PUT`/`DELETE` + redirect + session flash
- JS: strictement optionnel (copy share link, interactions non bloquantes)
