import React from 'react';
import { router, useForm } from '@inertiajs/react';

import Badge from '../../../Components/Badge';
import Button from '../../../Components/Button';
import Input from '../../../Components/Input';
import Select from '../../../Components/Select';
import Textarea from '../../../Components/Textarea';
import HelpCenterLayout from '../../../Layouts/HelpCenterLayout';
import AdminEditorPanel from '../components/AdminEditorPanel';

const emptyCategory = {
    title: '',
    slug: '',
    description: '',
    intro: '',
    icon: '',
    landing_bucket: 'understanding_platform',
    tutorial_video_url: '',
    status: 'draft',
    sort_order: 0,
};

const emptyArticle = {
    help_category_id: '',
    title: '',
    slug: '',
    summary: '',
    body: '',
    short_answer: '',
    keywords: '',
    tutorial_video_url: '',
    cta_label: '',
    cta_url: '',
    status: 'draft',
    is_featured: false,
    is_faq: false,
    sort_order: 0,
};

const emptyGlossary = {
    term: '',
    slug: '',
    definition: '',
    short_answer: '',
    status: 'draft',
    is_featured: false,
    sort_order: 0,
};

const emptyTourStep = {
    step_number: 1,
    title: '',
    summary: '',
    body: '',
    visual_title: '',
    visual_body: '',
    cta_label: '',
    cta_url: '',
    tutorial_video_url: '',
    status: 'draft',
    sort_order: 1,
};

function StatCard({ label, value }) {
    return (
        <article className="rounded-2xl border border-ui-border/12 bg-black/20 p-4">
            <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">{label}</p>
            <p className="mt-3 text-3xl font-semibold text-white">{value}</p>
        </article>
    );
}

function FormActions({ processing, editing, onReset }) {
    return (
        <div className="flex flex-wrap gap-3">
            <Button type="submit" disabled={processing}>
                {processing ? 'Enregistrement...' : editing ? 'Mettre a jour' : 'Creer'}
            </Button>
            {editing ? (
                <Button type="button" variant="secondary" onClick={onReset}>
                    Annuler
                </Button>
            ) : null}
        </div>
    );
}

function StatusOptions({ statuses = [] }) {
    return statuses.map((status) => (
        <option key={status} value={status}>
            {status}
        </option>
    ));
}

export default function HelpAdminIndex({ page }) {
    const categoryForm = useForm(emptyCategory);
    const articleForm = useForm(emptyArticle);
    const glossaryForm = useForm(emptyGlossary);
    const stepForm = useForm(emptyTourStep);

    const resetCategory = () => {
        categoryForm.setData(() => ({ ...emptyCategory }));
        categoryForm.clearErrors();
    };
    const resetArticle = () => {
        articleForm.setData(() => ({ ...emptyArticle }));
        articleForm.clearErrors();
    };
    const resetGlossary = () => {
        glossaryForm.setData(() => ({ ...emptyGlossary }));
        glossaryForm.clearErrors();
    };
    const resetStep = () => {
        stepForm.setData(() => ({ ...emptyTourStep }));
        stepForm.clearErrors();
    };

    const submitCategory = (event) => {
        event.preventDefault();
        const url = categoryForm.data.id ? page.categories.find((item) => item.id === categoryForm.data.id)?.update_url : page.endpoints.categories_store;
        const method = categoryForm.data.id ? categoryForm.put.bind(categoryForm) : categoryForm.post.bind(categoryForm);
        method(url, {
            preserveScroll: true,
            onSuccess: resetCategory,
        });
    };

    const submitArticle = (event) => {
        event.preventDefault();
        const url = articleForm.data.id ? page.articles.find((item) => item.id === articleForm.data.id)?.update_url : page.endpoints.articles_store;
        const method = articleForm.data.id ? articleForm.put.bind(articleForm) : articleForm.post.bind(articleForm);
        method(url, {
            preserveScroll: true,
            onSuccess: resetArticle,
        });
    };

    const submitGlossary = (event) => {
        event.preventDefault();
        const url = glossaryForm.data.id ? page.glossary.find((item) => item.id === glossaryForm.data.id)?.update_url : page.endpoints.glossary_store;
        const method = glossaryForm.data.id ? glossaryForm.put.bind(glossaryForm) : glossaryForm.post.bind(glossaryForm);
        method(url, {
            preserveScroll: true,
            onSuccess: resetGlossary,
        });
    };

    const submitStep = (event) => {
        event.preventDefault();
        const url = stepForm.data.id ? page.tourSteps.find((item) => item.id === stepForm.data.id)?.update_url : page.endpoints.tour_steps_store;
        const method = stepForm.data.id ? stepForm.put.bind(stepForm) : stepForm.post.bind(stepForm);
        method(url, {
            preserveScroll: true,
            onSuccess: resetStep,
        });
    };

    return (
        <HelpCenterLayout
            mode="admin"
            title="Administration du centre d aide"
            subtitle="Categories, articles, glossaire, parcours guide et reponses courtes pretes pour la future IA."
        >
            <section className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <StatCard label="Categories" value={page.overview.categories} />
                <StatCard label="Articles publies" value={page.overview.published_articles} />
                <StatCard label="FAQ mises en avant" value={page.overview.featured_faqs} />
                <StatCard label="Etapes guidees" value={page.overview.tour_steps} />
            </section>

            <AdminEditorPanel title="Categories d aide" subtitle="Gerer les grandes portes d entree du centre d aide.">
                <div className="grid gap-5 xl:grid-cols-[minmax(0,0.95fr)_minmax(360px,0.8fr)]">
                    <form className="grid gap-4 md:grid-cols-2" onSubmit={submitCategory}>
                        <Input label="Titre" value={categoryForm.data.title} onChange={(event) => categoryForm.setData('title', event.target.value)} />
                        <Input label="Slug" value={categoryForm.data.slug} onChange={(event) => categoryForm.setData('slug', event.target.value)} />
                        <Input label="Icone" value={categoryForm.data.icon} onChange={(event) => categoryForm.setData('icon', event.target.value)} />
                        <Input label="Ordre" type="number" min="0" value={categoryForm.data.sort_order} onChange={(event) => categoryForm.setData('sort_order', event.target.value)} />
                        <Select label="Bloc home" value={categoryForm.data.landing_bucket} onChange={(event) => categoryForm.setData('landing_bucket', event.target.value)}>
                            {page.options.landingBuckets.map((bucket) => <option key={bucket.value} value={bucket.value}>{bucket.label}</option>)}
                        </Select>
                        <Select label="Statut" value={categoryForm.data.status} onChange={(event) => categoryForm.setData('status', event.target.value)}>
                            <StatusOptions statuses={page.options.statuses} />
                        </Select>
                        <Input className="md:col-span-2" label="Video tutorielle" value={categoryForm.data.tutorial_video_url} onChange={(event) => categoryForm.setData('tutorial_video_url', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Description" value={categoryForm.data.description} onChange={(event) => categoryForm.setData('description', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Intro categorie" value={categoryForm.data.intro} onChange={(event) => categoryForm.setData('intro', event.target.value)} />
                        <div className="md:col-span-2">
                            <FormActions processing={categoryForm.processing} editing={Boolean(categoryForm.data.id)} onReset={resetCategory} />
                        </div>
                    </form>

                    <div className="space-y-3">
                        {page.categories.map((category) => (
                            <article key={category.id} className="rounded-2xl border border-white/6 bg-black/20 p-4">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h4 className="text-sm font-semibold text-white">{category.title}</h4>
                                        <p className="mt-1 text-xs text-ui-muted">{category.articles_count} article(s) · {category.status}</p>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button type="button" variant="secondary" className="px-3 py-2 text-xs" onClick={() => categoryForm.setData(() => ({ ...category, id: category.id }))}>Editer</Button>
                                        <Button type="button" variant="danger" className="px-3 py-2 text-xs" onClick={() => { if (confirm('Supprimer cette categorie ?')) router.delete(category.delete_url, { preserveScroll: true }); }}>Supprimer</Button>
                                    </div>
                                </div>
                            </article>
                        ))}
                    </div>
                </div>
            </AdminEditorPanel>

            <AdminEditorPanel title="Articles et FAQ" subtitle="Le contenu principal du centre d aide et les reponses courtes pour la future IA.">
                <div className="grid gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(360px,0.8fr)]">
                    <form className="grid gap-4 md:grid-cols-2" onSubmit={submitArticle}>
                        <Select label="Categorie" value={articleForm.data.help_category_id} onChange={(event) => articleForm.setData('help_category_id', event.target.value)}>
                            <option value="">Selectionner</option>
                            {page.categories.map((category) => <option key={category.id} value={category.id}>{category.title}</option>)}
                        </Select>
                        <Select label="Statut" value={articleForm.data.status} onChange={(event) => articleForm.setData('status', event.target.value)}>
                            <StatusOptions statuses={page.options.statuses} />
                        </Select>
                        <Input label="Titre" value={articleForm.data.title} onChange={(event) => articleForm.setData('title', event.target.value)} />
                        <Input label="Slug" value={articleForm.data.slug} onChange={(event) => articleForm.setData('slug', event.target.value)} />
                        <Input className="md:col-span-2" label="Mots cles (separes par des virgules)" value={articleForm.data.keywords} onChange={(event) => articleForm.setData('keywords', event.target.value)} />
                        <Input className="md:col-span-2" label="Video tutorielle" value={articleForm.data.tutorial_video_url} onChange={(event) => articleForm.setData('tutorial_video_url', event.target.value)} />
                        <Input label="CTA label" value={articleForm.data.cta_label} onChange={(event) => articleForm.setData('cta_label', event.target.value)} />
                        <Input label="CTA url" value={articleForm.data.cta_url} onChange={(event) => articleForm.setData('cta_url', event.target.value)} />
                        <Input label="Ordre" type="number" min="0" value={articleForm.data.sort_order} onChange={(event) => articleForm.setData('sort_order', event.target.value)} />
                        <div className="flex items-center gap-4 pt-7 text-sm text-ui-muted">
                            <label className="flex items-center gap-2"><input type="checkbox" checked={articleForm.data.is_featured} onChange={(event) => articleForm.setData('is_featured', event.target.checked)} /> Mis en avant</label>
                            <label className="flex items-center gap-2"><input type="checkbox" checked={articleForm.data.is_faq} onChange={(event) => articleForm.setData('is_faq', event.target.checked)} /> FAQ</label>
                        </div>
                        <Textarea className="md:col-span-2" label="Resume" value={articleForm.data.summary} onChange={(event) => articleForm.setData('summary', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Reponse courte IA" value={articleForm.data.short_answer} onChange={(event) => articleForm.setData('short_answer', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Corps de l article" value={articleForm.data.body} onChange={(event) => articleForm.setData('body', event.target.value)} />
                        <div className="md:col-span-2">
                            <FormActions processing={articleForm.processing} editing={Boolean(articleForm.data.id)} onReset={resetArticle} />
                        </div>
                    </form>

                    <div className="space-y-3">
                        {page.articles.map((article) => (
                            <article key={article.id} className="rounded-2xl border border-white/6 bg-black/20 p-4">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h4 className="text-sm font-semibold text-white">{article.title}</h4>
                                        <p className="mt-1 text-xs text-ui-muted">{article.category?.title} · {article.status}</p>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button type="button" variant="secondary" className="px-3 py-2 text-xs" onClick={() => articleForm.setData(() => ({ ...article, id: article.id }))}>Editer</Button>
                                        <Button type="button" variant="danger" className="px-3 py-2 text-xs" onClick={() => { if (confirm('Supprimer cet article ?')) router.delete(article.delete_url, { preserveScroll: true }); }}>Supprimer</Button>
                                    </div>
                                </div>
                            </article>
                        ))}
                    </div>
                </div>
            </AdminEditorPanel>

            <div className="grid gap-6 xl:grid-cols-2">
                <AdminEditorPanel title="Glossaire" subtitle="Definitions courtes et reponses rapides pretes pour l aide contextuelle.">
                    <form className="grid gap-4 md:grid-cols-2" onSubmit={submitGlossary}>
                        <Input label="Terme" value={glossaryForm.data.term} onChange={(event) => glossaryForm.setData('term', event.target.value)} />
                        <Input label="Slug" value={glossaryForm.data.slug} onChange={(event) => glossaryForm.setData('slug', event.target.value)} />
                        <Select label="Statut" value={glossaryForm.data.status} onChange={(event) => glossaryForm.setData('status', event.target.value)}>
                            <StatusOptions statuses={page.options.statuses} />
                        </Select>
                        <Input label="Ordre" type="number" min="0" value={glossaryForm.data.sort_order} onChange={(event) => glossaryForm.setData('sort_order', event.target.value)} />
                        <label className="md:col-span-2 flex items-center gap-2 text-sm text-ui-muted">
                            <input type="checkbox" checked={glossaryForm.data.is_featured} onChange={(event) => glossaryForm.setData('is_featured', event.target.checked)} />
                            Mettre en avant
                        </label>
                        <Textarea className="md:col-span-2" label="Definition" value={glossaryForm.data.definition} onChange={(event) => glossaryForm.setData('definition', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Reponse courte" value={glossaryForm.data.short_answer} onChange={(event) => glossaryForm.setData('short_answer', event.target.value)} />
                        <div className="md:col-span-2">
                            <FormActions processing={glossaryForm.processing} editing={Boolean(glossaryForm.data.id)} onReset={resetGlossary} />
                        </div>
                    </form>

                    <div className="mt-5 space-y-3">
                        {page.glossary.map((term) => (
                            <article key={term.id} className="rounded-2xl border border-white/6 bg-black/20 p-4">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h4 className="text-sm font-semibold text-white">{term.term}</h4>
                                        <p className="mt-1 text-xs text-ui-muted">{term.status}</p>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button type="button" variant="secondary" className="px-3 py-2 text-xs" onClick={() => glossaryForm.setData(() => ({ ...term, id: term.id }))}>Editer</Button>
                                        <Button type="button" variant="danger" className="px-3 py-2 text-xs" onClick={() => { if (confirm('Supprimer ce terme ?')) router.delete(term.delete_url, { preserveScroll: true }); }}>Supprimer</Button>
                                    </div>
                                </div>
                            </article>
                        ))}
                    </div>
                </AdminEditorPanel>

                <AdminEditorPanel title="Parcours guide" subtitle="Les 6 etapes qui servent a onboarder un visiteur ou un membre.">
                    <form className="grid gap-4 md:grid-cols-2" onSubmit={submitStep}>
                        <Input label="Etape" type="number" min="1" max="6" value={stepForm.data.step_number} onChange={(event) => stepForm.setData('step_number', event.target.value)} />
                        <Input label="Ordre" type="number" min="1" max="6" value={stepForm.data.sort_order} onChange={(event) => stepForm.setData('sort_order', event.target.value)} />
                        <Input className="md:col-span-2" label="Titre" value={stepForm.data.title} onChange={(event) => stepForm.setData('title', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Resume" value={stepForm.data.summary} onChange={(event) => stepForm.setData('summary', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Texte pedagogique" value={stepForm.data.body} onChange={(event) => stepForm.setData('body', event.target.value)} />
                        <Input label="Titre visuel" value={stepForm.data.visual_title} onChange={(event) => stepForm.setData('visual_title', event.target.value)} />
                        <Input label="Video" value={stepForm.data.tutorial_video_url} onChange={(event) => stepForm.setData('tutorial_video_url', event.target.value)} />
                        <Textarea className="md:col-span-2" label="Texte visuel" value={stepForm.data.visual_body} onChange={(event) => stepForm.setData('visual_body', event.target.value)} />
                        <Input label="CTA label" value={stepForm.data.cta_label} onChange={(event) => stepForm.setData('cta_label', event.target.value)} />
                        <Input label="CTA url" value={stepForm.data.cta_url} onChange={(event) => stepForm.setData('cta_url', event.target.value)} />
                        <Select label="Statut" value={stepForm.data.status} onChange={(event) => stepForm.setData('status', event.target.value)}>
                            <StatusOptions statuses={page.options.statuses} />
                        </Select>
                        <div className="md:col-span-2">
                            <FormActions processing={stepForm.processing} editing={Boolean(stepForm.data.id)} onReset={resetStep} />
                        </div>
                    </form>

                    <div className="mt-5 space-y-3">
                        {page.tourSteps.map((step) => (
                            <article key={step.id} className="rounded-2xl border border-white/6 bg-black/20 p-4">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h4 className="text-sm font-semibold text-white">{step.step_number}/6 · {step.title}</h4>
                                        <p className="mt-1 text-xs text-ui-muted">{step.status}</p>
                                    </div>
                                    <div className="flex gap-2">
                                        <Button type="button" variant="secondary" className="px-3 py-2 text-xs" onClick={() => stepForm.setData(() => ({ ...step, id: step.id }))}>Editer</Button>
                                        <Button type="button" variant="danger" className="px-3 py-2 text-xs" onClick={() => { if (confirm('Supprimer cette etape ?')) router.delete(step.delete_url, { preserveScroll: true }); }}>Supprimer</Button>
                                    </div>
                                </div>
                            </article>
                        ))}
                    </div>
                </AdminEditorPanel>
            </div>
        </HelpCenterLayout>
    );
}
