<x-mail::message>
# ¡Buenas noticias! 🌸

El producto **{{ $product->name }}** que estabas esperando ya está **disponible** de nuevo en Belleza Áurea.

Corre antes de que se agote otra vez.

<x-mail::button :url="$url" color="primary">
Ver producto
</x-mail::button>

Con cariño,<br>
**Belleza Áurea**
</x-mail::message>
