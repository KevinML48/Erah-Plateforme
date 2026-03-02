import Tile from './ui/Tile';

export default function Panel({ title, subtitle, action, className = '', children }) {
    return (
        <Tile title={title} subtitle={subtitle} action={action} className={className}>
            {children}
        </Tile>
    );
}
