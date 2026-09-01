@extends('layouts.admin')

@section('title', 'Editar página de contacto')
@section('page_title', 'Página de contacto')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
     x-data="{
        openSection: 'hero',
        toggle(s) { this.openSection = this.openSection === s ? null : s; }
     }">

    <p class="text-sm text-gray-500 mb-6">Edita el contenido de la página de contacto. Los cambios se reflejan inmediatamente.</p>

    <form method="POST" action="{{ route('admin.pages.contact.update') }}">
        @method('PUT')
        @csrf

        {{-- ═══════════ HERO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('hero')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Hero</h3>
                <svg :class="openSection === 'hero' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'hero'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="hero_title" value="{{ $page->hero_title }}"
                           placeholder="Contáctanos"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtítulo</label>
                    <textarea name="hero_subtitle" rows="2"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                              placeholder="¿Tienes dudas? Estamos aquí para ayudarte.">{{ $page->hero_subtitle }}</textarea>
                </div>
            </div>
        </div>

        {{-- ═══════════ DATOS DE CONTACTO ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('info')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Datos de contacto</h3>
                <svg :class="openSection === 'info' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'info'" x-collapse class="px-6 pb-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ $page->email }}"
                               placeholder="contacto@bellezaaurea.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="phone" value="{{ $page->phone }}"
                               placeholder="+52 (33) 1234-5678"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp (link tarjeta)</label>
                        <input type="text" name="whatsapp" value="{{ $page->whatsapp }}"
                               placeholder="https://wa.me/5233..."
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Link completo que se usa solo en la tarjeta de la página /contacto.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horario de atención</label>
                        <input type="text" name="schedule" value="{{ $page->schedule }}"
                               placeholder="Lunes a viernes, 9:00 a 18:00"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                {{-- Botón flotante / CTA blog --}}
                <div class="border-t pt-4 mt-2">
                    <h4 class="text-sm font-semibold text-gray-800 mb-1">Botón flotante de WhatsApp y CTA del blog</h4>
                    <p class="text-xs text-gray-500 mb-3">Estos dos campos controlan el <strong>botón flotante verde</strong> que aparece en todas las páginas y el <strong>botón "WhatsApp"</strong> que cierra los artículos del blog. Si los dejas vacíos, se usa el número y mensaje por defecto.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de WhatsApp</label>
                            <input type="text" name="whatsapp_number" value="{{ $page->whatsapp_number }}"
                                   placeholder="528146964477"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                            <p class="text-xs text-gray-400 mt-1">Solo dígitos, con código de país (sin <code>+</code>, sin espacios). Ej: <code>528146964477</code>.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje pre-llenado</label>
                            <textarea name="whatsapp_message" rows="2"
                                      placeholder="Hola, me interesa información sobre los productos de Belleza Áurea"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->whatsapp_message }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Texto que verá el cliente cargado en WhatsApp al hacer clic.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════ REDES SOCIALES ═══════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('social')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Redes sociales</h3>
                <svg :class="openSection === 'social' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'social'" x-collapse class="px-6 pb-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                    <input type="url" name="instagram_url" value="{{ $page->instagram_url }}"
                           placeholder="https://instagram.com/bellezaaurea"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                    <input type="url" name="facebook_url" value="{{ $page->facebook_url }}"
                           placeholder="https://facebook.com/bellezaaurea"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">TikTok</label>
                    <input type="url" name="tiktok_url" value="{{ $page->tiktok_url }}"
                           placeholder="https://tiktok.com/@bellezaaurea"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
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
@endsection
