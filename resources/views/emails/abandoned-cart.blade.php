<x-mail::message>
# ¿Se te quedó algo? 🛍️

Hola{{ optional($cart->customer)->name ? ' '.$cart->customer->name : '' }}, guardamos los productos que dejaste en tu carrito de **Belleza Áurea** por si quieres completar tu compra:

<x-mail::table>
| Producto | Cant. | Precio |
|:---------|:-----:|-------:|
@foreach($items as $item)
| {{ $item['name'] }} | {{ $item['qty'] }} | ${{ number_format($item['unit_price'] * $item['qty'], 0, ',', '.') }} |
@endforeach
</x-mail::table>

<x-mail::button :url="$url" color="primary">
Volver a mi carrito
</x-mail::button>

Los productos vuelan… ¡no te quedes sin los tuyos! 💛<br>
**Belleza Áurea**
</x-mail::message>
