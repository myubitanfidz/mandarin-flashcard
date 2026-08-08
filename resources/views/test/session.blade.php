<x-layouts.app title="Uji Hafalan - Mandarin Flashcard" xData="testApp()">

    <!-- EFEK LAYER ANIMASI TRANSISI -->
    <div x-show="isTransitioning" 
         :class="animMode === 'uncover' ? 'animate-iris-uncover' : 'animate-iris-cover bg-gradient-to-br from-red-600 via-red-700 to-amber-500'"
         class="fixed inset-0 pointer-events-none z-50 bg-gradient-to-br from-red-600 via-red-700 to-amber-500">
    </div>

    <!-- KARTU TEST LATIHAN INTERAKTIF -->
    <div class="z-30 w-full max-w-md bg-white rounded-3xl shadow-2xl border-4 border-amber-400 p-8 flex flex-col items-center text-center relative my-auto">

        <button @click="goHome('/')" class="absolute top-4 right-4 text-gray-400 hover:text-red-600 font-bold text-xl cursor-pointer">✕</button>

        <div class="flex items-center gap-2 mb-6">
            <span :class="testMode === 'easy' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'"
                  class="px-3 py-1 text-xs font-bold rounded-full uppercase"
                  x-text="'Mode: ' + testMode"></span>
            <span class="text-xs font-bold text-gray-400" x-text="'Skor: ' + score"></span>
        </div>

        <template x-if="currentVocab.id">
            <div class="w-full flex flex-col items-center">
                
                <h1 class="text-7xl font-extrabold text-gray-900 tracking-wide mb-2" x-text="currentVocab.hanzi"></h1>
                
                <div class="h-8 mb-4 flex items-center justify-center">
                    <template x-if="testMode === 'easy'">
                        <p class="text-2xl font-semibold text-amber-600" x-text="currentVocab.pinyin"></p>
                    </template>
                    <template x-if="testMode === 'normal'">
                        <span class="text-xs bg-slate-100 text-gray-400 px-3 py-1 rounded-full italic">Pinyin Disembunyikan</span>
                    </template>
                </div>

                <span class="text-xs bg-slate-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200 mb-6" x-text="currentVocab.type"></span>

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

    <script>
        function testApp() {
            return {
                isTransitioning: true,
                animMode: 'uncover',
                testMode: new URLSearchParams(window.location.search).get('mode') || 'easy',
                
                userAnswer: '',
                score: 0,
                feedback: { show: false, status: 'wrong', message: '', note: '' },

                currentVocab: { id: null, hanzi: '', pinyin: '', type: '', meaning: '', hsk_level: 1 },

                async init() {
                    await this.fetchMasteredVocab();
                    this.animMode = 'uncover';
                    setTimeout(() => {
                        this.isTransitioning = false;
                    }, 850);
                },

                async fetchMasteredVocab() {
                    this.userAnswer = '';
                    this.feedback.show = false;

                    try {
                        const response = await fetch('/api/flashcards/mastered/random');
                        const json = await response.json();
                        if (json.success && json.data) {
                            this.currentVocab = json.data;
                        } else {
                            alert('Sesi latihan selesai!');
                            this.goHome('/');
                        }
                    } catch (error) {
                        console.error('Gagal mengambil data hafal:', error);
                    }
                },

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

                checkAnswer() {
                    if (!this.currentVocab.meaning) return;

                    const cleanMeaning = this.currentVocab.meaning.toLowerCase().replace(/[^\w\s]/gi, '').replace(/\s+/g, ' ').trim();
                    const cleanUser = this.userAnswer.toLowerCase().replace(/[^\w\s]/gi, '').replace(/\s+/g, ' ').trim();

                    const targetWords = cleanMeaning.split(' ');
                    const userWords = cleanUser.split(' ');

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

                    let minDistance = 999;
                    for (let uWord of userWords) {
                        for (let tWord of targetWords) {
                            const dist = this.levenshteinDistance(uWord, tWord);
                            if (dist < minDistance) minDistance = dist;
                        }
                    }

                    if (minDistance <= 2 && cleanUser.length >= 2) {
                        this.feedback = {
                            show: true,
                            status: 'close',
                            message: '🤏 Sedikit lagi benar!',
                            note: 'Ada sedikit kesalahan ejaan / typo.'
                        };
                        this.score += 5;
                        return;
                    }

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

                goHome(url) {
                    this.animMode = 'cover';
                    this.isTransitioning = true;
                    setTimeout(() => {
                        window.location.href = url;
                    }, 850);
                }
            }
        }
    </script>
</x-layouts.app>