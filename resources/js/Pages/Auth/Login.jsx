import { Link, useForm } from '@inertiajs/react';

import HeroTile from '../../Components/ui/HeroTile';
import PillButton from '../../Components/ui/PillButton';
import PillInput from '../../Components/ui/PillInput';
import Tile from '../../Components/ui/Tile';
import GameLayout from '../../Layouts/GameLayout';

export default function Login({ status, canResetPassword = true }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: true,
    });

    const submit = (event) => {
        event.preventDefault();
        post('/login');
    };

    return (
        <GameLayout title="Connexion" subtitle="Acces securise ERAH" hideNavigation>
            <div className="mx-auto max-w-5xl space-y-4">
                <div className="toy-grid toy-grid-hero">
                    <HeroTile
                        title="Bienvenue"
                        description="Connecte-toi pour acceder aux ligues, paris, missions et clips."
                        ctaLabel="Creer un compte"
                        ctaVariant="secondary"
                        ctaHref="/register"
                        variant="light"
                    >
                        <div className="flex flex-wrap gap-2">
                            <a href="/auth/google/redirect" className="toy-pill-btn toy-pill-btn-secondary">
                                Continuer avec Google
                            </a>
                            <a href="/auth/discord/redirect" className="toy-pill-btn toy-pill-btn-secondary">
                                Continuer avec Discord
                            </a>
                        </div>
                    </HeroTile>

                    <Tile title="Connexion" subtitle="Email + mot de passe" size="l">
                        <form className="space-y-3" onSubmit={submit}>
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
                                autoComplete="current-password"
                                value={data.password}
                                onChange={(event) => setData('password', event.target.value)}
                                error={errors.password}
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

                            {status && <p className="text-sm text-emerald-300">{status}</p>}

                            <div className="flex flex-wrap gap-2 pt-1">
                                <PillButton type="submit" disabled={processing}>
                                    {processing ? 'Connexion...' : 'Se connecter'}
                                </PillButton>
                                {canResetPassword && (
                                    <Link href="/forgot-password">
                                        <PillButton variant="ghost">Mot de passe oublie</PillButton>
                                    </Link>
                                )}
                            </div>
                        </form>
                    </Tile>
                </div>

                <div className="text-sm text-ui-muted">
                    Pas encore de compte ?{' '}
                    <Link href="/register" className="text-white hover:text-red-200">
                        Inscription
                    </Link>
                </div>
            </div>
        </GameLayout>
    );
}

