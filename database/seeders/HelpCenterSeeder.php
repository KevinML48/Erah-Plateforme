<?php

namespace Database\Seeders;

use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\HelpGlossaryTerm;
use App\Models\HelpTourStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class HelpCenterSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $this->seedArticles($categories);
        $this->seedGlossary();
        $this->seedTourSteps();
    }

    /**
     * @return Collection<string, HelpCategory>
     */
    private function seedCategories(): Collection
    {
        return collect([
            [
                'title' => 'Commencer sur ERAH',
                'slug' => 'commencer-sur-erah',
                'description' => 'Les bases pour comprendre le r\u00f4le de la plateforme, cr\u00e9er son compte et trouver les premiers modules utiles.',
                'intro' => 'Cette cat\u00e9gorie regroupe les rep\u00e8res essentiels pour un nouveau visiteur ou un nouveau membre.',
                'icon' => 'rocket',
                'landing_bucket' => 'getting_started',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 10,
            ],
            [
                'title' => 'Visite guidee de la plateforme',
                'slug' => 'visite-guidee-de-la-plateforme',
                'description' => '\u00c9tape 6 pour comprendre la logique globale d\'ERAH.',
                'intro' => 'La visite guid\u00e9e relie les grands modules et aide \u00e0 prendre ses marques rapidement.',
                'icon' => 'map',
                'landing_bucket' => 'getting_started',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 20,
            ],
            [
                'title' => 'Matchs et paris',
                'slug' => 'matchs-et-paris',
                'description' => 'Comment lire un match, pr\u00e9dire avant le verrouillage et comprendre le r\u00e8glement.',
                'intro' => 'Le module matchs rassemble les rencontres, les statuts et les paris disponibles.',
                'icon' => 'target',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 30,
            ],
            [
                'title' => 'Missions, points et progression',
                'slug' => 'missions-points-et-progression',
                'description' => 'Tout ce qui fait monter votre progression, vos ligues et vos ressources.',
                'intro' => 'Missions, points, XP, streak et progression communautaire sont lies ici.',
                'icon' => 'spark',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 40,
            ],
            [
                'title' => 'Clips, interactions et communaute',
                'slug' => 'clips-interactions-et-communaute',
                'description' => 'Voir, liker, commenter, partager et comprendre ce qui rapporte sur le module clips.',
                'intro' => 'Le c\u0153ur communautaire d\'ERAH vit beaucoup autour des clips et des interactions.',
                'icon' => 'play',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 50,
            ],
            [
                'title' => 'Classements, duels et profil',
                'slug' => 'classements-duels-et-profil',
                'description' => 'Lire votre progression, g\u00e9rer vos duels et mettre en avant votre profil.',
                'intro' => 'Cette zone aide \u00e0 comprendre la comp\u00e9tition communautaire et vos statistiques personnelles.',
                'icon' => 'trophy',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 60,
            ],
            [
                'title' => 'Cadeaux et reward wallet',
                'slug' => 'cadeaux-et-reward-wallet',
                'description' => 'Utiliser vos points, verifier votre reward wallet et suivre vos redemptions.',
                'intro' => 'Le module cadeaux transforme l\'activit\u00e9 de la plateforme en r\u00e9compenses concr\u00e8tes.',
                'icon' => 'gift',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 70,
            ],
            [
                'title' => 'Notifications et parametres',
                'slug' => 'notifications-et-parametres',
                'description' => 'Alertes, pr\u00e9f\u00e9rences et r\u00e9glages utiles pour rester inform\u00e9 sans saturation.',
                'intro' => 'Param\u00e9trez les notifications et gardez le contr\u00f4le sur votre compte.',
                'icon' => 'bell',
                'landing_bucket' => 'technical',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 80,
            ],
            [
                'title' => 'Questions techniques',
                'slug' => 'questions-techniques',
                'description' => 'Connexion, compatibilite navigateur, PWA, video, problemes courants.',
                'intro' => 'Les r\u00e9ponses techniques prioritaires pour les visiteurs et les membres.'
                'icon' => 'shield',
                'landing_bucket' => 'technical',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 90,
            ],
        ])->mapWithKeys(function (array $payload): array {
            $category = HelpCategory::query()->updateOrCreate(
                ['slug' => $payload['slug']],
                $payload,
            );

            return [$payload['slug'] => $category];
        });
    }

    /**
     * @param Collection<string, HelpCategory> $categories
     */
    private function seedArticles(Collection $categories): void
    {
        $publishedAt = now()->subDays(2);
        $index = 0;

        foreach ($this->articles() as $article) {
            HelpArticle::query()->updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'help_category_id' => $categories[$article['category']]->id,
                    'title' => $article['title'],
                    'summary' => $article['summary'],
                    'body' => $article['body'],
                    'short_answer' => $article['short_answer'],
                    'keywords' => $article['keywords'],
                    'tutorial_video_url' => $article['tutorial_video_url'] ?? null,
                    'cta_label' => $article['cta_label'] ?? null,
                    'cta_url' => $article['cta_url'] ?? null,
                    'status' => HelpArticle::STATUS_PUBLISHED,
                    'is_featured' => $article['is_featured'],
                    'is_faq' => $article['is_faq'],
                    'sort_order' => $article['sort_order'],
                    'published_at' => $publishedAt->copy()->addHours($index++),
                ],
            );
        }
    }

    private function seedGlossary(): void
    {
        foreach ($this->glossaryTerms() as $term) {
            HelpGlossaryTerm::query()->updateOrCreate(
                ['slug' => $term['slug']],
                $term,
            );
        }
    }

    private function seedTourSteps(): void
    {
        foreach ($this->tourSteps() as $step) {
            HelpTourStep::query()->updateOrCreate(
                ['step_number' => $step['step_number']],
                $step,
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function articles(): array
    {
        return [
            [
                'category' => 'commencer-sur-erah',
                'title' => 'Comprendre le role de la plateforme',
                'slug' => 'comprendre-le-role-de-la-plateforme',
                'summary' => 'ERAH m\u00e9lange lecture publique, participation communautaire, progression et r\u00e9compenses.',
                'body' => "ERAH est une plateforme communautaire esport. Vous pouvez commencer en simple visiteur pour regarder les contenus, comprendre les modules et consulter les profils publics.\n\nD\u00e8s que vous voulez participer, un compte devient utile. Il permet de commenter, aimer les clips, suivre les missions, entrer dans les classements, utiliser vos points et demander des r\u00e9compenses.\n\nL'objectif n'est pas seulement de consommer du contenu. La plateforme relie activit\u00e9, progression, avantages et mise en avant communautaire.",
                'short_answer' => 'ERAH permet de consulter du contenu librement puis de participer avec un compte pour gagner des points, monter en XP et activer tous les modules.',
                'keywords' => ['plateforme', 'visiteur', 'compte', 'progression'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Cr\u00e9er un compte'
                'cta_url' => route('register'),
            ],
            [
                'category' => 'commencer-sur-erah',
                'title' => 'Cr\u00e9er son compte et acc\u00e9der \u00e0 son espace',
                'slug' => 'créer-son-compte-et-acceder-a-son-espace',
                'summary' => 'Le compte débloque les interactions, les gains et votre progression complète.',
                'body' => "L'inscription peut se faire avec un formulaire classique ou via les connexions sociales d\u00e9j\u00e0 branch\u00e9es sur la plateforme.\n\nUne fois connect\u00e9, vous acc\u00e9dez \u00e0 votre dashboard, \u00e0 vos missions, \u00e0 vos notifications, \u00e0 vos paris, \u00e0 vos duels et \u00e0 votre profil public. C'est aussi la condition pour \u00eatre r\u00e9compens\u00e9 quand vous interagissez avec les clips ou les missions.\n\nSi vous \u00eates seulement curieux, vous pouvez d'abord explorer les pages publiques avant de cr\u00e9er votre compte.",
                'short_answer' => 'Le compte donne accès au dashboard, aux interactions, aux gains de points et à votre profil complet.',
                'keywords' => ['compte', 'dashboard', 'connexion', 'profil'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 20,
                'cta_label' => 'Ouvrir la connexion',
                'cta_url' => route('login'),
            ],
            [
                'category' => 'commencer-sur-erah',
                'title' => 'Devenir supporter ERAH et choisir sa formule',
                'slug' => 'devenir-supporter-erah-et-choisir-sa-formule',
                'summary' => 'La page Supporter sert à comparer les formules et activer le programme.',
                'body' => "Le programme supporter permet de débloquant un badge visible, des missions réservées, des réactions premium, des votes clips et d'autres avantages communautaires.\n\nPour l'activer, il faut ouvrir la page Supporter, comparer les formules disponibles puis lancer le checkout. La facturation et l'activation sont ensuite gérées via le parcours prévu sur la plateforme.\n\nSi vous êtes déjà membre, la console supporter vous aide ensuite à suivre votre statut, vos avantages et vos missions exclusives.",
                'short_answer' => 'Pour devenir supporter, il faut passer par la page Supporter, choisir une formule puis lancer le checkout.',
                'keywords' => ['supporter', 'abonnement', 'badge supporter', 'formule', 'checkout'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 25,
                'cta_label' => 'Voir la page Supporter',
                'cta_url' => route('supporter.show'),
            ],
            [
                'category' => 'visite-guidee-de-la-plateforme',
                'title' => 'Suivre la visite guid\u00e9e (1 sur 6 \u00e0 6 sur 6)',
                'slug' => 'suivre-la-visite-guidee-1-sur-6-a-6-sur-6',
                'summary' => 'Le parcours guid\u00e9 relie les grandes zones ERAH dans le bon ordre.',
                'body' => "La visite guid\u00e9e donne une vue claire des priorit\u00e9s : comprendre le r\u00f4le de la plateforme, ouvrir son espace, suivre les matchs, gagner des points, participer \u00e0 la communaut\u00e9 puis r\u00e9cup\u00e9rer ses avantages.\n\nChaque \u00e9tape contient un r\u00e9sum\u00e9 court, un bloc illustratif et un lien vers la vraie page concern\u00e9e. C'est le meilleur point d'entr\u00e9e pour un membre qui veut tout comprendre sans chercher module par module.",
                'short_answer' => 'La visite guid\u00e9e est le fil rouge du centre d\'aide pour passer rapidement de la d\u00e9couverte \u00e0 l\'action.',
                'keywords' => ['visite', 'guid\u00e9e', '\u00e9tapes', 'parcours'],
                'is_featured' => true,
                'is_faq' => false,
                'sort_order' => 10,
                'cta_label' => 'Commencer la visite',
                'cta_url' => '/aide#tour-guide',
            ],
            [
                'category' => 'matchs-et-paris',
                'title' => 'Placer un pari avant le verrouillage',
                'slug' => 'placer-un-pari-avant-le-verrouillage',
                'summary' => 'Les paris ne restent ouverts que jusqu\'au lock du match ou de l\'\u00e9v\u00e9nement.',
                'body' => "Sur un match, vous verrez le format, les options disponibles et l'heure utile. Tant que le match n'est pas verrouill\u00e9, vous pouvez choisir une s\u00e9lection et engager vos points.\n\nUne fois le lock pass\u00e9, le pari n'est plus modifiable. Il faut donc v\u00e9rifier l'heure, le statut et le type de march\u00e9 avant validation.\n\nSi vous \u00eates visiteur, la lecture reste ouverte mais le pari vous demandera de cr\u00e9er un compte.",
                'short_answer' => 'Un pari doit \u00eatre valid\u00e9 avant le lock du match. Pass\u00e9 ce moment, l\'action est ferm\u00e9e.',
                'keywords' => ['match', 'pari', 'lock', 'mise'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir les matchs',
                'cta_url' => route('matches.index'),
            ],
            [
                'category' => 'matchs-et-paris',
                'title' => 'Comprendre le règlement des paris',
                'slug' => 'comprendre-le-règlement-des-paris',
                'summary' => 'Les gains et pertes suivent des règles simples reliées au résultat final.',
                'body' => "Si votre pari est gagnant, vous récupérerez votre mise et le gain prévu par le marché. La progression peut aussi vous attribuer de l'XP selon les règles communautaires actives.\n\nSi le pari est perdant, la mise est perdue. Le moteur de règlement ne se déclenche qu'une fois le résultat du match saisi et validé.\n\nLe suivi de vos paris se fait ensuite depuis les pages dédiées, avec historique et statut.",
                'short_answer' => 'Les paris sont réglés après validation du résultat : gain si la sélection est bonne, perte de la mise sinon.',
                'keywords' => ['règlement', 'gain', 'perte', 'historique'],
                'is_featured' => false,
                'is_faq' => true,
                'sort_order' => 20,
                'cta_label' => 'Voir mes paris',
                'cta_url' => route('bets.index'),
            ],
            [
                'category' => 'missions-points-et-progression',
                'title' => 'Gagner des points avec les missions quotidiennes',
                'slug' => 'gagner-des-points-avec-les-missions-quotidiennes',
                'summary' => 'Les missions donnent un cadre clair pour progresser chaque jour.',
                'body' => "Chaque jour, la plateforme peut vous proposer un lot de missions simples, moyennes et spéciales. Elles servent à orienter votre activité vers les modules importants : clips, paris, quiz, duels ou codes live.\n\nQuand une mission est complétée, ses récompenses s'appliquent selon le template prévu. Un bonus supplémentaire peut s'ajouter si la série quotidienne complète est terminée.\n\nLes missions sont donc l'un des meilleurs moyens de transformer votre activité en points et en XP.",
                'short_answer' => 'Les missions quotidiennes structurent l\'activité et donnent des récompenses directes en points et progression.',
                'keywords' => ['missions', 'quotidiennes', 'points', 'bonus'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir les missions',
                'cta_url' => route('missions.index'),
            ],
            [
                'category' => 'missions-points-et-progression',
                'title' => 'XP, ligues et classement principal',
                'slug' => 'xp-ligues-et-classement-principal',
                'summary' => 'L\'XP sert à progresser et à monter de ligue, pas à dépenser.',
                'body' => "Les points servent de monnaie interne. L'XP est réservée à la progression et au classement principal. Les ligues se basent sur cette progression globale.\n\nCela signifie qu'un membre peut dépenser des points dans certains modules tout en gardant une progression XP intacte. L'XP fait donc office de trace durable de votre activité utile sur la plateforme.\n\nLe classement principal et la ligue affichent ensuite où vous vous situez par rapport aux autres membres.",
                'short_answer' => 'Les points se dépensent, l\'XP sert à progresser et à déterminer votre ligue.',
                'keywords' => ['xp', 'ligue', 'classement', 'points'],
                'is_featured' => true,
                'is_faq' => false,
                'sort_order' => 20,
                'cta_label' => 'Voir le classement',
                'cta_url' => route('leaderboards.index'),
            ],
            [
                'category' => 'clips-interactions-et-communaute',
                'title' => 'Voir un clip, liker et ajouter en favoris',
                'slug' => 'voir-un-clip-liker-et-ajouter-en-favoris',
                'summary' => 'Le module clips melange consultation publique et interactions reservees aux membres.',
                'body' => "Un visiteur peut lire le feed clips et regarder les contenus publiés. En revanche, le like, le favori et les autres interactions nécessitent un compte connecté.\n\nUne fois membre, vous pouvez liker, commenter, répondre à un commentaire, partager et constituer votre bibliothèque de favoris. Certaines interactions donnent aussi des récompenses, dans les limites prévues par la plateforme.\n\nCela permet de garder une découverte sans friction tout en réservant la participation active aux comptes vérifiables.",
                'short_answer' => 'Les clips sont lisibles sans compte, mais les interactions comme le like ou le favori demandent une connexion.',
                'keywords' => ['clips', 'like', 'favoris', 'visiteur'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir les clips',
                'cta_url' => route('clips.index'),
            ],
            [
                'category' => 'clips-interactions-et-communaute',
                'title' => 'Commentaires, réponses et vie communautaire',
                'slug' => 'commentaires-réponses-et-vie-communautaire',
                'summary' => 'Les commentaires publics aident à faire vivre les clips, avec des réponses limitées à un niveau.',
                'body' => "Sous un clip, les commentaires publiés restent visibles pour tout le monde. Les membres connectés peuvent répondre à un commentaire principal, mais la profondeur reste volontairement limitée pour garder une lecture claire.\n\nCette contrainte évite les fils trop longs et rend la modération plus simple. Les réponses servent donc à prolonger un échange sans transformer la page en forum profond.\n\nLes interactions communautaires peuvent aussi alimenter votre progression, dans les limites et caps journaliers définis par le moteur clips.",
                'short_answer' => 'Les réponses a commentaires sont limitees a un seul niveau pour garder une lecture simple et moderable.',
                'keywords' => ['commentaire', 'réponse', 'communaute', 'moderation'],
                'is_featured' => false,
                'is_faq' => true,
                'sort_order' => 20,
                'cta_label' => 'Ouvrir un clip',
                'cta_url' => route('clips.index'),
            ],
            [
                'category' => 'classements-duels-et-profil',
                'title' => 'Comprendre les duels et leur classement',
                'slug' => 'comprendre-les-duels-et-leur-classement',
                'summary' => 'Les duels ont leur propre logique de compétition et leur propre lecture.',
                'body' => "Le module duels sert à lancer, accepter ou refuser des confrontations entre membres. Il est séparé du classement principal et peut avoir ses propres statistiques.\n\nLa page duels sert d'abord à suivre vos défis en cours et terminés. Le classement duel dispose quant à lui de sa propre page dédiée pour bien distinguer la compétition globale et votre historique personnel.\n\nSelon l'issue d'un duel, la plateforme applique ensuite des récompenses et met à jour les statistiques du module.",
                'short_answer' => 'Les duels ont une page personnelle pour vos défis et un classement dédié pour la compétition globale.',
                'keywords' => ['duel', 'classement duel', 'profil', 'defi'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir mes duels',
                'cta_url' => route('duels.index'),
            ],
            [
                'category' => 'classements-duels-et-profil',
                'title' => 'Mettre en avant son profil public',
                'slug' => 'mettre-en-avant-son-profil-public',
                'summary' => 'Le profil public regroupe vos stats, vos liens et votre image de membre.',
                'body' => "Le profil public est la vitrine d'un membre : pseudo, bio, avatar, progression, statut et liens publics. Il peut aussi faire remonter votre avis sur le club si vous en avez publié un.\n\nCet espace est consultable sans connexion, ce qui permet à un visiteur de découvrir les membres de la communauté et d'avoir une image claire de la plateforme.\n\nIl faut donc privilégier un pseudo propre, une bio lisible et des liens utiles. Les admins conservent des outils de modération si un contenu doit être corrigé.",
                'short_answer' => 'Votre profil public sert de vitrine communautaire et reste visible meme pour les visiteurs non connectes.',
                'keywords' => ['profil public', 'avatar', 'bio', 'liens'],
                'is_featured' => false,
                'is_faq' => false,
                'sort_order' => 20,
                'cta_label' => 'Voir mon profil',
                'cta_url' => route('profile.show'),
            ],
            [
                'category' => 'cadeaux-et-reward-wallet',
                'title' => 'Utiliser le reward wallet et demander un cadeau',
                'slug' => 'utiliser-le-reward-wallet-et-demander-un-cadeau',
                'summary' => 'Les cadeaux s appuient sur votre reserve de points et l historique de redemption.',
                'body' => "Le reward wallet sert à visualiser votre réserve disponible pour les cadeaux. Depuis le catalogue, vous pouvez ouvrir une fiche détail, vérifier le coût, le stock et lancer une demande si votre solde le permet.\n\nUne fois la redemption envoyée, le suivi continue dans l'historique. L'équipe admin peut ensuite approuver, refuser ou faire progresser le statut selon la récompense concernée.\n\nCe module transforme les gains de la plateforme en avantages concrets, il est donc important de vérifier vos points avant chaque demande.",
                'short_answer' => 'Le reward wallet affiche votre réserve et le catalogue cadeaux permet de lancer une redemption si le solde est suffisant.',
                'keywords' => ['cadeaux', 'reward wallet', 'redemption', 'stock'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir les cadeaux',
                'cta_url' => route('gifts.index'),
            ],
            [
                'category' => 'notifications-et-parametres',
                'title' => 'Gérer les notifications et les préférences',
                'slug' => 'gérer-les-notifications-et-les-préférences',
                'summary' => 'Les notifications vous tiennent informé sans vous noyer.',
                'body' => "ERAH peut vous notifier pour les missions, les duels, les paris, les commentaires, les quiz ou les codes live. Les préférences servent à garder les catégories utiles et à réduire les alertes superflues.\n\nLa page de préférences permet donc d'ajuster ce que vous recevez in-app et, à terme, ce que vous souhaitez aussi pousser vers les canaux web push.\n\nUn bon réglage des notifications permet de rester réactif tout en gardant une interface propre.",
                'short_answer' => 'Les préférences de notifications servent a choisir les categories utiles et a eviter la saturation.',
                'keywords' => ['notifications', 'préférences', 'alertes', 'push'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir mes notifications',
                'cta_url' => route('notifications.index'),
            ],
            [
                'category' => 'questions-techniques',
                'title' => 'Résoudre un problème de connexion ou de chargement',
                'slug' => 'resoudre-un-probleme-de-connexion-ou-de-chargement',
                'summary' => 'Quelques vérifications simples règlent la majorité des problèmes.',
                'body' => "Si une page semble vide ou ancienne, commencez par recharger complètement le navigateur. Vérifiez ensuite si vous êtes bien sur la bonne URL de l'environnement local ou de production.\n\nSi le problème concerne la connexion, ouvrez la page login en direct puis retentez l'opération. Pour une page interactive, assurez-vous aussi que votre session n'a pas expiré.\n\nEnfin, si un comportement semble incohérent, le centre d'aide doit rester votre point de référence avant toute remonte plus technique.",
                'short_answer' => 'En cas de page vide ou ancienne, commencez par un hard refresh puis vérifiez l\'URL et votre session.',
                'keywords' => ['connexion', 'chargement', 'cache', 'session'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Ouvrir le login',
                'cta_url' => route('login'),
            ],
            [
                'category' => 'questions-techniques',
                'title' => 'Compatibilité mobile, navigateur et accès visiteur',
                'slug' => 'compatibilite-mobile-navigateur-et-acces-visiteur',
                'summary' => 'La plateforme reste consultable sans compte, mais la participation demande une connexion.',
                'body' => "Le mode visiteur sert à laisser découvrir les contenus publics sans friction. Les clips, les profils publics, les classements et certains flux peuvent donc être consultés avant inscription.\n\nEn revanche, tout ce qui change l'état de la plateforme ou rapporte une progression demande un compte connecté : like, commentaire, favori, pari, duel, mission, achat ou redemption.\n\nSur mobile, il faut aussi vérifier que le navigateur accepte bien les cookies et le stockage local si vous souhaitez conserver une session stable.",
                'short_answer' => 'Le visiteur peut consulter, mais toute action communautaire ou retribuee demande une connexion.',
                'keywords' => ['visiteur', 'mobile', 'navigateur', 'connexion'],
                'is_featured' => false,
                'is_faq' => true,
                'sort_order' => 20,
                'cta_label' => 'Voir le centre d aide',
                'cta_url' => route('help.index'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function glossaryTerms(): array
    {
        return [
            ['term' => 'XP', 'slug' => 'xp', 'definition' => 'L\'XP représente votre progression communautaire globale et sert à déterminer votre ligue.', 'short_answer' => 'L\'XP sert à progresser, pas à dépenser.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 10],
            ['term' => 'Points', 'slug' => 'points', 'definition' => 'Les points sont la monnaie interne qui peut servir aux paris, cadeaux et autres modules selon les règles en place.', 'short_answer' => 'Les points se gagnent et se dépensent selon les modules.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 20],
            ['term' => 'Reward wallet', 'slug' => 'reward-wallet', 'definition' => 'Le reward wallet regroupe la réserve utilisable pour les cadeaux et récompenses dédiées.', 'short_answer' => 'Le reward wallet sert au catalogue cadeaux.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 30],
            ['term' => 'Lock', 'slug' => 'lock', 'definition' => 'Le lock correspond au moment où une action comme un pari n\'est plus modifiable.', 'short_answer' => 'Après le lock, le pari ne peut plus être changé.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 40],
            ['term' => 'Mission daily', 'slug' => 'mission-daily', 'definition' => 'Une mission daily est une mission quotidienne générée pour entretenir l\'activité de la plateforme.', 'short_answer' => 'Les daily missions reviennent chaque jour.', 'is_featured' => false, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 50],
            ['term' => 'Duel', 'slug' => 'duel', 'definition' => 'Un duel est un défi direct entre deux membres avec ses propres résultats et sa propre lecture compétitive.', 'short_answer' => 'Le duel est un module de confrontation entre membres.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 60],
            ['term' => 'Favori', 'slug' => 'favori', 'definition' => 'Un favori permet de conserver un clip dans votre bibliothèque personnelle pour y revenir plus tard.', 'short_answer' => 'Le favori sauvegarde un clip dans votre liste.', 'is_featured' => false, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 70],
            ['term' => 'Streak', 'slug' => 'streak', 'definition' => 'Le streak mesure une régularité, par exemple une suite de connexions ou de victoires selon le module.', 'short_answer' => 'Le streak récompense la régularité.', 'is_featured' => false, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 80],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tourSteps(): array
    {
        return [
            ['step_number' => 1, 'title' => 'Comprendre le r\u00f4le de la plateforme', 'summary' => 'Voir \u00e0 quoi sert ERAH avant m\u00eame de participer.', 'body' => 'ERAH combine lecture publique, interactions communautaires, progression et r\u00e9compenses. Commencez par identifier les modules utiles pour vous.', 'visual_title' => 'Vue d\'ensemble', 'visual_body' => 'Visiteur libre, membre actif, progression par XP, points et modules communautaires.', 'cta_label' => 'Lire le r\u00f4le de la plateforme', 'cta_url' => route('help.articles.show', 'comprendre-le-role-de-la-plateforme'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 10],
            ['step_number' => 2, 'title' => 'Cr\u00e9er son compte et acc\u00e9der \u00e0 son espace', 'summary' => 'Le compte ouvre les interactions et la progression compl\u00e8te.', 'body' => 'D\u00e8s que vous voulez commenter, miser, lancer un duel ou gagner des points, cr\u00e9ez votre compte puis ouvrez votre espace personnel.', 'visual_title' => 'Compte et espace perso', 'visual_body' => 'Connexion, dashboard, profil, notifications, parcours personnel.', 'cta_label' => 'Aller \u00e0 l\'inscription', 'cta_url' => route('register'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 20],
            ['step_number' => 3, 'title' => 'D\u00e9couvrir les matchs et les paris', 'summary' => 'Lire un match, comprendre le lock et agir avant la fermeture.', 'body' => 'Le module matchs centralise les rencontres, les formats et les paris disponibles. C\'est la porte d\'entr\u00e9e pour les membres qui veulent se positionner sur un r\u00e9sultat.', 'visual_title' => 'Match center', 'visual_body' => 'Calendrier, statuts, march\u00e9s ouverts, actions r\u00e9serv\u00e9es aux comptes connect\u00e9s.', 'cta_label' => 'Ouvrir les matchs', 'cta_url' => route('matches.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 30],
            ['step_number' => 4, 'title' => 'Gagner des points via les missions et l\'activit\u00e9', 'summary' => 'Missions, interactions et progression structurent les gains.', 'body' => 'Les missions quotidiennes, les quiz, les clips et certaines actions communautaires servent \u00e0 faire monter votre activit\u00e9 et vos ressources.', 'visual_title' => 'Boucle de progression', 'visual_body' => 'Points, XP, ligues et r\u00e9compenses sont li\u00e9s pour donner un cap quotidien.', 'cta_label' => 'Voir les missions', 'cta_url' => route('missions.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 40],
            ['step_number' => 5, 'title' => 'Participer \u00e0 la vie de la communaut\u00e9', 'summary' => 'Clips, commentaires, favoris, partages et profil public donnent de la visibilit\u00e9.', 'body' => 'Le c\u0153ur communautaire passe par les clips, les interactions et le profil public. C\'est ici que les membres deviennent vraiment visibles.', 'visual_title' => 'Commu active', 'visual_body' => 'Clips, r\u00e9ponses, favoris, profils publics et duels structurent l\'engagement.', 'cta_label' => 'Ouvrir les clips', 'cta_url' => route('clips.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 50],
            ['step_number' => 6, 'title' => 'R\u00e9cup\u00e9rer ses avantages et rester inform\u00e9', 'summary' => 'Le reward wallet, les cadeaux et les notifications ferment la boucle.', 'body' => 'Quand votre activit\u00e9 porte ses fruits, vous pouvez consulter vos ressources, suivre vos cadeaux et affiner vos pr\u00e9f\u00e9rences de notification.', 'visual_title' => 'Avantages et suivi', 'visual_body' => 'Reward wallet, cadeaux, notifications et r\u00e9glages personnels.', 'cta_label' => 'Voir les cadeaux', 'cta_url' => route('gifts.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 60],
        ];
    }
}
