import StatPill from './ui/StatPill';

const map = {
    default: 'default',
    league: 'new',
    status: 'dark',
    success: 'success',
    warning: 'warning',
    danger: 'danger',
    category: 'default',
};

export default function Badge({ variant = 'default', children, className = '' }) {
    return (
        <StatPill variant={map[variant] ?? 'default'} className={className}>
            {children}
        </StatPill>
    );
}
