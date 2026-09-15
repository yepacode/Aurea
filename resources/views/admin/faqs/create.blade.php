@extends('layouts.admin')

@section('title', 'Nueva FAQ')
@section('page_title', 'Nueva FAQ')

@section('content')
    <form method="POST" action="{{ route('admin.faqs.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @include('admin.faqs._form')
    </form>
@endsection
