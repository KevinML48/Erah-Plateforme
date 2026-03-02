export default function PillTabs({ items = [], active, onChange, className = '' }) {
    return (
        <div className={`toy-pill-tabs ${className}`.trim()} role="tablist">
            {items.map((item) => (
                <button
                    key={item.value}
                    type="button"
                    role="tab"
                    aria-selected={item.value === active}
                    className={`toy-pill-tab ${item.value === active ? 'active' : ''}`}
                    onClick={() => onChange?.(item.value)}
                >
                    {item.label}
                </button>
            ))}
        </div>
    );
}

