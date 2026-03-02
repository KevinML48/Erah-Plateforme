import { Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import HomeCarousel from '../Components/Home/HomeCarousel';
import HomeDarkTile from '../Components/Home/HomeDarkTile';
import HomeHeroTile from '../Components/Home/HomeHeroTile';
import HomeMiniTile from '../Components/Home/HomeMiniTile';
import GameLayout from '../Layouts/GameLayout';
import { formatDate, formatNumber } from '../lib/format';
import useApiData from '../lib/useApiData';

function toArray(payload) {
    if (Array.isArray(payload?.data)) {
        return payload.data;
    }

    if (Array.isArray(payload)) {
        return payload;
    }

    return [];
}

function teamA(match) {
    return match?.team_a_name ?? match?.home_team ?? match?.teamA ?? 'Team A';
}

function teamB(match) {
    return match?.team_b_name ?? match?.away_team ?? match?.teamB ?? 'Team B';
}

function buildSlides({ matches, clips, missions, duels, notifications }, activeTab) {
    const startSlides = [
        ...matches.slice(0, 2).map((match, index) => ({
            id: `match-${match.id ?? index}`,
            title: `${teamA(match)} vs ${teamB(match)}`,
            meta: match.starts_at ? `Starts ${formatDate(match.starts_at)}` : 'Upcoming match',
            type: 'Matches',
            tone: 'dark',
            thumb: 'match',
            badge: index === 0 ? 'NEW' : null,
        })),
        ...clips.slice(0, 2).map((clip, index) => ({
            id: `clip-${clip.id ?? index}`,
            title: clip.title ?? 'Trending clip',
            meta: `${formatNumber(clip.likes_count ?? 0)} likes`,
            type: 'Clips',
            tone: 'light',
            thumb: 'clip',
            badge: index === 0 ? 'HOT' : null,
        })),
        ...missions.slice(0, 1).map((mission, index) => ({
            id: `mission-${mission.id ?? index}`,
            title: mission.title ?? mission.key ?? 'Mission',
            meta: `${mission.progress_count ?? 0}/${mission.target_count ?? 1}`,
            type: 'Mission',
            tone: 'light',
            thumb: 'mission',
            badge: mission.completed_at ? 'DONE' : 'NEW',
        })),
        ...duels.slice(0, 1).map((duel, index) => ({
            id: `duel-${duel.id ?? index}`,
            title: `Duel vs ${duel.challenged_user_name ?? duel.challenger_user_name ?? 'Player'}`,
            meta: duel.status ?? 'pending',
            type: 'Duels',
            tone: 'dark',
            thumb: 'duel',
            badge: duel.status === 'pending' ? 'PENDING' : null,
        })),
    ];

    const recentSlides = [
        ...notifications.slice(0, 3).map((item, index) => ({
            id: `notif-${item.id ?? index}`,
            title: item.title ?? 'Notification',
            meta: item.created_at ? formatDate(item.created_at) : 'Recent',
            type: 'Notifs',
            tone: 'dark',
            thumb: 'notif',
            badge: item.read_at ? null : 'NEW',
        })),
        ...clips.slice(0, 2).map((clip, index) => ({
            id: `recent-clip-${clip.id ?? index}`,
            title: clip.title ?? 'Clip recent',
            meta: `${formatNumber(clip.comments_count ?? 0)} comments`,
            type: 'Clips',
            tone: 'light',
            thumb: 'clip',
            badge: null,
        })),
    ];

    const chosen = activeTab === 'recent' ? recentSlides : startSlides;

    if (chosen.length) {
        return chosen;
    }

    return [
        {
            id: 'placeholder-1',
            title: 'Highlights ERAH',
            meta: 'Content will appear here from API.',
            type: 'System',
            tone: 'light',
            thumb: 'default',
            badge: 'NEW',
        },
        {
            id: 'placeholder-2',
            title: 'Upcoming match',
            meta: 'Add seeded matches to fill carousel.',
            type: 'Matches',
            tone: 'dark',
            thumb: 'match',
            badge: null,
        },
        {
            id: 'placeholder-3',
            title: 'Trending clip',
            meta: 'Add seeded clips to fill carousel.',
            type: 'Clips',
            tone: 'light',
            thumb: 'clip',
            badge: null,
        },
    ];
}

export default function Dashboard() {
    const [activeTab, setActiveTab] = useState('start');

    const progressQuery = useApiData('/me/progress');
    const missionsQuery = useApiData('/missions/today');
    const clipsQuery = useApiData('/clips?sort=popular&limit=8');
    const matchesQuery = useApiData('/matches?status=scheduled&limit=8');
    const duelsQuery = useApiData('/duels?status=pending&limit=8');
    const notificationsQuery = useApiData('/notifications?limit=8');

    const progress = progressQuery.data ?? null;
    const missions = toArray(missionsQuery.data);
    const clips = toArray(clipsQuery.data);
    const matches = toArray(matchesQuery.data);
    const duels = toArray(duelsQuery.data);
    const notifications = toArray(notificationsQuery.data);

    const heroLeague = progress?.league?.name ?? 'Bronze';
    const heroPoints = formatNumber(progress?.total_rank_points ?? 0);

    const firstMission = missions[0];
    const firstClip = clips[0];
    const firstMatch = matches[0];
    const secondMatch = matches[1];

    const slides = useMemo(
        () => buildSlides({ matches, clips, missions, duels, notifications }, activeTab),
        [matches, clips, missions, duels, notifications, activeTab],
    );

    return (
        <GameLayout title="Dashboard" hideHeader shellMode="open">
            <div className="home-reference-canvas">
                <header className="home-reference-header">
                    <div className="home-reference-logo">
                        <span className="home-logo-mark" aria-hidden="true" />
                        <span className="home-logo-text">ToyCAD</span>
                    </div>

                    <div className="home-reference-tabs" role="tablist" aria-label="Home tabs">
                        <button
                            type="button"
                            role="tab"
                            aria-selected={activeTab === 'start'}
                            className={`home-reference-tab ${activeTab === 'start' ? 'is-active' : ''}`}
                            onClick={() => setActiveTab('start')}
                        >
                            Start
                        </button>
                        <button
                            type="button"
                            role="tab"
                            aria-selected={activeTab === 'recent'}
                            className={`home-reference-tab ${activeTab === 'recent' ? 'is-active' : ''}`}
                            onClick={() => setActiveTab('recent')}
                        >
                            My Recent
                        </button>
                    </div>

                    <Link href="/app/matches" className="home-reference-power">
                        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="m4 20 7-7m0 0 4-8 5-1-1 5-8 4z" />
                        </svg>
                        Full Power
                    </Link>
                </header>

                <section className="home-tiles-layout" aria-label="Main tiles">
                    <HomeHeroTile
                        title="Get Started"
                        subtitle="ERAH Arena: progresse, parie, gagne des recompenses."
                        leagueName={heroLeague}
                        points={heroPoints}
                        href="/app/matches"
                        imageSrc="/assets/toycad/hero-hand.svg"
                    />

                    <div className="home-mini-stack">
                        <HomeMiniTile
                            label="Tutorial"
                            title="Missions du jour"
                            description={firstMission ? `${firstMission.progress_count ?? 0}/${firstMission.target_count ?? 1} objectifs` : 'No active mission'}
                            href="/app/missions"
                            media="mission"
                            imageSrc="/assets/toycad/chick.svg"
                        />

                        <HomeMiniTile
                            label="Tutorial"
                            title="Clips trending"
                            description={firstClip ? firstClip.title : 'No trending clip'}
                            href="/app/clips"
                            media="clip"
                            imageSrc="/assets/toycad/teddy.svg"
                        />
                    </div>

                    <div className="home-dark-stack">
                        <HomeDarkTile
                            dateLabel={firstMatch?.starts_at ? formatDate(firstMatch.starts_at) : 'Apr 24, 11:38 AM'}
                            title={firstMatch ? `${teamA(firstMatch)} vs ${teamB(firstMatch)}` : 'Matchs a venir'}
                            href={firstMatch ? `/app/matches/${firstMatch.id}` : '/app/matches'}
                            media="match"
                            imageSrc="/assets/toycad/turtle.svg"
                        />

                        <HomeDarkTile
                            dateLabel={secondMatch?.starts_at ? formatDate(secondMatch.starts_at) : 'Apr 18, 3:59 PM'}
                            title={secondMatch ? `${teamA(secondMatch)} vs ${teamB(secondMatch)}` : 'Classement'}
                            href="/leaderboards"
                            media="rank"
                            imageSrc="/assets/toycad/piggy.svg"
                        />
                    </div>
                </section>

                <section className="home-carousel-section" aria-label="Highlights slider">
                    <HomeCarousel
                        slides={slides}
                        label={activeTab === 'recent' ? 'My Recent' : 'Highlights'}
                        collapsed={activeTab === 'start'}
                    />
                </section>
            </div>
        </GameLayout>
    );
}
