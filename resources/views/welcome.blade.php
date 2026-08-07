<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mandarin Flashcard</title>

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            /* Hexagon Flat-Topped */
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

            /* ANIMASI IRIS WIPE SMOOTH */
            @keyframes irisWipe {
                0% {
                    --fill-size: 0%;
                    --hole-size: 0%;
                }
                40% {
                    --fill-size: 150%;
                    --hole-size: 0%;
                }
                100% {
                    --fill-size: 150%;
                    --hole-size: 150%;
                }
            }

            .animate-iris-wipe {
                animation: irisWipe 1.1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                -webkit-mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
                mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
            }
        </style>
    </head>
    <body class="bg-slate-100 text-gray-900 font-sans min-h-screen flex items-center justify-center p-4 overflow-hidden" x-data="flashcardApp()">
        
        <!-- ================================================================= -->
        <!-- LAYER 1: HONEYCOMB HEXAGON MAIN MENU                              -->
        <!-- ================================================================= -->
        <div x-show="!isStarted" class="relative w-[580px] h-[580px] flex items-center justify-center">

            <!-- TOMBOL PUSAT: START! -->
            <div class="absolute z-20 w-[220px] h-[195px] flex items-center justify-center">
                <div class="absolute inset-0 bg-amber-400 hexagon"></div>
                <button @click="startSession()" 
                        class="absolute inset-[4px] bg-gradient-to-br from-red-600 via-red-600 to-amber-600 text-white font-black text-2xl md:text-3xl hexagon shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 flex flex-col items-center justify-center gap-1 group cursor-pointer">
                    <span class="tracking-wider group-hover:animate-pulse">START!</span>
                    <span class="text-[10px] md:text-xs font-normal text-amber-200" x-text="'HSK Level ' + selectedLevel"></span>
                </button>
            </div>

            <!-- 1. SISI ATAS (Daftar Hafal) -->
            <button style="transform: translateY(-182px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 cursor-pointer">
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
        <div x-show="showCard" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-90 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="z-30 w-full max-w-md bg-white rounded-3xl shadow-2xl border-4 border-amber-400 p-8 flex flex-col items-center text-center relative">

            <!-- Tombol Kembali ke Dashboard -->
            <button @click="closeCard()" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold text-xl cursor-pointer">✕</button>

            <!-- Tag Level HSK -->
            <span class="px-3 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-full mb-6" x-text="'HSK Level ' + currentVocab.hsk_level"></span>

            <!-- State Loading -->
            <template x-if="isLoading">
                <div class="py-12 flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-500 font-medium">Loading Kosakata...</p>
                </div>
            </template>

            <!-- Display Kosakata -->
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
                        <button @click="handleMastery(false)" 
                                :disabled="isSubmitting"
                                class="flex-1 py-3 bg-slate-200 hover:bg-slate-300 active:scale-95 text-gray-800 font-bold rounded-xl transition cursor-pointer disabled:opacity-50">
                            Belum Hafal
                        </button>
                        <button @click="handleMastery(true)" 
                                :disabled="isSubmitting"
                                class="flex-1 py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold rounded-xl shadow-lg transition cursor-pointer disabled:opacity-50">
                            Sudah Hafal ✅
                        </button>
                    </div>
                </div>
            </template>

        </div>

        <!-- ================================================================= -->
        <!-- LAYER 4: MODAL KELOLA FILE HSK (LEVEL 1 - 9 LENGKAP)              -->
        <!-- ================================================================= -->
        <div x-show="showHskModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">

            <div class="bg-white w-full max-w-2xl max-h-[85vh] rounded-3xl shadow-2xl border-2 border-slate-100 p-6 md:p-8 relative flex flex-col gap-6">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl">📚</span>
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-900">Kelola Dataset HSK (1 - 9)</h2>
                            <p class="text-xs text-gray-500">Unduh PDF dari GitHub atau pilih level latihan Flashcard</p>
                        </div>
                    </div>
                    <button @click="showHskModal = false" class="text-gray-400 hover:text-red-600 font-bold text-xl cursor-pointer">✕</button>
                </div>

                <!-- Scrollable Grid HSK 1 - 9 -->
                <div class="overflow-y-auto pr-1 flex flex-col gap-4">
                    <template x-for="item in hskLevels" :key="item.level">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-amber-400 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center font-black text-sm shadow-sm" x-text="item.tag"></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base font-bold text-gray-800" x-text="item.title"></h3>
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full" x-text="item.count + ' Vocabs'"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5" x-text="item.desc"></p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <!-- Direct Download dari GitHub -->
                                <a :href="'https://raw.githubusercontent.com/username/repo/main/public/downloads/hsk/' + item.pdf" 
                                   target="_blank" 
                                   download
                                   class="py-2 px-3 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white text-xs font-bold rounded-xl text-center shadow transition flex items-center gap-1.5 cursor-pointer">
                                    <span>📥 PDF</span>
                                </a>

                                <button @click="selectLevel(item.level)" 
                                        :class="selectedLevel === item.level ? 'bg-amber-500 text-white' : 'bg-red-600 hover:bg-red-700 text-white'"
                                        class="py-2 px-3 active:scale-95 text-xs font-bold rounded-xl text-center shadow transition cursor-pointer">
                                    <span x-text="selectedLevel === item.level ? '✓ Aktif' : 'Pilih'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="text-center pt-2 border-t border-slate-100 shrink-0">
                    <p class="text-[11px] text-gray-400">Total 11.000 kosakata resmi New HSK 1 - 9 tersimpan di GitHub.</p>
                </div>

            </div>
        </div>

        <script>
            function flashcardApp() {
                return {
                    isStarted: false,
                    isAnimating: false,
                    showCard: false,
                    isLoading: false,
                    isSubmitting: false,
                    showHskModal: false,
                    selectedLevel: 1,

                    hskLevels: [
                        { level: 1, tag: 'L1', title: 'New HSK Level 1', count: '300', desc: 'Dasar Pemula', pdf: 'New-HSK-Vocabulary-Level-1.pdf' },
                        { level: 2, tag: 'L2', title: 'New HSK Level 2', count: '200', desc: 'Kalimat Sederhana', pdf: 'New-HSK-Vocabulary-Level-2.pdf' },
                        { level: 3, tag: 'L3', title: 'New HSK Level 3', count: '500', desc: 'Komunikasi Harian', pdf: 'New-HSK-Vocabulary-Level-3.pdf' },
                        { level: 4, tag: 'L4', title: 'New HSK Level 4', count: '1.000', desc: 'Diskusi Topik Luas', pdf: 'New-HSK-Vocabulary-Level-4.pdf' },
                        { level: 5, tag: 'L5', title: 'New HSK Level 5', count: '1.600', desc: 'Membaca Koran & Film', pdf: 'New-HSK-Vocabulary-Level-5.pdf' },
                        { level: 6, tag: 'L6', title: 'New HSK Level 6', count: '1.800', desc: 'Tingkat Mahir', pdf: 'New-HSK-Vocabulary-L6.pdf' },
                        { level: 7, tag: 'L7-9', title: 'New HSK Level 7 - 9', count: '5.600', desc: 'Tingkat Spesialis & Akademik', pdf: 'New-HSK-Vocabulary-Level-7-9.pdf' },
                    ],

                    currentVocab: {
                        id: null,
                        hanzi: '',
                        pinyin: '',
                        type: '',
                        meaning: '',
                        hsk_level: 1
                    },

                    async fetchRandomVocab() {
                        this.isLoading = true;
                        try {
                            const response = await fetch(`/api/flashcards/random?level=${this.selectedLevel}`);
                            const json = await response.json();
                            if (json.success && json.data) {
                                this.currentVocab = json.data;
                            }
                        } catch (error) {
                            console.error('Gagal mengambil data kosakata:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async handleMastery(isMastered) {
                        if (!this.currentVocab.id || this.isSubmitting) return;

                        this.isSubmitting = true;
                        try {
                            await fetch(`/api/flashcards/${this.currentVocab.id}/toggle-mastered`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ is_mastered: isMastered })
                            });

                            await this.fetchRandomVocab();
                        } catch (error) {
                            console.error('Gagal memperbarui status hafalan:', error);
                        } finally {
                            this.isSubmitting = false;
                        }
                    },

                    selectLevel(level) {
                        this.selectedLevel = level;
                        this.showHskModal = false;
                    },

                    startSession() {
                        this.isAnimating = true;
                        this.fetchRandomVocab();

                        setTimeout(() => {
                            this.isStarted = true;
                        }, 440);

                        setTimeout(() => {
                            this.isAnimating = false;
                            this.showCard = true;
                        }, 1050);
                    },

                    closeCard() {
                        this.showCard = false;
                        this.isStarted = false;
                    }
                }
            }
        </script>
    </body>
</html>