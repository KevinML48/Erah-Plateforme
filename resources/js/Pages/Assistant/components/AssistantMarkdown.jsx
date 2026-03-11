import React from 'react';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';

export default function AssistantMarkdown({ content }) {
    if (!content) {
        return null;
    }

    return (
        <div className="assistant-markdown text-sm leading-7 text-white/88">
            <ReactMarkdown
                remarkPlugins={[remarkGfm]}
                components={{
                    p: ({ children }) => <p className="mb-4 last:mb-0">{children}</p>,
                    ul: ({ children }) => <ul className="mb-4 list-disc space-y-2 pl-5 last:mb-0">{children}</ul>,
                    ol: ({ children }) => <ol className="mb-4 list-decimal space-y-2 pl-5 last:mb-0">{children}</ol>,
                    li: ({ children }) => <li className="text-white/82">{children}</li>,
                    strong: ({ children }) => <strong className="font-semibold text-white">{children}</strong>,
                    h1: ({ children }) => <h1 className="mb-3 text-xl font-semibold text-white">{children}</h1>,
                    h2: ({ children }) => <h2 className="mb-3 text-lg font-semibold text-white">{children}</h2>,
                    h3: ({ children }) => <h3 className="mb-2 text-base font-semibold text-white">{children}</h3>,
                    code: ({ inline, children }) => (
                        inline ? (
                            <code className="rounded-lg border border-white/10 bg-white/8 px-1.5 py-0.5 text-[0.92em] text-red-100">
                                {children}
                            </code>
                        ) : (
                            <code className="block overflow-x-auto rounded-2xl border border-white/10 bg-black/50 p-4 text-[13px] text-red-50">
                                {children}
                            </code>
                        )
                    ),
                    pre: ({ children }) => <pre className="mb-4 last:mb-0">{children}</pre>,
                    a: ({ href, children }) => (
                        <a
                            href={href}
                            className="font-medium text-red-200 underline decoration-red-400/40 underline-offset-4 transition hover:text-white"
                        >
                            {children}
                        </a>
                    ),
                }}
            >
                {content}
            </ReactMarkdown>
        </div>
    );
}
