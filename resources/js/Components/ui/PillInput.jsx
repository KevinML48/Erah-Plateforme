function InputElement({ element = 'input', className = '', children, ...props }) {
    if (element === 'textarea') {
        return (
            <textarea className={`toy-input is-area ${className}`} {...props}>
                {children}
            </textarea>
        );
    }

    if (element === 'select') {
        return (
            <select className={`toy-input ${className}`} {...props}>
                {children}
            </select>
        );
    }

    return <input className={`toy-input ${className}`} {...props} />;
}

export default function PillInput({ label, error, element = 'input', className = '', children, ...props }) {
    return (
        <label className="block space-y-1.5">
            {label && <span className="text-sm font-semibold text-ui-text/95">{label}</span>}
            <InputElement element={element} className={className} {...props}>
                {children}
            </InputElement>
            {error && <p className="text-xs text-red-300">{error}</p>}
        </label>
    );
}

