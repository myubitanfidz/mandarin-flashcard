<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Mandarin Flashcard & Latihan Hafalan</title>

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

            <!-- TOMBOL PUSAT: START! (Latihan Test Hafalan) -->
            <div class="absolute z-20 w-[220px] h-[195px] flex items-center justify-center">
                <div class="absolute inset-0 bg-amber-400 hexagon"></div>
                <button @click="openDifficultyModal()" 
                        class="absolute inset-[4px] bg-gradient-to-br from-red-600 via-red-600 to-amber-600 text-white font-black text-2xl md:text-3xl hexagon shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 flex flex-col items-center justify-center gap-1 group cursor-pointer">
                    <span class="tracking-wider group-hover:animate-pulse">START!</span>
                    <span class="text-[9px] md:text-[10px] font-normal text-amber-200">Uji Hafalan (测试)</span>
                </button>
            </div>

            <!-- 1. SISI ATAS (Daftar Hafal) -->
            <button @click="goToMasteredPage()" style="transform: translateY(-182px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 active:scale-95 transition flex flex-col items-center justify-center p-2 cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">已学会</span>
                <span class="text-xs font-medium">Daftar Hafal</span>
            </button>

            <!-- 2. SISI KANAN ATAS (+ 词汇) -->
            <button style="transform: translate(172px, -91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">+ 词汇</span>
                <span class="text-[10px] leading-tight font-medium">Tambah / Cari</span>
            </button>

            <!-- 3. SISI KANAN BAWAH (Cek Nada) -->
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

            <!-- 5. SISI KIRI BAWAH (Tulis Hanzi) -->
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
        <!-- LAYER 3: MODAL PILIHAN TINGKAT KESULITAN (EASY vs NORMAL)         -->
        <!-- ================================================================= -->
        <div x-show="showDifficultyModal" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 relative flex flex-col items-center text-center gap-5 border border-slate-100">
                <button @click="showDifficultyModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold cursor-pointer">✕</button>
                
                <div>
                    <h2 class="text-xl font-black text-gray-900">Pilih Mode Latihan</h2>
                    <p class="text-xs text-gray-500 mt-1">Uji kosakata yang telah Anda hafal</p>
                </div>

                <div class="flex flex-col gap-3 w-full">
                    <!-- MODE EASY -->
                    <button @click="startTestSession('easy')" 
                            class="p-4 bg-amber-50 border-2 border-amber-300 hover:border-amber-500 rounded-2xl flex items-center justify-between text-left transition group cursor-pointer">
                        <div>
                            <span class="text-xs font-black text-amber-700 uppercase tracking-wider block">Mode Easy (简单)</span>
                            <span class="text-[11px] text-gray-600">Pinyin ditampilkan & ketik arti</span>
                        </div>
                        <span class="text-xl group-hover:translate-x-1 transition">➡️</span>
                    </button>

                    <!-- MODE NORMAL -->
                    <button @click="startTestSession('normal')" 
                            class="p-4 bg-red-50 border-2 border-red-300 hover:border-red-500 rounded-2xl flex items-center justify-between text-left transition group cursor-pointer">
                        <div>
                            <span class="text-xs font-black text-red-700 uppercase tracking-wider block">Mode Normal (普通)</span>
                            <span class="text-[11px] text-gray-600">Pinyin tersembunyi & ketik arti</span>
                        </div>
                        <span class="text-xl group-hover:translate-x-1 transition">🔥</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- LAYER 4: KARTU TEST LATIHAN INTERAKTIF                            -->
        <!-- ================================================================= -->
        <div x-show="currentView === 'flashcard'" 
             x-transition:enter="transition ease-out duration-500 delay-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="z-30 w-full max-w-md bg-white rounded-3xl shadow-2xl border-4 border-amber-400 p-8 flex flex-col items-center text-center relative">

            <button @click="goHome()" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold text-xl cursor-pointer">✕</button>

            <!-- Mode Tag & Status Skor -->
            <div class="flex items-center gap-2 mb-6">
                <span :class="testMode === 'easy' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'"
                      class="px-3 py-1 text-xs font-bold rounded-full uppercase"
                      x-text="'Mode: ' + testMode"></span>
                <span class="text-xs font-bold text-gray-400" x-text="'Skor: ' + score"></span>
            </div>

            <template x-if="isLoading">
                <div class="py-12 flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm text-gray-500 font-medium">Memuat Soal Latihan...</p>
                </div>
            </template>

            <template x-if="!isLoading && currentVocab.id">
                <div class="w-full flex flex-col items-center">
                    
                    <!-- Hanzi Utama -->
                    <h1 class="text-7xl font-extrabold text-gray-900 tracking-wide mb-2" x-text="currentVocab.hanzi"></h1>
                    
                    <!-- Pinyin (Tampil jika Easy, Tersembunyi jika Normal) -->
                    <div class="h-8 mb-4 flex items-center justify-center">
                        <template x-if="testMode === 'easy'">
                            <p class="text-2xl font-semibold text-amber-600" x-text="currentVocab.pinyin"></p>
                        </template>
                        <template x-if="testMode === 'normal'">
                            <span class="text-xs bg-slate-100 text-gray-400 px-3 py-1 rounded-full italic">Pinyin Disembunyikan</span>
                        </template>
                    </div>

                    <!-- Jenis Kata -->
                    <span class="text-xs bg-slate-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200 mb-6" x-text="currentVocab.type"></span>

                    <!-- Form Input Ketik Jawaban -->
                    <form @submit.prevent="checkAnswer()" class="w-full flex flex-col gap-3 mb-4">
                        <div class="relative">
                            <input type="text" 
                                   x-model="userAnswer" 
                                   :disabled="feedback.show"
                                   placeholder="Ketik terjemahan bahasa Indonesia..." 
                                   class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-2xl text-sm font-semibold text-center focus:outline-none focus:border-amber-500 transition">
                        </div>

                        <button type="submit" 
                                :disabled="feedback.show || userAnswer.trim() === ''"
                                class="w-full py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold rounded-2xl shadow-lg transition cursor-pointer disabled:opacity-50 text-xs">
                            Periksa Jawaban 🔍
                        </button>
                    </form>

                    <!-- Feedback Hasil Periksaan dengan Pesan Koreksi -->
                    <template x-if="feedback.show">
                        <div :class="{
                                'bg-green-50 border-green-200 text-green-800': feedback.status === 'exact',
                                'bg-amber-50 border-amber-200 text-amber-800': feedback.status === 'close',
                                'bg-red-50 border-red-200 text-red-800': feedback.status === 'wrong'
                             }"
                             class="w-full p-4 rounded-2xl border flex flex-col gap-1 mb-4 transition-all duration-300">
                            
                            <p class="font-extrabold text-sm" x-text="feedback.message"></p>
                            <p x-show="feedback.note" class="text-[11px] font-medium opacity-90" x-text="feedback.note"></p>
                            
                            <div class="mt-1 pt-2 border-t border-black/5 text-xs text-left">
                                <span class="text-gray-500 text-[10px] uppercase font-bold block">Jawaban Sebenarnya:</span>
                                <span class="font-bold" x-text="currentVocab.meaning"></span>
                            </div>
                            
                            <button @click="nextTestVocab()" 
                                    class="mt-3 w-full py-2 bg-slate-800 text-white font-bold rounded-xl text-xs cursor-pointer shadow">
                                Soal Selanjutnya ➡️
                            </button>
                        </div>
                    </template>

                </div>
            </template>

        </div>

        <!-- ================================================================= -->
        <!-- LAYER 5: HALAMAN PENUH DAFTAR HAFAL (FULL SCREEN VIEW)             -->
        <!-- ================================================================= -->
        <div x-show="currentView === 'mastered_page'" 
             x-transition:enter="transition ease-out duration-400 delay-200"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300 transform opacity-0 translate-y-8"
             class="fixed inset-0 z-30 bg-slate-100 flex flex-col p-4 md:p-6 overflow-y-auto">

            <div class="max-w-6xl w-full mx-auto bg-white rounded-3xl shadow-sm border border-slate-200 p-6 mb-6 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <button @click="goHome()" class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer">
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

            <!-- List Kosakata 2 Kolom -->
            <div class="max-w-6xl w-full mx-auto grid grid-cols-1 md:grid-cols-2 gap-3 pb-12">
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

        <!-- ================================================================= -->
        <!-- LAYER 6: MODAL KELOLA FILE HSK                                   -->
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
                    showHskModal: false,
                    showDifficultyModal: false,

                    testMode: 'easy', // 'easy' / 'normal'
                    userAnswer: '',
                    score: 0,
                    feedback: { show: false, status: 'wrong', message: '', note: '' }, // status: 'exact', 'close', 'wrong'

                    allVocabs: [],
                    filteredVocabs: [],
                    selectedHskFilter: 1,
                    searchQuery: '',

                    currentVocab: { id: null, hanzi: '', pinyin: '', type: '', meaning: '', hsk_level: 1 },

                    async openDifficultyModal() {
                        try {
                            const response = await fetch('/api/flashcards/mastered/random');
                            const json = await response.json();
                            if (json.success && json.data) {
                                this.showDifficultyModal = true;
                            } else {
                                alert('Belum ada kosakata yang dihafal! Silakan tandai beberapa kosakata terlebih dahulu dari daftar HSK.');
                            }
                        } catch (error) {
                            console.error('Gagal mengecek data hafal:', error);
                        }
                    },

                    async startTestSession(mode) {
                        this.testMode = mode;
                        this.showDifficultyModal = false;
                        this.score = 0;
                        this.isAnimating = true;

                        await this.fetchMasteredVocab();

                        setTimeout(() => { 
                            this.currentView = 'flashcard'; 
                        }, 440);
                        setTimeout(() => {
                            this.isAnimating = false;
                        }, 1050);
                    },

                    async fetchMasteredVocab() {
                        this.isLoading = true;
                        this.userAnswer = '';
                        this.feedback.show = false;

                        try {
                            const response = await fetch('/api/flashcards/mastered/random');
                            const json = await response.json();
                            if (json.success && json.data) {
                                this.currentVocab = json.data;
                            } else {
                                alert('Semua sesi latihan selesai!');
                                this.goHome();
                            }
                        } catch (error) {
                            console.error('Gagal mengambil data hafal:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    // Algoritma Levenshtein Distance untuk menghitung jarak typo
                    levenshteinDistance(a, b) {
                        const matrix = [];
                        for (let i = 0; i <= b.length; i++) matrix[i] = [i];
                        for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

                        for (let i = 1; i <= b.length; i++) {
                            for (let j = 1; j <= a.length; j++) {
                                if (b.charAt(i - 1) === a.charAt(j - 1)) {
                                    matrix[i][j] = matrix[i - 1][j - 1];
                                } else {
                                    matrix[i][j] = Math.min(
                                        matrix[i - 1][j - 1] + 1,
                                        matrix[i][j - 1] + 1,
                                        matrix[i - 1][j] + 1
                                    );
                                }
                            }
                        }
                        return matrix[b.length][a.length];
                    },

                    // Fungsi Pemeriksa Jawaban dengan Koreksi Pintar
                    checkAnswer() {
                        if (!this.currentVocab.meaning) return;

                        // Bersihkan spasi ganda dan karakter khusus
                        const cleanMeaning = this.currentVocab.meaning.toLowerCase().replace(/[^\w\s]/gi, '').replace(/\s+/g, ' ').trim();
                        const cleanUser = this.userAnswer.toLowerCase().replace(/[^\w\s]/gi, '').replace(/\s+/g, ' ').trim();

                        // Pecah kata kunci dari arti (misal: "to love, like" -> ["love", "like"])
                        const targetWords = cleanMeaning.split(' ');
                        const userWords = cleanUser.split(' ');

                        // 1. Cek Pencocokan Sempurna / Mengandung Kata Kunci
                        const isExact = cleanMeaning.includes(cleanUser) || cleanUser.includes(cleanMeaning);

                        if (isExact) {
                            this.feedback = {
                                show: true,
                                status: 'exact',
                                message: '🎉 Benar sekali!',
                                note: ''
                            };
                            this.score += 10;
                            return;
                        }

                        // 2. Cek Typo / Toleransi Jarak (Levenshtein)
                        let minDistance = 999;
                        for (let uWord of userWords) {
                            for (let tWord of targetWords) {
                                const dist = this.levenshteinDistance(uWord, tWord);
                                if (dist < minDistance) minDistance = dist;
                            }
                        }

                        // Toleransi typo max 2 karakter (seperti 'bsa' -> 'bisa')
                        if (minDistance <= 2 && cleanUser.length >= 2) {
                            this.feedback = {
                                show: true,
                                status: 'close',
                                message: '🤏 Sedikit lagi benar!',
                                note: 'Ada sedikit kesalahan ejaan / typo.'
                            };
                            this.score += 5; // Beri poin sebagian
                            return;
                        }

                        // 3. Jika Salah
                        this.feedback = {
                            show: true,
                            status: 'wrong',
                            message: '❌ Kurang tepat!',
                            note: ''
                        };
                    },

                    async nextTestVocab() {
                        await this.fetchMasteredVocab();
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