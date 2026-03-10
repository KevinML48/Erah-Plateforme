import React from 'react';
export default function AdminEditorPanel({ title, subtitle, children, actions }) {
    return (
        <section className="rounded-[1.75rem] border border-ui-border/12 bg-ui-surface/92 p-5">
            <div className="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.16em] text-ui-muted">Gestion</p>
                    <h3 className="mt-2 text-xl font-semibold text-white">{title}</h3>
                    {subtitle ? <p className="mt-2 max-w-3xl text-sm leading-7 text-ui-muted">{subtitle}</p> : null}
                </div>
                {actions}
            </div>

            <div className="mt-5">{children}</div>
        </section>
    );
}
