const toyPalette = ['#c7c225', '#d9c71f', '#d8921d', '#9bcf2f', '#accf30', '#e28f1a', '#8ecb2e'];
const erahPalette = ['#7f1d1f', '#a42b24', '#be3d2a', '#946b21', '#7a0a10', '#a64321', '#632024'];

const polygons = [
    '0,0 24,0 12,20 0,16',
    '24,0 48,0 38,20 12,20',
    '48,0 72,0 66,24 38,20',
    '72,0 100,0 100,22 66,24',
    '0,16 12,20 8,42 0,36',
    '12,20 38,20 30,42 8,42',
    '38,20 66,24 54,48 30,42',
    '66,24 100,22 100,52 54,48',
    '0,36 8,42 0,72',
    '8,42 30,42 22,72 0,72',
    '30,42 54,48 44,78 22,72',
    '54,48 100,52 100,82 44,78',
    '0,72 22,72 16,100 0,100',
    '22,72 44,78 34,100 16,100',
    '44,78 100,82 100,100 34,100',
];

export default function PolygonBackground({ theme = 'toy', className = '' }) {
    const palette = theme === 'erah' ? erahPalette : toyPalette;
    const darkOverlayClass =
        theme === 'erah'
            ? 'bg-[linear-gradient(180deg,rgba(0,0,0,0.16),rgba(0,0,0,0.34))]'
            : 'bg-[linear-gradient(180deg,rgba(255,255,255,0.02),rgba(0,0,0,0.1))]';
    const radialOverlayClass =
        theme === 'erah'
            ? 'bg-[radial-gradient(circle_at_20%_8%,rgba(225,6,19,0.14),transparent_34%)]'
            : 'bg-[radial-gradient(circle_at_22%_12%,rgba(255,255,255,0.06),transparent_34%)]';

    return (
        <div className={`pointer-events-none fixed inset-0 ${className}`.trim()} aria-hidden="true">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" className="h-full w-full">
                {polygons.map((points, index) => (
                    <polygon
                        key={`${points}-${index}`}
                        points={points}
                        fill={palette[index % palette.length]}
                        fillOpacity={theme === 'erah' ? 0.82 : 0.96}
                    />
                ))}
            </svg>
            <div className={`absolute inset-0 ${darkOverlayClass}`} />
            <div className={`absolute inset-0 ${radialOverlayClass}`} />
        </div>
    );
}
