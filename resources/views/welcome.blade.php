<x-layouts.app title="Mandarin Flashcard" xData="homeApp()">

    <!-- LAYER COVER LAYAR (IRIS WIPE COVER PHASE) -->
    <div x-show="isCovering" 
         class="fixed inset-0 pointer-events-none z-50 bg-gradient-to-br from-red-600 via-red-700 to-amber-500 animate-iris-cover">
    </div>

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
</x-layouts.app>