import PillInput from './ui/PillInput';

export default function Select({ children, ...props }) {
    return (
        <PillInput element="select" {...props}>
            {children}
        </PillInput>
    );
}
