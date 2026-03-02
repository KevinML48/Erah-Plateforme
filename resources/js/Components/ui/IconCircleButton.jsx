import { Link } from '@inertiajs/react';

export default function IconCircleButton({ label, onClick, children, href, className = '' }) {
    const baseClass =
        `inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/8 text-white transition hover:-translate-y-0.5 hover:border-white/35 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ui-red ${className}`.trim();

    if (href) {
        return (
            <Link href={href} className={baseClass} aria-label={label}>
                {children}
            </Link>
        );
    }

    return (
        <button type="button" onClick={onClick} className={baseClass} aria-label={label}>
            {children}
        </button>
    );
}
