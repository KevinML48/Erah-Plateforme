export default function SkeletonTile({ className = '' }) {
    return (
        <div className={`skeleton-tile ${className}`.trim()} aria-hidden="true">
            <div className="skeleton-line w-2/3" />
            <div className="skeleton-line w-1/2" />
            <div className="skeleton-line w-5/6" />
        </div>
    );
}
