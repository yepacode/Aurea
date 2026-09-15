@extends('layouts.admin')

@section('title', 'Editar FAQ')
@section('page_title', 'Editar FAQ')

@section('content')
    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="max-w-3xl space-y-6">
        @csrf @method('PUT')
        @include('admin.faqs._form')
    </form>
@endsection
