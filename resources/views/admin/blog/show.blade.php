@extends('layouts.admin')

@section('title', $post->title)
@section('page_title', 'Artículo')

@section('content')
    <div class="max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al listado</a>
            <a href="{{ route('admin.blog.edit', $post) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">Editar</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-4">
                @if($post->published_at && $post->published_at->isPast())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Publicado</span>
                @elseif($post->published_at)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Programado</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Borrador</span>
                @endif
                @if($post->published_at)
                    <span class="text-xs text-gray-500">{{ $post->published_at->format('d/m/Y H:i') }}</span>
                @endif
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $post->title }}</h1>
            <p class="text-xs text-gray-400 font-mono mb-6">slug: {{ $post->slug }}</p>

            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="" class="w-full max-h-64 object-cover rounded-lg border border-gray-200 mb-6">
            @endif

            @if($post->excerpt)
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                    <p class="text-sm font-medium text-gray-500 mb-1">Extracto</p>
                    <p class="text-sm text-gray-700">{{ $post->excerpt }}</p>
                </div>
            @endif

            <div class="prose prose-sm max-w-none text-gray-700">
                {!! \App\Support\SafeHtml::sanitize($post->content) !!}
            </div>
        </div>

        @if($post->meta_title || $post->meta_description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">SEO</h2>
                <dl class="space-y-3 text-sm">
                    @if($post->meta_title)
                        <div>
                            <dt class="text-gray-500">Meta título</dt>
                            <dd class="mt-1 font-medium">{{ $post->meta_title }}</dd>
                        </div>
                    @endif
                    @if($post->meta_description)
                        <div>
                            <dt class="text-gray-500">Meta descripción</dt>
                            <dd class="mt-1 text-gray-600">{{ $post->meta_description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        @endif
    </div>
@endsection
