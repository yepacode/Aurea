@extends('layouts.admin')

@section('title', 'Editar categoría de FAQ')
@section('page_title', 'Editar categoría de FAQ')

@section('content')
    <form method="POST" action="{{ route('admin.faq-categories.update', $category) }}" class="max-w-2xl space-y-6">
        @csrf @method('PUT')
        @include('admin.faqs.categories._form')
    </form>
@endsection
