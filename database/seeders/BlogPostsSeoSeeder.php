<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Carga 4 artículos de blog orientados a SEO long-tail colombiano.
 *
 * Se crean como BORRADOR (status = draft, published_at = null) para que la
 * dueña los revise desde /admin/blog antes de publicarlos.
 *
 * Idempotente: usa updateOrCreate por slug. El slug se calcula con Str::slug()
 * sobre el título porque el modelo BlogPost lo regenera así en el hook saving().
 */
class BlogPostsSeoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->posts() as $data) {
            $slug = Str::slug($data['title']);

            BlogPost::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, [
                    'slug'         => $slug,
                    'image'        => null,
                    'status'       => 'draft',
                    'published_at' => null,
                    'author_name'  => 'Belleza Áurea',
                    'schema_type'  => 'BlogPosting',
                    'noindex'      => false,
                    'nofollow'     => false,
                    'og_type'      => 'article',
                    'twitter_card' => 'summary_large_image',
                ])
            );

            $this->command?->info('Borrador listo: '.$slug);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function posts(): array
    {
        return [
            [
                'title'            => 'Cómo aplicar aceite de cutícula en casa: guía paso a paso',
                'category'         => 'unas',
                'focus_keyword'    => 'aceite de cutícula',
                'meta_title'       => 'Cómo aplicar aceite de cutícula en casa paso a paso | Áurea',
                'meta_description' => 'Guía paso a paso para aplicar aceite de cutícula en casa: cuándo usarlo, cuánto producto necesitas, cada cuánto repetirlo y qué errores evitar.',
                'meta_keywords'    => 'aceite de cutícula, cómo aplicar aceite de cutícula, cuidado de uñas en casa, manicure en casa',
                'excerpt'          => 'Dos minutos al día bastan. Te contamos cuándo aplicar el aceite de cutícula, cuánto usar y los errores que hacen que no se vea el resultado.',
                'og_title'         => 'Cómo aplicar aceite de cutícula en casa, paso a paso',
                'og_description'   => 'Cuándo aplicarlo, cuánto producto usar y los errores más comunes. Una guía sencilla para el cuidado diario de tus uñas.',
                'content'          => $this->postAceiteCuticula(),
            ],
            [
                'title'            => 'Esmalte semipermanente: cuánto dura de verdad y cómo hacerlo durar más',
                'category'         => 'unas',
                'focus_keyword'    => 'esmalte semipermanente',
                'meta_title'       => 'Esmalte semipermanente: cuánto dura de verdad | Áurea',
                'meta_description' => 'Cuánto dura realmente el esmalte semipermanente, por qué se levanta antes de tiempo y qué puedes hacer en casa para que cada aplicación dure más.',
                'meta_keywords'    => 'esmalte semipermanente, cuánto dura el semipermanente, cómo hacer durar el semipermanente, manicure semipermanente',
                'excerpt'          => '¿Dos semanas, tres, cuatro? Te contamos qué determina la duración real del esmalte semipermanente y cómo estirar cada aplicación sin maltratar la uña.',
                'og_title'         => 'Esmalte semipermanente: cuánto dura y cómo hacerlo durar más',
                'og_description'   => 'Preparación, sellado y cuidados en casa: lo que de verdad decide si tu semipermanente llega a la semana tres.',
                'content'          => $this->postSemipermanente(),
            ],
            [
                'title'            => 'Limas de uñas: qué significa el granulaje 100/180 y cuál usar en cada paso',
                'category'         => 'unas',
                'focus_keyword'    => 'granulaje de limas de uñas',
                'meta_title'       => 'Granulaje de limas de uñas: qué es 100/180 | Áurea',
                'meta_description' => 'Qué significa el granulaje de las limas de uñas, por qué una lima dice 100/180 y cuál corresponde a cada paso, desde la forma hasta el brillo.',
                'meta_keywords'    => 'granulaje de limas de uñas, lima 100/180, limas para uñas, lima de brillo, buffer',
                'excerpt'          => 'El número de la lima no es decoración: define cuánto material retira. Aprende qué significa el granulaje y cuál usar en cada paso del servicio.',
                'og_title'         => 'Granulaje de limas: qué significa 100/180 y cuándo usarlo',
                'og_description'   => 'Una guía clara del granulaje de las limas de uñas y de qué lima corresponde a cada paso, de la preparación al brillo final.',
                'content'          => $this->postLimas(),
            ],
            [
                'title'            => 'Rutina facial para clima cálido: cómo adaptarla en Bogotá, Medellín y la costa',
                'category'         => 'skincare',
                'focus_keyword'    => 'rutina facial para clima cálido',
                'meta_title'       => 'Rutina facial para clima cálido en Colombia | Áurea',
                'meta_description' => 'Cómo adaptar tu rutina facial para clima cálido y húmedo, y qué cambia entre Bogotá, Medellín y la costa colombiana. Guía práctica y sencilla.',
                'meta_keywords'    => 'rutina facial para clima cálido, rutina facial Colombia, skincare clima húmedo, cuidado facial Bogotá',
                'excerpt'          => 'La misma rutina no funciona igual en Bogotá que en Cartagena. Te mostramos qué ajustar según el clima de tu ciudad, sin cambiar todos tus productos.',
                'og_title'         => 'Rutina facial para clima cálido: Bogotá, Medellín y la costa',
                'og_description'   => 'Qué ajustar en tu rutina facial según el clima de tu ciudad, sin tener que cambiar todos tus productos.',
                'content'          => $this->postRutinaClima(),
            ],
        ];
    }

    private function postAceiteCuticula(): string
    {
        return <<<'HTML'
<p>Si compraste tu primer <strong>aceite de cutícula</strong> y terminó guardado en un cajón porque no sabías bien en qué momento usarlo, este artículo es para ti. Aplicarlo en casa toma menos de dos minutos y es uno de esos gestos pequeños que, con constancia, cambian por completo el aspecto de tus manos.</p>

<p>Acá te contamos qué hace el producto, cómo aplicarlo paso a paso y cuáles son los errores que hacen que mucha gente diga "a mí no me funcionó".</p>

<h2>Qué hace el aceite de cutícula (y qué no)</h2>

<p>La cutícula es esa franja de piel delgada que bordea la base de la uña. Es piel, y como toda piel se reseca: con el agua, con el jabón, con el gel antibacterial, con el aire acondicionado de la oficina.</p>

<p>Un aceite de cutícula es un cosmético hidratante. Su trabajo es dejar esa zona suave, flexible y con mejor apariencia. Cuando la piel del contorno está hidratada se ve menos levantada, menos blanca y menos propensa a esos padrastros que uno termina arrancando con los dientes.</p>

<p>Y ahora lo que no hace, porque también importa: no es un tratamiento médico, no reemplaza una consulta con un profesional de la salud si notas cambios raros en la uña, y no hace crecer la uña más rápido. Es cuidado cosmético, y como tal, su efecto se nota con uso continuo, no con una sola aplicación.</p>

<h2>Lo que necesitas antes de empezar</h2>

<ul>
  <li>Tu <a href="/productos/aceite-restaurador-de-cuticula-floral-15-ml-1223">aceite de cutícula</a>, preferiblemente con gotero o pincel aplicador.</li>
  <li>Manos limpias y secas.</li>
  <li>Opcional: un empujador de cutícula si vas a hacer también la parte de mantenimiento.</li>
  <li>Dos minutos sin afán. En serio, ese es el único requisito difícil.</li>
</ul>

<h2>Cómo aplicar aceite de cutícula en casa, paso a paso</h2>

<h3>1. Lava y seca bien las manos</h3>
<p>El aceite se absorbe mejor sobre piel limpia. Si tienes restos de crema, de esmalte o de removedor, la película que queda hace que el producto se quede encima sin penetrar.</p>

<h3>2. Aplica una gota por uña</h3>
<p>Una gota. No más. El error más común es echar media botella pensando que así funciona mejor: lo único que logras es sentir las manos grasosas y gastar producto. Ubica la gota justo en la base de la uña, donde la piel se encuentra con el borde.</p>

<h3>3. Masajea en círculos</h3>
<p>Con el pulgar o el índice de la otra mano, masajea la base de la uña con movimientos circulares durante unos cinco segundos. No lo hagas rápido: el masaje ayuda a repartir el producto y es parte del gesto.</p>

<h3>4. Sigue por los laterales</h3>
<p>Mucha gente aplica solo en la base y se olvida de los costados de la uña, que es justo donde aparecen los padrastros. Pasa el dedo por todo el contorno, de un lado al otro.</p>

<h3>5. Termina por debajo del borde libre</h3>
<p>Si te queda producto en el dedo, pásalo por debajo del borde libre de la uña. Esa zona también se reseca y casi nadie la atiende.</p>

<h3>6. Deja actuar y no laves de inmediato</h3>
<p>Dale al menos unos minutos antes de lavarte las manos. Por eso la hora ideal es antes de dormir: el producto se queda ahí toda la noche sin que lo arrastres con agua.</p>

<h2>Cada cuánto aplicarlo</h2>

<p>La respuesta corta: todos los días si puedes. La respuesta realista: por lo menos cinco veces por semana, y la noche es el mejor momento.</p>

<p>Si tienes las manos muy resecas, si trabajas con agua o con químicos, o si usas gel antibacterial varias veces al día, vale la pena una segunda aplicación en la mañana. Lleva el frasquito en el bolso y aplícalo como aplicarías un bálsamo labial: cuando te acuerdes.</p>

<blockquote>La constancia le gana al producto caro. Un aceite sencillo usado a diario se nota mucho más que uno costoso usado una vez al mes.</blockquote>

<h2>Errores comunes que arruinan el resultado</h2>

<ul>
  <li><strong>Cortar la cutícula con tijera en casa.</strong> Si vas a hacer mantenimiento, lo recomendable es ablandar, empujar con cuidado y retirar únicamente la piel muerta que se desprende sola. Cortar de más deja la zona irritada.</li>
  <li><strong>Aplicar sobre las manos mojadas.</strong> El agua y el aceite no se llevan bien; el producto se desliza y no se absorbe.</li>
  <li><strong>Usarlo solo cuando ya se ve mal.</strong> Es un producto de mantenimiento, no de rescate. Funciona como hábito.</li>
  <li><strong>Aplicarlo justo antes de esmaltar.</strong> El aceite deja una capa que impide que el esmalte se adhiera. Si vas a pintarte las uñas, aplícalo <em>después</em> de terminar.</li>
  <li><strong>Esperar resultados en tres días.</strong> La piel se renueva con su propio ritmo. Dale un par de semanas de uso diario antes de juzgar.</li>
</ul>

<h2>¿Y si ya uso semipermanente?</h2>

<p>Perfecto, el aceite se vuelve todavía más importante. Las uñas con semipermanente pasan por procesos de limado y remoción que resecan el contorno. Aplicar el aceite a diario mantiene la piel alrededor con mejor aspecto entre una cita y otra, y además el contraste de un esmalte impecable con un contorno descuidado se nota muchísimo.</p>

<p>Eso sí: aplícalo siempre al final, nunca antes del esmaltado.</p>

<h2>Cómo elegir tu aceite</h2>

<p>Fíjate en tres cosas sencillas:</p>

<ol>
  <li><strong>El aplicador.</strong> El gotero rinde más; el pincel es más cómodo para llevar en el bolso.</li>
  <li><strong>La textura.</strong> Si odias la sensación grasosa, busca fórmulas de absorción rápida.</li>
  <li><strong>El aroma.</strong> Suena superficial, pero si te gusta cómo huele, lo vas a usar. Y el uso es lo único que de verdad importa.</li>
</ol>

<p>En nuestra <a href="/productos?category=preparadores-y-finalizadores">categoría de preparadores y finalizadores</a> encuentras el aceite junto con el resto de productos que acompañan el cuidado de la uña, y en <a href="/productos?category=remocion-y-mantenimiento">remoción y mantenimiento</a> está lo que necesitas para la parte de ablandar y empujar.</p>

<h2>Un hábito de dos minutos</h2>

<p>No necesitas una rutina elaborada ni diez productos. Un aceite, una gota por uña y treinta segundos de masaje antes de dormir. Si lo dejas junto al cepillo de dientes o en la mesa de noche, lo vas a usar; si lo guardas en el fondo del cajón, ya sabes cómo termina la historia.</p>

<p>Tus manos están en todas tus fotos, en todas tus reuniones y en todos tus saludos. Dos minutos es poco a cambio de eso.</p>
HTML;
    }

    private function postSemipermanente(): string
    {
        return <<<'HTML'
<p>La pregunta llega todos los días por WhatsApp: ¿cuánto dura de verdad un <strong>esmalte semipermanente</strong>? La respuesta honesta es que depende, pero no de la suerte. Depende de cosas concretas que se pueden controlar, y por eso una misma persona puede pasar de dos semanas a casi cuatro cambiando apenas un par de hábitos.</p>

<p>Acá te contamos qué determina la duración real, por qué se levanta antes de tiempo y qué hacer en casa para estirar cada aplicación sin maltratar la uña.</p>

<h2>Cuánto dura de verdad el esmalte semipermanente</h2>

<p>En condiciones normales, un semipermanente bien aplicado se mantiene entre dos y tres semanas. Hay casos que llegan a cuatro y casos que no pasan de diez días.</p>

<p>Pero hay un detalle que casi nadie menciona: la duración no la decide solo el esmalte, la decide el <em>crecimiento de tu uña</em>. Aunque el color siga perfecto, a las tres semanas se ve una franja en la base que delata el tiempo. Por eso el cambio suele hacerse antes de que el producto falle.</p>

<h3>Lo que acorta la duración</h3>

<ul>
  <li>Manos que pasan mucho tiempo en agua: lavar loza, nadar, limpiar.</li>
  <li>Productos de aseo fuertes sin guantes.</li>
  <li>Usar las uñas como herramienta: abrir latas, despegar etiquetas, rascar.</li>
  <li>Uñas naturalmente muy flexibles o con mucha grasa natural en la lámina.</li>
  <li>Una preparación apurada antes de aplicar el color.</li>
</ul>

<h2>Por qué se levanta antes de tiempo</h2>

<p>Cuando un semipermanente se levanta en los bordes a los pocos días, casi nunca es culpa del color. El problema suele estar antes de que el color entrara en escena.</p>

<h3>La lámina no quedó bien preparada</h3>
<p>Si queda brillo natural, grasa o humedad sobre la uña, el producto no se adhiere como debería. La preparación no es un paso opcional: es el que sostiene todo lo demás.</p>

<h3>El producto tocó la piel</h3>
<p>Si el esmalte invade la cutícula o los laterales, ahí se crea un punto débil. Al mover el dedo, esa zona se despega y el levantamiento avanza hacia adentro.</p>

<h3>Capas demasiado gruesas</h3>
<p>Dos capas delgadas duran más que una gruesa. Una capa espesa no cura parejo y queda blanda por dentro, aunque por fuera se sienta dura.</p>

<h3>No se selló el borde libre</h3>
<p>Pasar el pincel por el filo del borde libre, en cada capa, es lo que evita que el agua entre por la punta. Es un gesto de dos segundos que cambia el resultado.</p>

<h2>Cómo hacer que tu esmalte semipermanente dure más</h2>

<h3>Antes: la preparación lo es todo</h3>

<ol>
  <li>Retira el brillo natural de la lámina con una lima suave, sin desgastar la uña.</li>
  <li>Empuja la cutícula y retira solo la piel muerta adherida a la uña.</li>
  <li>Deshidrata y limpia la superficie. Nada de crema ni aceite antes de esmaltar.</li>
  <li>Aplica capas delgadas y sella siempre el borde libre.</li>
</ol>

<p>Si estás armando tu kit, en la categoría de <a href="/productos?category=preparadores-y-finalizadores">preparadores y finalizadores</a> está justo lo que corresponde a esta etapa, y en <a href="/productos?category=limas">limas</a> encuentras las que necesitas para el paso de preparación.</p>

<h3>Durante la semana: hábitos que sí importan</h3>

<ul>
  <li><strong>Guantes para el aseo.</strong> Es el consejo más repetido porque es el que más funciona.</li>
  <li><strong>Aceite de cutícula a diario.</strong> Mantiene el contorno flexible y hace que la manicure se vea cuidada más tiempo. Aplícalo siempre después del esmaltado, nunca antes.</li>
  <li><strong>No uses las uñas como herramienta.</strong> Un golpe seco en el borde es suficiente para iniciar un levantamiento.</li>
  <li><strong>Evita el agua muy caliente prolongada.</strong> Los baños largos y el calor ablandan el sello de los bordes.</li>
  <li><strong>Si se levanta una esquina, no la jales.</strong> Jalar arrastra capas de la uña natural. Lima el filo y espera a la remoción adecuada.</li>
</ul>

<h2>Cuándo toca retirarlo</h2>

<p>Lo ideal es cambiar antes de que haya levantamientos visibles. Cuando una esquina se despega, queda un espacio entre el producto y la uña donde se acumula humedad, y eso es justo lo que no quieres.</p>

<p>La remoción debe hacerse con el método adecuado: ablandar el producto y retirarlo sin arrancar. Si sientes que tienes que hacer fuerza, algo está mal. Los productos de <a href="/productos?category=remocion-y-mantenimiento">remoción y mantenimiento</a> existen precisamente para que ese paso no maltrate la lámina.</p>

<h2>Semipermanente frente a otras opciones</h2>

<table>
  <thead>
    <tr><th>Opción</th><th>Duración típica</th><th>Para quién</th></tr>
  </thead>
  <tbody>
    <tr><td>Esmalte tradicional</td><td>2 a 5 días</td><td>Quien cambia de color seguido</td></tr>
    <tr><td><a href="/productos/esmalte-semipermanente-x-15ml-1001">Esmalte semipermanente</a></td><td>2 a 3 semanas</td><td>Quien quiere color estable sin cambiar de forma</td></tr>
    <tr><td>Sistemas de construcción</td><td>3 a 4 semanas con retoque</td><td>Quien quiere alargar o reforzar la uña</td></tr>
  </tbody>
</table>

<p>No hay una opción mejor que otra: hay una que se ajusta a tu ritmo. Si trabajas con las manos todo el día, el semipermanente suele ser el punto medio más cómodo.</p>

<h2>¿La uña descansa o no descansa?</h2>

<p>Es la duda eterna. Lo que sí podemos decir con tranquilidad es que la uña se ve y se siente mejor cuando la remoción se hace sin arrancar y cuando el contorno se mantiene hidratado entre aplicaciones. El maltrato que la gente le atribuye al semipermanente casi siempre viene de retirarlo a la brava, no del producto en sí.</p>

<p>Si notas la lámina delgada o sensible, dale un par de semanas de descanso con un buen aceite de cutícula y vuelve cuando se sienta bien. Y si ves algo que te preocupa de verdad en la uña, consulta con un profesional de la salud: eso no se resuelve con cosméticos.</p>

<h2>En resumen</h2>

<p>Un esmalte semipermanente dura entre dos y tres semanas, y buena parte de ese resultado se juega en los diez minutos de preparación. Capas delgadas, borde libre sellado, guantes para el aseo y aceite de cutícula a diario: con eso solo ya ganas varios días en cada aplicación.</p>

<p>Puedes ver todo lo relacionado con esmaltado en nuestra sección de <a href="/productos?category=unas">uñas</a>, donde está el surtido completo para el servicio.</p>
HTML;
    }

    private function postLimas(): string
    {
        return <<<'HTML'
<p>Agarras una lima, le das vuelta y ves un número: 100/180. Si nunca te lo explicaron, parece un código. En realidad es la información más útil que trae el producto, porque el <strong>granulaje de las limas de uñas</strong> es lo que define cuánto material retira y, por lo tanto, en qué paso del servicio se debe usar.</p>

<p>Esta guía es para manicuristas que están armando su kit y para quien quiere entender por qué la lima del supermercado deja las uñas deshilachadas.</p>

<h2>Qué significa el número de una lima</h2>

<p>El número indica el grano o granulaje: la cantidad de partículas abrasivas por unidad de superficie.</p>

<p>La regla es inversa y conviene aprendérsela de una vez: <strong>a menor número, más áspera la lima; a mayor número, más suave</strong>.</p>

<ul>
  <li>Una lima 80 es muy agresiva y retira material rápido.</li>
  <li>Una lima 180 es de trabajo general sobre uña natural.</li>
  <li>Una lima 240 apenas refina.</li>
  <li>Un buffer de 1000 o más ya solo pule.</li>
</ul>

<p>Dicho de otra forma: el número bajo trabaja, el número alto termina.</p>

<h2>Qué significa exactamente 100/180</h2>

<p>Cuando una lima trae dos números es porque tiene dos caras con granulajes distintos. Una <a href="/productos/lima-sponge-100180-1026">lima 100/180</a> tiene una cara de 100, para retirar material, y una de 180, para refinar y dar forma.</p>

<p>Es la combinación más versátil que existe y por eso es la que casi siempre se recomienda cuando alguien compra su primera lima profesional: resuelve dos pasos con una sola herramienta.</p>

<h3>La cara 100</h3>
<p>Áspera. Sirve para rebajar material con rapidez: acortar, reducir volumen, trabajar sobre producto. Sobre uña natural hay que usarla con mucha prudencia, porque retira más de lo que uno calcula.</p>

<h3>La cara 180</h3>
<p>Media. Es la que se usa para dar forma a la uña natural, emparejar el borde libre y refinar lo que dejó la cara anterior. Es la cara que más vas a usar en el día a día.</p>

<h2>Tabla rápida de granulajes</h2>

<table>
  <thead>
    <tr><th>Granulaje</th><th>Tacto</th><th>Uso principal</th></tr>
  </thead>
  <tbody>
    <tr><td>80 - 100</td><td>Muy áspero</td><td>Rebajar material, acortar, reducir volumen</td></tr>
    <tr><td>150 - 180</td><td>Medio</td><td>Dar forma, emparejar borde libre, uña natural</td></tr>
    <tr><td>220 - 240</td><td>Suave</td><td>Refinar, alisar superficie, retirar brillo con cuidado</td></tr>
    <tr><td>400 y más</td><td>Muy suave</td><td>Pulir, preparar para brillo</td></tr>
    <tr><td>1000 - 4000 (buffer)</td><td>Pulidor</td><td>Brillo espejo sobre uña natural</td></tr>
  </tbody>
</table>

<h2>Cuál usar en cada paso del servicio</h2>

<h3>Paso 1: acortar</h3>
<p>Si vas a reducir largo de forma considerable, usa cortaúñas o alicate primero. La lima sirve para ajustar los últimos milímetros, no para recorrer todo el camino. Para este ajuste va bien la cara de 100 en limas de dos caras, siempre con movimientos firmes y en una sola dirección.</p>

<h3>Paso 2: dar forma</h3>
<p>Acá entra la cara de 180. Lima desde el lateral hacia el centro, sin movimientos de ida y vuelta. El vaivén es justo lo que deja el borde deshilachado y provoca que la uña se parta después.</p>

<p>Una <a href="/productos/lima-zebra-con-regla-1027-a">lima con regla</a> ayuda mucho cuando buscas simetría entre las diez uñas, que es de las cosas más difíciles de lograr a ojo.</p>

<h3>Paso 3: emparejar la superficie</h3>
<p>Para alisar irregularidades de la lámina se usa un granulaje suave, de 220 o más. Acá la mano debe ir ligera: la idea es emparejar, no desgastar.</p>

<h3>Paso 4: retirar el brillo antes de esmaltar</h3>
<p>Cuando vas a aplicar semipermanente necesitas quitar el brillo natural para que el producto se adhiera. Se hace con un granulaje suave y con pasadas ligeras y parejas. Si sientes calor en la uña, estás presionando demasiado.</p>

<h3>Paso 5: brillo final</h3>
<p>Si el servicio termina en uña natural sin color, el último paso es el pulido. Una <a href="/productos/lima-de-brillo-profesional-1207">lima de brillo</a> deja un acabado espejo muy bonito. Eso sí, no la uses en cada servicio: pulir también desgasta.</p>

<h2>Forma del bloque: no todo es el número</h2>

<p>Además del granulaje, fíjate en el formato:</p>

<ul>
  <li><strong>Lima recta.</strong> La clásica, buena para trabajar bordes y formas definidas.</li>
  <li><strong>Lima banana o curva.</strong> Se adapta al contorno natural de la uña.</li>
  <li><strong>Lima sponge o de esponja.</strong> Tiene un núcleo acolchado que amortigua la presión. Más cómoda y más indulgente con la mano que todavía está aprendiendo.</li>
  <li><strong>Taco o buffer.</strong> Bloque de varias caras para alisar y pulir.</li>
</ul>

<h2>Higiene y vida útil</h2>

<p>Una lima no dura para siempre y esto es clave si atiendes clientas:</p>

<ul>
  <li>Las limas de cartón son de un solo uso por persona. No se pueden desinfectar de verdad.</li>
  <li>Las limas con base lavable se limpian y desinfectan entre servicios siguiendo las indicaciones del fabricante.</li>
  <li>Cuando el grano ya no muerde, cámbiala. Una lima gastada obliga a hacer presión y la presión es la que maltrata la uña.</li>
  <li>Guárdalas separadas por granulaje. Suena obvio, pero ahorra tiempo en cada servicio.</li>
</ul>

<h2>Qué comprar si estás armando tu kit</h2>

<p>Para empezar con lo esencial:</p>

<ol>
  <li>Una 100/180 de dos caras para forma y rebaje.</li>
  <li>Una de granulaje suave (220 o más) para refinar.</li>
  <li>Un buffer o lima de brillo para el acabado.</li>
</ol>

<p>Con esas tres resuelves la gran mayoría de los servicios. Puedes ver el surtido completo en nuestra <a href="/productos?category=limas">categoría de limas</a>, y si estás montando el kit desde cero vale la pena mirar también <a href="/productos?category=unas">la sección de uñas</a>.</p>

<h2>Lo que hay que recordar</h2>

<p>Número bajo, lima áspera. Número alto, lima suave. 100/180 significa dos caras, una para trabajar y otra para terminar. Y la presión siempre debe ser menor de lo que crees: la lima hace el trabajo, no tu mano.</p>

<p>Entender el granulaje es lo que separa una manicure que se ve bien de una que además dura bien.</p>
HTML;
    }

    private function postRutinaClima(): string
    {
        return <<<'HTML'
<p>Una <strong>rutina facial para clima cálido</strong> no es la misma que funciona en una ciudad fría, y en Colombia eso se nota más que en casi cualquier parte: puedes salir de Bogotá a catorce grados y aterrizar en Cartagena a treinta y dos el mismo día.</p>

<p>La buena noticia es que no necesitas cambiar todos tus productos cada vez que viajas o te mudas. Casi siempre basta con ajustar texturas y cantidades. Acá te contamos qué cambiar y qué dejar igual.</p>

<h2>Por qué el clima cambia tu rutina</h2>

<p>La piel responde al ambiente. En clima seco pierde agua más rápido y se siente tirante. En clima húmedo y cálido la piel suda, la grasa natural se mueve distinto y los productos pesados se sienten incómodos encima.</p>

<p>Esto no significa que tu tipo de piel cambie al cambiar de ciudad. Significa que la misma piel necesita una textura diferente para sentirse cómoda. Es un ajuste de formato, no de identidad.</p>

<h2>Bogotá: frío, altura y aire seco</h2>

<p>En Bogotá el tema no es el calor sino la combinación de temperatura baja, viento y altura. Es un ambiente que reseca, y encima hay radiación solar fuerte incluso en días nublados, que es el error número uno de quienes viven en la sabana.</p>

<h3>Qué ajustar</h3>

<ul>
  <li><strong>Texturas más ricas.</strong> Si en la costa usabas gel, acá una crema se siente mucho mejor.</li>
  <li><strong>Limpieza más suave.</strong> Los limpiadores que dejan la cara "chillando de limpia" son los peores aliados en clima seco.</li>
  <li><strong>Labios y contorno.</strong> Son las primeras zonas que avisan cuando el ambiente reseca.</li>
  <li><strong>Protector solar igual, sin excusas.</strong> Que esté nublado y haga frío no cambia la exposición.</li>
</ul>

<p>Un tónico suave como el <a href="/productos/agua-de-rosas-x-250ml-1229">agua de rosas</a> funciona bien acá para refrescar después de limpiar sin resecar.</p>

<h2>Medellín y el Eje Cafetero: templado y agradable</h2>

<p>Clima templado con humedad media. Es, en términos de piel, el escenario más cómodo del país: ni reseca como la sabana ni satura como la costa.</p>

<h3>Qué ajustar</h3>

<ul>
  <li><strong>Texturas intermedias.</strong> Emulsiones y lociones ligeras suelen ser el punto exacto.</li>
  <li><strong>Atención a los cambios de piso térmico.</strong> Si subes y bajas de temperatura en el día, escucha cómo se siente la piel y ajusta la cantidad, no el producto.</li>
  <li><strong>Exfoliación moderada.</strong> Una o dos veces por semana, sin exagerar. Un <a href="/productos/exfoliante-250ml-2039">exfoliante suave</a> es suficiente.</li>
</ul>

<h2>La costa: calor, humedad y sal</h2>

<p>Cartagena, Santa Marta, Barranquilla, San Andrés. Acá el reto es que todo se siente pesado, el sudor es constante y además entra la sal del mar y el cloro de la piscina.</p>

<h3>Qué ajustar</h3>

<ul>
  <li><strong>Pasa a texturas ligeras.</strong> Geles, fluidos, lociones acuosas. Las cremas densas se sienten incómodas y se mueven con el sudor.</li>
  <li><strong>Doble limpieza en la noche.</strong> Entre protector solar, sudor y maquillaje, la piel acumula más que en otras ciudades.</li>
  <li><strong>Reaplica el protector solar.</strong> Con sudor y agua se va. Reaplicar durante el día es la parte que más gente olvida.</li>
  <li><strong>Enjuaga después del mar o la piscina.</strong> La sal y el cloro resecan y quedan ahí si no los retiras.</li>
  <li><strong>Menos capas.</strong> Tres productos bien aplicados funcionan mejor que siete amontonados.</li>
</ul>

<h2>La base que no cambia en ninguna ciudad</h2>

<p>Independiente del clima, hay tres pasos que sostienen cualquier rutina:</p>

<ol>
  <li><strong>Limpiar.</strong> En la noche siempre; en la mañana, según cómo amanezca tu piel.</li>
  <li><strong>Hidratar.</strong> Toda piel necesita agua, incluso la grasa. Lo que cambia es la textura, no la necesidad.</li>
  <li><strong>Proteger.</strong> Protector solar en el día, todos los días, en todas las ciudades.</li>
</ol>

<p>Todo lo demás (tónicos, sérums, mascarillas, aceites) es construcción sobre esa base. Si la base no está firme, lo de arriba no sostiene nada.</p>

<h2>Cómo saber si tu rutina necesita ajuste</h2>

<p>Tu piel te avisa. Presta atención a estas señales:</p>

<ul>
  <li><strong>Se siente tirante después de limpiar:</strong> tu limpiador es muy fuerte para el clima actual.</li>
  <li><strong>El producto se siente pesado o "queda encima":</strong> necesitas una textura más ligera.</li>
  <li><strong>Brilla más de lo normal a media mañana:</strong> el clima húmedo pide reducir capas, no sumar productos matificantes.</li>
  <li><strong>Se descama en zonas puntuales:</strong> falta hidratación o sobra exfoliación.</li>
</ul>

<p>Un consejo práctico para quien viaja seguido: arma un neceser con una versión ligera y una versión rica de tu hidratante. Cambiar solo esa pieza suele ser suficiente para adaptarte sin cargar media droguería.</p>

<h2>Errores frecuentes en clima cálido</h2>

<ul>
  <li><strong>Saltarse la hidratación porque la piel brilla.</strong> Brillo no es lo mismo que hidratación; son cosas distintas.</li>
  <li><strong>Exfoliar todos los días para "controlar la grasa".</strong> Es contraproducente y deja la piel sensible.</li>
  <li><strong>Cambiar toda la rutina de golpe.</strong> Si cambias cinco productos a la vez y algo no te cae bien, no sabes cuál fue.</li>
  <li><strong>Usar protector solar solo en la playa.</strong> El día a día en la ciudad también cuenta.</li>
</ul>

<h2>Empieza por lo simple</h2>

<p>Si estás arrancando, no te compliques: un limpiador suave, un hidratante acorde a tu clima y un protector solar. Con eso ya tienes una rutina que funciona en Bogotá, en Medellín y en Santa Marta, cambiando apenas la textura del hidratante.</p>

<p>En nuestra <a href="/productos?category=rostro-y-piel">sección de rostro y piel</a> encuentras los productos para cada paso. Y si tienes una condición de piel específica o algo que te preocupe, lo mejor siempre es consultarlo con un profesional de la salud antes de armar tu rutina.</p>

<p>Tu piel no necesita más productos. Necesita los correctos para el clima en el que vives.</p>
HTML;
    }
}
