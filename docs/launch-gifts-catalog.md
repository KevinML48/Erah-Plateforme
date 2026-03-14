# Catalogue cadeaux de lancement

Source canonique :

- `database/seeders/data/launch_gifts.php`

Seeder dedie :

- `Database\\Seeders\\LaunchGiftCatalogSeeder`

Principes :

- 30 cadeaux de lancement exactement
- catalogue idempotent base sur des cles source stables
- points plateforme comme monnaie unique
- anciens cadeaux hors catalogue desactives au lieu d etre supprimes
- les cadeaux `profile_digital` attribuent un vrai objet de profil dans `user_profile_cosmetics`
- les cadeaux `manual_reward` et `digital_reward` passent par le workflow redemption admin existant
- les objets de profil actifs peuvent etre equipes depuis le profil membre et s affichent sur le profil public

Commandes Laravel Cloud :

```bash
php artisan migrate --force
php artisan db:seed --class="Database\\Seeders\\LaunchGiftCatalogSeeder" --force
```

Migration :

- migrations requises :
  - `2026_03_14_101000_add_launch_catalog_fields_to_gifts_table.php`
  - `2026_03_14_101100_create_user_profile_cosmetics_table.php`

Verification manuelle apres injection :

- verifier `/console/gifts`
- verifier `/console/admin/gifts`
- verifier que les 30 cadeaux sont actifs, ordonnes et sans doublons
- verifier les categories `Profil numerique`, `Digital`, `Recompense manuelle`, `Physique`, `Premium`
- verifier que les cadeaux premium ont un stock tres faible
- verifier qu un cadeau hors catalogue existant est passe inactif
- acheter un objet `Badge exclusif de profil` et verifier :
  - redemption passee en `delivered`
  - ligne creee dans `user_profile_cosmetics`
  - badge visible sur `/console/profile` et `/u/{user}`
- acheter `Pack profil prestige` et verifier :
  - plusieurs objets debloques
  - theme, couleur pseudo et mise en avant avec expiration
- acheter `1 mois de mise en avant profil premium` et verifier :
  - prolongation de `profile_featured_until`
  - profil public affiche bien la mise en avant tant que l effet n est pas expire
- acheter `Achat Amazon 10€` ou `Gain libre 5€` et verifier :
  - redemption creee en `pending`
  - aucun objet de profil cree
  - demande visible et traitable dans `/console/admin/gifts`
