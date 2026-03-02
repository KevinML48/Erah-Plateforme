import { useState } from 'react';

import EmptyTile from '../Components/ui/EmptyTile';
import FloatingAction from '../Components/ui/FloatingAction';
import HeroTile from '../Components/ui/HeroTile';
import KebabMenu from '../Components/ui/KebabMenu';
import MediaTile from '../Components/ui/MediaTile';
import Modal from '../Components/ui/Modal';
import PillButton from '../Components/ui/PillButton';
import PillInput from '../Components/ui/PillInput';
import PillTabBar from '../Components/ui/PillTabBar';
import PillTabs from '../Components/ui/PillTabs';
import StatPill from '../Components/ui/StatPill';
import Tile from '../Components/ui/Tile';
import Toggle from '../Components/ui/Toggle';
import GameLayout from '../Layouts/GameLayout';

export default function UiKit() {
    const [active, setActive] = useState('library');
    const [open, setOpen] = useState(false);
    const [enabled, setEnabled] = useState(true);

    return (
        <GameLayout title="UI Kit" subtitle="Composants ToyCAD x ERAH">
            <Tile title="Navigation pills" subtitle="Top tabs + bottom app bar">
                <div className="space-y-4">
                    <PillTabs
                        items={[
                            { label: 'ToyCAD Library', value: 'library' },
                            { label: 'My Library', value: 'mine' },
                        ]}
                        active={active}
                        onChange={setActive}
                    />
                    <div className="pointer-events-none rounded-hud border border-ui-border/12 bg-ui-panel/80 p-3">
                        <PillTabBar hidden />
                        <p className="text-xs text-ui-muted">Bottom tab bar active en bas des pages.</p>
                    </div>
                </div>
            </Tile>

            <div className="toy-grid toy-grid-hero">
                <HeroTile
                    title="Hero Tile"
                    description="Grande tuile principale pour onboarding, dashboard et pages clefs."
                    ctaLabel="Action"
                    secondaryAction={<PillButton variant="secondary">Secondary</PillButton>}
                    variant="light"
                >
                    <div className="flex flex-wrap gap-2">
                        <StatPill variant="new">NEW</StatPill>
                        <StatPill variant="warning">LIVE</StatPill>
                    </div>
                </HeroTile>

                <Tile title="Buttons + badges" subtitle="Core interactive controls">
                    <div className="space-y-3">
                        <div className="flex flex-wrap gap-2">
                            <PillButton>Primary</PillButton>
                            <PillButton variant="secondary">Secondary</PillButton>
                            <PillButton variant="ghost">Ghost</PillButton>
                            <PillButton variant="danger">Danger</PillButton>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <StatPill>Default</StatPill>
                            <StatPill variant="new">New</StatPill>
                            <StatPill variant="success">Success</StatPill>
                            <StatPill variant="warning">Warning</StatPill>
                        </div>
                    </div>
                </Tile>
            </div>

            <div className="toy-grid toy-grid-3">
                <MediaTile title="Media Tile" meta="Thumbnail / item preview" badge="NEW" action={<KebabMenu />} />
                <Tile title="Inputs">
                    <div className="space-y-2">
                        <PillInput label="Pseudo" placeholder="Player One" />
                        <PillInput label="Ligue" element="select" defaultValue="bronze">
                            <option value="bronze">Bronze</option>
                            <option value="argent">Argent</option>
                        </PillInput>
                    </div>
                </Tile>
                <Tile title="Empty + modal">
                    <div className="space-y-2">
                        <EmptyTile title="No data" description="Etat vide unifie." />
                        <PillButton variant="secondary" onClick={() => setOpen(true)}>
                            Ouvrir modal
                        </PillButton>
                    </div>
                </Tile>
            </div>

            <Tile title="Utility widgets">
                <div className="flex flex-wrap items-center gap-3">
                    <Toggle label="Notifications" description="Toggle style ToyCAD" checked={enabled} onChange={setEnabled} />
                    <FloatingAction label="Nouvelle action" />
                </div>
            </Tile>

            <Modal open={open} title="Modal example" onClose={() => setOpen(false)}>
                <p className="text-sm text-ui-muted">Confirmer cette action ?</p>
                <div className="mt-4 flex justify-end gap-2">
                    <PillButton variant="ghost" onClick={() => setOpen(false)}>
                        Annuler
                    </PillButton>
                    <PillButton onClick={() => setOpen(false)}>Confirmer</PillButton>
                </div>
            </Modal>
        </GameLayout>
    );
}

