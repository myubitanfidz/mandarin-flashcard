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
            /* Hexagon Flat-Topped (Sisi Datar Atas & Bawah) */
            .hexagon {
                clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            }

            @keyframes liquidSpill {
                0% {
                    transform: scale(0);
                    opacity: 0.9;
                    border-radius: 50%;
                }
                50% {
                    border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
                }
                100% {
                    transform: scale(25);
                    opacity: 1;
                    border-radius: 0%;
                }
            }

            .animate-spill {
                animation: liquidSpill 0.7s ease-in-out forwards;
            }
        </style>
    </head>
    <body class="bg-slate-100 text-gray-900 font-sans min-h-screen flex items-center justify-center p-4 overflow-hidden" x-data="flashcardApp()">
        
        <!-- ================================================================= -->
        <!-- LAYER 1: HONEYCOMB HEXAGON MAIN MENU (CLEAN & PERFECT GOLD BORDER) -->
        <!-- ================================================================= -->
        <div x-show="!isStarted" class="relative w-[580px] h-[580px] flex items-center justify-center">

            <!-- TOMBOL PUSAT: START! (Dengan Garis Emas Sempurna Menyusuri Clip-path) -->
            <div class="absolute z-20 w-[220px] h-[195px] flex items-center justify-center">
                <!-- Outer Gold Border Layer -->
                <div class="absolute inset-0 bg-amber-400 hexagon"></div>
                <!-- Inner Red Button Layer (Memberikan Efek Border Emas 4px Rata di Seluruh Sisi) -->
                <button @click="startSession()" 
                        class="absolute inset-[4px] bg-gradient-to-br from-red-600 to-red-700 text-white font-black text-2xl md:text-3xl hexagon shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 flex flex-col items-center justify-center gap-1 group cursor-pointer">
                    <span class="tracking-wider group-hover:animate-pulse">START!</span>
                    <span class="text-[10px] md:text-xs font-normal text-amber-200">开始学习</span>
                </button>
            </div>

            <!-- 1. SISI ATAS (Daftar Hafal) - Polos Tanpa Border -->
            <button style="transform: translateY(-182px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">已学会</span>
                <span class="text-xs font-medium">Daftar Hafal</span>
            </button>

            <!-- 2. SISI KANAN ATAS (Tambah Kosakata) - Polos Tanpa Border -->
            <button style="transform: translate(172px, -91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">+ 词汇</span>
                <span class="text-[10px] leading-tight font-medium">Tambah / Cari</span>
            </button>

            <!-- 3. SISI KANAN BAWAH (Pengecek Nada) - Polos Tanpa Border -->
            <button style="transform: translate(172px, 91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">🎙️ 声调</span>
                <span class="text-[10px] leading-tight font-medium">Cek Nada</span>
            </button>

            <!-- 4. SISI BAWAH (Kelola HSK) - Polos Tanpa Border -->
            <button style="transform: translateY(182px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">📚 HSK</span>
                <span class="text-[10px] leading-tight font-medium">Kelola File</span>
            </button>

            <!-- 5. SISI KIRI BAWAH (Canvas Penulisan) - Polos Tanpa Border -->
            <button style="transform: translate(-172px, 91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-amber-600 font-bold">✍️ 笔画</span>
                <span class="text-[10px] leading-tight font-medium">Tulis Hanzi</span>
            </button>

            <!-- 6. SISI KIRI ATAS (Grammar AI) - Polos Tanpa Border -->
            <button style="transform: translate(-172px, -91px);"
                    class="absolute z-10 w-[148px] h-[130px] bg-white text-gray-900 hexagon font-semibold shadow-lg hover:bg-slate-50 hover:scale-105 transition flex flex-col items-center justify-center p-2 text-center cursor-pointer">
                <span class="text-xs text-red-600 font-bold">🤖 语法</span>
                <span class="text-[10px] leading-tight font-medium">Grammar AI</span>
            </button>

        </div>

        <!-- ================================================================= -->
        <!-- LAYER 2: EFEK WATER/LIQUID SPILL ANIMATION                        -->
        <!-- ================================================================= -->
        <div x-show="isAnimating" 
             class="fixed inset-0 pointer-events-none z-20 flex items-center justify-center">
            <div class="w-16 h-16 bg-gradient-to-br from-red-600 via-amber-500 to-red-700 animate-spill"></div>
        </div>

        <!-- ================================================================= -->
        <!-- LAYER 3: FLASHCARD INTERACTIVE VIEW                              -->
        <!-- ================================================================= -->
        <div x-show="showCard" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 scale-90 translate-y-10"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="z-30 w-full max-w-md bg-white rounded-3xl shadow-2xl border-4 border-amber-400 p-8 flex flex-col items-center text-center relative">

            <!-- Tombol Kembali ke Hexagon -->
            <button @click="closeCard()" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold text-xl cursor-pointer">✕</button>

            <!-- Tag Level HSK -->
            <span class="px-3 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-full mb-6">HSK Level 1</span>

            <!-- 1. Karakter Hanzi Besar -->
            <h1 class="text-7xl font-extrabold text-gray-900 tracking-wide mb-4" x-text="currentVocab.hanzi"></h1>

            <!-- 2. Pinyin dengan Nada -->
            <p class="text-2xl font-semibold text-amber-600 mb-2" x-text="currentVocab.pinyin"></p>

            <!-- Jenis Kata (Part of Speech Tag) -->
            <span class="text-xs bg-slate-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200 mb-6" x-text="currentVocab.type"></span>

            <!-- 3. Terjemahan / Arti -->
            <div class="w-full bg-slate-50 rounded-2xl p-4 border border-slate-100 mb-6">
                <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">Arti / Terjemahan</p>
                <p class="text-xl font-bold text-gray-800" x-text="currentVocab.meaning"></p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 w-full">
                <button class="flex-1 py-3 bg-slate-200 hover:bg-slate-300 text-gray-800 font-bold rounded-xl transition cursor-pointer">Belum Hafal</button>
                <button class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition cursor-pointer">Sudah Hafal ✅</button>
            </div>
        </div>

        <script>
            function flashcardApp() {
                return {
                    isStarted: false,
                    isAnimating: false,
                    showCard: false,

                    currentVocab: {
                        hanzi: '你好',
                        pinyin: 'nǐ hǎo',
                        type: '代词 (Kata Ganti / Pronoun)',
                        meaning: 'Halo / Apa Kabar'
                    },

                    startSession() {
                        this.isAnimating = true;

                        setTimeout(() => {
                            this.isStarted = true;
                            this.isAnimating = false;
                            this.showCard = true;
                        }, 650);
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