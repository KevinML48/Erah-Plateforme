import { useState } from 'react';

import BadgeNew from '../Components/ui/BadgeNew';
import DarkTile from '../Components/ui/DarkTile';
import HeroTile from '../Components/ui/HeroTile';
import MiniTile from '../Components/ui/MiniTile';
import PillButton from '../Components/ui/PillButton';
import SkeletonTile from '../Components/ui/SkeletonTile';
import Tile from '../Components/ui/Tile';
import GameLayout from '../Layouts/GameLayout';

export default function UiKitToycad() {
    const [tab, setTab] = useState('start');

    return (
        <GameLayout
            title="UI Kit ToyCAD"
            subtitle="Validation visuelle board + tuiles"
            topTabs={[
                { label: 'Start', value: 'start' },
                { label: 'My Recent', value: 'recent' },
            ]}
            topTabsActive={tab}
            onTopTabsChange={setTab}
        >
            <div className="board-grid board-grid-main">
                <HeroTile
                    title="Get Started"
                    description="Reproduction ToyCAD: hero tile blanche, board noir, fond polygonal visible."
                    ctaLabel="Primary CTA"
                    variant="light"
                    className="min-h-[340px]"
                >
                    <div className="flex flex-wrap gap-2">
                        <BadgeNew />
                        <PillButton variant="secondary">Action secondaire</PillButton>
                    </div>
                </HeroTile>

                <MiniTile title="Tutorial" subtitle="Tuile claire">
                    <p className="text-sm text-black/70">Mini tile blanche avec contenu compact et coins tres arrondis.</p>
                </MiniTile>

                <MiniTile title="Duck" subtitle="Recent card">
                    <p className="text-sm text-black/70">Exemple de contenu type ToyCAD.</p>
                </MiniTile>

                <DarkTile title="Recent" subtitle="Dark vertical tile" className="row-span-2">
                    <p className="text-sm text-white/80">Tuile verticale sombre pour derniers events.</p>
                </DarkTile>

                <Tile title="Media tile" subtitle="Image / thumbnail" variant="light" className="min-h-[180px]" />
                <Tile title="Panel dark" subtitle="Status + KPIs" variant="dark" className="min-h-[180px]" />
            </div>

            <div className="mt-5 grid gap-4 md:grid-cols-3">
                <SkeletonTile className="min-h-[150px]" />
                <SkeletonTile className="min-h-[150px]" />
                <SkeletonTile className="min-h-[150px]" />
            </div>
        </GameLayout>
    );
}
