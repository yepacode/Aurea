@extends('layouts.admin')

@section('title', 'Reseñas')
@section('page_title', 'Reseñas')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @php
        $stars = fn ($n) => str_repeat('★', $n) . str_repeat('☆', 5 - $n);
    @endphp

    {{-- Pendientes de aprobar --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Pendientes de aprobar
            @if($pending->count())<span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full ml-1">{{ $pending->count() }}</span>@endif
        </h2>
        @if($pending->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-sm text-gray-400">No hay reseñas pendientes.</div>
        @else
        <div class="space-y-3">
            @foreach($pending as $r)
            <div class="bg-white rounded-xl border border-amber-200 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm"><span style="color:#D9B56D;letter-spacing:1px;">{{ $stars($r->rating) }}</span>
                           <strong class="text-gray-900 ml-1">{{ $r->author_name }}</strong>
                           <span class="text-gray-400 text-xs ml-1">· {{ optional($r->product)->name }}</span></p>
                        @if($r->title)<p class="text-sm font-medium text-gray-800 mt-1">{{ $r->title }}</p>@endif
                        @if($r->comment)<p class="text-sm text-gray-600 mt-0.5">{{ $r->comment }}</p>@endif
                        @if($r->image_path)<a href="{{ asset('storage/'.$r->image_path) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/'.$r->image_path) }}" alt="Foto de la reseña" class="mt-2 rounded-lg border border-gray-200" style="width:72px;height:72px;object-fit:cover;"></a>@endif
                        <p class="text-xs text-gray-400 mt-1">{{ $r->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">@csrf @method('PUT')
                            <button class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg">Aprobar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('¿Eliminar esta reseña?')">@csrf @method('DELETE')
                            <button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Publicadas --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Publicadas</h2>
        @if($approved->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-sm text-gray-400">Aún no hay reseñas publicadas.</div>
        @else
        <div class="space-y-3">
            @foreach($approved as $r)
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm"><span style="color:#D9B56D;letter-spacing:1px;">{{ $stars($r->rating) }}</span>
                           <strong class="text-gray-900 ml-1">{{ $r->author_name }}</strong>
                           <span class="text-gray-400 text-xs ml-1">· {{ optional($r->product)->name }}</span></p>
                        @if($r->title)<p class="text-sm font-medium text-gray-800 mt-1">{{ $r->title }}</p>@endif
                        @if($r->comment)<p class="text-sm text-gray-600 mt-0.5">{{ $r->comment }}</p>@endif
                        @if($r->image_path)<a href="{{ asset('storage/'.$r->image_path) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/'.$r->image_path) }}" alt="Foto de la reseña" class="mt-2 rounded-lg border border-gray-200" style="width:72px;height:72px;object-fit:cover;"></a>@endif
                        <p class="text-xs text-gray-400 mt-1">{{ $r->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('admin.reviews.unapprove', $r) }}">@csrf @method('PUT')
                            <button class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-medium rounded-lg">Ocultar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('¿Eliminar esta reseña?')">@csrf @method('DELETE')
                            <button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-lg">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $approved->links() }}</div>
        @endif
    </div>
</div>
@endsection
