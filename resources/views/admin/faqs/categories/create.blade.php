@extends('layouts.admin')

@section('title', 'Nueva categoría de FAQ')
@section('page_title', 'Nueva categoría de FAQ')

@section('content')
    <form method="POST" action="{{ route('admin.faq-categories.store') }}" class="max-w-2xl space-y-6">
        @csrf
        @include('admin.faqs.categories._form')
    </form>
@endsection
