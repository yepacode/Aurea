<x-mail::message>
# ¿Cómo te fue con tu compra? 🌸

Hola{{ $order->customer && $order->customer->name ? ' '.$order->customer->name : '' }}, esperamos que estés disfrutando tu pedido de **Belleza Áurea**.

Tu opinión nos ayuda muchísimo (y ayuda a otras clientas a elegir mejor). ¿Nos regalas unos segundos para dejar tu reseña?

<x-mail::button :url="$url" color="primary">
Dejar mi reseña
</x-mail::button>

¡Gracias por confiar en nosotras! 💛<br>
**Belleza Áurea**
</x-mail::message>
