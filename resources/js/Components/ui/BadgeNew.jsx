export default function BadgeNew({ children = 'NEW', className = '' }) {
    return (
        <span className={`badge-new ${className}`.trim()}>
            {children}
        </span>
    );
}
