# Routes Mapping

Cartographie simplifiee des parcours effectivement exposes par l application.

## 1) Entree publique (`/`)

- Rendu : landing marketing Blade
- Objectif : faire decouvrir ERAH, ses modules publics et ses appels a l action
- Points d entree :
  - login / inscription
  - parcours public `/app/*`
  - centre d aide `/aide`
  - page supporter `/supporter`

## 2) Auth (`/login`, `/register`, social auth)

- Rendu principal : surfaces Inertia deja branchees
- Actions :
  - connexion classique
  - inscription
  - redirect Google / Discord

## 3) Parcours public `/app/*`

Lecture publique ou semi-publique des modules decouverte :

- `/app/classement`
- `/app/classement/{leagueKey}`
- `/app/clips`
- `/app/clips/{slug}`
- `/app/matchs`
- `/app/matchs/{matchId}`
- `/app/statistics`
- `/app/duels/classement`

Quand un compte est connecte, `/app/*` expose aussi :

- `/app/ma-ligue`
- `/app/missions`
- `/app/paris`
- `/app/duels`
- `/app/notifications`
- `/app/profil`
- `/app/live-codes`
- `/app/quizzes`
- `/app/shop`
- `/app/achievements`

## 4) Espace membre `/console/*`

Le coeur produit connecte passe par `/console/*`.

- `/console/dashboard`
- `/console/onboarding`
- `/console/matches`
- `/console/clips`
- `/console/bets`
- `/console/leaderboards`
- `/console/missions`
- `/console/gifts`
- `/console/duels`
- `/console/wallet`
- `/console/notifications`
- `/console/profile`
- `/console/settings`
- `/console/help`
- `/console/assistant`
- `/console/supporter`

## 5) Aide et assistant

- Public :
  - `/aide`
  - `/aide/assistant`
- Membre :
  - `/console/help`
  - `/console/assistant`

Le centre d aide est Blade-first.
L assistant membre passe par Inertia cote console.

## 6) Admin `/console/admin/*`

Pilotage principal :

- dashboard admin
- clips
- matchs
- cadeaux / demandes
- missions
- live codes
- quizzes
- evenements
- galerie
- avis
- moderation profils publics

## 7) Routes de compatibilite

- `/dashboard` redirige vers `/console/dashboard`
- plusieurs modules conservent des doublons `/app/*` et `/console/*` pour separer decouverte publique et espace membre sans casser les parcours existants

## 8) Strategie de rendu

- Blade pour la majeure partie de l application
- Inertia uniquement sur les surfaces deja integrees proprement
- Actions web via formulaires Laravel + flash messages
- JS leger pour interactions d appoint, PWA, guided tour, assistant et toasts live
