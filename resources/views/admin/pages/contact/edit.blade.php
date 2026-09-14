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

    @php
        $__waHours = $page->whatsappHours();
        $__waDayLabels = \App\Models\ContactPageSetting::DAY_LABELS_ES;
    @endphp

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

        {{-- ═══════════ WIDGET WHATSAPP ═══════════ --}}
        <div id="whatsapp-widget" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4">
            <button type="button" @click="toggle('whatsapp')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left">
                <h3 class="text-base font-semibold text-gray-900">Widget flotante de WhatsApp</h3>
                <svg :class="openSection === 'whatsapp' && 'rotate-180'" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
            <div x-show="openSection === 'whatsapp'" x-collapse class="px-6 pb-6 space-y-5">
                <p class="text-xs text-gray-500 -mt-1">
                    Configura el popup del widget: mensajes de bienvenida (en línea / fuera de horario), horario por día
                    (zona <strong>America/Bogotá</strong>) y visibilidad. El indicador <em>En línea</em> se calcula automáticamente.
                </p>

                {{-- Encender / apagar --}}
                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                    <input type="hidden" name="whatsapp_widget_enabled" value="0">
                    <input type="checkbox" name="whatsapp_widget_enabled" value="1"
                           {{ $page->whatsapp_widget_enabled ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                    <span class="text-sm text-gray-800">Mostrar widget flotante en la tienda</span>
                </label>

                {{-- Mensajes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje de bienvenida — En línea</label>
                        <textarea name="whatsapp_welcome_online" rows="3"
                                  placeholder="¡Hola! Somos Belleza Áurea. Escríbenos y te respondemos en minutos."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->whatsapp_welcome_online }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Se muestra cuando estamos dentro del horario configurado.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Autoresponse — Fuera de horario</label>
                        <textarea name="whatsapp_welcome_offline" rows="3"
                                  placeholder="¡Hola! Ahora estamos fuera de horario. Escríbenos y te respondemos apenas abramos."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $page->whatsapp_welcome_offline }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Se muestra cuando el widget detecta que estamos fuera del horario configurado.</p>
                    </div>
                </div>

                {{-- Horario semanal --}}
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Horario de atención (America/Bogotá)</h4>
                    <p class="text-xs text-gray-500 mb-3">Marca los días activos y define apertura/cierre en formato 24h.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pr-3 font-medium">Día</th>
                                    <th class="py-2 pr-3 font-medium">Activo</th>
                                    <th class="py-2 pr-3 font-medium">Abre</th>
                                    <th class="py-2 pr-3 font-medium">Cierra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($__waHours as $__day => $__slot)
                                    <tr class="border-b last:border-b-0">
                                        <td class="py-2 pr-3 text-gray-800">{{ $__waDayLabels[$__day] }}</td>
                                        <td class="py-2 pr-3">
                                            {{-- hidden 0 + checkbox 1 permite que el navegador envíe siempre un valor --}}
                                            <input type="hidden" name="whatsapp_hours[{{ $__day }}][enabled]" value="0">
                                            <input type="checkbox" name="whatsapp_hours[{{ $__day }}][enabled]" value="1"
                                                   {{ $__slot['enabled'] ? 'checked' : '' }}
                                                   class="w-4 h-4 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                        </td>
                                        <td class="py-2 pr-3">
                                            <input type="time" name="whatsapp_hours[{{ $__day }}][open]"
                                                   value="{{ $__slot['open'] }}"
                                                   class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </td>
                                        <td class="py-2 pr-3">
                                            <input type="time" name="whatsapp_hours[{{ $__day }}][close]"
                                                   value="{{ $__slot['close'] }}"
                                                   class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-2 rounded-lg border border-yellow-200 bg-yellow-50 px-3 py-2 text-xs text-yellow-800">
                    Estado actual (según horario configurado):
                    @if($page->isWhatsappOnline())
                        <strong class="text-green-700">En línea</strong>
                    @else
                        <strong class="text-gray-600">Fuera de horario</strong>
                    @endif
                    · Resumen: {{ $page->whatsappHoursSummary() }}
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
