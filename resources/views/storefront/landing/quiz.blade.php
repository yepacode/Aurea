@extends('layouts.app')

@section('body_class', 'bg-bg text-text')

@section('title', ($seoSettings->meta_title ?? null) ?: ($quizPage->meta_title ?? 'Quiz de piel — Descubre tu ritual | Belleza Áurea'))
@section('meta_description', ($seoSettings->meta_description ?? null) ?: ($quizPage->meta_description ?? 'Responde unas preguntas rápidas y descubre los productos de belleza ideales para tu tipo de piel. Quiz de Belleza Áurea.'))
@section('canonical', $seoSettings->canonical_url ?? route('landing.quiz'))
@section('og_title', ($seoSettings->og_title ?? null) ?: ($seoSettings->meta_title ?? null) ?: 'Quiz de piel | Belleza Áurea')
@section('og_description', ($seoSettings->og_description ?? null) ?: ($seoSettings->meta_description ?? null) ?: 'Descubre los productos de belleza ideales para tu tipo de piel.')
@section('twitter_title', ($seoSettings->twitter_title ?? null) ?: ($seoSettings->meta_title ?? null) ?: 'Quiz de piel | Belleza Áurea')
@section('twitter_description', ($seoSettings->twitter_description ?? null) ?: ($seoSettings->meta_description ?? null) ?: 'Descubre los productos de belleza ideales para tu tipo de piel.')
@section('og_image', ($seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))
@section('twitter_image', ($seoSettings->twitter_image_url ?? $seoSettings->og_image_url ?? null) ?: asset('img/brand/logo-principal.png'))

@section('content')

    <section class="min-h-[80vh] flex items-center py-12">
        <div class="w-full max-w-2xl mx-auto px-4 sm:px-6"
             x-data="quizApp()"
             x-cloak>

            {{-- Hero (only on first step) --}}
            <div class="text-center mb-8" x-show="step === 1">
                <h1 class="font-brand text-3xl md:text-4xl font-bold mb-3">{{ $quizPage->hero_title ?? '¿Cuál es tu ritual ideal?' }}</h1>
                <p class="text-text-muted/60">{{ $quizPage->hero_subtitle ?? 'Responde unas preguntas rápidas sobre tu piel y te recomendamos los productos perfectos para ti.' }}</p>
            </div>

            {{-- Progress bar --}}
            <div class="mb-8" x-show="step <= totalSteps">
                <div class="flex justify-between text-xs text-text-muted/50 mb-2">
                    <span>Pregunta <span x-text="step"></span> de <span x-text="totalSteps"></span></span>
                    <span x-text="Math.round((step / totalSteps) * 100) + '%'"></span>
                </div>
                <div class="h-1.5 bg-surface rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all duration-500"
                         :style="'width: ' + ((step / totalSteps) * 100) + '%'"></div>
                </div>
            </div>

            {{-- Dynamic questions --}}
            <template x-for="(question, qIdx) in questions" :key="question.key">
                <div x-show="step === qIdx + 1"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-8"
                     x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="font-brand text-2xl md:text-3xl font-bold mb-2" x-text="question.label"></h2>
                    <p class="text-text-muted/60 mb-8" x-text="question.subtitle" x-show="question.subtitle"></p>

                    <div class="grid gap-4"
                         :class="question.options.length === 2 ? 'grid-cols-1 sm:grid-cols-2' : (question.options.length <= 4 ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1')">
                        <template x-for="option in question.options" :key="option.value">
                            <button type="button" @click="answerQuestion(question.key, option.value)"
                                    class="group bg-surface border-2 rounded-2xl p-6 text-left transition-all hover:border-secondary"
                                    :class="answers[question.key] === option.value ? 'border-secondary' : 'border-border'">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                                         :class="answers[question.key] === option.value ? 'border-secondary bg-secondary' : 'border-muted/30'">
                                        <svg x-show="answers[question.key] === option.value"
                                             class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m4.5 12.75 6 6 9-13.5"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold" x-text="option.label"></h3>
                                        <p class="text-sm text-text-muted/50 mt-1" x-text="option.desc" x-show="option.desc"></p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Lead capture step (after all questions) --}}
            <div x-show="step === leadStep"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                    </div>
                    <h2 class="font-brand text-2xl md:text-3xl font-bold">¡Tenemos tu recomendación!</h2>
                    <p class="mt-2 text-text-muted/60">Ingresa tus datos para ver tu recomendación personalizada.</p>
                </div>

                <form @submit.prevent="submitQuiz()" class="space-y-4 max-w-sm mx-auto">
                    <div>
                        <label for="quiz-name" class="block text-sm font-medium text-text-muted/70 mb-1">Tu nombre</label>
                        <input type="text" id="quiz-name" x-model="form.name" required
                               class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent placeholder-text-muted/30"
                               placeholder="Ej: María García">
                    </div>
                    <div>
                        <label for="quiz-email" class="block text-sm font-medium text-text-muted/70 mb-1">Tu email</label>
                        <input type="email" id="quiz-email" x-model="form.email" required
                               class="w-full bg-surface border border-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent placeholder-text-muted/30"
                               placeholder="tu@email.com">
                    </div>
                    <button type="submit" :disabled="loading"
                            class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold uppercase tracking-wider text-sm transition-colors disabled:opacity-50 shadow-lg shadow-primary/30">
                        <span x-show="!loading">Ver mi recomendación</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                            Procesando...
                        </span>
                    </button>
                    <p x-show="error" x-text="error" class="text-danger text-sm text-center"></p>
                </form>
            </div>

            {{-- Result step --}}
            <div x-show="step === resultStep"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="text-center mb-8">
                    <span class="inline-block text-primary text-sm font-medium tracking-wider uppercase mb-2">Tu resultado</span>
                    <h2 class="font-brand text-2xl md:text-3xl font-bold text-text">{{ $quizPage->result_title ?? 'Tu ritual recomendado' }}</h2>
                    <p class="mt-3 text-text-muted max-w-lg mx-auto" x-text="result.message"></p>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:gap-5 max-w-2xl mx-auto">
                    <template x-for="p in result.products" :key="p.url">
                        <a :href="p.url" class="group bg-surface rounded-2xl border border-border overflow-hidden transition-all hover:-translate-y-1 hover:border-primary/50" style="text-decoration:none;">
                            <div class="aspect-[4/5] bg-bg overflow-hidden">
                                <template x-if="p.image">
                                    <img :src="'/storage/' + p.image" :alt="p.name" style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;padding:12px;" class="transition-transform duration-700 group-hover:scale-105">
                                </template>
                            </div>
                            <div class="p-4">
                                <p class="text-[10px] uppercase tracking-widest text-primary font-semibold" x-text="p.category"></p>
                                <h3 class="font-brand text-sm font-semibold text-text mt-1 leading-snug" x-text="p.name"></h3>
                                <span class="text-primary font-bold text-sm mt-2 inline-block">$<span x-text="Number(p.price).toLocaleString('es-CO')"></span></span>
                            </div>
                        </a>
                    </template>
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-dark text-sm font-semibold">
                        Ver todo el catálogo →
                    </a>
                </div>
            </div>

            {{-- Back button --}}
            <div class="mt-8" x-show="step > 1 && step <= leadStep">
                <button @click="prevStep()" class="text-sm text-text-muted/50 hover:text-text-muted/80 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    Anterior
                </button>
            </div>

        </div>
    </section>

@endsection

@push('scripts')
<script>
    function quizApp() {
        return {
            questions: @json($questions),
            step: 1,
            loading: false,
            error: '',
            answers: {},
            form: { name: '', email: '' },
            result: {},

            get totalSteps() { return this.questions.length; },
            get leadStep() { return this.questions.length + 1; },
            get resultStep() { return this.questions.length + 2; },

            init() {
                // Initialize answers with empty values for each question key
                this.questions.forEach(q => {
                    this.answers[q.key] = '';
                });
            },

            answerQuestion(key, value) {
                this.answers[key] = value;
                this.nextStep();
            },

            nextStep() {
                if (this.step < this.leadStep) {
                    this.step++;
                }
            },
            prevStep() {
                if (this.step > 1) this.step--;
            },

            async submitQuiz() {
                this.loading = true;
                this.error = '';
                try {
                    const response = await fetch('{{ route("landing.quiz.result") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            name: this.form.name,
                            email: this.form.email,
                            answers: this.answers,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.result = data.recommendation;
                        this.step = this.resultStep;
                    } else {
                        this.error = data.message || 'Ocurrió un error. Intenta de nuevo.';
                    }
                } catch (e) {
                    this.error = 'Error de conexión. Verifica tu internet e intenta de nuevo.';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
@endpush
