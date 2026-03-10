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
                'description' => 'Les bases pour comprendre le role de la plateforme, creer son compte et trouver les premiers modules utiles.',
                'intro' => 'Cette categorie regroupe les reperes essentiels pour un nouveau visiteur ou un nouveau membre.',
                'icon' => 'rocket',
                'landing_bucket' => 'getting_started',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 10,
            ],
            [
                'title' => 'Visite guidee de la plateforme',
                'slug' => 'visite-guidee-de-la-plateforme',
                'description' => 'Le parcours en 6 etapes pour comprendre la logique globale d ERAH.',
                'intro' => 'La visite guidee relie les grands modules et aide a prendre ses marques rapidement.',
                'icon' => 'map',
                'landing_bucket' => 'getting_started',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 20,
            ],
            [
                'title' => 'Matchs et paris',
                'slug' => 'matchs-et-paris',
                'description' => 'Comment lire un match, predire avant le verrouillage et comprendre le reglement.',
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
                'intro' => 'Le coeur communautaire d ERAH vit beaucoup autour des clips et des interactions.',
                'icon' => 'play',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 50,
            ],
            [
                'title' => 'Classements, duels et profil',
                'slug' => 'classements-duels-et-profil',
                'description' => 'Lire votre progression, gerer vos duels et mettre en avant votre profil.',
                'intro' => 'Cette zone aide a comprendre la competition communautaire et vos statistiques personnelles.',
                'icon' => 'trophy',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 60,
            ],
            [
                'title' => 'Cadeaux et reward wallet',
                'slug' => 'cadeaux-et-reward-wallet',
                'description' => 'Utiliser vos points, verifier votre reward wallet et suivre vos redemptions.',
                'intro' => 'Le module cadeaux transforme l activite de la plateforme en recompenses concretes.',
                'icon' => 'gift',
                'landing_bucket' => 'understanding_platform',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 70,
            ],
            [
                'title' => 'Notifications et parametres',
                'slug' => 'notifications-et-parametres',
                'description' => 'Alertes, preferences et reglages utiles pour rester informe sans saturation.',
                'intro' => 'Parametrez les notifications et gardez le controle sur votre compte.',
                'icon' => 'bell',
                'landing_bucket' => 'technical',
                'status' => HelpCategory::STATUS_PUBLISHED,
                'sort_order' => 80,
            ],
            [
                'title' => 'Questions techniques',
                'slug' => 'questions-techniques',
                'description' => 'Connexion, compatibilite navigateur, PWA, video, problemes courants.',
                'intro' => 'Les reponses techniques prioritaires pour les visiteurs et les membres.',
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
                'summary' => 'ERAH melange lecture publique, participation communautaire, progression et recompenses.',
                'body' => "ERAH est une plateforme communautaire esport. Vous pouvez commencer en simple visiteur pour regarder les contenus, comprendre les modules et consulter les profils publics.\n\nDes que vous voulez participer, un compte devient utile. Il permet de commenter, aimer les clips, suivre les missions, entrer dans les classements, utiliser vos points et demander des recompenses.\n\nL objectif n est pas seulement de consommer du contenu. La plateforme relie activite, progression, avantages et mise en avant communautaire.",
                'short_answer' => 'ERAH permet de consulter du contenu librement puis de participer avec un compte pour gagner des points, monter en XP et activer tous les modules.',
                'keywords' => ['plateforme', 'visiteur', 'compte', 'progression'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Creer un compte',
                'cta_url' => route('register'),
            ],
            [
                'category' => 'commencer-sur-erah',
                'title' => 'Creer son compte et acceder a son espace',
                'slug' => 'creer-son-compte-et-acceder-a-son-espace',
                'summary' => 'Le compte debloque les interactions, les gains et votre progression complete.',
                'body' => "L inscription peut se faire avec un formulaire classique ou via les connexions sociales deja branchees sur la plateforme.\n\nUne fois connecte, vous accedez a votre dashboard, a vos missions, a vos notifications, a vos paris, a vos duels et a votre profil public. C est aussi la condition pour etre recompense quand vous interagissez avec les clips ou les missions.\n\nSi vous etes seulement curieux, vous pouvez d abord explorer les pages publiques avant de creer votre compte.",
                'short_answer' => 'Le compte donne acces au dashboard, aux interactions, aux gains de points et a votre profil complet.',
                'keywords' => ['compte', 'dashboard', 'connexion', 'profil'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 20,
                'cta_label' => 'Ouvrir la connexion',
                'cta_url' => route('login'),
            ],
            [
                'category' => 'visite-guidee-de-la-plateforme',
                'title' => 'Suivre la visite guidee 1 sur 6 a 6 sur 6',
                'slug' => 'suivre-la-visite-guidee-1-sur-6-a-6-sur-6',
                'summary' => 'Le parcours guide relie les grandes zones ERAH dans le bon ordre.',
                'body' => "La visite guidee donne une vue claire des priorites: comprendre le role de la plateforme, ouvrir son espace, suivre les matchs, gagner des points, participer a la communaute puis recuperer ses avantages.\n\nChaque etape contient un resume court, un bloc illustratif et un lien vers la vraie page concernee. C est le meilleur point d entree pour un membre qui veut tout comprendre sans chercher module par module.",
                'short_answer' => 'La visite guidee est le fil rouge du centre d aide pour passer rapidement de la decouverte a l action.',
                'keywords' => ['visite', 'guidee', 'etapes', 'parcours'],
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
                'summary' => 'Les paris ne restent ouverts que jusqu au lock du match ou de l evenement.',
                'body' => "Sur un match, vous verrez le format, les options disponibles et l heure utile. Tant que le match n est pas verrouille, vous pouvez choisir une selection et engager vos points.\n\nUne fois le lock passe, le pari n est plus modifiable. Il faut donc verifier l heure, le statut et le type de marche avant validation.\n\nSi vous etes visiteur, la lecture reste ouverte mais le pari vous demandera de creer un compte.",
                'short_answer' => 'Un pari doit etre valide avant le lock du match. Passe ce moment, l action est fermee.',
                'keywords' => ['match', 'pari', 'lock', 'mise'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir les matchs',
                'cta_url' => route('matches.index'),
            ],
            [
                'category' => 'matchs-et-paris',
                'title' => 'Comprendre le reglement des paris',
                'slug' => 'comprendre-le-reglement-des-paris',
                'summary' => 'Les gains et pertes suivent des regles simples reliees au resultat final.',
                'body' => "Si votre pari est gagnant, vous recupererez votre mise et le gain prevu par le marche. La progression peut aussi vous attribuer de l XP selon les regles communautaires actives.\n\nSi le pari est perdant, la mise est perdue. Le moteur de reglement ne se declenche qu une fois le resultat du match saisi et valide.\n\nLe suivi de vos paris se fait ensuite depuis les pages dediees, avec historique et statut.",
                'short_answer' => 'Les paris sont regles apres validation du resultat: gain si la selection est bonne, perte de la mise sinon.',
                'keywords' => ['reglement', 'gain', 'perte', 'historique'],
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
                'body' => "Chaque jour, la plateforme peut vous proposer un lot de missions simples, moyennes et speciales. Elles servent a orienter votre activite vers les modules importants: clips, paris, quiz, duels ou codes live.\n\nQuand une mission est completee, ses recompenses s appliquent selon le template prevu. Un bonus supplementaire peut s ajouter si la serie quotidienne complete est terminee.\n\nLes missions sont donc l un des meilleurs moyens de transformer votre activite en points et en XP.",
                'short_answer' => 'Les missions quotidiennes structurent l activite et donnent des recompenses directes en points et progression.',
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
                'summary' => 'L XP sert a progresser et a monter de ligue, pas a depenser.',
                'body' => "Les points servent de monnaie interne. L XP est reservee a la progression et au classement principal. Les ligues se basent sur cette progression globale.\n\nCela signifie qu un membre peut depenser des points dans certains modules tout en gardant une progression XP intacte. L XP fait donc office de trace durable de votre activite utile sur la plateforme.\n\nLe classement principal et la ligue affichent ensuite ou vous vous situez par rapport aux autres membres.",
                'short_answer' => 'Les points se depensent, l XP sert a progresser et a determiner votre ligue.',
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
                'body' => "Un visiteur peut lire le feed clips et regarder les contenus publies. En revanche, le like, le favori et les autres interactions necessitent un compte connecte.\n\nUne fois membre, vous pouvez liker, commenter, repondre a un commentaire, partager et constituer votre bibliotheque de favoris. Certaines interactions donnent aussi des recompenses, dans les limites prevues par la plateforme.\n\nCela permet de garder une decouverte sans friction tout en reservant la participation active aux comptes verifiables.",
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
                'title' => 'Commentaires, reponses et vie communautaire',
                'slug' => 'commentaires-reponses-et-vie-communautaire',
                'summary' => 'Les commentaires publics aident a faire vivre les clips, avec des reponses limitees a un niveau.',
                'body' => "Sous un clip, les commentaires publies restent visibles pour tout le monde. Les membres connectes peuvent repondre a un commentaire principal, mais la profondeur reste volontairement limitee pour garder une lecture claire.\n\nCette contrainte evite les fils trop longs et rend la moderation plus simple. Les reponses servent donc a prolonger un echange sans transformer la page en forum profond.\n\nLes interactions communautaires peuvent aussi alimenter votre progression, dans les limites et caps journaliers definis par le moteur clips.",
                'short_answer' => 'Les reponses a commentaires sont limitees a un seul niveau pour garder une lecture simple et moderable.',
                'keywords' => ['commentaire', 'reponse', 'communaute', 'moderation'],
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
                'summary' => 'Les duels ont leur propre logique de competition et leur propre lecture.',
                'body' => "Le module duels sert a lancer, accepter ou refuser des confrontations entre membres. Il est separe du classement principal et peut avoir ses propres statistiques.\n\nLa page duels sert d abord a suivre vos defis en cours et termines. Le classement duel dispose quant a lui de sa propre page dediee pour bien distinguer la competition globale et votre historique personnel.\n\nSelon l issue d un duel, la plateforme applique ensuite des recompenses et met a jour les statistiques du module.",
                'short_answer' => 'Les duels ont une page personnelle pour vos defis et un classement dedie pour la competition globale.',
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
                'body' => "Le profil public est la vitrine d un membre: pseudo, bio, avatar, progression, statut et liens publics. Il peut aussi faire remonter votre avis sur le club si vous en avez publie un.\n\nCet espace est consultable sans connexion, ce qui permet a un visiteur de decouvrir les membres de la communaute et d avoir une image claire de la plateforme.\n\nIl faut donc privilegier un pseudo propre, une bio lisible et des liens utiles. Les admins conservent des outils de moderation si un contenu doit etre corrige.",
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
                'body' => "Le reward wallet sert a visualiser votre reserve disponible pour les cadeaux. Depuis le catalogue, vous pouvez ouvrir une fiche detail, verifier le cout, le stock et lancer une demande si votre solde le permet.\n\nUne fois la redemption envoyee, le suivi continue dans l historique. L equipe admin peut ensuite approuver, refuser ou faire progresser le statut selon la recompense concernee.\n\nCe module transforme les gains de la plateforme en avantages concrets, il est donc important de verifier vos points avant chaque demande.",
                'short_answer' => 'Le reward wallet affiche votre reserve et le catalogue cadeaux permet de lancer une redemption si le solde est suffisant.',
                'keywords' => ['cadeaux', 'reward wallet', 'redemption', 'stock'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir les cadeaux',
                'cta_url' => route('gifts.index'),
            ],
            [
                'category' => 'notifications-et-parametres',
                'title' => 'Gerer les notifications et les preferences',
                'slug' => 'gerer-les-notifications-et-les-preferences',
                'summary' => 'Les notifications vous tiennent informe sans vous noyer.',
                'body' => "ERAH peut vous notifier pour les missions, les duels, les paris, les commentaires, les quiz ou les codes live. Les preferences servent a garder les categories utiles et a reduire les alertes superflues.\n\nLa page de preferences permet donc d ajuster ce que vous recevez in-app et, a terme, ce que vous souhaitez aussi pousser vers les canaux web push.\n\nUn bon reglage des notifications permet de rester reactif tout en gardant une interface propre.",
                'short_answer' => 'Les preferences de notifications servent a choisir les categories utiles et a eviter la saturation.',
                'keywords' => ['notifications', 'preferences', 'alertes', 'push'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Voir mes notifications',
                'cta_url' => route('notifications.index'),
            ],
            [
                'category' => 'questions-techniques',
                'title' => 'Resoudre un probleme de connexion ou de chargement',
                'slug' => 'resoudre-un-probleme-de-connexion-ou-de-chargement',
                'summary' => 'Quelques verifications simples reglent la majorite des problemes.',
                'body' => "Si une page semble vide ou ancienne, commencez par recharger completement le navigateur. Verifiez ensuite si vous etes bien sur la bonne URL de l environnement local ou de production.\n\nSi le probleme concerne la connexion, ouvrez la page login en direct puis retentez l operation. Pour une page interactive, assurez-vous aussi que votre session n a pas expire.\n\nEnfin, si un comportement semble incoherent, le centre d aide doit rester votre point de reference avant toute remontee plus technique.",
                'short_answer' => 'En cas de page vide ou ancienne, commencez par un hard refresh puis verifiez l URL et votre session.',
                'keywords' => ['connexion', 'chargement', 'cache', 'session'],
                'is_featured' => true,
                'is_faq' => true,
                'sort_order' => 10,
                'cta_label' => 'Ouvrir le login',
                'cta_url' => route('login'),
            ],
            [
                'category' => 'questions-techniques',
                'title' => 'Compatibilite mobile, navigateur et acces visiteur',
                'slug' => 'compatibilite-mobile-navigateur-et-acces-visiteur',
                'summary' => 'La plateforme reste consultable sans compte, mais la participation demande une connexion.',
                'body' => "Le mode visiteur sert a laisser decouvrir les contenus publics sans friction. Les clips, les profils publics, les classements et certains flux peuvent donc etre consultes avant inscription.\n\nEn revanche, tout ce qui change l etat de la plateforme ou rapporte une progression demande un compte connecte: like, commentaire, favori, pari, duel, mission, achat ou redemption.\n\nSur mobile, il faut aussi verifier que le navigateur accepte bien les cookies et le stockage local si vous souhaitez conserver une session stable.",
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
            ['term' => 'XP', 'slug' => 'xp', 'definition' => 'L XP represente votre progression communautaire globale et sert a determiner votre ligue.', 'short_answer' => 'L XP sert a progresser, pas a depenser.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 10],
            ['term' => 'Points', 'slug' => 'points', 'definition' => 'Les points sont la monnaie interne qui peut servir aux paris, cadeaux et autres modules selon les regles en place.', 'short_answer' => 'Les points se gagnent et se depensent selon les modules.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 20],
            ['term' => 'Reward wallet', 'slug' => 'reward-wallet', 'definition' => 'Le reward wallet regroupe la reserve utilisable pour les cadeaux et recompenses dediees.', 'short_answer' => 'Le reward wallet sert au catalogue cadeaux.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 30],
            ['term' => 'Lock', 'slug' => 'lock', 'definition' => 'Le lock correspond au moment ou une action comme un pari n est plus modifiable.', 'short_answer' => 'Apres le lock, le pari ne peut plus etre change.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 40],
            ['term' => 'Mission daily', 'slug' => 'mission-daily', 'definition' => 'Une mission daily est une mission quotidienne generee pour entretenir l activite de la plateforme.', 'short_answer' => 'Les daily missions reviennent chaque jour.', 'is_featured' => false, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 50],
            ['term' => 'Duel', 'slug' => 'duel', 'definition' => 'Un duel est un defi direct entre deux membres avec ses propres resultats et sa propre lecture competitive.', 'short_answer' => 'Le duel est un module de confrontation entre membres.', 'is_featured' => true, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 60],
            ['term' => 'Favori', 'slug' => 'favori', 'definition' => 'Un favori permet de conserver un clip dans votre bibliotheque personnelle pour y revenir plus tard.', 'short_answer' => 'Le favori sauvegarde un clip dans votre liste.', 'is_featured' => false, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 70],
            ['term' => 'Streak', 'slug' => 'streak', 'definition' => 'Le streak mesure une regularite, par exemple une suite de connexions ou de victoires selon le module.', 'short_answer' => 'Le streak recompense la regularite.', 'is_featured' => false, 'status' => HelpGlossaryTerm::STATUS_PUBLISHED, 'sort_order' => 80],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function tourSteps(): array
    {
        return [
            ['step_number' => 1, 'title' => 'Comprendre le role de la plateforme', 'summary' => 'Voir a quoi sert ERAH avant meme de participer.', 'body' => 'ERAH combine lecture publique, interactions communautaires, progression et recompenses. Commencez par identifier les modules utiles pour vous.', 'visual_title' => 'Vue d ensemble', 'visual_body' => 'Visiteur libre, membre actif, progression par XP, points et modules communautaires.', 'cta_label' => 'Lire le role de la plateforme', 'cta_url' => route('help.articles.show', 'comprendre-le-role-de-la-plateforme'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 10],
            ['step_number' => 2, 'title' => 'Creer son compte et acceder a son espace', 'summary' => 'Le compte ouvre les interactions et la progression complete.', 'body' => 'Des que vous voulez commenter, miser, lancer un duel ou gagner des points, creez votre compte puis ouvrez votre espace personnel.', 'visual_title' => 'Compte et espace perso', 'visual_body' => 'Connexion, dashboard, profil, notifications, parcours personnel.', 'cta_label' => 'Aller a l inscription', 'cta_url' => route('register'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 20],
            ['step_number' => 3, 'title' => 'Decouvrir les matchs et les paris', 'summary' => 'Lire un match, comprendre le lock et agir avant la fermeture.', 'body' => 'Le module matchs centralise les rencontres, les formats et les paris disponibles. C est la porte d entree pour les membres qui veulent se positionner sur un resultat.', 'visual_title' => 'Match center', 'visual_body' => 'Calendrier, statuts, marches ouverts, actions reservees aux comptes connectes.', 'cta_label' => 'Ouvrir les matchs', 'cta_url' => route('matches.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 30],
            ['step_number' => 4, 'title' => 'Gagner des points via les missions et l activite', 'summary' => 'Missions, interactions et progression structurent les gains.', 'body' => 'Les missions quotidiennes, les quiz, les clips et certaines actions communautaires servent a faire monter votre activite et vos ressources.', 'visual_title' => 'Boucle de progression', 'visual_body' => 'Points, XP, ligues et recompenses sont relies pour donner un cap quotidien.', 'cta_label' => 'Voir les missions', 'cta_url' => route('missions.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 40],
            ['step_number' => 5, 'title' => 'Participer a la vie de la communaute', 'summary' => 'Clips, commentaires, favoris, partages et profil public donnent de la visibilite.', 'body' => 'Le coeur communautaire passe par les clips, les interactions et le profil public. C est ici que les membres deviennent vraiment visibles.', 'visual_title' => 'Commu active', 'visual_body' => 'Clips, reponses, favoris, profils publics et duels structurent l engagement.', 'cta_label' => 'Ouvrir les clips', 'cta_url' => route('clips.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 50],
            ['step_number' => 6, 'title' => 'Recuperer ses avantages et rester informe', 'summary' => 'Le reward wallet, les cadeaux et les notifications ferment la boucle.', 'body' => 'Quand votre activite porte ses fruits, vous pouvez consulter vos ressources, suivre vos cadeaux et affiner vos preferences de notification.', 'visual_title' => 'Avantages et suivi', 'visual_body' => 'Reward wallet, cadeaux, notifications et reglages personnels.', 'cta_label' => 'Voir les cadeaux', 'cta_url' => route('gifts.index'), 'status' => HelpTourStep::STATUS_PUBLISHED, 'sort_order' => 60],
        ];
    }
}
