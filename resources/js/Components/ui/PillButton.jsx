const variants = {
    primary: 'toy-pill-btn toy-pill-btn-primary',
    secondary: 'toy-pill-btn toy-pill-btn-secondary',
    ghost: 'toy-pill-btn toy-pill-btn-ghost',
    danger: 'toy-pill-btn toy-pill-btn-danger',
};

export default function PillButton({ type = 'button', variant = 'primary', className = '', children, ...props }) {
    return (
        <button type={type} className={`${variants[variant] ?? variants.primary} ${className}`.trim()} {...props}>
            {children}
        </button>
    );
}

