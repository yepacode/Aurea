@extends('layouts.admin')

@section('title', 'Editar: ' . $post->title)
@section('page_title', 'Editar publicación')

@section('content')
    <form method="POST" action="{{ route('admin.blog.update', $post) }}" enctype="multipart/form-data" class="max-w-4xl space-y-6"
          x-data="blogForm()">
        @csrf @method('PUT')

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Content --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Contenido</h2>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                           x-model="title"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-400">Slug: <span class="font-mono" x-text="slug"></span></p>
                </div>
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Extracto</label>
                    <textarea id="excerpt" name="excerpt" rows="2" maxlength="160"
                              x-model="excerpt"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                    <p class="mt-1 text-xs" :class="excerpt.length > 160 ? 'text-red-500' : 'text-gray-400'">
                        <span x-text="excerpt.length"></span>/160 caracteres
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenido *</label>
                    <textarea id="content" name="content" style="display:none;">{{ old('content', $post->content) }}</textarea>
                    <div id="quill-editor" style="height:400px;background:#fff;border:1px solid #d1d5db;border-radius:0 0 8px 8px;"></div>
                </div>
            </div>
        </div>

        {{-- Image --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Imagen destacada</h2>
            <div class="space-y-4">
                @if($post->image)
                    <div>
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->featured_image_alt }}" class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400">Subir nueva imagen reemplaza la actual.</p>
                <div>
                    <label for="featured_image_alt" class="block text-sm font-medium text-gray-700 mb-1">Alt text (SEO)</label>
                    <input type="text" id="featured_image_alt" name="featured_image_alt" value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
                           placeholder="Descripción de la imagen para buscadores y accesibilidad"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Categoría del blog (no forma parte del panel SEO) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Categoría</h2>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoría del blog</label>
                <select id="category" name="category"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @php
                        $blogCategories = [
                            '' => '— Detectar automáticamente por keyword —',
                            'salud-visual' => 'Salud visual',
                            'luz-azul' => 'Luz azul',
                            'habitos' => 'Hábitos digitales',
                            'lentes' => 'Lentes',
                        ];
                        $currentCat = old('category', $post->category);
                    @endphp
                    @foreach($blogCategories as $val => $label)
                        <option value="{{ $val }}" {{ $currentCat === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">Determina en qué filtro aparece el publicación en <code>/blog</code>.</p>
            </div>
        </div>

        {{-- Panel SEO completo (Google preview, meta, Open Graph, Twitter, JSON-LD) --}}
        @include('admin.partials.seo-panel', [
            'seo'           => $post,
            'ogCol'         => 'og_image',
            'twCol'         => 'twitter_image_path',
            'baseUrl'       => url('/rituales'),
            'slug'          => old('slug', $post->slug ?? ''),
            'titleFallback' => ($post->title ?? 'Ritual') . ' | Belleza Áurea',
            'descFallback'  => 'Descripción del ritual…',
        ])

        {{-- Publish --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Publicación</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="status" name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Borrador</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archivado</option>
                    </select>
                </div>
                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Fecha de publicación</label>
                    <input type="datetime-local" id="published_at" name="published_at"
                           value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="author_name" class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                    <input type="text" id="author_name" name="author_name" value="{{ old('author_name', $post->author_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="schema_type" class="block text-sm font-medium text-gray-700 mb-1">Schema type</label>
                    <select id="schema_type" name="schema_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="BlogPosting" {{ old('schema_type', $post->schema_type) === 'BlogPosting' ? 'selected' : '' }}>BlogPosting</option>
                        <option value="Article" {{ old('schema_type', $post->schema_type) === 'Article' ? 'selected' : '' }}>Article</option>
                        <option value="NewsArticle" {{ old('schema_type', $post->schema_type) === 'NewsArticle' ? 'selected' : '' }}>NewsArticle</option>
                    </select>
                </div>
            </div>
            @if($post->reading_time)
                <p class="mt-4 text-xs text-gray-400">Tiempo de lectura estimado: {{ $post->reading_time }} min</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.blog.index') }}" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                Guardar cambios
            </button>
        </div>
    </form>

    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow { border: 1px solid #d1d5db; border-radius: 8px 8px 0 0; background: #f9fafb; }
        .ql-container.ql-snow { border: none; font-size: 14px; font-family: 'Montserrat', sans-serif; }
        .ql-editor { min-height: 360px; }
        .ql-editor h2 { font-size: 1.4em; font-weight: 700; margin: 1em 0 0.4em; }
        .ql-editor h3 { font-size: 1.15em; font-weight: 600; margin: 0.8em 0 0.3em; }
        .ql-editor p { margin-bottom: 0.6em; }
        .ql-editor ul, .ql-editor ol { margin-bottom: 0.6em; }
        .ql-editor blockquote { border-left: 4px solid #D9B56D; padding-left: 12px; color: #475569; }
    </style>

    <script>
        function blogForm() {
            return {
                title: @json(old('title', $post->title)),
                excerpt: @json(old('excerpt', $post->excerpt ?? '')),
                metaTitle: @json(old('meta_title', $post->meta_title ?? '')),
                metaDescription: @json(old('meta_description', $post->meta_description ?? '')),
                get slug() {
                    return this.title
                        .toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                }
            };
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var contentInput = document.getElementById('content');
            var editorEl = document.getElementById('quill-editor');
            if (!contentInput || !editorEl) return;

            var quill = new Quill(editorEl, {
                theme: 'snow',
                placeholder: 'Escribe el contenido del publicación...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'image'],
                        [{ 'align': [] }],
                        ['clean']
                    ]
                }
            });

            function syncContent() {
                var html = quill.root.innerHTML;
                if (html === '<p><br></p>') html = '';
                contentInput.value = html;
            }

            // Load existing content
            var existing = contentInput.value;
            if (existing && existing.trim().length > 0) {
                quill.root.innerHTML = existing;
            }

            quill.on('text-change', syncContent);

            // Targetear el form del blog (no el form de logout del layout).
            var form = editorEl.closest('form');
            if (form) {
                form.addEventListener('submit', syncContent);
            }
        });
    </script>
@endsection
