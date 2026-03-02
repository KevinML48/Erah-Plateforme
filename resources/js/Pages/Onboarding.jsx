import { useForm } from '@inertiajs/react';

import Button from '../Components/Button';
import Panel from '../Components/Panel';
import Toggle from '../Components/Toggle';
import GameLayout from '../Layouts/GameLayout';

export default function Onboarding({ channels }) {
    const { data, setData, post, processing, errors } = useForm({
        email_opt_in: Boolean(channels?.email_opt_in),
        push_opt_in: Boolean(channels?.push_opt_in),
    });

    const submit = (event) => {
        event.preventDefault();
        post('/onboarding');
    };

    return (
        <GameLayout title="Onboarding" hideNavigation>
            <form className="mx-auto max-w-2xl space-y-4" onSubmit={submit}>
                <Panel title="Bienvenue dans l'Arena" subtitle="Derniere etape avant d'entrer dans la plateforme." hover={false}>
                    <div className="space-y-3">
                        <p className="text-sm text-muted">
                            Configure tes notifications rapides. Tu pourras les modifier plus tard dans Settings.
                        </p>
                        <Toggle
                            label="Notifications email"
                            description="Recevoir les updates importantes par email."
                            checked={data.email_opt_in}
                            onChange={(checked) => setData('email_opt_in', checked)}
                        />
                        <Toggle
                            label="Notifications push"
                            description="Recevoir les alertes en temps reel."
                            checked={data.push_opt_in}
                            onChange={(checked) => setData('push_opt_in', checked)}
                        />
                        {(errors.email_opt_in || errors.push_opt_in) && (
                            <p className="text-sm text-red-300">{errors.email_opt_in || errors.push_opt_in}</p>
                        )}
                        <div className="pt-2">
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Enregistrement...' : 'Commencer'}
                            </Button>
                        </div>
                    </div>
                </Panel>
            </form>
        </GameLayout>
    );
}
