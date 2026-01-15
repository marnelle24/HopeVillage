<div>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold md:text-xl text-2xl text-gray-800 leading-tight">
                {{ __('API Documentation') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                <!-- File Selector -->
                <div class="mb-6">
                    <label for="markdown-file" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Documentation File
                    </label>
                    <div class="flex gap-3">
                        <select 
                            id="markdown-file"
                            wire:model.live.debounce.500ms="selectedFile"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700"
                        >
                            <option value="">-- Select a file --</option>
                            @foreach($availableFiles as $file)
                                <option value="{{ $file['name'] }}">{{ $file['name'] }}</option>
                            @endforeach
                        </select>
                        <button 
                            type="button"
                            wire:click="readFile"
                            wire:loading.attr="disabled"
                            @disabled(empty($selectedFile))
                            class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-gray-100 font-medium rounded-lg focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="readFile">Open File</span>
                            <span wire:loading wire:target="readFile">Opening...</span>
                        </button>
                    </div>
                    @if(session()->has('error'))
                        <p class="mt-2 text-sm text-red-600">{{ session('error') }}</p>
                    @endif
                </div>

                <!-- Markdown Content Display -->
                @if($selectedFile && !empty($markdownContent))
                    <div 
                        id="api-doc-markdown-container"
                        class="markdown-body"
                        wire:ignore
                        data-content="{{ base64_encode($markdownContent) }}"
                        style="background-color: #423f3f; overflow-x: auto; max-height: 600px;"
                    >
                        <!-- Content will be rendered here -->
                    </div>
                @elseif($selectedFile && empty($markdownContent))
                    <div class="text-center text-gray-400 py-12 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Click the "Read" button to view the selected documentation file.</p>
                    </div>
                @else
                    <div class="text-center text-gray-400 py-12 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Please select a documentation file and click "Read" to view its content.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Markdown Viewer Libraries -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css@5.2.0/github-markdown.min.css">
        <script src="https://cdn.jsdelivr.net/npm/markdown-it@14.0.0/dist/markdown-it.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/lib/highlight.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github.min.css">
        
        <script>
            function renderApiDocMarkdown() {
                const container = document.getElementById('api-doc-markdown-container');
                if (!container) return;
                
                const encoded = container.getAttribute('data-content');
                if (!encoded) {
                    container.innerHTML = '';
                    return;
                }
                
                try {
                    const content = atob(encoded);
                    if (content && typeof markdownit !== 'undefined') {
                        // Initialize markdown-it with options
                        const md = window.markdownit({
                            html: true,
                            linkify: true,
                            typographer: true,
                            highlight: function (str, lang) {
                                // Check if highlight.js is available
                                if (typeof hljs !== 'undefined' && lang && hljs.getLanguage(lang)) {
                                    try {
                                        return '<pre class="hljs"><code>' +
                                            hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
                                            '</code></pre>';
                                    } catch (__) {
                                        // Fallback if highlighting fails
                                        return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>';
                                    }
                                }
                                // Fallback if hljs is not available or language not supported
                                return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>';
                            }
                        });
                        
                        // Render markdown to HTML
                        const html = md.render(content);
                        container.innerHTML = html;
                        
                        // Highlight code blocks after rendering (if hljs is available)
                        if (typeof hljs !== 'undefined' && typeof hljs.highlightElement === 'function') {
                            container.querySelectorAll('pre code').forEach((block) => {
                                try {
                                    hljs.highlightElement(block);
                                } catch (e) {
                                    // Ignore highlighting errors
                                    console.warn('Failed to highlight code block:', e);
                                }
                            });
                        }
                    } else if (content) {
                        container.innerHTML = '<p class="text-red-500">Loading markdown viewer...</p>';
                        setTimeout(renderApiDocMarkdown, 100);
                    } else {
                        container.innerHTML = '';
                    }
                } catch (e) {
                    container.innerHTML = '<p class="text-red-500">Error rendering markdown: ' + e.message + '</p>';
                }
            }
            
            // Wait for all libraries to load
            function initMarkdownViewer() {
                if (typeof markdownit === 'undefined') {
                    console.error('Markdown-it failed to load');
                    return;
                }
                
                // Wait a bit for highlight.js to load if it's not ready
                if (typeof hljs === 'undefined') {
                    setTimeout(initMarkdownViewer, 100);
                    return;
                }
                
                renderApiDocMarkdown();
            }
            
            // Initial render
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMarkdownViewer);
            } else {
                initMarkdownViewer();
            }
            
            // Listen for Livewire updates
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', () => {
                    setTimeout(renderApiDocMarkdown, 100);
                });
            });
        </script>
    @endpush

    <style>
        .markdown-body {
            box-sizing: border-box;
            min-width: 200px;
            max-width: 100%;
            margin: 0 auto;
            padding: 45px;
            font-size: 16px;
            line-height: 1.6;
            word-wrap: break-word;
        }
        
        .markdown-body h1,
        .markdown-body h2,
        .markdown-body h3,
        .markdown-body h4,
        .markdown-body h5,
        .markdown-body h6 {
            margin-top: 24px;
            margin-bottom: 16px;
            font-weight: 600;
            line-height: 1.25;
            color: #ff9809;
        }
        
        .markdown-body h1 {
            font-size: 2em;
            border-bottom: 1px solid #d0d7de;
            padding-bottom: 0.3em;
        }
        
        .markdown-body h2 {
            font-size: 1.5em;
            border-bottom: 1px solid #d0d7de;
            padding-bottom: 0.3em;
        }
        
        .markdown-body a {
            color: #f97316;
            text-decoration: none;
        }
        
        .markdown-body a:hover {
            text-decoration: underline;
        }
        
        .markdown-body code {
            padding: 0.2em 0.4em;
            margin: 0;
            font-size: 85%;
            background-color: rgba(175, 184, 193, 0.2);
            border-radius: 6px;
            color: #f97316;
        }
        
        .markdown-body pre {
            padding: 16px;
            overflow: auto;
            font-size: 85%;
            line-height: 1.45;
            background-color: #f6f8fa;
            border-radius: 6px;
        }
        
        .markdown-body pre code {
            display: inline;
            max-width: auto;
            padding: 0;
            margin: 0;
            overflow: visible;
            line-height: inherit;
            word-wrap: normal;
            background-color: transparent;
            border: 0;
            color: inherit;
        }
        
        .markdown-body table {
            border-spacing: 0;
            border-collapse: collapse;
            display: block;
            width: max-content;
            max-width: 100%;
            overflow: auto;
        }
        
        .markdown-body table th,
        .markdown-body table td {
            padding: 6px 13px;
            border: 1px solid #d0d7de;
        }
        
        .markdown-body table th {
            font-weight: 600;
            background-color: #f6f8fa;
        }
        
        .markdown-body table tr {
            background-color: #ffffff;
            border-top: 1px solid #cbd5e1;
        }
        
        .markdown-body table tr:nth-child(2n) {
            background-color: #f6f8fa;
        }
        
        .markdown-body blockquote {
            padding: 0 1em;
            color: #656d76;
            border-left: 0.25em solid #d0d7de;
            margin: 0;
        }
        
        .markdown-body ul,
        .markdown-body ol {
            padding-left: 2em;
        }
        
        .markdown-body li {
            margin: 0.25em 0;
        }
        
        .markdown-body strong {
            font-weight: 600;
            color: #c0c0c0 !important;
        }
        
        .markdown-body pre {
            font-weight: 600;
            color: #c0c0c0 !important;
        }

        .markdown-body table > tr > th {
            font-weight: 600;
            color: #c0c0c0 !important;
        }
        
        .markdown-body em {
            font-style: italic;
            color: #24292f;
        }
        /* chagne the md body background to light color ( gray) */
        .markdown-body {
    </style>
</div>
