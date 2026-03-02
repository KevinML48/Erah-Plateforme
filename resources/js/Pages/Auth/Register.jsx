import { Link, useForm } from '@inertiajs/react';

import HeroTile from '../../Components/ui/HeroTile';
import PillButton from '../../Components/ui/PillButton';
import PillInput from '../../Components/ui/PillInput';
import Tile from '../../Components/ui/Tile';
import GameLayout from '../../Layouts/GameLayout';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        remember: true,
    });

    const submit = (event) => {
        event.preventDefault();
        post('/register');
    };

    return (
        <GameLayout title="Inscription" subtitle="Creation de compte ERAH" hideNavigation>
            <div className="mx-auto max-w-5xl space-y-4">
                <div className="toy-grid toy-grid-hero">
                    <HeroTile
                        title="Creer ton profil"
                        description="Active ton compte pour rejoindre la plateforme communautaire gamifiee."
                        ctaLabel="Retour connexion"
                        ctaVariant="secondary"
                        ctaHref="/login"
                        variant="light"
                    >
                        <div className="flex flex-wrap gap-2">
                            <a href="/auth/google/redirect" className="toy-pill-btn toy-pill-btn-secondary">
                                Google
                            </a>
                            <a href="/auth/discord/redirect" className="toy-pill-btn toy-pill-btn-secondary">
                                Discord
                            </a>
                        </div>
                    </HeroTile>

                    <Tile title="Formulaire" subtitle="Informations de base" size="l">
                        <form className="space-y-3" onSubmit={submit}>
                            <PillInput label="Nom" value={data.name} onChange={(event) => setData('name', event.target.value)} error={errors.name} />
                            <PillInput
                                label="Email"
                                type="email"
                                autoComplete="email"
                                value={data.email}
                                onChange={(event) => setData('email', event.target.value)}
                                error={errors.email}
                            />
                            <PillInput
                                label="Mot de passe"
                                type="password"
                                autoComplete="new-password"
                                value={data.password}
                                onChange={(event) => setData('password', event.target.value)}
                                error={errors.password}
                            />
                            <PillInput
                                label="Confirmation"
                                type="password"
                                autoComplete="new-password"
                                value={data.password_confirmation}
                                onChange={(event) => setData('password_confirmation', event.target.value)}
                                error={errors.password_confirmation}
                            />

                            <label className="flex items-center gap-2 text-sm text-ui-muted">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(event) => setData('remember', event.target.checked)}
                                    className="rounded border-ui-border/25 bg-ui-bg text-ui-red focus:ring-ui-red"
                                />
                                Se souvenir de moi
                            </label>

                            <PillButton type="submit" disabled={processing}>
                                {processing ? 'Creation...' : 'Creer mon compte'}
                            </PillButton>
                        </form>
                    </Tile>
                </div>

                <p className="text-sm text-ui-muted">
                    Deja inscrit ?{' '}
                    <Link href="/login" className="text-white hover:text-red-200">
                        Se connecter
                    </Link>
                </p>
            </div>
        </GameLayout>
    );
}

