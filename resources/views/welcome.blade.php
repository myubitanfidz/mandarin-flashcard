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

            /* Animasi Iris Cover diperlama jadi 0.85s (Penutupan dari Tengah ke Luar) */
            @keyframes irisCover {
                0% { --fill-size: 0%; --hole-size: 0%; }
                100% { --fill-size: 150%; --hole-size: 0%; }
            }

            .animate-iris-cover {
                animation: irisCover 0.85s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                -webkit-mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
                mask-image: radial-gradient(circle at 50% 50%, transparent var(--hole-size), black var(--hole-size), black var(--fill-size), transparent var(--fill-size));
            }
        </style>
    </head>
    <body class="bg-slate-100 text-gray-900 font-sans min-h-screen flex items-center justify-center p-4 overflow-hidden" x-data="homeApp()">
        
        <!-- HONEYCOMB HEXAGON MAIN MENU -->
        <div class="relative w-[580px] h-[580px] flex items-center justify-center">

            <!-- TOMBOL PUSAT: START! -->
            <div class="absolute z-20 w-[220px] h-[195px] flex items-center justify-center">
                <div class="absolute inset-0 bg-amber-400 hexagon"></div>
                <button @click="openDifficultyModal()" 
                        class="absolute inset-[4px] bg-gradient-to-br from-red-600 via-red-600 to-amber-600 text-white font-black text-2xl md:text-3xl hexagon shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 flex flex-col items-center justify-center gap-1 group cursor-pointer">
                    <span class="tracking-wider group-hover:animate-pulse">START!</span>
                    <span class="text-[9px] md:text-[10px] font-normal text-amber-200">Uji Hafalan (测试)</span>
                </button>
            </div>

            <!-- 1. SISI ATAS (Daftar Hafal) -->
            <button @click="navigateTo('/mastered')" style="transform: translateY(-182px);"
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

        <!-- LAYER COVER LAYAR (IRIS WIPE COVER PHASE) -->
        <div x-show="isCovering" 
             class="fixed inset-0 pointer-events-none z-50 bg-gradient-to-br from-red-600 via-red-700 to-amber-500 animate-iris-cover">
        </div>

        <!-- MODAL PILIHAN TINGKAT KESULITAN -->
        <div x-show="showDifficultyModal" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 relative flex flex-col items-center text-center gap-5 border border-slate-100">
                <button @click="showDifficultyModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold cursor-pointer">✕</button>
                
                <div>
                    <h2 class="text-xl font-black text-gray-900">Pilih Mode Latihan</h2>
                    <p class="text-xs text-gray-500 mt-1">Uji kosakata yang telah Anda hafal</p>
                </div>

                <div class="flex flex-col gap-3 w-full">
                    <button @click="startTestSession('easy')" 
                            class="p-4 bg-amber-50 border-2 border-amber-300 hover:border-amber-500 rounded-2xl flex items-center justify-between text-left transition group cursor-pointer">
                        <div>
                            <span class="text-xs font-black text-amber-700 uppercase tracking-wider block">Mode Easy (简单)</span>
                            <span class="text-[11px] text-gray-600">Pinyin ditampilkan & ketik arti</span>
                        </div>
                        <span class="text-xl group-hover:translate-x-1 transition">➡️</span>
                    </button>

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

        <!-- MODAL KELOLA FILE HSK -->
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
            function homeApp() {
                return {
                    isCovering: false,
                    showHskModal: false,
                    showDifficultyModal: false,

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

                    navigateTo(url) {
                        this.isCovering = true;
                        // Menunggu durasi animasi cover (0.85s / 850ms) baru berpindah halaman
                        setTimeout(() => {
                            window.location.href = url;
                        }, 850);
                    },

                    startTestSession(mode) {
                        this.showDifficultyModal = false;
                        this.navigateTo(`/test?mode=${mode}`);
                    }
                }
            }
        </script>
    </body>
</html>