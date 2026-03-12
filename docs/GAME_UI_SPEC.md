# UI Spec - ERAH Plateforme

Document de reference pour l interface active du projet.

Le produit n est pas un front React-first autonome. La base visible est Blade-first, avec quelques surfaces Inertia la ou elles sont deja en place. Les patterns issus de `templates-neuf` servent de support d integration, pas de base de refonte complete.

## 1) Direction artistique

- Univers principal : noir, rouge, neutres profonds
- Impression produit attendue : esport/community premium, lisible, dense sans etre technique
- Priorite : clarte des parcours, surfaces sombres, typographie expressive, CTA visibles

## 2) Tokens utiles

- `--ui-bg`: `#0A0A0D`
- `--ui-panel`: `#111114`
- `--ui-surface`: `#17171C`
- `--ui-text`: `rgba(255,255,255,0.92)`
- `--ui-muted`: `rgba(255,255,255,0.70)`
- `--ui-red`: `#E10613`
- `--ui-red-dark`: `#7A0A10`
- `--ui-border`: `rgba(255,255,255,0.10)`

## 3) Principes de composition

- Hero et headers : grands titres, sous-titres courts, CTA clairs
- Cartes : surfaces sombres, bordures subtiles, rayons genereux
- Formulaires : label au-dessus, champ sombre premium, focus rouge lisible
- Listes et tableaux : hierarchie simple, statut visible, actions proches
- Empty states : expliquer quoi faire ensuite au lieu d afficher une zone vide brute

## 4) Composants a privilegier

- layouts Blade existants
- composants Blade de `resources/views/components`
- patterns premium de `templates-neuf` pour :
  - hero
  - cartes statistiques
  - surfaces de formulaires
  - grilles editoriales

## 5) Responsive

- conserver le responsive deja bon
- corriger prioritairement :
  - cartes trop serrees
  - tableaux illisibles
  - CTA mal empiles
  - champs qui debordent

## 6) Accessibilite et micro-interactions

- focus visibles
- contrastes forts
- transitions courtes et utiles
- `prefers-reduced-motion` respecte si une animation est ajoutee

## 7) Navigation produit

- entree publique pour decouverte
- parcours `/app/*` pour lecture publique et exposition partielle
- parcours `/console/*` pour l espace membre complet
- admin separe sous `/console/admin/*`
