import Tile from './Tile';

export default function MiniTile({ title, subtitle, children, className = '', ...props }) {
    return (
        <Tile title={title} subtitle={subtitle} variant="light" size="m" className={`min-h-[180px] ${className}`.trim()} {...props}>
            {children}
        </Tile>
    );
}
