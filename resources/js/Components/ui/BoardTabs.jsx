export default function BoardTabs({ items = [], active, onChange }) {
    if (!items.length) {
        return null;
    }

    return (
        <div className="inline-flex rounded-full border border-white/18 bg-black/70 p-1 backdrop-blur" role="tablist">
            {items.map((item) => {
                const isActive = item.value === active;
                return (
                    <button
                        key={item.value}
                        type="button"
                        role="tab"
                        aria-selected={isActive}
                        onClick={() => onChange?.(item.value)}
                        className={[
                            'rounded-full px-5 py-2 text-sm font-semibold transition',
                            isActive ? 'bg-white text-black' : 'text-white/88 hover:text-white',
                        ].join(' ')}
                    >
                        {item.label}
                    </button>
                );
            })}
        </div>
    );
}

