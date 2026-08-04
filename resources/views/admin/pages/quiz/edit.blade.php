@extends('layouts.admin')

@section('title', 'Editar página del quiz')
@section('page_title', 'Página del Quiz')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="quizAdmin()"
     x-init="init()">

    <p class="text-sm text-gray-500 mb-6">Edita los textos del quiz y las reglas de recomendación. Las 4 preguntas del quiz se mantienen fijas, pero puedes decidir qué producto se recomienda según las respuestas.</p>

    <form method="POST" action="{{ route('admin.pages.quiz.update') }}">
        @method('PUT')
        @csrf

        {{-- ═══════════ 1. TEXTOS ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('textos')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">1. Textos del quiz</h3>
                <svg :class="openSection === 'textos' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'textos'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título del hero</label>
                    <input type="text" name="hero_title" value="{{ $page->hero_title }}"
                           placeholder="¿Cuál es tu ritual de belleza?"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo del hero</label>
                    <input type="text" name="hero_subtitle" value="{{ $page->hero_subtitle }}"
                           placeholder="Responde 4 preguntas rápidas y te recomendamos los mejores productos para tu piel."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>

        {{-- ═══════════ 2. RESULTADO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('resultado')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">2. Pantalla de resultado</h3>
                <svg :class="openSection === 'resultado' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'resultado'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título del resultado</label>
                    <input type="text" name="result_title" value="{{ $page->result_title }}"
                           placeholder="Tu recomendación"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto del botón CTA</label>
                    <input type="text" name="result_cta_text" value="{{ $page->result_cta_text }}"
                           placeholder="Ver este producto"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón por defecto</label>
                    <textarea name="default_reason" rows="2"
                              placeholder="Este es nuestro modelo más popular y versátil."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->default_reason }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Se muestra cuando ninguna regla de recomendación coincide.</p>
                </div>
            </div>
        </div>

        {{-- ═══════════ 3. PRODUCTO POR DEFECTO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('default')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">3. Producto por defecto</h3>
                <svg :class="openSection === 'default' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'default'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto recomendado por defecto</label>
                    <select name="default_product_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">— Usar primer destacado —</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ $page->default_product_id == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Se usa cuando ninguna regla de recomendación coincide con las respuestas.</p>
                </div>
            </div>
        </div>

        {{-- ═══════════ 4. PREGUNTAS DEL QUIZ ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('questions')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">4. Preguntas del quiz</h3>
                <svg :class="openSection === 'questions' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'questions'" x-collapse class="px-6 pb-6">
                <p class="text-xs text-gray-500 mb-3">Define las preguntas que verán los usuarios. Cada pregunta necesita una <strong>clave única</strong> (ej: "usage", "hours") que luego se usa en las reglas de recomendación. Si no configuras ninguna pregunta, se usarán las 4 preguntas por defecto.</p>

                <div x-data="questionsRepeater()" x-init="init()">
                    {{-- Single JSON payload — bulletproof across repeaters --}}
                    <input type="hidden" name="questions_json" :value="JSON.stringify(items)">

                    <template x-for="(question, qIdx) in items" :key="qIdx">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-gray-700" x-text="'Pregunta ' + (qIdx + 1)"></span>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="if (qIdx > 0) { [items[qIdx], items[qIdx-1]] = [items[qIdx-1], items[qIdx]]; }"
                                            class="text-xs text-gray-500 hover:text-gray-700" title="Mover arriba">↑</button>
                                    <button type="button" @click="if (qIdx < items.length - 1) { [items[qIdx], items[qIdx+1]] = [items[qIdx+1], items[qIdx]]; }"
                                            class="text-xs text-gray-500 hover:text-gray-700" title="Mover abajo">↓</button>
                                    <button type="button" @click="items.splice(qIdx, 1)"
                                            class="text-xs text-red-500 hover:text-red-700">Eliminar pregunta</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Clave (único, sin espacios)</label>
                                    <input type="text" x-model="question.key"
                                           placeholder="usage, hours, style..."
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm font-mono focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Pregunta (visible al usuario)</label>
                                    <input type="text" x-model="question.label"
                                           placeholder="¿Qué buscas para tu piel?"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Subtítulo / aclaración</label>
                                <input type="text" x-model="question.subtitle"
                                       placeholder="Selecciona tu uso principal."
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-medium text-gray-600">Opciones de respuesta</label>
                                    <button type="button" @click="question.options.push({value:'', label:'', desc:''})"
                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">+ Agregar opción</button>
                                </div>

                                <template x-for="(option, oIdx) in question.options" :key="oIdx">
                                    <div class="bg-white rounded-lg p-3 mb-2 border border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs text-gray-500" x-text="'Opción ' + (oIdx + 1)"></span>
                                            <button type="button" @click="question.options.splice(oIdx, 1)"
                                                    class="text-xs text-red-400 hover:text-red-600">Eliminar</button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                            <input type="text" x-model="option.value"
                                                   placeholder="Valor (ej: piel-grasa)"
                                                   class="w-full rounded-lg border-gray-300 shadow-sm text-xs font-mono focus:border-blue-500 focus:ring-blue-500">
                                            <input type="text" x-model="option.label"
                                                   placeholder="Texto visible"
                                                   class="w-full rounded-lg border-gray-300 shadow-sm text-xs focus:border-blue-500 focus:ring-blue-500">
                                            <input type="text" x-model="option.desc"
                                                   placeholder="Descripción corta"
                                                   class="w-full rounded-lg border-gray-300 shadow-sm text-xs focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="items.push({key:'', label:'', subtitle:'', options:[{value:'',label:'',desc:''}]})"
                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Agregar pregunta
                        </button>
                        <button type="button" @click="if (confirm('¿Restablecer las preguntas por defecto? Perderás las que tengas configuradas.')) { items = JSON.parse(JSON.stringify(defaultQuestions)); }"
                                class="text-sm text-gray-500 hover:text-gray-700">Restablecer por defecto</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ 5. REGLAS DE RECOMENDACIÓN ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('rules')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">5. Reglas de recomendación</h3>
                <svg :class="openSection === 'rules' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'rules'" x-collapse class="px-6 pb-6">
                <p class="text-xs text-gray-500 mb-3">Define qué producto recomendar según las respuestas del quiz. La primera regla que coincida será la usada. Si ninguna coincide, se usa el producto por defecto.</p>

                <div x-data="rulesRepeater()" x-init="init()">
                    {{-- Single JSON payload --}}
                    <input type="hidden" name="recommendation_rules_json" :value="JSON.stringify(items)">

                    <template x-for="(rule, idx) in items" :key="idx">
                        <div class="bg-gray-50 rounded-lg p-4 mb-3">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500" x-text="'Regla ' + (idx + 1)"></span>
                                <button type="button" @click="items.splice(idx, 1)" class="text-xs text-red-500 hover:text-red-700">Eliminar</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Si la respuesta de...</label>
                                    <select x-model="rule.condition_field"
                                            x-init="$nextTick(() => { if (rule.condition_field) $el.value = rule.condition_field; })"
                                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">— Selecciona pregunta —</option>
                                        <template x-for="q in $store.quizShared.questions" :key="q.key">
                                            <option :value="q.key" :selected="q.key === rule.condition_field" x-text="q.label + ' (' + q.key + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">... es igual a</label>
                                    <select x-model="rule.condition_value"
                                            x-init="$nextTick(() => { if (rule.condition_value) $el.value = rule.condition_value; })"
                                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">— Selecciona respuesta —</option>
                                        <template x-for="opt in optionsFor(rule.condition_field)" :key="opt.value">
                                            <option :value="opt.value" :selected="opt.value === rule.condition_value" x-text="opt.label + ' (' + opt.value + ')'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Recomendar producto</label>
                                    <select x-model.number="rule.product_id"
                                            x-init="$nextTick(() => { if (rule.product_id) $el.value = rule.product_id; })"
                                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">— Selecciona —</option>
                                        @foreach($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Razón que ve el usuario</label>
                                <input type="text" x-model="rule.reason"
                                       placeholder="Ej: Para piel grasa, productos de control de grasa y textura ligera."
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="items.push({condition_field:'usage', condition_value:'piel-grasa', product_id:'', reason:''})"
                            class="mt-2 flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Agregar regla
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════ 6. SEO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('seo')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">6. SEO</h3>
                <svg :class="openSection === 'seo' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'seo'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta título</label>
                    <input type="text" name="meta_title" value="{{ $page->meta_title }}"
                           placeholder="Quiz de piel | Belleza Áurea"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta descripción</label>
                    <textarea name="meta_description" rows="2"
                              placeholder="Responde 4 preguntas sobre tu piel y descubre los productos de belleza perfectos para ti."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->meta_description }}</textarea>
                </div>
            </div>
        </div>

        {{-- Botón guardar --}}
        <button type="submit"
                class="w-full py-3 rounded-xl text-white font-medium text-base transition-colors"
                style="background:#D9B56D;"
                onmouseover="this.style.background='#BE9A53'"
                onmouseout="this.style.background='#D9B56D'">
            Guardar cambios
        </button>
    </form>
</div>

@push('scripts')
<script>
// Shared store so the rules section can react to question edits in real time.
document.addEventListener('alpine:init', () => {
    const initialQuestions = @json($page->questions ?? \App\Models\QuizPageSetting::defaultQuestions());
    Alpine.store('quizShared', {
        questions: JSON.parse(JSON.stringify(initialQuestions)),
    });
});

function quizAdmin() {
    return {
        openSection: 'textos',
        toggle(s) { this.openSection = this.openSection === s ? null : s; },
        init() {},
    };
}

function questionsRepeater() {
    return {
        items: [],
        defaultQuestions: @json(\App\Models\QuizPageSetting::defaultQuestions()),
        init() {
            const saved = @json($page->questions ?? []);
            const hasSaved = Array.isArray(saved) && saved.length > 0;
            // Deep clone so edits don't mutate the shared defaults reference.
            this.items = hasSaved
                ? JSON.parse(JSON.stringify(saved))
                : JSON.parse(JSON.stringify(this.defaultQuestions));
            // Keep options array valid on legacy entries
            this.items.forEach(q => {
                if (!Array.isArray(q.options)) q.options = [];
                if (q.options.length === 0) q.options.push({value:'', label:'', desc:''});
            });
            // Push initial state into the shared store so the rules section
            // sees the same data the repeater is editing (not just the snapshot
            // taken at page load, which may differ if the JSON was malformed).
            Alpine.store('quizShared').questions = JSON.parse(JSON.stringify(this.items));
            // Keep the store in sync with edits.
            this.$watch('items', (val) => {
                Alpine.store('quizShared').questions = JSON.parse(JSON.stringify(val));
            }, { deep: true });
        }
    };
}

function rulesRepeater() {
    return {
        items: [],
        init() {
            const saved = @json($page->recommendation_rules ?? []);
            this.items = (Array.isArray(saved) ? JSON.parse(JSON.stringify(saved)) : []).map(r => ({
                condition_field: r.condition_field ?? '',
                condition_value: r.condition_value ?? '',
                product_id: r.product_id != null ? Number(r.product_id) : '',
                reason: r.reason ?? '',
            }));

            // Cuando cambian las preguntas (el cliente edita en la misma pagina),
            // re-asignamos los valores guardados a los selects para que no se pierdan
            // si la pregunta sigue existiendo.
            this.$watch('$store.quizShared.questions', () => {
                this.$nextTick(() => {
                    document.querySelectorAll('[data-rule-select]').forEach(sel => {
                        const expected = sel.dataset.ruleValue;
                        if (expected != null && expected !== '' && sel.value !== expected) {
                            sel.value = expected;
                        }
                    });
                });
            }, { deep: true });
        },
        optionsFor(key) {
            if (!key) return [];
            const q = (Alpine.store('quizShared').questions || []).find(q => q.key === key);
            return q ? (q.options || []) : [];
        },
    };
}
</script>
@endpush
@endsection
