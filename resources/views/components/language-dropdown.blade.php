<div x-data="{ 
    currentLang: '{{ request()->get('lang', 'en') }}',
    languages: [
        { code: 'en', name: 'English', flag: '🇬🇧' },
        { code: 'bang', name: 'Bengali', flag: '🇧🇩' },
        { code: 'ta', name: 'Tamil', flag: '🇮🇳' },
        { code: 'zh', name: 'Chinese', flag: '🇨🇳' }
    ],

    open: false,
    init() {
        // Set current language from URL parameter
        var urlParams = new URLSearchParams(window.location.search);
        var langParam = urlParams.get('lang');
        if (langParam) {
            this.currentLang = langParam;
        } else {
            this.currentLang = 'en';
        }
    },
    getCurrentLanguage() {
        return this.languages.find(lang => lang.code === this.currentLang) || this.languages[0];
    },
    changeLanguage(langCode) {
        this.currentLang = langCode;
        var url = new URL(window.location.href);
        
        // Remove lang parameter if English (default)
        if (langCode === 'en') {
            url.searchParams.delete('lang');
        } else {
            url.searchParams.set('lang', langCode);
        }
        
        // Reload page with new language parameter
        window.location.href = url.toString();
    }
}"
@click.away="open = false"
class="relative inline-block text-left">
    <button 
        @click.stop="open = !open"
        type="button"
        class="inline-flex gap-1 items-center cursor-pointer justify-center px-2 w-full text-xs font-medium text-gray-700 hover:bg-orange-50 duration-300 transition-all"
    >
        <span class="text-xl" x-text="getCurrentLanguage().flag"></span>
        <span class="text-gray-700 text-sm" x-text="getCurrentLanguage().name"></span>
        <svg class="-mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div 
        x-show="open"
        @click.stop
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        x-cloak
        class="absolute right-0 mt-2 min-w-52 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        role="menu"
        aria-orientation="vertical"
    >
        <div class="py-1" role="none">
            <template x-for="language in languages" :key="language.code">
                <button
                    @click="changeLanguage(language.code)"
                    :class="currentLang === language.code ? 'bg-orange-50 text-orange-600' : 'text-gray-700'"
                    class="w-full cursor-pointer text-left px-3 py-1 text-sm hover:text-orange-600 flex items-center"
                    role="menuitem"
                >
                    <span class="mr-3 text-xl" x-text="language.flag"></span>
                    <span x-text="language.name"></span>
                    <svg x-show="currentLang === language.code" class="ml-auto h-5 w-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </template>
        </div>
    </div>
</div>

