import Tile from './Tile';

export default function DarkTile({ title, subtitle, children, className = '', ...props }) {
    return (
        <Tile title={title} subtitle={subtitle} variant="dark" size="l" className={`min-h-[240px] ${className}`.trim()} {...props}>
            {children}
        </Tile>
    );
}
