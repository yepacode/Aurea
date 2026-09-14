<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | Belleza Áurea</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/brand/favicon-32.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Responsive admin: sidebar overlay on mobile, resized paddings, no horizontal overflow --}}
    <style>
        html,body{max-width:100%;overflow-x:hidden;}
        .admin-main-wrap{min-width:0;}
        @media (max-width: 767.98px){
            .admin-sidebar{
                position:fixed !important;
                top:0;left:0;bottom:0;z-index:60;
                width:260px !important;
                transform:translateX(-100%);
                transition:transform .28s ease;
                box-shadow:0 10px 40px rgba(0,0,0,.25);
            }
            .admin-sidebar.is-open{transform:translateX(0);}
            .admin-backdrop{
                position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:55;
            }
            .admin-main-wrap{width:100%;}
            .admin-header{padding:12px 14px !important;}
            .admin-header h1{font-size:15px !important;}
            .admin-main{padding:14px !important;}
        }
        @media (max-width: 380px){
            .admin-main{padding:10px !important;}
        }

        /* ─── A11y: focus visible dorado (WCAG 2.4.7) ─── */
        input:focus,select:focus,textarea:focus,button:focus,a:focus,[role="button"]:focus{
            outline:2px solid #BE9A53 !important;
            outline-offset:2px !important;
            box-shadow:0 0 0 4px rgba(217,181,109,.25) !important;
        }
        input:focus:not(:focus-visible),select:focus:not(:focus-visible),textarea:focus:not(:focus-visible),button:focus:not(:focus-visible),a:focus:not(:focus-visible),[role="button"]:focus:not(:focus-visible){
            outline:none !important;
            box-shadow:none !important;
        }
    </style>
    @stack('head')
</head>

<body class="bg-gray-100 text-gray-900 font-body min-h-screen"
      x-data="{ sidebarOpen: window.innerWidth >= 768, isMobile: window.innerWidth < 768 }"
      x-init="window.addEventListener('resize', () => { isMobile = window.innerWidth < 768; if (!isMobile && !sidebarOpen) sidebarOpen = true; })">
    <div class="flex min-h-screen">
        {{-- Mobile backdrop --}}
        <div x-show="isMobile && sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="admin-backdrop md:hidden"></div>

        {{-- Sidebar --}}
        <aside :class="{ 'w-64': sidebarOpen && !isMobile, 'w-16': !sidebarOpen && !isMobile, 'is-open': isMobile && sidebarOpen }"
               class="admin-sidebar text-white transition-all duration-300 flex flex-col shrink-0"
               style="background:#2E2A26;">
            <div class="p-4" style="border-bottom:1px solid rgba(232,204,146,0.15);">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('img/brand/favicon-192.png') }}" alt="Belleza Áurea" class="h-9 w-9 object-contain shrink-0">
                    <span x-show="sidebarOpen" style="font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:#D9B56D;line-height:1;">Áurea</span>
                    <span x-show="sidebarOpen" class="uppercase tracking-wider" style="font-size:10px;color:rgba(247,243,237,0.55);">admin</span>
                </a>
            </div>

            <nav class="flex-1 py-4 px-2 overflow-y-auto">
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/10' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    <span x-show="sidebarOpen">Dashboard</span>
                </a>

                {{-- ── GRUPO: Catálogo ── --}}
                <div x-show="sidebarOpen" class="px-3 pt-5 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Catálogo</p>
                </div>
                <div x-show="!sidebarOpen" class="my-3 mx-3 border-t border-white/10"></div>

                <div class="space-y-0.5">
                    <a href="{{ route('admin.products.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.products.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Productos</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Categorías</span>
                    </a>
                    <a href="{{ route('admin.brands.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.brands.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Marcas</span>
                    </a>
                    <a href="{{ route('admin.reviews.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.reviews.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Reseñas</span>
                    </a>
                </div>

                {{-- ── GRUPO: Ventas ── --}}
                <div x-show="sidebarOpen" class="px-3 pt-5 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Ventas</p>
                </div>
                <div x-show="!sidebarOpen" class="my-3 mx-3 border-t border-white/10"></div>

                <div class="space-y-0.5">
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Pedidos</span>
                    </a>
                    <a href="{{ route('admin.customers.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.customers.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Clientes</span>
                    </a>
                    <a href="{{ route('admin.stock-notifications.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.stock-notifications.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                        </svg>
                        <span x-show="sidebarOpen">Avisos de stock</span>
                    </a>
                    <a href="{{ route('admin.leads.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.leads.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-1.183 1.98l-6.478 3.489a2.25 2.25 0 0 1-2.134 0L3.432 19.67A2.25 2.25 0 0 1 2.25 17.69V6.31a2.25 2.25 0 0 1 1.183-1.98l6.478-3.488a2.25 2.25 0 0 1 2.134 0l6.478 3.489a2.25 2.25 0 0 1 1.183 1.98V17.69Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Leads</span>
                    </a>
                    <a href="{{ route('admin.discount-codes.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.discount-codes.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Descuentos</span>
                    </a>
                    <a href="{{ route('admin.nps.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.nps.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M7 14l3-3 4 4 5-6"/>
                        </svg>
                        <span x-show="sidebarOpen">NPS 📊</span>
                    </a>
                </div>

                {{-- ── GRUPO: Contenido ── --}}
                <div x-show="sidebarOpen" class="px-3 pt-5 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Contenido</p>
                </div>
                <div x-show="!sidebarOpen" class="my-3 mx-3 border-t border-white/10"></div>

                <div class="space-y-0.5">
                    {{-- Hero --}}
                    <a href="{{ route('admin.hero.edit') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.hero.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/>
                        </svg>
                        <span x-show="sidebarOpen">Hero</span>
                    </a>

                    {{-- Secciones (home) --}}
                    <a href="{{ route('admin.pages.home.edit') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.pages.home.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Secciones</span>
                    </a>

                    {{-- Infografías --}}
                    <a href="{{ route('admin.infographics.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.infographics.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Infografías</span>
                    </a>

                    {{-- Publicaciones (blog) — colapsable con Portada de la página --}}
                    <div x-data="{ openBlog: {{ request()->routeIs('admin.blog.*', 'admin.pages.blog.*', 'admin.pages.blue-light.*') ? 'true' : 'false' }} }">
                        <button type="button" @click="openBlog = !openBlog"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.blog.*', 'admin.pages.blog.*', 'admin.pages.blue-light.*') ? 'bg-white/10' : '' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>
                                </svg>
                                <span x-show="sidebarOpen">Publicaciones</span>
                            </div>
                            <svg x-show="sidebarOpen" :class="openBlog && 'rotate-180'" class="w-4 h-4 text-white/40 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div x-show="openBlog && sidebarOpen" x-collapse class="ml-8 space-y-0.5">
                            <a href="{{ route('admin.blog.index') }}"
                               class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-colors text-xs {{ request()->routeIs('admin.blog.*') ? 'bg-white/10 text-white' : 'text-white/60' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.blog.*') ? 'bg-yellow-400' : 'bg-white/30' }}"></span>
                                <span>Publicaciones</span>
                            </a>
                            <a href="{{ route('admin.pages.blue-light.edit') }}"
                               class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-colors text-xs {{ request()->routeIs('admin.pages.blue-light.*') ? 'bg-white/10 text-white' : 'text-white/60' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.pages.blue-light.*') ? 'bg-yellow-400' : 'bg-white/30' }}"></span>
                                <span>Portada de la página</span>
                            </a>
                        </div>
                    </div>

                    {{-- Página productos --}}
                    <a href="{{ route('admin.pages.lentes.edit') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.pages.lentes.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Página productos</span>
                    </a>

                    {{-- Quiz --}}
                    <a href="{{ route('admin.pages.quiz.edit') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.pages.quiz.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Página Quiz</span>
                    </a>

                    {{-- Contacto --}}
                    <a href="{{ route('admin.pages.contact.edit') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.pages.contact.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                        <span x-show="sidebarOpen">Página Contacto</span>
                    </a>

                    {{-- Envíos y devoluciones --}}
                    <a href="{{ route('admin.pages.shipping-returns.edit') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.pages.shipping-returns.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/>
                        </svg>
                        <span x-show="sidebarOpen">Envíos y devol.</span>
                    </a>

                    {{-- Testimonios --}}
                    <a href="{{ route('admin.testimonials.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.testimonials.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Testimonios</span>
                    </a>
                </div>

                {{-- ── GRUPO: Configuración ── --}}
                <div x-show="sidebarOpen" class="px-3 pt-5 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Configuración</p>
                </div>
                <div x-show="!sidebarOpen" class="my-3 mx-3 border-t border-white/10"></div>

                <div class="space-y-0.5">
                    {{-- Envíos (rates) --}}
                    <a href="{{ route('admin.shipping.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.shipping.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                        </svg>
                        <span x-show="sidebarOpen">Envíos</span>
                    </a>

                    {{-- Transferencia bancaria --}}
                    <a href="{{ route('admin.bank-transfer.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.bank-transfer.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Transferencia bancaria</span>
                    </a>

                    {{-- SEO --}}
                    <a href="{{ route('admin.seo.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.seo.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                        <span x-show="sidebarOpen">SEO</span>
                    </a>

                    {{-- Pasarela ePayco --}}
                    <a href="{{ route('admin.payments.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.payments.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                        </svg>
                        <span x-show="sidebarOpen">Pasarela ePayco</span>
                    </a>

                    {{-- Analítica (GA4 + Meta Pixel) --}}
                    <a href="{{ route('admin.analytics.index') }}"
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm {{ request()->routeIs('admin.analytics.*') ? 'bg-white/10' : '' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M7 15l4-4 4 4 5-6"/>
                        </svg>
                        <span x-show="sidebarOpen">Analítica</span>
                    </a>
                </div>
            </nav>

            {{-- User / Logout --}}
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-secondary flex items-center justify-center text-sm font-bold shrink-0">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div x-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-xs text-white/60 hover:text-white">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="admin-main-wrap flex-1 flex flex-col min-w-0" style="max-width:100%;">
            {{-- Top bar --}}
            <header class="admin-header bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between" style="min-width:0;gap:8px;">
                <div class="flex items-center space-x-3 min-w-0" style="min-width:0;">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 shrink-0" aria-label="Menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800 truncate" style="min-width:0;">@yield('page_title', 'Dashboard')</h1>
                </div>
                <a href="{{ route('home') }}" target="_blank" class="text-sm text-secondary hover:underline shrink-0 whitespace-nowrap">Ver tienda &rarr;</a>
            </header>

            {{-- Page content --}}
            <main class="admin-main flex-1 p-6" style="min-width:0;max-width:100%;">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         x-transition class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         x-transition class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
