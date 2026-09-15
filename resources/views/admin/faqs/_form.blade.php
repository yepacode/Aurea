@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
    <div>
        <label for="faq_category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
        <select name="faq_category_id" id="faq_category_id" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ old('faq_category_id', $faq->faq_category_id ?? request('category')) == $c->id ? 'selected' : '' }}>
                    {{ $c->emoji }} {{ $c->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="question" class="block text-sm font-medium text-gray-700 mb-1">Pregunta *</label>
        <input type="text" id="question" name="question" required maxlength="255"
               value="{{ old('question', $faq->question ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="¿Cuánto tarda mi pedido?">
    </div>

    <div>
        <label for="answer" class="block text-sm font-medium text-gray-700 mb-1">Respuesta *</label>
        <textarea id="answer" name="answer" rows="8" required
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Escribe la respuesta de forma clara y amable...">{{ old('answer', $faq->answer ?? '') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Los saltos de línea se conservan en la vista pública.</p>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
            <input type="number" id="sort_order" name="sort_order" min="0"
                   value="{{ old('sort_order', $faq->sort_order ?? 0) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                Activa
            </label>
        </div>
    </div>

    @if(isset($faq) && $faq->exists)
        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 text-sm text-gray-500">
            <div>Vistas: <span class="font-medium text-gray-800">{{ $faq->view_count }}</span></div>
            <div>Marcada útil: <span class="font-medium text-gray-800">{{ $faq->helpful_count }}</span> veces</div>
        </div>
    @endif
</div>

<div class="flex items-center justify-end space-x-3">
    <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</a>
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
        {{ isset($faq) && $faq->exists ? 'Guardar cambios' : 'Crear FAQ' }}
    </button>
</div>
