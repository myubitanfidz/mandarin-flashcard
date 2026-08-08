<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mandarin Flashcard & Daftar Hafal</title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            .hexagon {
                clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            }

            @property --hole-size {
                syntax: '<percentage>';
                inherits: false;
                initial-value: 0%;
            }

            @property --fill-size {
                syntax: '<percentage>';
                inherits: false;
                initial-value: 0%;
            }

            @keyframes irisWipe {
                0% { --fill-size: 0%; --hole-size: 0%; }
                40% { --fill-size: 150%; --hole-size: 0%; }
                100% { --fill-size: 150%; --hole-size: 150%; }
            }

            .animate-iris-wipe {
                animation: irisWipe 1.1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                -webkit-mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
                mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
            }
        </style>
    </head>
    <body class="bg-slate-100 text-gray-900 font-sans min-h-screen flex items-center justify-center p-4 overflow-x-hidden" x-data="flashcardApp()">
        
        <!-- ================================================================= -->
        <!-- LAYER 1: HONEYCOMB HEXAGON MAIN MENU (HOME)                       -->
        <!-- ================================================================= -->
        <div x-show="currentView === 'home'" 
             x-transition:leave="transition ease-in duration-300 transform opacity-0 scale-90"
             class="relative w-[580px] h-[580px] flex items-center justify-center">

            <!-- TOMBOL PUSAT: START! (Review Hafalan) -->
            <div class="absolute z-20 w-[220px] h-[195px] flex items-center justify-center">
                <div class="absolute inset-0 bg-amber-400 hexagon"></div>
                <button @click="startMasteredSession()" 
                        class="absolute inset-[4px] bg-gradient-to-br from-red-600 via-red-600 to-amber-600 text-white font-black text-2xl md:text-3xl hexagon shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 flex flex-col items-center justify-center gap-1 group cursor-pointer">
                    <span class="tracking-wider group-hover:animate-pulse">START!</span>
                    <span class="text-[9px] md:text-[10px] font-normal text-amber-200">Review Hafalan (复习)</span>
                </button>
            </div>

            <!-- 1. SISI ATAS (Daftar Hafal) -> BERPINDAH KE HALAMAN PENUH DAFTAR HAFAL -->
            <button @click="goToMasteredPage()" style="transform: translateY(-182px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 active:scale-95 transition flex flex-col items-center justify-center p-2 cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">已学会</span>
                <span class="text-xs font-medium">Daftar Hafal</span>
            </button>

            <!-- 2. SISI KANAN ATAS (Tambah Kosakata) -->
            <button style="transform: translate(172px, -91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">+ 词汇</span>
                <span class="text-[10px] leading-tight font-medium">Tambah / Cari</span>
            </button>

            <!-- 3. SISI KANAN BAWAH (Pengecek Nada) -->
            <button style="transform: translate(172px, 91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">🎙️ 声调</span>
                <span class="text-[10px] leading-tight font-medium">Cek Nada</span>
            </button>

            <!-- 4. SISI BAWAH (Kelola HSK) -->
            <button @click="showHskModal = true" style="transform: translateY(182px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 active:scale-95 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">📚 HSK</span>
                <span class="text-[10px] leading-tight font-medium">Kelola File</span>
            </button>

            <!-- 5. SISI KIRI BAWAH (Canvas Penulisan) -->
            <button style="transform: translate(-172px, 91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">✍️ 笔画</span>
                <span class="text-[10px] leading-tight font-medium">Tulis Hanzi</span>
            </button>

            <!-- 6. SISI KIRI ATAS (Grammar AI) -->
            <button style="transform: translate(-172px, -91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">🤖 语法</span>
                <span class="text-[10px] leading-tight font-medium">Grammar AI</span>
            </button>

        </div>

        <!-- ================================================================= -->
        <!-- LAYER 2: EFEK TRANSISI IRIS WIPE                                  -->
        <!-- ================================================================= -->
        <div x-show="isAnimating" 
             class="fixed inset-0 pointer-events-none z-20 bg-gradient-to-br from-red-600 via-red-700 to-amber-500 animate-iris-wipe">
        </div>

        <!-- ================================================================= -->
        <!-- LAYER 3: FLASHCARD INTERACTIVE VIEW                              -->
        <!-- ================================================================= -->
        <div x-show="currentView === 'flashcard'" 
             x-transition:enter="transition ease-out duration-500 delay-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="z-30 w-full max-w-md bg-white rounded-3xl shadow-2xl border-4 border-amber-400 p-8 flex flex-col items-center text-center relative">

            <button @click="goHome()" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold text-xl cursor-pointer">✕</button>

            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full mb-6">Mode Review Hafalan</span>

            <template x-if="isLoading">
                <div class="py-12 flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-500 font-medium">Memuat Kosakata Hafalan...</p>
                </div>
            </template>

            <template x-if="!isLoading && currentVocab.id">
                <div class="w-full flex flex-col items-center">
                    <h1 class="text-7xl font-extrabold text-gray-900 tracking-wide mb-4" x-text="currentVocab.hanzi"></h1>
                    <p class="text-2xl font-semibold text-amber-600 mb-2" x-text="currentVocab.pinyin"></p>
                    <span class="text-xs bg-slate-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200 mb-6" x-text="currentVocab.type"></span>

                    <div class="w-full bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-6">
                        <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Arti / Terjemahan</p>
                        <p class="text-xl font-bold text-gray-800" x-text="currentVocab.meaning"></p>
                    </div>

                    <div class="flex gap-4 w-full">
                        <button @click="toggleMastered(currentVocab.id, false)" 
                                :disabled="isSubmitting"
                                class="flex-1 py-3 bg-slate-200 hover:bg-slate-300 active:scale-95 text-gray-800 font-bold rounded-xl transition cursor-pointer disabled:opacity-50 text-xs">
                            Batalkan Hafal ❌
                        </button>
                        <button @click="nextMasteredVocab()" 
                                :disabled="isSubmitting"
                                class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold rounded-xl shadow-lg transition cursor-pointer disabled:opacity-50 text-xs">
                            Lanjut Review ➡️
                        </button>
                    </div>
                </div>
            </template>

        </div>

        <!-- ================================================================= -->
        <!-- LAYER 4: HALAMAN PENUH DAFTAR HAFAL (FULL SCREEN VIEW)             -->
        <!-- ================================================================= -->
        <div x-show="currentView === 'mastered_page'" 
             x-transition:enter="transition ease-out duration-400 delay-200"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300 transform opacity-0 translate-y-8"
             class="fixed inset-0 z-30 bg-slate-100 flex flex-col p-4 md:p-6 overflow-y-auto">

            <!-- Header Utama Full Screen -->
            <div class="max-w-6xl w-full mx-auto bg-white rounded-3xl shadow-sm border border-slate-200 p-6 mb-6 flex flex-col gap-4">
                
                <div class="flex items-center justify-between">
                    <!-- Tombol Kembali dengan Animasi -->
                    <button @click="goHome()" 
                            class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
                        <span>⬅️ Kembali</span>
                    </button>

                    <!-- Judul Hanyu Shuiping Kaoshi -->
                    <div class="text-center">
                        <h1 class="text-xl md:text-2xl font-black tracking-wider text-red-600 uppercase">Hanyu Shuiping Kaoshi</h1>
                        <p class="text-xs text-gray-400 font-medium tracking-wide">汉语水平考试 — Seluruh Daftar Kosakata HSK</p>
                    </div>

                    <div class="w-20"></div>
                </div>

                <!-- Bagian Bawah Header: Tombol HSK 1-9 & Search Bar di Pojok Kanan -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                    
                    <!-- List Tombol HSK 1 - 9 -->
                    <div class="flex flex-wrap items-center justify-center gap-1.5">
                        <template x-for="lvl in [1, 2, 3, 4, 5, 6, '7-9']" :key="lvl">
                            <button @click="selectedHskFilter = lvl; loadVocabByLevel()"
                                    :class="selectedHskFilter === lvl ? 'bg-red-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    class="px-3 py-1.5 text-xs font-extrabold rounded-xl transition cursor-pointer"
                                    x-text="'HSK ' + lvl">
                            </button>
                        </template>
                    </div>

                    <!-- Search Bar (Pinyin / Arti / Hanzi) -->
                    <div class="w-full md:w-72">
                        <input type="text" 
                               x-model="searchQuery" 
                               @input.debounce.300ms="filterVocabs()"
                               placeholder="Cari pinyin, arti, atau hanzi..." 
                               class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-red-500 transition">
                    </div>

                </div>

            </div>

            <!-- List Kosakata Bentukan Kiri-Kanan (Grid 2 Kolom) -->
            <div class="max-w-6xl w-full mx-auto grid grid-cols-1 md:grid-cols-2 gap-3 pb-12">
                <template x-for="(item, index) in filteredVocabs" :key="item.id">
                    <div :class="item.is_mastered ? 'bg-white border-slate-200' : 'bg-white/70 border-slate-200 opacity-50 blur-[0.4px] grayscale hover:opacity-100 hover:blur-none hover:grayscale-0 transition-all duration-300'"
                         class="rounded-2xl p-4 border shadow-sm flex items-center justify-between gap-4 transition">
                        
                        <!-- Kiri: Nomor urut, Hanzi, Pinyin, Jenis, Arti -->
                        <div class="flex items-center gap-4">
                            <!-- 1. Nomor urut (tidak nempel ke huruf) -->
                            <span class="text-xs font-bold text-gray-400 w-6 text-right" x-text="(index + 1) + '.'"></span>

                            <div class="flex items-baseline gap-3">
                                <!-- 2. Hanzi -->
                                <span class="text-2xl font-black text-gray-900" x-text="item.hanzi"></span>
                                
                                <div>
                                    <div class="flex items-center gap-2">
                                        <!-- Pinyin -->
                                        <span class="text-xs font-semibold text-amber-600" x-text="item.pinyin"></span>
                                        <!-- 3. Jenis kata -->
                                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200" x-text="item.type"></span>
                                    </div>
                                    <!-- 4. Arti -->
                                    <p class="text-xs text-gray-600 mt-0.5" x-text="item.meaning"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Kanan: Tombol Animasi Goresan, Suara, dan Tombol Tambah (+) -->
                        <div class="flex items-center gap-2 shrink-0">
                            <!-- Tombol Goresan (Placeholder) -->
                            <button class="p-2 bg-slate-50 hover:bg-amber-100 text-slate-500 hover:text-amber-700 rounded-xl border border-slate-200 text-xs transition cursor-pointer" title="Urutan Goresan (Segera)">
                                ✍️ Goresan
                            </button>
                            <!-- Tombol Suara (Placeholder) -->
                            <button class="p-2 bg-slate-50 hover:bg-red-100 text-slate-500 hover:text-red-700 rounded-xl border border-slate-200 text-xs transition cursor-pointer" title="Audio Suara (Segera)">
                                🔊 Audio
                            </button>
                            <!-- Tombol Tambah (+) untuk Toggle Hafal -->
                            <button @click="toggleMastered(item.id, !item.is_mastered)" 
                                    :class="item.is_mastered ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="w-8 h-8 rounded-xl border border-slate-200 font-extrabold text-sm flex items-center justify-center transition cursor-pointer shadow-sm"
                                    :title="item.is_mastered ? 'Sudah Dihafal' : 'Tandai Hafal'">
                                <span x-text="item.is_mastered ? '✓' : '+'"></span>
                            </button>
                        </div>

                    </div>
                </template>

                <!-- State Kosong -->
                <template x-if="filteredVocabs.length === 0">
                    <div class="col-span-full py-16 text-center text-gray-400 text-xs bg-white rounded-2xl border border-slate-200">
                        Tidak ada kosakata yang cocok dengan pencarian tersebut.
                    </div>
                </template>
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- LAYER 5: MODAL KELOLA FILE HSK                                   -->
        <!-- ================================================================= -->
        <div x-show="showHskModal" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-6 relative flex flex-col gap-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-lg font-bold">Kelola File HSK</h2>
                    <button @click="showHskModal = false" class="text-gray-400 hover:text-red-600 font-bold cursor-pointer">✕</button>
                </div>
                <p class="text-xs text-gray-600">Fitur unduh dataset resmi HSK 1 - 9 tersedia melalui open source repository.</p>
                <a href="/downloads/hsk/New-HSK-Vocabulary-Level-1.pdf" target="_blank" download class="py-2 px-4 bg-slate-800 text-white text-xs font-bold rounded-xl text-center">📥 Download PDF HSK 1</a>
            </div>
        </div>

        <script>
            function flashcardApp() {
                return {
                    currentView: 'home', // 'home', 'flashcard', 'mastered_page'
                    isAnimating: false,
                    isLoading: false,
                    isSubmitting: false,
                    showHskModal: false,
                    
                    allVocabs: [],
                    filteredVocabs: [],
                    selectedHskFilter: 1,
                    searchQuery: '',

                    currentVocab: { id: null, hanzi: '', pinyin: '', type: '', meaning: '', hsk_level: 1 },

                    async fetchMasteredVocab() {
                        this.isLoading = true;
                        try {
                            const response = await fetch('/api/flashcards/mastered/random');
                            const json = await response.json();
                            if (json.success && json.data) {
                                this.currentVocab = json.data;
                            } else {
                                alert('Belum ada kosakata yang dihafal! Silakan tandai beberapa kosakata terlebih dahulu.');
                                this.goHome();
                            }
                        } catch (error) {
                            console.error('Gagal mengambil data hafal:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async goToMasteredPage() {
                        this.isAnimating = true;
                        await this.loadVocabByLevel();

                        setTimeout(() => {
                            this.currentView = 'mastered_page';
                        }, 440);

                        setTimeout(() => {
                            this.isAnimating = false;
                        }, 1050);
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
                            console.error('Gagal memuat kosakata berdasarkan level:', error);
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

                            // Update state lokal langsung agar UI reaktif tanpa reload
                            const vocab = this.allVocabs.find(v => v.id === id);
                            if (vocab) {
                                vocab.is_mastered = newStatus;
                            }
                            this.filterVocabs();

                            if (this.currentView === 'flashcard') {
                                await this.fetchMasteredVocab();
                            }
                        } catch (error) {
                            console.error('Gagal memperbarui status:', error);
                        }
                    },

                    async nextMasteredVocab() {
                        await this.fetchMasteredVocab();
                    },

                    startMasteredSession() {
                        this.isAnimating = true;
                        this.fetchMasteredVocab().then(() => {
                            if (!this.currentVocab.id) {
                                this.isAnimating = false;
                                return;
                            }
                            setTimeout(() => { 
                                this.currentView = 'flashcard'; 
                            }, 440);
                            setTimeout(() => {
                                this.isAnimating = false;
                            }, 1050);
                        });
                    },

                    goHome() {
                        this.isAnimating = true;
                        setTimeout(() => {
                            this.currentView = 'home';
                        }, 440);
                        setTimeout(() => {
                            this.isAnimating = false;
                        }, 1050);
                    }
                }
            }
        </script>
    </body>
</html>