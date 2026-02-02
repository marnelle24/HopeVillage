@php
    $modelName = $attributes->get('wire:model', 'body');
@endphp
<div
    x-data="setupEditor($wire.entangle('{{ $modelName }}').live)"
    x-init="setTimeout(() => { const el = $refs.editor; if (el) init(el); }, 0)"
    wire:ignore
    {{ $attributes->except('wire:model')->merge(['class' => 'rounded-md border border-gray-300 bg-white shadow-sm overflow-hidden']) }}
>
    <div class="flex flex-wrap items-center gap-0.5 border-b border-gray-200 bg-gray-50 px-2 py-1.5" role="toolbar">
            <button type="button" @click="toggleBold()" :class="isActive('bold') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition" title="Bold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z"/></svg>
            </button>
            <button type="button" @click="toggleItalic()" :class="isActive('italic') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition" title="Italic">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z"/></svg>
            </button>
            <button type="button" @click="toggleStrike()" :class="isActive('strike') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition" title="Strikethrough">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 12h12M7 5h10a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2zM7 19h10a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2z"/></svg>
            </button>
            <button type="button" @click="toggleCode()" :class="isActive('code') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition font-mono text-sm" title="Inline code">&lt;/&gt;</button>
            <span class="mx-1 w-px self-stretch bg-gray-300" aria-hidden="true"></span>
            <button type="button" @click="setParagraph()" :class="isActive('paragraph') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded px-2 py-1.5 text-xs font-medium transition" title="Paragraph">P</button>
            <button type="button" @click="toggleHeading(1)" :class="isActive('heading', { level: 1 }) ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded px-2 py-1.5 text-xs font-bold transition" title="Heading 1">H1</button>
            <button type="button" @click="toggleHeading(2)" :class="isActive('heading', { level: 2 }) ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded px-2 py-1.5 text-xs font-bold transition" title="Heading 2">H2</button>
            <button type="button" @click="toggleHeading(3)" :class="isActive('heading', { level: 3 }) ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded px-2 py-1.5 text-xs font-bold transition" title="Heading 3">H3</button>
            <span class="mx-1 w-px self-stretch bg-gray-300" aria-hidden="true"></span>
            <button type="button" @click="toggleBulletList()" :class="isActive('bulletList') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition" title="Bullet list">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
            <button type="button" @click="toggleOrderedList()" :class="isActive('orderedList') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition text-xs font-medium" title="Numbered list">1.</button>
            <button type="button" @click="toggleBlockquote()" :class="isActive('blockquote') ? 'bg-gray-300 text-gray-900' : 'text-gray-600 hover:bg-gray-200'" class="rounded p-1.5 transition" title="Quote">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
            </button>
            <button type="button" @click="setHorizontalRule()" class="rounded p-1.5 text-gray-600 transition hover:bg-gray-200" title="Horizontal rule">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
    </div>
    <div x-ref="editor" class="min-h-[200px] w-full text-black"></div>
</div>
<style>
    .tiptap {
        outline: none;
        color: #000;
    }
    .tiptap p,
    .tiptap li,
    .tiptap blockquote {
        color: #000;
    }
    .tiptap p { margin: 0.25em 0; }
    .tiptap h1, .tiptap h2, .tiptap h3 {
        margin: 0.5em 0 0.25em;
        font-weight: 600;
        color: #000;
    }
    .tiptap ul, .tiptap ol { padding-left: 1.5rem; margin: 0.25em 0; }
    .tiptap code { color: #000; }
</style>
