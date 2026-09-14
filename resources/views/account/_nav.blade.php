@php
    $wholesaleStatus = auth('customer')->user()?->wholesaler_status ?? 'none';
    $wholesaleLabels = [
        'none'     => 'Programa mayorista 🏪',
        'pending'  => 'Solicitud mayorista ⏳',
        'approved' => 'Mi cuenta mayorista ✨',
        'rejected' => 'Programa mayorista 🏪',
    ];
    $navItems = [
        ['route' => 'account.dashboard', 'label' => 'Mi cuenta',   'active' => request()->routeIs('account.dashboard')],
        ['route' => 'account.orders',    'label' => 'Mis pedidos', 'active' => request()->routeIs('account.orders') || request()->routeIs('account.order')],
        ['route' => 'account.wishlist',  'label' => 'Mis favoritos', 'active' => request()->routeIs('account.wishlist')],
        ['route' => 'account.loyalty',   'label' => 'Mis puntos ⭐', 'active' => request()->routeIs('account.loyalty')],
        ['route' => 'account.referrals', 'label' => 'Referidos 💛', 'active' => request()->routeIs('account.referrals')],
        ['route' => 'account.wholesale.request', 'label' => $wholesaleLabels[$wholesaleStatus] ?? 'Programa mayorista 🏪', 'active' => request()->routeIs('account.wholesale.*')],
        ['route' => 'account.addresses', 'label' => 'Mis direcciones', 'active' => request()->routeIs('account.addresses')],
        ['route' => 'account.profile',   'label' => 'Mis datos',   'active' => request()->routeIs('account.profile')],
    ];
@endphp

<nav aria-label="Menú de cuenta"
     style="background:#FFFFFF;border:1px solid #E5DCC9;border-radius:18px;padding:14px;box-shadow:0 18px 40px -30px rgba(120,92,44,.4);">
    <ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px;">
        @foreach($navItems as $item)
            <li>
                <a href="{{ route($item['route']) }}"
                   style="display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:12px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;text-decoration:none;transition:background .2s,color .2s;
                   @if($item['active']) background:linear-gradient(120deg,rgba(224,190,119,.20),rgba(217,181,109,.12));color:#8A6E2E;border-left:3px solid #D9B56D;padding-left:11px;
                   @else color:#6B6157;border-left:3px solid transparent; @endif">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $item['active'] ? '#D9B56D' : '#E5DCC9' }};flex-shrink:0;"></span>
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach

        <li style="margin-top:6px;padding-top:10px;border-top:1px solid #EFE7D8;">
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit"
                        style="width:100%;text-align:left;display:flex;align-items:center;gap:10px;padding:11px 14px;border:none;background:none;border-radius:12px;font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;color:#C97B6B;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='rgba(201,123,107,.08)'"
                        onmouseout="this.style.background='none'">
                    <span style="width:6px;height:6px;border-radius:50%;background:#E5C3BB;flex-shrink:0;"></span>
                    Cerrar sesión
                </button>
            </form>
        </li>
    </ul>
</nav>
