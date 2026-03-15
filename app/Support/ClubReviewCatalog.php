<?php

namespace App\Support;

use App\Models\ClubReview;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ClubReviewCatalog
{
    /**
     * @return array<int, array{author_name: string, author_profile_url: string, content: string}>
     */
    public static function legacyReviews(): array
    {
        return [
            [
                'author_name' => 'Hermes_vlr',
                'author_profile_url' => 'https://x.com/Hermes_vlr/status/2003525435775467839?s=20',
                'content' => "meilleur club et je pese mes mots",
            ],
            [
                'author_name' => 'Eliott "YRT"',
                'author_profile_url' => 'https://x.com/YRT_TV/status/1914994083593965715',
                'content' => "Enfin une structure qui cesse d'afficher des ambitions impossibles au vu de ses moyens et qui assume pleinement la formation.",
            ],
            [
                'author_name' => 'Soul Chains',
                'author_profile_url' => 'https://x.com/ChainsSoul/status/1914992066683093293',
                'content' => "Le programme d'ERAH semble vraiment bien structure. Former des joueuses de niveau Diamant/Ascendant et les faire evoluer dans deux equipes en tandem pourrait, a long terme, renforcer la reputation d'ERAH en tant que centre de formation.",
            ],
            [
                'author_name' => 'Pikali',
                'author_profile_url' => 'https://x.com/Pikaliplay',
                'content' => "Je tiens a vous dire que C'est vraiment une structure dont j'aime suivre l'avanc\u00e9e. Je le dis sincerement : vous etes l avenir de l esport en France, et il est tout a fait normal de soutenir des projets comme le votre.",
            ],
            [
                'author_name' => 'GuiltyObiwan',
                'author_profile_url' => 'https://x.com/GuiltyObiwan/status/1873956565222645773',
                'content' => "Je n'ai aucun regret d'avoir joue sous les couleurs de cette structure. Merci encore pour la confiance et l'opportunite qui m'ont ete offertes.",
            ],
            [
                'author_name' => 'Oxwig',
                'author_profile_url' => 'https://x.com/ERAH_Oxwig/status/1873756925072224455',
                'content' => "Heureux d'avoir pu contribuer a monter le pole Valorant chez ERAH et d'y voir des personnes de valeur le faire progresser, que ce soient nos cinq joueurs ou le staff. Big up a tous ceux qui sont passes chez nous : vous avez chacun fait evoluer le projet a votre maniere.",
            ],
            [
                'author_name' => 'Kaayyz',
                'author_profile_url' => 'https://x.com/KaayyzPrime/status/1873989255103746473',
                'content' => "Une fierte d'avoir eu la possibilite de representer vos couleurs. Merci pour le soutien, l'encouragement et pour m'avoir ouvert les yeux.",
            ],
            [
                'author_name' => 'Yusoh',
                'author_profile_url' => 'https://x.com/YusohFR/status/1873756595404124668',
                'content' => "Je remercie chaque personne qui a cru au projet, de pres ou de loin. Sans vous, rien n'aurait ete possible.",
            ],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function fallbackPresentedReviews(?int $limit = null): Collection
    {
        $baseTimestamp = Carbon::create(2026, 3, 8, 12, 0, 0);
        $reviews = collect(self::legacyReviews())
            ->values()
            ->map(function (array $review, int $index) use ($baseTimestamp): array {
                return [
                    'id' => 'legacy-'.($index + 1),
                    'content' => $review['content'],
                    'author_name' => $review['author_name'],
                    'author_url' => $review['author_profile_url'],
                    'author_cta' => 'Voir la source',
                    'avatar_url' => null,
                    'initials' => Str::upper(Str::substr(trim($review['author_name']), 0, 2)),
                    'published_at' => $baseTimestamp->copy()->subMinutes($index),
                    'is_member' => false,
                    'is_supporter' => false,
                    'supporter_label' => null,
                    'meta' => [],
                    'badges' => [],
                    'source_label' => ClubReview::sourceLabels()[ClubReview::SOURCE_SEED],
                ];
            });

        return $limit !== null ? $reviews->take(max(1, $limit))->values() : $reviews;
    }
}
