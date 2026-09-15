@extends('layouts.admin')

@section('title', 'Nueva FAQ')
@section('page_title', 'Nueva FAQ')

@section('content')
    <div class="max-w-lg bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <p class="text-gray-700 mb-4">Necesitas crear al menos una categoría antes de agregar preguntas.</p>
        <a href="{{ route('admin.faq-categories.create') }}"
           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Crear categoría
        </a>
    </div>
@endsection
