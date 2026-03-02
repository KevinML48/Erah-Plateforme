import { Link } from '@inertiajs/react';

import BoardActions from './BoardActions';
import BoardTabs from './BoardTabs';

export default function BoardHeader({
    title,
    subtitle,
    tabs = [
        { label: 'Start', value: 'start' },
        { label: 'My Recent', value: 'recent' },
    ],
    activeTab = 'start',
    onTabChange,
    user,
}) {
    return (
        <header className="board-header">
            <div className="board-brand">
                <Link href="/dashboard" className="board-brand-logo" aria-label="ERAH home">
                    <span className="board-brand-dot" />
                    <span>ERAH</span>
                </Link>
                <div className="board-brand-copy">
                    <p className="board-brand-title">{title}</p>
                    {subtitle ? <p className="board-brand-subtitle">{subtitle}</p> : null}
                </div>
            </div>

            <div className="board-header-tabs">
                <BoardTabs items={tabs} active={activeTab} onChange={onTabChange} />
            </div>

            <BoardActions user={user} />
        </header>
    );
}
