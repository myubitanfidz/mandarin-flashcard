<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Daftar Hafal - Hanyu Shuiping Kaoshi</title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            @property --hole-size {
                syntax: '<percentage>';
                inherits: false;
                initial-value: 0%;
            }

            @property --fill-size {
                syntax: '<percentage>';
                inherits: false;
                initial-value: 150%;
            }

            /* Animasi Iris Cover (Menutupi Layar saat tombol Kembali dipencet) */
            @keyframes irisCover {
                0% { --fill-size: 0%; --hole-size: 0%; }
                100% { --fill-size: 150%; --hole-size: 0%; }
            }

            /* Animasi Iris Uncover diperlama jadi 0.85s (Pembukaan dari Tengah) */
            @keyframes irisUncover {
                0% { --fill-size: 150%; --hole-size: 0%; }
                100% { --fill-size: 150%; --hole-size: 150%; }
            }

            .animate-iris-cover {
                animation: irisCover 0.85s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                -webkit-mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
                mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
            }

            .animate-iris-uncover {
                animation: irisUncover 0.85s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                -webkit-mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
                mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
            }
        </style>
    </head>
    <body class="bg-slate-100 text-gray-900 font-sans min-h-screen p-4 md:p-6 overflow-y-auto" x-data="masteredApp()">

        <!-- EFEK LAYER ANIMASI TRANSISI -->
        <div x-show="isTransitioning" 
             :class="animMode === 'uncover' ? 'animate-iris-uncover' : 'animate-iris-cover bg-gradient-to-br from-red-600 via-red-700 to-amber-500'"
             class="fixed inset-0 pointer-events-none z-50 bg-gradient-to-br from-red-600 via-red-700 to-amber-500">
        </div>

        <div class="max-w-6xl w-full mx-auto">
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 mb-6 flex flex-col gap-4">
                
                <div class="flex items-center justify-between">
                    <button @click="goBack('/')" 
                            class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        <span>⬅️ Kembali</span>
                    </button>

                    <div class="text-center">
                        <h1 class="text-xl md:text-2xl font-black tracking-wider text-red-600 uppercase">Hanyu Shuiping Kaoshi</h1>
                        <p class="text-xs text-gray-400 font-medium tracking-wide">汉语水平考试 — Seluruh Daftar Kosakata HSK</p>
                    </div>

                    <div class="w-20"></div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                    <div class="flex flex-wrap items-center justify-center gap-1.5">
                        <template x-for="lvl in [1, 2, 3, 4, 5, 6, '7-9']" :key="lvl">
                            <button @click="selectedHskFilter = lvl; loadVocabByLevel()"
                                    :class="selectedHskFilter === lvl ? 'bg-red-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    class="px-3 py-1.5 text-xs font-extrabold rounded-xl transition cursor-pointer"
                                    x-text="'HSK ' + lvl">
                            </button>
                        </template>
                    </div>

                    <div class="w-full md:w-72">
                        <input type="text" 
                               x-model="searchQuery" 
                               @input.debounce.300ms="filterVocabs()"
                               placeholder="Cari pinyin, arti, atau hanzi..." 
                               class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-red-500 transition">
                    </div>
                </div>

            </div>

            <!-- List Kosakata Grid 2 Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pb-12">
                <template x-for="(item, index) in filteredVocabs" :key="item.id">
                    <div :class="item.is_mastered ? 'bg-white border-slate-200' : 'bg-white/70 border-slate-200 opacity-50 blur-[0.4px] grayscale hover:opacity-100 hover:blur-none hover:grayscale-0 transition-all duration-300'"
                         class="rounded-2xl p-4 border shadow-sm flex items-center justify-between gap-4 transition">
                        
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold text-gray-400 w-6 text-right" x-text="(index + 1) + '.'"></span>

                            <div class="flex items-baseline gap-3">
                                <span class="text-2xl font-black text-gray-900" x-text="item.hanzi"></span>
                                
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-amber-600" x-text="item.pinyin"></span>
                                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200" x-text="item.type"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-0.5" x-text="item.meaning"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button class="p-2 bg-slate-50 hover:bg-amber-100 text-slate-500 hover:text-amber-700 rounded-xl border border-slate-200 text-xs transition cursor-pointer" title="Urutan Goresan">✍️ Goresan</button>
                            <button class="p-2 bg-slate-50 hover:bg-red-100 text-slate-500 hover:text-red-700 rounded-xl border border-slate-200 text-xs transition cursor-pointer" title="Audio Suara">🔊 Audio</button>
                            <button @click="toggleMastered(item.id, !item.is_mastered)" 
                                    :class="item.is_mastered ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="w-8 h-8 rounded-xl border border-slate-200 font-extrabold text-sm flex items-center justify-center transition cursor-pointer shadow-sm">
                                <span x-text="item.is_mastered ? '✓' : '+'"></span>
                            </button>
                        </div>

                    </div>
                </template>
            </div>

        </div>

        <script>
            function masteredApp() {
                return {
                    isTransitioning: true,
                    animMode: 'uncover', // 'uncover' saat masuk, 'cover' saat keluar
                    allVocabs: [],
                    filteredVocabs: [],
                    selectedHskFilter: 1,
                    searchQuery: '',

                    async init() {
                        await this.loadVocabByLevel();
                        // Setelah data siap, bukakan layar dari tengah (durasi 0.85s)
                        this.animMode = 'uncover';
                        setTimeout(() => {
                            this.isTransitioning = false;
                        }, 850);
                    },

                    async loadVocabByLevel() {
                        try {
                            const response = await fetch(`/api/flashcards/level/${this.selectedHskFilter}`);
                            const json = await response.json();
                            if (json.success) {
                                this.allVocabs = json.data;
                                this.filterVocabs();
                            }
                        } catch (error) {
                            console.error('Gagal memuat kosakata:', error);
                        }
                    },

                    filterVocabs() {
                        let result = this.allVocabs;

                        if (this.searchQuery.trim() !== '') {
                            const q = this.searchQuery.toLowerCase();
                            result = result.filter(item => 
                                item.hanzi.toLowerCase().includes(q) ||
                                item.pinyin.toLowerCase().includes(q) ||
                                item.meaning.toLowerCase().includes(q)
                            );
                        }

                        this.filteredVocabs = result;
                    },

                    async toggleMastered(id, newStatus) {
                        try {
                            await fetch(`/api/flashcards/${id}/toggle-mastered`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ is_mastered: newStatus })
                            });

                            const vocab = this.allVocabs.find(v => v.id === id);
                            if (vocab) {
                                vocab.is_mastered = newStatus;
                            }
                            this.filterVocabs();
                        } catch (error) {
                            console.error('Gagal memperbarui status:', error);
                        }
                    },

                    goBack(url) {
                        this.animMode = 'cover';
                        this.isTransitioning = true;
                        // Tunggu animasi penutupan 0.85s baru pindah halaman
                        setTimeout(() => {
                            window.location.href = url;
                        }, 850);
                    }
                }
            }
        </script>
    </body>
</html>