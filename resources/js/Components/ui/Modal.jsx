export default function Modal({ open, title, children, onClose }) {
    if (!open) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-[80] bg-black/70 backdrop-blur-sm">
            <button type="button" className="absolute inset-0 h-full w-full cursor-default" onClick={onClose} aria-label="Fermer modal" />

            <div className="relative mx-auto mt-20 w-[calc(100%-2rem)] max-w-md rounded-panel border border-ui-border/18 bg-ui-panel p-5 shadow-tile-lift animate-drawer-up">
                <div className="mb-2 flex items-center justify-between gap-3">
                    {title && <h3 className="font-display text-lg font-bold">{title}</h3>}
                    <button
                        type="button"
                        className="toy-pill-btn toy-pill-btn-secondary h-9 w-9 p-0"
                        onClick={onClose}
                        aria-label="Fermer"
                    >
                        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>
                {children}
            </div>
        </div>
    );
}

