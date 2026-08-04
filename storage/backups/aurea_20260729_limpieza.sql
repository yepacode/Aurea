-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: aurea
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bank_transfer_settings`
--

DROP TABLE IF EXISTS `bank_transfer_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_transfer_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bank_transfer_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_transfer_settings`
--

LOCK TABLES `bank_transfer_settings` WRITE;
/*!40000 ALTER TABLE `bank_transfer_settings` DISABLE KEYS */;
INSERT INTO `bank_transfer_settings` VALUES (1,'bank_name','','2026-05-23 00:28:29','2026-05-23 00:28:29'),(2,'account_holder','','2026-05-23 00:28:29','2026-05-23 00:28:29'),(3,'clabe','','2026-05-23 00:28:29','2026-05-23 00:28:29'),(4,'account_number','','2026-05-23 00:28:29','2026-05-23 00:28:29'),(5,'reference_instructions','Usa tu número de pedido como referencia','2026-05-23 00:28:29','2026-05-23 00:28:29'),(6,'additional_notes','','2026-05-23 00:28:29','2026-05-23 00:28:29');
/*!40000 ALTER TABLE `bank_transfer_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_page_settings`
--

DROP TABLE IF EXISTS `blog_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hero_label` varchar(255) DEFAULT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_title_line2` varchar(255) DEFAULT NULL,
  `hero_title_accent` varchar(255) DEFAULT NULL,
  `hero_subtitle` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_page_settings`
--

LOCK TABLES `blog_page_settings` WRITE;
/*!40000 ALTER TABLE `blog_page_settings` DISABLE KEYS */;
INSERT INTO `blog_page_settings` VALUES (1,NULL,NULL,NULL,NULL,NULL,1,'2026-06-16 08:27:52','2026-06-16 08:27:52');
/*!40000 ALTER TABLE `blog_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured_image_alt` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `focus_keyword` varchar(255) DEFAULT NULL,
  `category` varchar(32) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `reading_time` int(10) unsigned NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `author_name` varchar(255) NOT NULL DEFAULT 'nuvion glass',
  `schema_type` varchar(255) NOT NULL DEFAULT 'BlogPosting',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'El ritual de las 3 capas: cómo construir una rutina facial atemporal','ritual-tres-capas-rutina-facial-atemporal','<h2>Menos productos, más constancia</h2>\n<p>La mejor rutina facial no es la más larga, sino la que puedes mantener todos los días. En Belleza Áurea creemos en el <strong>ritual de las 3 capas</strong>: tónico, sérum y crema. Tres pasos, dos minutos, resultados visibles en 28 días.</p>\n\n<h2>Capa 1 — Tónico: el reset de tu piel</h2>\n<p>Después de limpiar el rostro, la piel queda ligeramente desequilibrada. El <strong>Tónico Floral Reequilibrante</strong> con agua de rosas y niacinamida restaura el pH, refresca y prepara la piel para absorber lo que viene.</p>\n<p><em>Aplicación:</em> dos pulsaciones en algodón reutilizable, pasa por todo el rostro con gestos ascendentes.</p>\n\n<h2>Capa 2 — Sérum: el activo concentrado</h2>\n<p>Aquí está la diferencia real. El <strong>Sérum Áureo</strong> con vitamina C estabilizada y rosa mosqueta concentra los activos que iluminan, unifican el tono y suavizan líneas finas. Es el paso más transformador de tu rutina.</p>\n<p><em>Aplicación:</em> 3 gotas en la palma, presiona suavemente sobre el rostro y cuello. Espera 30 segundos antes del siguiente paso.</p>\n\n<h2>Capa 3 — Crema: el sello protector</h2>\n<p>La <strong>Crema Hidratante Botánica</strong> con manteca de karité y ácido hialurónico sella todo el ritual y mantiene la hidratación por 24 horas. Forma una capa ligera, no grasa, ideal incluso bajo maquillaje.</p>\n<p><em>Aplicación:</em> una avellana de producto, distribuye con masaje circular ascendente.</p>\n\n<h2>El cuarto paso opcional: aceite o mascarilla nocturna</h2>\n<p>Una o dos noches por semana, intensifica con el <strong>Aceite Esencial de Rosa</strong> o la <strong>Mascarilla Nocturna de Oro</strong>. La piel descansada absorbe mejor estos tratamientos.</p>\n\n<h2>Consejos para hacer del ritual un hábito</h2>\n<ul>\n  <li><strong>Hazlo a la misma hora</strong> cada mañana y noche. La consistencia gana a la perfección.</li>\n  <li><strong>Crea un espacio bonito</strong>: una bandeja con tus productos, una vela, una toalla suave.</li>\n  <li><strong>Tómate tiempo</strong>: dos minutos de cuidado real valen más que cinco minutos apurados.</li>\n  <li><strong>Confía en los 28 días</strong>: la piel se renueva en ese ciclo. Antes no juzgues los resultados.</li>\n</ul>\n\n<h2>¿Por dónde empezar?</h2>\n<p>Si nunca tuviste una rutina, comienza con el <strong>Ritual Esencial</strong>: los tres productos en formato regalo, con instrucciones detalladas. Tu piel notará la diferencia desde la primera semana.</p>','Tónico, sérum y crema. Tres gestos cuidados que transforman cualquier piel sin complicaciones. Te enseñamos a estructurar tu ritual diario.',NULL,NULL,'Ritual de 3 capas: rutina facial diaria | Belleza Áurea','Aprende a construir tu rutina facial perfecta con tres pasos clave: tónico, sérum y crema. Belleza natural y atemporal.','rutina facial diaria',NULL,NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-05-16 00:28:29','2026-05-23 00:28:29','2026-05-23 00:28:29'),(2,'5 ingredientes botánicos que sí funcionan (y por qué)','cinco-ingredientes-botanicos-que-si-funcionan','<h2>Naturaleza con respaldo científico</h2>\n<p>\"Natural\" no significa \"efectivo\". Por eso en Belleza Áurea seleccionamos solo activos botánicos cuya eficacia está respaldada por estudios in vitro y clínicos. Te contamos los cinco que están en la mayoría de nuestras fórmulas.</p>\n\n<h2>1. Rosa mosqueta</h2>\n<p>Aceite prensado en frío de la rosa <em>Rosa rubiginosa</em>. Rico en ácidos grasos esenciales (omega 3, 6 y 9) y vitamina A natural. <strong>Estimula la regeneración celular, suaviza cicatrices y mejora la elasticidad</strong>. Está en nuestro Sérum Áureo y Aceite Esencial de Rosa.</p>\n\n<h2>2. Niacinamida (Vitamina B3)</h2>\n<p>Aunque suena a laboratorio, la niacinamida es un activo derivado de plantas. Estudios publicados en el <em>Journal of Cosmetic Dermatology</em> muestran que en concentraciones de 4-5% <strong>minimiza poros, controla brillo, mejora la barrera cutánea y unifica el tono</strong>. Pilar de nuestro Tónico Floral.</p>\n\n<h2>3. Ácido hialurónico vegetal</h2>\n<p>Obtenido por fermentación de plantas (no animal). Molécula que retiene hasta 1000 veces su peso en agua, <strong>hidratando profundamente sin sensación grasa</strong>. Mantiene la piel rellena y suave todo el día. Activo clave de nuestra Crema Hidratante Botánica.</p>\n\n<h2>4. Manteca de karité</h2>\n<p>Extraída de la nuez del árbol <em>Vitellaria paradoxa</em> de África occidental. Rica en vitaminas A, E, F y ácidos grasos. <strong>Nutre, calma irritaciones y crea una barrera protectora natural</strong> contra agresiones ambientales.</p>\n\n<h2>5. Vitamina C estabilizada</h2>\n<p>La forma estable de la vitamina C (ascorbil glucósido o tetraisopalmitato) es uno de los antioxidantes más estudiados. <strong>Ilumina la piel, neutraliza radicales libres, estimula colágeno y reduce manchas</strong>. Concentración óptima entre 10-15%, como en nuestro Sérum Áureo.</p>\n\n<h2>Lo que evitamos</h2>\n<ul>\n  <li><strong>Parabenos</strong>: conservantes con evidencia de disrupción hormonal.</li>\n  <li><strong>Sulfatos agresivos</strong> (SLS, SLES): resecan e irritan la barrera cutánea.</li>\n  <li><strong>Fragancias sintéticas</strong>: pueden generar sensibilidad. Usamos aceites esenciales en bajas dosis.</li>\n  <li><strong>Aceites minerales</strong>: oclusivos sin aportar nutrición real.</li>\n</ul>\n\n<h2>Cómo leer una etiqueta</h2>\n<p>Los ingredientes se listan de mayor a menor concentración. Si el activo \"estrella\" aparece al final, está en concentración decorativa. En Belleza Áurea siempre ves los activos clave en los <strong>primeros cinco lugares</strong> de la lista INCI.</p>','Rosa mosqueta, niacinamida, ácido hialurónico, karité y vitamina C. La ciencia detrás de los activos botánicos que transforman tu piel.',NULL,NULL,'5 ingredientes botánicos que funcionan en skincare | Belleza Áurea','Conoce los 5 ingredientes botánicos con evidencia científica que sí transforman tu piel: rosa mosqueta, niacinamida, hialurónico, karité y vitamina C.','ingredientes botánicos skincare',NULL,NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-05-19 00:28:29','2026-05-23 00:28:29','2026-05-23 00:28:29'),(3,'Cómo elegir tu perfume signature: la guía áurea','como-elegir-perfume-signature-guia','<h2>Tu perfume habla antes que tú</h2>\n<p>Un perfume bien elegido es una declaración silenciosa de quién eres. No se trata de seguir tendencias, sino de encontrar el aroma que te representa. Esta guía te ayuda a hacerlo con criterio.</p>\n\n<h2>Las 7 familias olfativas (en simple)</h2>\n<ol>\n  <li><strong>Florales</strong>: rosa, jazmín, peonía. Femeninos, elegantes, atemporales.</li>\n  <li><strong>Cítricos</strong>: bergamota, limón, mandarina. Frescos, energéticos, ideales para el día.</li>\n  <li><strong>Orientales</strong>: vainilla, ámbar, especias. Cálidos, envolventes, perfectos de noche.</li>\n  <li><strong>Amaderados</strong>: sándalo, cedro, vetiver. Sobrios, masculinos o unisex.</li>\n  <li><strong>Chipres</strong>: musgo de roble, bergamota, pachuli. Sofisticados y misteriosos.</li>\n  <li><strong>Fougère</strong>: lavanda, geranio, cumarina. Frescos y aromáticos.</li>\n  <li><strong>Gourmand</strong>: caramelo, chocolate, café. Dulces, jóvenes, divertidos.</li>\n</ol>\n\n<h2>La pirámide olfativa: salida, corazón, fondo</h2>\n<p>Todo buen perfume tiene tres fases:</p>\n<ul>\n  <li><strong>Notas de salida (0-15 min)</strong>: lo primero que hueles. Suelen ser cítricos o hierbas. Las más volátiles.</li>\n  <li><strong>Notas de corazón (15 min - 3 h)</strong>: el alma del perfume. Florales, especias suaves.</li>\n  <li><strong>Notas de fondo (3 h en adelante)</strong>: la base que persiste sobre tu piel. Maderas, ámbar, almizcle.</li>\n</ul>\n<p><em>El Eau de Parfum Áurea sigue exactamente esta estructura: salida cítrica de bergamota y mandarina, corazón floral de rosa y jazmín, fondo de vainilla y ámbar dorado. Una sinfonía pensada para durar 8-10 horas.</em></p>\n\n<h2>Cómo probar un perfume correctamente</h2>\n<ol>\n  <li><strong>No huelas más de 3 fragancias por visita.</strong> Tu nariz se satura.</li>\n  <li><strong>Aplica en la piel, no en la blotter</strong>: la química personal cambia todo.</li>\n  <li><strong>Espera al menos 20 minutos</strong> antes de juzgar — sentirás las notas de corazón.</li>\n  <li><strong>Pruébalo varios días</strong>: lo que ames en la tienda puede aburrirte en una semana, y viceversa.</li>\n</ol>\n\n<h2>Tips para que tu perfume dure más</h2>\n<ul>\n  <li>Aplica sobre <strong>piel hidratada</strong>: las moléculas se adhieren mejor a la piel con crema.</li>\n  <li><strong>Pulsos calientes</strong>: muñecas, detrás de las orejas, base del cuello, hueco del codo.</li>\n  <li><strong>No frotes</strong> las muñecas: rompes la estructura de las notas.</li>\n  <li><strong>Capas</strong>: usa una crema con perfume neutro debajo para extender la duración.</li>\n</ul>\n\n<h2>¿Por qué Eau de Parfum Áurea funciona en cualquier ocasión?</h2>\n<p>La fórmula combina frescura cítrica para el día, corazón floral para la tarde y fondo cálido para la noche. <strong>Es un perfume que evoluciona contigo durante el día</strong>, sin necesidad de tener tres botellas distintas.</p>\n<p>Aroma elegante, no invasivo, atemporal. Pensado para mujeres que valoran la sutileza como forma de distinción.</p>','Tu perfume es tu firma invisible. Te explicamos las familias olfativas, cómo probar un perfume correctamente y por qué Eau de Parfum Áurea funciona en cualquier ocasión.',NULL,NULL,'Cómo elegir tu perfume signature | Belleza Áurea','Guía completa para elegir tu perfume firma: familias olfativas, notas de salida, corazón y fondo. Tips para probarlo correctamente.','cómo elegir perfume',NULL,NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-05-22 00:28:29','2026-05-23 00:28:29','2026-05-23 00:28:29'),(4,'El ritual de piel: guía completa','ritual-de-piel','<p>Cuidar tu piel es el primer paso de todo ritual de belleza. Aquí te contamos cómo armar una rutina sencilla que de verdad funcione, sin complicarte.</p><h2>Los pasos esenciales</h2><p>Una buena rutina no necesita mil productos: necesita constancia y los pasos correctos.</p><ul><li><strong>Limpia</strong> tu piel mañana y noche con un producto suave.</li><li><strong>Hidrata</strong> con la piel aún húmeda para sellar mejor la humedad.</li><li><strong>Protege</strong> con protector solar cada día, incluso en interiores.</li></ul><h2>Míralo en video</h2><p>Un video lo explica mejor que mil palabras:</p><video controls playsinline style=\"width:100%;border-radius:12px;margin:1rem 0;\"><source src=\"/intro/intro.mp4\" type=\"video/mp4\"></video><blockquote>Tip: menos es más. Construye tu rutina de a poco y sé constante.</blockquote><p>¿Lista para empezar? Explora los productos que te pueden acompañar en tu ritual diario.</p>','Cómo armar una rutina de piel sencilla que de verdad funcione — paso a paso, con video.',NULL,NULL,NULL,NULL,NULL,'piel',NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-07-29 03:11:52','2026-07-29 03:11:52','2026-07-29 03:11:52'),(5,'La galería de uñas: color que es arte','ritual-de-unas','<p>El secreto de un manicure impecable está en la preparación. Sigue estos pasos para un acabado profesional que rinde caja tras caja.</p><h2>Prepara, aplica, sella</h2><ul><li><strong>Prepara la uña</strong>: empuja la cutícula y desengrasa la superficie.</li><li>Aplica una <strong>capa base</strong> para proteger y que el color dure.</li><li>Trabaja en <strong>capas finas</strong> y sella la punta.</li></ul><p>Aquí puedes subir fotos de tus diseños, tutoriales en video y más contenido desde el panel.</p>','Un buen manicure empieza antes del esmalte. Aprende a lograr un acabado que dura.',NULL,NULL,NULL,NULL,NULL,'unas',NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-07-29 03:11:52','2026-07-29 03:11:52','2026-07-29 03:11:52'),(6,'El tocador: la precisión del lujo','el-tocador','<p>Unas herramientas bien cuidadas duran más y dan mejores resultados. Estos son nuestros consejos para tu tocador.</p><ul><li><strong>Lava tus brochas</strong> cada semana con jabón suave.</li><li>Guarda todo <strong>seco y en su lugar</strong>.</li><li><strong>Desinfecta</strong> pinzas y cortaúñas entre usos.</li></ul>','Tus herramientas son la mitad del resultado. Aprende a cuidarlas.',NULL,NULL,NULL,NULL,NULL,'tocador',NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-07-29 03:11:52','2026-07-29 03:11:52','2026-07-29 03:11:52'),(7,'El toque de color: maquillaje que realza','ritual-de-maquillaje','<p>El maquillaje realza lo que ya eres. Con estos trucos lograrás un acabado natural que dura todo el día.</p><ul><li>Empieza con la <strong>piel preparada</strong> e hidratada.</li><li>Usa <strong>primer</strong> para que dure más.</li><li>Difumina siempre con <strong>luz natural</strong>.</li></ul>','El broche de oro de tu ritual. Trucos para un maquillaje natural que dura.',NULL,NULL,NULL,NULL,NULL,'maquillaje',NULL,NULL,NULL,NULL,1,'published','Belleza Áurea','BlogPosting','2026-07-29 03:11:52','2026-07-29 03:11:52','2026-07-29 03:11:52');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blue_light_page_settings`
--

DROP TABLE IF EXISTS `blue_light_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blue_light_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hero_title_prefix` varchar(255) DEFAULT NULL,
  `hero_title_accent` varchar(255) DEFAULT NULL,
  `hero_title_suffix` varchar(255) DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `science_label` varchar(255) DEFAULT NULL,
  `science_title` varchar(255) DEFAULT NULL,
  `science_paragraph1` text DEFAULT NULL,
  `science_paragraph2` text DEFAULT NULL,
  `symptoms_label` varchar(255) DEFAULT NULL,
  `symptoms_title` varchar(255) DEFAULT NULL,
  `symptoms_subtitle` text DEFAULT NULL,
  `symptoms_cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`symptoms_cards`)),
  `protection_label` varchar(255) DEFAULT NULL,
  `protection_title` varchar(255) DEFAULT NULL,
  `protection_description` text DEFAULT NULL,
  `shield_percentage` varchar(255) DEFAULT NULL,
  `shield_label` varchar(255) DEFAULT NULL,
  `shield_sublabel` varchar(255) DEFAULT NULL,
  `protection_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`protection_benefits`)),
  `profiles_label` varchar(255) DEFAULT NULL,
  `profiles_title` varchar(255) DEFAULT NULL,
  `profiles_subtitle` text DEFAULT NULL,
  `profiles_cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`profiles_cards`)),
  `faq_label` varchar(255) DEFAULT NULL,
  `faq_title` varchar(255) DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `compare_label` varchar(255) DEFAULT NULL,
  `compare_title` varchar(255) DEFAULT NULL,
  `compare_without_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`compare_without_items`)),
  `compare_with_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`compare_with_items`)),
  `compare_metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`compare_metrics`)),
  `compare_sources` text DEFAULT NULL,
  `compare_btn_text` varchar(255) DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_subtitle` text DEFAULT NULL,
  `cta_btn_text` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blue_light_page_settings`
--

LOCK TABLES `blue_light_page_settings` WRITE;
/*!40000 ALTER TABLE `blue_light_page_settings` DISABLE KEYS */;
INSERT INTO `blue_light_page_settings` VALUES (1,'Rituales de ','belleza',NULL,'Pequeños gestos que se vuelven costumbre. Cuidar tus uñas, tu piel y tu espacio no es rutina — es un ritual. Estos son los nuestros.',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-06-16 08:27:46','2026-07-30 03:33:55');
/*!40000 ALTER TABLE `blue_light_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `banner_path` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `country_origin` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`),
  KEY `brands_is_active_is_featured_sort_order_index` (`is_active`,`is_featured`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Wuhao Cosmetics','wuhao-cosmetics',NULL,NULL,'K-beauty con activos fermentados y texturas innovadoras.',NULL,NULL,'Corea',1,1,0,NULL,NULL,'2026-05-23 07:09:32','2026-05-23 07:09:32'),(2,'La Rose Botanique','la-rose-botanique',NULL,NULL,'Skincare artesanal con extractos botánicos de Provenza.',NULL,NULL,'Francia',1,1,1,NULL,NULL,'2026-05-23 07:09:32','2026-05-23 07:09:32'),(3,'Áurea Naturals','aurea-naturals',NULL,NULL,'Ingredientes amazónicos de comercio justo.',NULL,NULL,'Colombia',1,1,2,NULL,NULL,'2026-05-23 07:09:32','2026-05-23 07:09:32'),(4,'Bella Italia','bella-italia',NULL,NULL,'Cosmética italiana con aceite de oliva y uva.',NULL,NULL,'Italia',1,1,3,NULL,NULL,'2026-05-23 07:09:32','2026-05-23 07:09:32'),(5,'Sage & Honey','sage-honey',NULL,NULL,'Fórmulas limpias certificadas USDA Organic.',NULL,NULL,'EE.UU.',1,1,4,NULL,NULL,'2026-05-23 07:09:32','2026-05-23 07:09:32');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type_filter` varchar(255) DEFAULT NULL,
  `promo_2x1` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Uñas Modernas','unas-modernas',NULL,NULL,0,NULL,1,'2026-05-23 03:00:11','2026-07-30 04:28:53'),(2,'Preparadores y finalizadores','preparadores-y-finalizadores',NULL,NULL,0,NULL,2,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(3,'Decoracion','decoracion',NULL,NULL,0,NULL,3,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(4,'Equipos electricos','equipos-electricos',NULL,NULL,0,NULL,4,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(5,'Bledos','bledos',NULL,NULL,0,NULL,5,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(6,'Pinceleria','pinceleria',NULL,NULL,0,NULL,6,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(7,'Herramientas manuales','herramientas-manuales',NULL,NULL,0,NULL,7,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(8,'Limas','limas',NULL,NULL,0,NULL,8,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(9,'Uñas','unas',NULL,NULL,0,NULL,9,'2026-05-23 03:00:11','2026-05-23 03:00:11'),(10,'Construccion y Esculpido','construccion-y-esculpido',NULL,NULL,0,NULL,10,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(11,'Seguridad e Higiene','seguridad-e-higiene',NULL,NULL,0,NULL,11,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(12,'Remocion y Mantenimiento','remocion-y-mantenimiento',NULL,NULL,0,NULL,12,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(13,'Brocas','brocas',NULL,NULL,0,NULL,13,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(14,'Rostro y Piel','rostro-y-piel',NULL,NULL,0,NULL,14,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(15,'Depilacion','depilacion',NULL,NULL,0,NULL,15,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(16,'Tijeras','tijeras',NULL,NULL,0,NULL,16,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(17,'Gorros','gorros',NULL,NULL,0,NULL,17,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(18,'Cepillos','cepillos',NULL,NULL,0,NULL,18,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(19,'Peluqueria','peluqueria',NULL,NULL,0,NULL,19,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(20,'Pestañas','pestanas',NULL,NULL,0,NULL,20,'2026-05-23 03:00:12','2026-05-23 03:00:12'),(21,'Esmaltes','esmaltes','Esmaltes premium',NULL,0,'categories/Z41bS55acAwIiWmdZHvyuhVs1md0Rut0I91c7USj.jpg',1,'2026-05-23 03:21:51','2026-05-23 08:19:47'),(22,'Combos','combos','Combos y sets de productos con precio especial.',NULL,0,NULL,99,'2026-07-28 20:44:24','2026-07-28 20:44:24');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_page_settings`
--

DROP TABLE IF EXISTS `contact_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(32) DEFAULT NULL,
  `whatsapp_message` text DEFAULT NULL,
  `schedule` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_page_settings`
--

LOCK TABLES `contact_page_settings` WRITE;
/*!40000 ALTER TABLE `contact_page_settings` DISABLE KEYS */;
INSERT INTO `contact_page_settings` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-05-23 00:35:30','2026-05-23 00:35:30');
/*!40000 ALTER TABLE `contact_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'María Prueba Áurea','maria.prueba@bellezaaurea.com','3001234567','Calle 45 #23-15, Barrio Cabecera',NULL,'Santander','680001','2026-07-29 06:00:31','2026-07-29 06:00:31');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discount_codes`
--

DROP TABLE IF EXISTS `discount_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discount_codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT NULL,
  `max_uses` int(10) unsigned DEFAULT NULL,
  `times_used` int(10) unsigned NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discount_codes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discount_codes`
--

LOCK TABLES `discount_codes` WRITE;
/*!40000 ALTER TABLE `discount_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `discount_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hero_settings`
--

DROP TABLE IF EXISTS `hero_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hero_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `media_type` enum('video','image','gradient') NOT NULL DEFAULT 'gradient',
  `media_path` varchar(255) DEFAULT NULL,
  `hero_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hero_images`)),
  `overlay_opacity` decimal(3,2) NOT NULL DEFAULT 0.55,
  `video_position` tinyint(3) unsigned NOT NULL DEFAULT 50,
  `eyebrow_text` varchar(255) NOT NULL DEFAULT 'Protección de luz azul',
  `title_line1` varchar(255) NOT NULL DEFAULT 'Lentes que cuidan',
  `title_line2` varchar(255) NOT NULL DEFAULT 'tus ojos de las',
  `title_line3` varchar(255) NOT NULL DEFAULT 'pantallas',
  `title_highlight_word` varchar(255) NOT NULL DEFAULT 'pantallas',
  `subtitle` text DEFAULT NULL,
  `badge_text` varchar(255) DEFAULT NULL,
  `btn_primary_text` varchar(255) NOT NULL DEFAULT 'Ver lentes',
  `btn_primary_url` varchar(255) NOT NULL DEFAULT '/lentes',
  `btn_secondary_text` varchar(255) NOT NULL DEFAULT '¿Qué es la luz azul?',
  `btn_secondary_url` varchar(255) NOT NULL DEFAULT '/que-es-la-luz-azul',
  `trust_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`trust_items`)),
  `stat1_number` varchar(255) NOT NULL DEFAULT '2x1',
  `stat1_label` varchar(255) NOT NULL DEFAULT 'en todos los lentes',
  `stat2_number` varchar(255) NOT NULL DEFAULT '6',
  `stat2_label` varchar(255) NOT NULL DEFAULT 'modelos disponibles',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_settings`
--

LOCK TABLES `hero_settings` WRITE;
/*!40000 ALTER TABLE `hero_settings` DISABLE KEYS */;
INSERT INTO `hero_settings` VALUES (1,'video','hero/F56iy9CwGQ84bEaSRYzdKXoIHjJIqArnH9SvaM2P.mp4','[]',0.05,50,'Distribuidora oficial · Colombia','Tu belleza,','tu esencia.','','todo','Insumos para nail art, peluquería y estética + cremas, jabones, mantequillas y skincare de marca. Atendemos a profesionales y a quienes buscan calidad para su día a día.','Envío gratis desde $200.000 · Pedido mínimo no requerido','Ver catálogo completo','/productos','Marcas que distribuimos','/marcas','[{\"icon\":\"\\ud83d\\ude9a\",\"text\":\"Env\\u00edo nacional 24\\u201348 h\"},{\"icon\":\"\\ud83d\\udc8e\",\"text\":\"Precios mayoristas + venta al detal\"},{\"icon\":\"\\ud83c\\udf3f\",\"text\":\"Skincare y cosm\\u00e9tica natural\"},{\"icon\":\"\\u2713\",\"text\":\"Marcas oficiales con respaldo\"},{\"icon\":\"\\u2605\",\"text\":\"Asesor\\u00eda personalizada\"}]','322+','productos en stock','21','categorías premium',1,'2026-05-23 00:28:29','2026-07-29 22:58:53');
/*!40000 ALTER TABLE `hero_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `home_page_settings`
--

DROP TABLE IF EXISTS `home_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `home_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categories_label` varchar(255) NOT NULL DEFAULT 'Categorías',
  `categories_title` varchar(255) NOT NULL DEFAULT 'Encuentra tus lentes ideales',
  `categories_subtitle` varchar(255) NOT NULL DEFAULT 'Con o sin graduación, tenemos el modelo perfecto para ti.',
  `category_cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`category_cards`)),
  `catalog_label` varchar(255) NOT NULL DEFAULT 'Catálogo',
  `catalog_title` varchar(255) NOT NULL DEFAULT 'Nuestros lentes',
  `catalog_subtitle` varchar(255) NOT NULL DEFAULT 'Todos con filtro de luz azul y promoción 2×1.',
  `promo_label` varchar(255) NOT NULL DEFAULT 'Promoción',
  `promo_title` varchar(255) NOT NULL DEFAULT '2×1 en todos los lentes',
  `star_product_id` bigint(20) unsigned DEFAULT NULL,
  `promo_description` text DEFAULT NULL,
  `promo_price` varchar(255) NOT NULL DEFAULT '$499.90',
  `promo_price_note` varchar(255) NOT NULL DEFAULT 'por par · el segundo es gratis',
  `promo_btn_text` varchar(255) NOT NULL DEFAULT 'Aprovecha ahora',
  `promo_background` varchar(255) DEFAULT NULL,
  `benefits_label` varchar(255) NOT NULL DEFAULT 'Beneficios',
  `benefits_title` varchar(255) NOT NULL DEFAULT '¿Por qué elegir nuvion?',
  `benefits_subtitle` varchar(255) NOT NULL DEFAULT 'Tecnología que cuida tu visión. Diseño que querrás usar todo el día.',
  `benefits_cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`benefits_cards`)),
  `wipes_label` varchar(255) NOT NULL DEFAULT 'Accesorios',
  `wipes_title` varchar(255) NOT NULL DEFAULT 'Cuida tus lentes',
  `wipes_description` text DEFAULT NULL,
  `wipes_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`wipes_features`)),
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `trust_badges` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`trust_badges`)),
  `cta_title` varchar(255) NOT NULL DEFAULT '¿Listo para proteger tu visión?',
  `cta_subtitle` text DEFAULT NULL,
  `cta_btn_primary_text` varchar(255) NOT NULL DEFAULT 'Comprar ahora',
  `cta_btn_secondary_text` varchar(255) NOT NULL DEFAULT 'Aprende más',
  `cta_trust_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cta_trust_items`)),
  `comparison_label` varchar(255) NOT NULL DEFAULT 'Comparativo',
  `comparison_title` varchar(255) NOT NULL DEFAULT 'Con vs. sin protección',
  `comparison_subtitle` text DEFAULT NULL,
  `comparison_without_label` varchar(255) NOT NULL DEFAULT 'Sin protección',
  `comparison_without_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`comparison_without_items`)),
  `comparison_with_label` varchar(255) NOT NULL DEFAULT 'Con nuvion glass',
  `comparison_with_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`comparison_with_items`)),
  `authority_title` varchar(255) DEFAULT NULL,
  `testimonials_title` varchar(255) DEFAULT NULL,
  `faq_title` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `home_page_settings_star_product_id_foreign` (`star_product_id`),
  CONSTRAINT `home_page_settings_star_product_id_foreign` FOREIGN KEY (`star_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `home_page_settings`
--

LOCK TABLES `home_page_settings` WRITE;
/*!40000 ALTER TABLE `home_page_settings` DISABLE KEYS */;
INSERT INTO `home_page_settings` VALUES (1,'Catálogo organizado','Explora por categoría','Desde insumos para nail art hasta cremas, jabones y skincare. Encuentra todo lo que tu salón, estudio o casa necesitan, ordenado para que llegues rápido.','[{\"name\":\"Skincare\",\"link_param\":\"sin_graduacion\",\"description\":\"S\\u00e9rums, cremas, t\\u00f3nicos y mascarillas con ingredientes bot\\u00e1nicos.\",\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M12 3c2.5 3 4 5 4 8a4 4 0 1 1-8 0c0-3 1.5-5 4-8Z\\\"\\/>\"},{\"name\":\"Fragancias\",\"link_param\":\"sin_graduacion\",\"description\":\"Perfumes con notas florales, c\\u00edtricas y \\u00e1mbar dorado. Larga duraci\\u00f3n.\",\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M9 4h6v3H9V4Zm-1 3h8l-1 14H9L8 7Z\\\"\\/>\"},{\"name\":\"Rituales\",\"link_param\":\"toallitas\",\"description\":\"Sets seleccionados para tu rutina diaria. Todo lo que necesitas en uno.\",\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M4 7h16v12H4V7Zm4-3h8v3H8V4Z\\\"\\/>\"}]','Catálogo','Nuestros productos','Formulaciones limpias, packaging cuidado, envío en 48 h.','Producto estrella','Sérum Áureo con vitamina C',NULL,'Ilumina, unifica y suaviza líneas finas en 28 días. Textura ligera con vitamina C estabilizada y aceite de rosa mosqueta.','$580.00','30 ml · uso diario · piel sensible','Ver producto',NULL,'Por qué Belleza Áurea','Tu aliado en productos profesionales de belleza','Surtido completo, precios que compiten y respaldo real. Lo que tu cabina, salón o estudio necesita, listo para despacho a toda Colombia.','[{\"icon_svg\":\"\",\"title\":\"Precios de distribuidor\",\"description\":\"Acceso directo a tarifas mayoristas en cada referencia. Lo que ahorras en cada caja se traduce en el margen de tu negocio.\"},{\"icon_svg\":\"\",\"title\":\"Todo en un solo lugar\",\"description\":\"Esmaltes, decoraci\\u00f3n, pesta\\u00f1as, peluquer\\u00eda, equipos y bioseguridad bajo el mismo carrito. Cero tiempo perdido entre tiendas y proveedores.\"},{\"icon_svg\":\"\",\"title\":\"Calidad probada en cabina\",\"description\":\"Marcas seleccionadas por manicuristas, lashistas y estilistas que viven del trabajo bien hecho. Pigmento, durabilidad y acabado que tu clienta nota.\"},{\"icon_svg\":\"\",\"title\":\"Despacho y respaldo\",\"description\":\"Env\\u00edo r\\u00e1pido a todo el pa\\u00eds con seguimiento, devoluci\\u00f3n sencilla si algo falla y asesor\\u00eda por WhatsApp cuando la necesites.\"}]','Sets','Rituales completos','Lleva tu rutina al siguiente nivel con nuestros sets premium. Empacados en cajas de regalo doradas, listos para disfrutar o sorprender.','[\"Ahorra hasta 25% vs comprar por separado\",\"Empaque regalo con detalle dorado\",\"Combinan productos que se potencian entre s\\u00ed\",\"Tarjeta personalizada opcional\"]','[{\"q\":\"\\u00bfBelleza \\u00c1urea hace env\\u00edos a toda Colombia?\",\"a\":\"S\\u00ed. Despachamos a los 32 departamentos del pa\\u00eds con seguimiento. Los pedidos en Bogot\\u00e1 suelen entregarse entre 24 y 48 horas; para ciudades intermedias el tiempo es de 2 a 5 d\\u00edas h\\u00e1biles. El costo se calcula al confirmar la direcci\\u00f3n y se descuenta seg\\u00fan el valor del pedido.\"},{\"q\":\"\\u00bfLos productos que venden son originales?\",\"a\":\"100% originales. Trabajamos solo con marcas y proveedores con trazabilidad verificada. Cada caja que sale de nuestra bodega es producto aut\\u00e9ntico con sus c\\u00f3digos de f\\u00e1brica intactos. Si alguna vez recibes algo que te genera dudas, te lo cambiamos sin discusi\\u00f3n.\"},{\"q\":\"\\u00bfQu\\u00e9 tipo de productos puedo encontrar en el cat\\u00e1logo?\",\"a\":\"Cat\\u00e1logo completo para u\\u00f1as (esmaltes tradicionales, semipermanentes, polygel, decoraci\\u00f3n), pesta\\u00f1as (extensiones, lifting, adhesivos), peluquer\\u00eda (tintes, decolorantes, tratamientos), equipos el\\u00e9ctricos, herramientas profesionales y bioseguridad. M\\u00e1s de 300 referencias activas en stock real.\"},{\"q\":\"\\u00bfNecesito ser profesional o tener un negocio para comprar?\",\"a\":\"No. Belleza \\u00c1urea est\\u00e1 abierta tanto a profesionales de la belleza (manicuristas, lashistas, estilistas, due\\u00f1os de sal\\u00f3n) como a consumidores finales que quieren usar productos de calidad profesional en casa. El cat\\u00e1logo y los precios son los mismos para todos.\"},{\"q\":\"\\u00bfHay pedido m\\u00ednimo de compra?\",\"a\":\"No. Puedes comprar una sola unidad o armar un pedido grande de reposici\\u00f3n para tu cabina, sal\\u00f3n o estudio. Los precios mayoristas se aplican por referencia, no por volumen, as\\u00ed que ahorras desde la primera unidad.\"},{\"q\":\"\\u00bfQu\\u00e9 medios de pago aceptan?\",\"a\":\"Transferencia bancaria (Bancolombia, Davivienda, Nequi, Daviplata), PSE, tarjetas de cr\\u00e9dito y d\\u00e9bito (Visa, Mastercard, AmEx) y pago contra entrega en ciudades principales. Todos los pagos pasan por pasarelas certificadas con cifrado SSL.\"},{\"q\":\"\\u00bfPuedo devolver un producto si lleg\\u00f3 defectuoso o no era lo que esperaba?\",\"a\":\"S\\u00ed. Tienes 30 d\\u00edas desde la entrega para solicitar devoluci\\u00f3n o cambio. Si el producto lleg\\u00f3 da\\u00f1ado o no corresponde con el pedido, te lo reponemos sin costo y nosotros asumimos el env\\u00edo. Para devoluciones por gusto, el producto debe estar sin abrir y conservar su sello original.\"},{\"q\":\"\\u00bfC\\u00f3mo hago un pedido especial o resuelvo dudas sobre un producto?\",\"a\":\"Por WhatsApp directo. Nuestro equipo responde dentro de 24 horas h\\u00e1biles consultas t\\u00e9cnicas sobre productos, disponibilidad de marcas espec\\u00edficas, descuentos por volumen para profesionales y seguimiento de pedidos en ruta.\"}]','[{\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25\\\"\\/>\",\"title\":\"Env\\u00edo gratis\",\"description\":\"En compras desde $899\"},{\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M9 12.75 11.25 15 15 9.75\\\"\\/>\",\"title\":\"Cruelty-free\",\"description\":\"Cero pruebas en animales\"},{\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M16.023 9.348h4.992m-4.992 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7\\\"\\/>\",\"title\":\"30 d\\u00edas\",\"description\":\"Devoluci\\u00f3n sin costo\"},{\"icon_svg\":\"<path stroke-linecap=\\\"round\\\" stroke-linejoin=\\\"round\\\" stroke-width=\\\"1.5\\\" d=\\\"M2.25 8.25h19.5\\\"\\/>\",\"title\":\"Pago seguro\",\"description\":\"Stripe encriptado\"}]','¿Lista para tu ritual áureo?','Descubre tu rutina ideal en 90 segundos con el Quiz de Piel o explora nuestra colección completa.','Comenzar quiz','Ver productos','[\"Env\\u00edo gratis +$899\",\"Cruelty-free\",\"30 d\\u00edas de devoluci\\u00f3n\"]','El antes y después','Cómo cambia armar tus pedidos con Belleza Áurea','Lo que dejas atrás cuando trabajas con un distribuidor que entiende tu negocio.','Sin un proveedor real','[\"Saltas entre cinco tiendas para armar un solo pedido\",\"Pagas precio retail con el margen del intermediario encima\",\"Productos gen\\u00e9ricos que no rinden como prometen\",\"Env\\u00edos lentos y silencio cuando algo falla\"]','Con Belleza Áurea','[\"Todo el cat\\u00e1logo en un solo carrito, listo en minutos\",\"Precios mayoristas en cada referencia, sin pedido m\\u00ednimo\",\"Marcas probadas en cabina que rinden caja tras caja\",\"Despacho con seguimiento y WhatsApp directo cuando lo necesites\"]','Tu distribuidora de productos profesionales de belleza','Lo que dicen nuestras clientes','Preguntas frecuentes',1,'2026-05-23 00:28:29','2026-06-17 06:47:10');
/*!40000 ALTER TABLE `home_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infographic_images`
--

DROP TABLE IF EXISTS `infographic_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infographic_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infographic_images`
--

LOCK TABLES `infographic_images` WRITE;
/*!40000 ALTER TABLE `infographic_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `infographic_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"0794324a-bfa8-408b-8f63-55977986808b\",\"displayName\":\"App\\\\Mail\\\\LeadWelcome\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:20:\\\"App\\\\Mail\\\\LeadWelcome\\\":3:{s:4:\\\"lead\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Lead\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:20:\\\"prueba_quiz@test.com\\\";}}s:6:\\\"mailer\\\";s:3:\\\"log\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1785275857,\"delay\":null}',0,NULL,1785275857,1785275857),(2,'default','{\"uuid\":\"44dd2182-1350-43b5-bb44-4a9053841e31\",\"displayName\":\"App\\\\Mail\\\\LeadWelcome\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:20:\\\"App\\\\Mail\\\\LeadWelcome\\\":3:{s:4:\\\"lead\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Lead\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:22:\\\"yepavargas@hotmail.com\\\";}}s:6:\\\"mailer\\\";s:3:\\\"log\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1785275878,\"delay\":null}',0,NULL,1785275878,1785275878),(3,'default','{\"uuid\":\"5f1ef3e4-341f-457c-8f54-864e06cca7fa\",\"displayName\":\"App\\\\Mail\\\\LeadWelcome\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:20:\\\"App\\\\Mail\\\\LeadWelcome\\\":3:{s:4:\\\"lead\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\Lead\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:13:\\\"hola@hola.com\\\";}}s:6:\\\"mailer\\\";s:3:\\\"log\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1785279523,\"delay\":null}',0,NULL,1785279523,1785279523);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL DEFAULT 'footer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leads`
--

LOCK TABLES `leads` WRITE;
/*!40000 ALTER TABLE `leads` DISABLE KEYS */;
INSERT INTO `leads` VALUES (1,'Prueba','prueba_quiz@test.com','quiz','2026-07-29 02:57:34','2026-07-29 02:57:34'),(2,'jenn','yepavargas@hotmail.com','quiz','2026-07-29 02:57:58','2026-07-29 02:57:58'),(3,'jenn','hola@hola.com','quiz','2026-07-29 03:58:42','2026-07-29 03:58:42');
/*!40000 ALTER TABLE `leads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lentes_page_settings`
--

DROP TABLE IF EXISTS `lentes_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lentes_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `catalog_title` varchar(255) DEFAULT NULL,
  `catalog_subtitle` varchar(255) DEFAULT NULL,
  `product_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`product_benefits`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lentes_page_settings`
--

LOCK TABLES `lentes_page_settings` WRITE;
/*!40000 ALTER TABLE `lentes_page_settings` DISABLE KEYS */;
INSERT INTO `lentes_page_settings` VALUES (1,'Nuestro catálogo','Insumos profesionales y cosmética de marca — uñas, piel, maquillaje y más, en un solo lugar.',NULL,1,'2026-05-23 02:47:12','2026-07-30 02:06:28');
/*!40000 ALTER TABLE `lentes_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_02_24_030210_add_is_admin_to_users_table',1),(5,'2026_02_26_000001_create_categories_table',1),(6,'2026_02_26_000002_create_products_table',1),(7,'2026_02_26_000003_create_product_variants_table',1),(8,'2026_02_26_000004_create_customers_table',1),(9,'2026_02_26_000005_create_orders_table',1),(10,'2026_02_26_000006_create_order_items_table',1),(11,'2026_02_26_000007_create_leads_table',1),(12,'2026_02_26_000008_create_blog_posts_table',1),(13,'2026_03_12_000001_create_infographic_images_table',1),(14,'2026_03_12_000002_add_stripe_payment_intent_id_to_orders_table',1),(15,'2026_03_12_000003_create_discount_codes_table',1),(16,'2026_03_12_000004_add_discount_fields_to_orders_table',1),(17,'2026_03_12_000005_create_shipping_tables',1),(18,'2026_03_14_000001_add_tracking_token_to_orders_table',1),(19,'2026_03_14_000002_add_shipping_tracking_to_orders_table',1),(20,'2026_03_17_000001_enhance_blog_posts_table',1),(21,'2026_03_17_100000_add_type_columns_to_products_table',1),(22,'2026_03_17_100001_add_color_graduation_to_product_variants_table',1),(23,'2026_03_18_000001_create_hero_settings_table',1),(24,'2026_03_20_180532_add_video_position_to_hero_settings_table',1),(25,'2026_03_24_021603_create_home_page_settings_table',1),(26,'2026_03_24_025247_create_seo_settings_table',1),(27,'2026_03_27_005341_add_hero_images_to_hero_settings_table',1),(28,'2026_03_27_162838_add_promo_background_to_home_page_settings_table',1),(29,'2026_03_27_164506_add_benefits_to_home_page_settings_table',1),(30,'2026_03_27_165815_create_testimonials_table',1),(31,'2026_04_15_000001_add_image_path_to_product_variants_table',1),(32,'2026_04_15_000002_add_color_hex_to_product_variants_table',1),(33,'2026_04_16_015842_create_lentes_page_settings_table',1),(34,'2026_04_16_020928_create_blog_page_settings_table',1),(35,'2026_04_16_022748_create_blue_light_page_settings_table',1),(36,'2026_04_16_154324_add_payment_receipt_to_orders_table',1),(37,'2026_04_16_154324_create_bank_transfer_settings_table',1),(38,'2026_04_16_220708_create_contact_page_settings_table',1),(39,'2026_04_16_220711_create_shipping_returns_page_settings_table',1),(40,'2026_04_17_022525_change_product_type_to_json',1),(41,'2026_04_19_225152_add_product_benefits_to_lentes_page_settings',1),(42,'2026_04_19_230617_create_quiz_page_settings_table',1),(43,'2026_04_19_232417_add_questions_to_quiz_page_settings',1),(44,'2026_04_20_002207_make_shipping_rates_state_based',1),(45,'2026_04_20_014412_add_discount_2x1_to_orders_table',1),(46,'2026_05_04_120000_add_comparison_to_home_page_settings_table',1),(47,'2026_05_04_130000_add_type_filter_to_categories_table',1),(48,'2026_05_05_120000_backfill_payment_status_for_confirmed_orders',1),(49,'2026_05_05_140000_add_whatsapp_number_and_message_to_contact_page_settings',1),(50,'2026_05_06_120000_add_category_to_blog_posts_table',1),(51,'2026_05_22_220205_add_cost_price_to_products_table',2),(52,'2026_05_22_221649_add_option_type_to_product_variants',3),(53,'2026_05_22_221658_add_option_type_to_product_variants',3),(56,'2026_05_23_020239_create_brands_table',4),(57,'2026_05_23_020241_add_brand_id_to_products_table',4),(58,'2026_05_23_033649_add_seo_geo_fields_to_products',5),(59,'2026_06_16_023532_add_star_product_id_to_home_page_settings',6),(60,'2026_07_28_000001_create_payment_settings_table',7),(61,'2026_07_29_000001_add_section_titles_to_home_page_settings',8),(62,'2026_07_29_000002_add_promo_2x1_to_categories',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `variant_id` bigint(20) unsigned DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_variant_id_foreign` (`variant_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,3,NULL,1,8500.00,8500.00,'2026-07-29 06:00:31','2026-07-29 06:00:31');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_code` varchar(255) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_2x1` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_coupon` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_receipt` varchar(255) DEFAULT NULL,
  `stripe_payment_intent_id` varchar(255) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `shipping_carrier` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `tracking_url` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tracking_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_tracking_token_unique` (`tracking_token`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,'pending',8500.00,20000.00,NULL,0.00,0.00,0.00,28500.00,'transfer','pending',NULL,NULL,'Calle 45 #23-15, Barrio Cabecera, Santander, 680001',NULL,NULL,NULL,NULL,'bQADUV7e8FHSMgiE4SVDAYPZb7E8qXXOfdNu7nl9gDy4cidE','2026-07-29 06:00:31','2026-07-29 06:00:31');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_settings`
--

DROP TABLE IF EXISTS `payment_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_settings`
--

LOCK TABLES `payment_settings` WRITE;
/*!40000 ALTER TABLE `payment_settings` DISABLE KEYS */;
INSERT INTO `payment_settings` VALUES (1,'stripe_key','','2026-07-29 04:13:02','2026-07-29 04:14:22'),(2,'stripe_secret','','2026-07-29 04:13:02','2026-07-29 04:14:22'),(3,'stripe_webhook_secret','','2026-07-29 04:13:02','2026-07-29 04:13:02');
/*!40000 ALTER TABLE `payment_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `option_type` enum('color','size','scent','finish','style','material','quantity','other') NOT NULL DEFAULT 'other',
  `color` varchar(255) DEFAULT NULL,
  `color_hex` varchar(7) DEFAULT NULL,
  `graduation` varchar(255) DEFAULT NULL,
  `graduation_type` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `price_modifier` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_variants_product_id_color_graduation_type_index` (`product_id`,`color`,`graduation_type`),
  KEY `idx_pv_option_type` (`option_type`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,322,'color','Rojo Coral','#D9747A',NULL,NULL,NULL,'Tono','Rojo Coral',0.00,25,1,'2026-05-23 03:21:51','2026-05-23 03:21:51'),(2,322,'color','Nude Rosado','#E8D1C5',NULL,NULL,NULL,'Tono','Nude Rosado',0.00,25,1,'2026-05-23 03:21:51','2026-05-23 03:21:51'),(3,322,'color','Verde Sage','#A8B29A',NULL,NULL,NULL,'Tono','Verde Sage',0.00,25,1,'2026-05-23 03:21:51','2026-05-23 03:21:51'),(4,322,'color','Champagne','#D9B56D',NULL,NULL,NULL,'Tono','Champagne',0.00,25,1,'2026-05-23 03:21:51','2026-05-23 03:21:51'),(5,322,'size','15 ml',NULL,NULL,NULL,NULL,'Tamaño','15 ml',0.00,50,1,'2026-05-23 03:21:51','2026-05-23 08:50:54'),(6,322,'size','30 ml',NULL,NULL,NULL,NULL,'Tamaño','30 ml',6000.00,50,1,'2026-05-23 03:21:51','2026-05-23 08:50:54'),(7,322,'finish','Brillo',NULL,NULL,NULL,NULL,'Acabado','Brillo',0.00,50,1,'2026-05-23 03:21:51','2026-05-23 08:50:54'),(8,322,'finish','Mate',NULL,NULL,NULL,NULL,'Acabado','Mate',0.00,50,1,'2026-05-23 03:21:51','2026-05-23 08:50:54');
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `internal_code` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`type`)),
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `og_image_path` varchar(255) DEFAULT NULL,
  `focus_keyword` varchar(120) DEFAULT NULL,
  `noindex` tinyint(1) NOT NULL DEFAULT 0,
  `key_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`key_features`)),
  `how_to_use` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `suitable_for` varchar(500) DEFAULT NULL,
  `gtin` varchar(14) DEFAULT NULL,
  `mpn` varchar(70) DEFAULT NULL,
  `weight_value` decimal(8,2) DEFAULT NULL,
  `weight_unit` varchar(10) DEFAULT NULL,
  `country_origin` varchar(100) DEFAULT NULL,
  `is_cruelty_free` tinyint(1) NOT NULL DEFAULT 0,
  `is_vegan` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `badge_2x1` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_type_index` (`type`(768)),
  KEY `products_internal_code_index` (`internal_code`),
  KEY `products_brand_id_index` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=323 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'13',1,NULL,'esmalte baby dulce','esmalte-baby-dulce-13','[\"sin_graduacion\"]','esmalte baby...',9000.00,10000.00,6000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,1,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(2,'0708',1,NULL,'Painting en gel economico','painting-en-gel-economico-0708','[\"sin_graduacion\"]','Painting en gel economico',8500.00,NULL,6000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,1,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(3,'1223',2,NULL,'Aceite Restaurador de Cutícula Floral 15 ml','aceite-restaurador-de-cuticula-floral-15-ml-1223','[\"sin_graduacion\"]','Nutre e hidrata tus cutículas. Fragancia floral para un cuidado delicado. ¡Manos sanas y bonitas!...',8500.00,9000.00,6000.00,49,'[\"products\\/aceite-restaurador-de-cuticula-floral-15-ml-1223\\/1-6a1657c76e93f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,1,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(4,'1004-B',2,NULL,'Base rubber color x 15 ml','base-rubber-color-x-15-ml-1004-b','[\"sin_graduacion\"]','Base flexible para fortalecer uñas débiles. Nivela imperfecciones y protege el crecimiento. ¡Uña...',15000.00,16000.00,11000.00,50,'[\"products\\/base-rubber-color-x-15-ml-1004-b\\/1-6a1657b7d7e42.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,1,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(5,'1307',2,NULL,'Blooming Gel','blooming-gel-1307','[\"sin_graduacion\"]','Gel especial para crear efectos difuminados y artísticos. Permite que los colores se expandan suave...',16000.00,17000.00,12000.00,50,'[\"products\\/blooming-gel-1307\\/1-6a1657cfd0c12.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(6,'1284',2,NULL,'Esmalte Ojo de Gato x 15 ml','esmalte-ojo-de-gato-x-15-ml-1284','[\"sin_graduacion\"]','Explora un universo de color con nuestros esmaltes ojo de gato de 15ml. Elige entre 7 tonos vibrante...',19000.00,20000.00,14000.00,50,'[\"products\\/esmalte-ojo-de-gato-x-15-ml-1284\\/1-6a1657cd585f7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(7,'3001',2,NULL,'Esmalte Semipermanente Baby 10mL','esmalte-semipermanente-baby-10ml-3001','[\"sin_graduacion\"]','Esmalte Semipermanente Baby 10mL',9000.00,10000.00,6000.00,50,'[\"products\\/esmalte-semipermanente-baby-10ml-3001\\/1-6a1657d5effc9.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(8,'1001',2,NULL,'Esmalte semipermanente x 15ml','esmalte-semipermanente-x-15ml-1001','[\"sin_graduacion\"]','Explora un universo de color con nuestros esmaltes semipermanentes de 15ml. Elige entre 70 tonos vib...',16000.00,17000.00,14000.00,50,'[\"products\\/esmalte-semipermanente-x-15ml-1001\\/1-6a1657b194b25.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/2-6a1657b1abeb6.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/3-6a1657b1c331d.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/4-6a1657b1da657.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/5-6a1657b1f173d.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/6-6a1657b214149.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/7-6a1657b22add1.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/8-6a1657b243f7b.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/9-6a1657b25b590.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/10-6a1657b2720f1.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/11-6a1657b2899f0.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/12-6a1657b29fdfd.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/13-6a1657b2b6a8a.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/14-6a1657b2cee50.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/15-6a1657b2e5d2f.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/16-6a1657b30841a.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/17-6a1657b32030c.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/18-6a1657b3375e0.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/19-6a1657b34f0c4.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/20-6a1657b367235.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/21-6a1657b37ef5b.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/22-6a1657b396cce.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/23-6a1657b3af0b5.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/24-6a1657b3c6d52.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/25-6a1657b3deccf.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/26-6a1657b403216.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/27-6a1657b41bfbb.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/28-6a1657b434a62.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/29-6a1657b44bd0d.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/30-6a1657b463111.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/31-6a1657b47a7cb.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/32-6a1657b490c6a.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/33-6a1657b4a6c69.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/34-6a1657b4bcb80.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/35-6a1657b4d4078.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/36-6a1657b4ec3b4.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/37-6a1657b5103e1.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/38-6a1657b527abd.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/39-6a1657b5402ae.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/40-6a1657b557b3d.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/41-6a1657b56ec4a.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/42-6a1657b586153.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/43-6a1657b59ebf8.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/44-6a1657b5b6b31.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/45-6a1657b5cf300.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/46-6a1657b5e7090.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/47-6a1657b60b45a.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/48-6a1657b6237c8.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/49-6a1657b63bf84.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/50-6a1657b654db6.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/51-6a1657b66d24f.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/52-6a1657b684978.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/53-6a1657b69b268.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/54-6a1657b6b3081.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/55-6a1657b6caac6.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/56-6a1657b6e29c8.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/57-6a1657b705e79.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/58-6a1657b71cd37.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/59-6a1657b734506.webp\",\"products\\/esmalte-semipermanente-x-15ml-1001\\/60-6a1657b74c354.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(9,'1311',2,NULL,'Glue Gel 6 en 1','glue-gel-6-en-1-1311','[\"sin_graduacion\"]','Glue Gel 6 en 1',13000.00,14000.00,10000.00,50,'[\"products\\/glue-gel-6-en-1-1311\\/1-6a1657d028e9e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(10,'1004-A',2,NULL,'Pegante en gel x 15 ml','pegante-en-gel-x-15-ml-1004-a','[\"sin_graduacion\"]','Adhesivo profesional para uñas. Fijación fuerte y duradera para tips y decoraciones. ¡Resultados ...',13000.00,14000.00,10000.00,50,'[\"products\\/pegante-en-gel-x-15-ml-1004-a\\/1-6a1657b7bb359.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(11,'1062',2,NULL,'Pegante para Uñas en Brocha','pegante-para-unas-en-brocha-1062','[\"sin_graduacion\"]','Adhesivo de secado rápido con aplicador de brocha. Ideal para tips y reparaciones. ¡Aplicación pr...',5000.00,NULL,3500.00,50,'[\"products\\/pegante-para-unas-en-brocha-1062\\/1-6a1657be2731e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(12,'2021',2,NULL,'Top Coat Mate x 15 ml','top-coat-mate-x-15-ml-2021','[\"sin_graduacion\"]','Top coat de acabado mate para esmaltado semipermanente. Sella el color y ofrece acabado duradero. ¡...',19000.00,20000.00,15000.00,50,'[\"products\\/top-coat-mate-x-15-ml-2021\\/1-6a1657d33cf70.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(13,'1002',2,NULL,'Ultrabond x 15 ml','ultrabond-x-15-ml-1002','[\"sin_graduacion\"]','Primer sin ácido para máxima adherencia en uñas acrílicas y de gel. Previene levantamientos. ¡D...',12000.00,13000.00,9000.00,50,'[\"products\\/ultrabond-x-15-ml-1002\\/1-6a1657b774b7a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(14,'2001',3,NULL,'Anillo Decorativo','anillo-decorativo-2001','[\"sin_graduacion\"]','Elegancia en miniatura. Añade un toque glam a cualquier diseño, ideal para sets editoriales, temá...',7500.00,8000.00,4500.00,50,'[\"products\\/anillo-decorativo-2001\\/1-6a1657d073817.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(15,'1036',3,NULL,'Caviar para Uñas en Blister','caviar-para-unas-en-blister-1036','[\"sin_graduacion\"]','Microesferas decorativas para nail art. Crea texturas únicas y diseños llamativos. ¡Perfecto para...',7500.00,8000.00,5500.00,50,'[\"products\\/caviar-para-unas-en-blister-1036\\/1-6a1657bac40ba.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(16,'2011',3,NULL,'Decoración 3D Cuadrada','decoracion-3d-cuadrada-2011','[\"sin_graduacion\"]','Se envía referencias surtidas....',4000.00,NULL,2500.00,50,'[\"products\\/decoracion-3d-cuadrada-2011\\/1-6a1657d246e86.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(17,'2007',3,NULL,'Decoración Aurora #1','decoracion-aurora-1-2007','[\"sin_graduacion\"]','Decoración Aurora #1',8500.00,9000.00,6000.00,50,'[\"products\\/decoracion-aurora-1-2007\\/1-6a1657d13ee92.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(18,'2006',3,NULL,'Decoración Camaleón C1','decoracion-camaleon-c1-2006','[\"sin_graduacion\"]','Decoración Camaleón C1',8000.00,9000.00,5000.00,50,'[\"products\\/decoracion-camaleon-c1-2006\\/1-6a1657d12809b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(19,'2014',3,NULL,'Decoración Efecto Espejo Individual','decoracion-efecto-espejo-individual-2014','[\"sin_graduacion\"]','Polvo de color ultra vibrante para nail art. Crea diseños llamativos y luminosos. ¡Uñas que brill...',3000.00,NULL,2000.00,50,'[\"products\\/decoracion-efecto-espejo-individual-2014\\/1-6a1657d288765.webp\",\"products\\/decoracion-efecto-espejo-individual-2014\\/2-6a1657d2996f4.webp\",\"products\\/decoracion-efecto-espejo-individual-2014\\/3-6a1657d2aa50f.webp\",\"products\\/decoracion-efecto-espejo-individual-2014\\/4-6a1657d2c423f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(20,'2014-G',3,NULL,'Decoración Efecto Espejo Individual Gold','decoracion-efecto-espejo-individual-gold-2014-g','[\"sin_graduacion\"]','Polvo de color ultra vibrante para nail art. Crea diseños llamativos y luminosos. ¡Uñas que brill...',5000.00,NULL,3500.00,50,'[\"products\\/decoracion-efecto-espejo-individual-gold-2014-g\\/1-6a1657d2d74af.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(21,'2014-P',3,NULL,'Decoración Efecto Espejo Individual Pink','decoracion-efecto-espejo-individual-pink-2014-p','[\"sin_graduacion\"]','Polvo de color ultra vibrante para nail art. Crea diseños llamativos y luminosos. ¡Uñas que brill...',5000.00,NULL,3500.00,50,'[\"products\\/decoracion-efecto-espejo-individual-pink-2014-p\\/1-6a1657d2eb0fe.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(22,'2014-S',3,NULL,'Decoración Efecto Espejo Individual Silver','decoracion-efecto-espejo-individual-silver-2014-s','[\"sin_graduacion\"]','Polvo de color ultra vibrante para nail art. Crea diseños llamativos y luminosos. ¡Uñas que brill...',5000.00,NULL,3500.00,50,'[\"products\\/decoracion-efecto-espejo-individual-silver-2014-s\\/1-6a1657d309f87.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(23,'2003',3,NULL,'Decoración Efecto Perla','decoracion-efecto-perla-2003','[\"sin_graduacion\"]','Decoración Efecto Perla',11000.00,12000.00,8500.00,50,'[\"products\\/decoracion-efecto-perla-2003\\/1-6a1657d0ce9d6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(24,'2002',3,NULL,'Decoración Efecto Silver','decoracion-efecto-silver-2002','[\"sin_graduacion\"]','Decoración Efecto Silver',7000.00,NULL,4000.00,50,'[\"products\\/decoracion-efecto-silver-2002\\/1-6a1657d08f2df.webp\",\"products\\/decoracion-efecto-silver-2002\\/2-6a1657d0a0b41.webp\",\"products\\/decoracion-efecto-silver-2002\\/3-6a1657d0b1c15.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(25,'2029',3,NULL,'Decoración Flaker Camaleón','decoracion-flaker-camaleon-2029','[\"sin_graduacion\"]','Efecto camaleónico que deslumbra. Destellos multicolor con acabado tornasolado, ideal para sets art...',5000.00,NULL,4000.00,50,'[\"products\\/decoracion-flaker-camaleon-2029\\/1-6a1657d49d793.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(26,'2031',3,NULL,'Decoracion Flaker Individual','decoracion-flaker-individual-2031','[\"sin_graduacion\"]','Hojuelas decorativas para nail art. Crea efectos iridiscentes y metálicos únicos. ¡Uñas con un a...',6000.00,NULL,3500.00,50,'[\"products\\/decoracion-flaker-individual-2031\\/1-6a1657d4d0bf6.webp\",\"products\\/decoracion-flaker-individual-2031\\/2-6a1657d4e2867.webp\",\"products\\/decoracion-flaker-individual-2031\\/3-6a1657d500596.webp\",\"products\\/decoracion-flaker-individual-2031\\/4-6a1657d5124d0.webp\",\"products\\/decoracion-flaker-individual-2031\\/5-6a1657d524ce0.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(27,'2024',3,NULL,'Decoración Flor y Concha Nácar','decoracion-flor-y-concha-nacar-2024','[\"sin_graduacion\"]','Decoración Flor y Concha Nácar',7500.00,8000.00,4500.00,50,'[\"products\\/decoracion-flor-y-concha-nacar-2024\\/1-6a1657d3a04c3.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(28,'2025B/N',3,NULL,'Decoración Flores Blanco/Negro','decoracion-flores-blanconegro-2025b/n','[\"sin_graduacion\"]','Las flores 3D transforman cualquier set de uñas en una obra de arte sofisticada. Textura realista y...',5500.00,NULL,3900.00,50,'[\"products\\/decoracion-flores-blanconegro-2025b\\/n\\/1-6a1657d432bbd.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(29,'2025-Mix',3,NULL,'Decoración Flores Mix','decoracion-flores-mix-2025-mix','[\"sin_graduacion\"]','Las flores 3D transforman cualquier set de uñas en una obra de arte sofisticada. Textura realista y...',5500.00,NULL,3900.00,50,'[\"products\\/decoracion-flores-mix-2025-mix\\/1-6a1657d3e3e5b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(30,'2027',3,NULL,'Decoración Holográfica','decoracion-holografica-2027','[\"sin_graduacion\"]','Brillo multidimensional que hipnotiza. Transforma cualquier set en una explosión de color y luz. Id...',5000.00,NULL,3500.00,50,'[\"products\\/decoracion-holografica-2027\\/1-6a1657d467a91.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(31,'2009',3,NULL,'Decoración Mix','decoracion-mix-2009','[\"sin_graduacion\"]','Decoración Mix',7000.00,NULL,4000.00,50,'[\"products\\/decoracion-mix-2009\\/1-6a1657d1630ba.webp\",\"products\\/decoracion-mix-2009\\/2-6a1657d1747b2.webp\",\"products\\/decoracion-mix-2009\\/3-6a1657d185e16.webp\",\"products\\/decoracion-mix-2009\\/4-6a1657d197628.webp\",\"products\\/decoracion-mix-2009\\/5-6a1657d1a8db8.webp\",\"products\\/decoracion-mix-2009\\/6-6a1657d1ba056.webp\",\"products\\/decoracion-mix-2009\\/7-6a1657d1cc021.webp\",\"products\\/decoracion-mix-2009\\/8-6a1657d1ddbb1.webp\",\"products\\/decoracion-mix-2009\\/9-6a1657d1eede4.webp\",\"products\\/decoracion-mix-2009\\/10-6a1657d20b4ff.webp\",\"products\\/decoracion-mix-2009\\/11-6a1657d21cc85.webp\",\"products\\/decoracion-mix-2009\\/12-6a1657d22da9e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(32,'2004',3,NULL,'Decoración Moño 3D','decoracion-mono-3d-2004','[\"sin_graduacion\"]','Toque femenino y encantador para cualquier diseño de uñas. Ideal para sets románticos, infantiles...',5000.00,NULL,2500.00,50,'[\"products\\/decoracion-mono-3d-2004\\/1-6a1657d0e9181.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(33,'3051',3,NULL,'Decoración Murano Caja Flor','decoracion-murano-caja-flor-3051','[\"sin_graduacion\"]','Decoración Murano Caja Flor',7000.00,NULL,5000.00,50,'[\"products\\/decoracion-murano-caja-flor-3051\\/1-6a1657d610d25.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(34,'2026',3,NULL,'Decoración Naturaleza Muerta','decoracion-naturaleza-muerta-2026','[\"sin_graduacion\"]','Decoración Naturaleza Muerta',9500.00,10000.00,7000.00,50,'[\"products\\/decoracion-naturaleza-muerta-2026\\/1-6a1657d44d3b6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(35,'2012',3,NULL,'Decoración para Uñas x24 Unidades','decoracion-para-unas-x24-unidades-2012','[\"sin_graduacion\"]','Decoración para Uñas x24 Unidades',14400.00,NULL,0.00,50,'[\"products\\/decoracion-para-unas-x24-unidades-2012\\/1-6a1657d25e9b2.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(36,'1252',3,NULL,'Efecto Unicornio','efecto-unicornio-1252','[\"sin_graduacion\"]','Polvo iridiscente para uñas con acabado mágico. Crea un efecto nacarado y multicolor. ¡Uñas fant...',7500.00,8000.00,5000.00,50,'[\"products\\/efecto-unicornio-1252\\/1-6a1657ca3c889.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(37,'1317',3,NULL,'Painting Gel de Saturación Diamond','painting-gel-de-saturacion-diamond-1317','[\"sin_graduacion\"]','Painting Gel de Saturación Diamond',52000.00,60000.00,42000.00,50,'[\"products\\/painting-gel-de-saturacion-diamond-1317\\/1-6a1657d05abaf.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(38,'1266',3,NULL,'Painting Gel de Saturación Individual - 8ml','painting-gel-de-saturacion-individual-8ml-1266','[\"sin_graduacion\"]','Gel de alta pigmentación para nail art. Color intenso y cobertura total. ¡Crea diseños precisos y...',13000.00,14000.00,10000.00,50,'[\"products\\/painting-gel-de-saturacion-individual-8ml-1266\\/1-6a1657cb9ccb6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(39,'1265',3,NULL,'Painting Gel Efecto Espejo - 8 ml','painting-gel-efecto-espejo-8-ml-1265','[\"sin_graduacion\"]','Gel de alta pigmentación para acabados metálicos. Crea diseños con efecto espejo en tus uñas. ¡...',15000.00,16000.00,11000.00,50,'[\"products\\/painting-gel-efecto-espejo-8-ml-1265\\/1-6a1657cb84489.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(40,'1004-C',3,NULL,'Painting Premiun','painting-premiun-1004-c','[\"sin_graduacion\"]','Painting Premiun',13000.00,14000.00,10000.00,50,'[\"products\\/painting-premiun-1004-c\\/1-6a1657b802966.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(41,'1202',3,NULL,'Painting Primavera','painting-primavera-1202','[\"sin_graduacion\"]','Painting Primavera',38000.00,NULL,27000.00,50,'[\"products\\/painting-primavera-1202\\/1-6a1657c56bb44.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(42,'1034',3,NULL,'Piedra de Resina Tradicional','piedra-de-resina-tradicional-1034','[\"sin_graduacion\"]','Cristales decorativos para nail art. Añade brillo y elegancia a tus diseños. ¡Decora tus uñas co...',8500.00,9000.00,6000.00,50,'[\"products\\/piedra-de-resina-tradicional-1034\\/1-6a1657baa54c0.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(43,'1263',3,NULL,'Piedra Resina Naturaleza Muerta','piedra-resina-naturaleza-muerta-1263','[\"sin_graduacion\"]','Decoraciones únicas de resina para nail art. Diseños detallados de naturaleza muerta. ¡Crea manic...',9500.00,10000.00,7500.00,50,'[\"products\\/piedra-resina-naturaleza-muerta-1263\\/1-6a1657cb2f239.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(44,'1033',3,NULL,'Piedra Swarovski 5 Líneas','piedra-swarovski-5-lineas-1033','[\"sin_graduacion\"]','Decora tus uñas con cristales brillantes. Disponible en blanco, plateado y tornasol. ¡Añade lujo ...',7000.00,NULL,4500.00,50,'[\"products\\/piedra-swarovski-5-lineas-1033\\/1-6a1657ba8aac6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(45,'2005',3,NULL,'Piedra Swarovsky Mixta','piedra-swarovsky-mixta-2005','[\"sin_graduacion\"]','Piedra Swarovsky Mixta',7000.00,NULL,4500.00,50,'[\"products\\/piedra-swarovsky-mixta-2005\\/1-6a1657d10e759.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(46,'1038',3,NULL,'Pigmento Neón Jumbo','pigmento-neon-jumbo-1038','[\"sin_graduacion\"]','Torre Pigmento Neón en presentación sólida para manicure. Colores vibrantes, organizados y sin re...',9500.00,10000.00,7000.00,50,'[\"products\\/pigmento-neon-jumbo-1038\\/1-6a1657bb105de.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(47,'1040-A',3,NULL,'Porta Brochas Corazón','porta-brochas-corazon-1040-a','[\"sin_graduacion\"]','Organiza tus pinceles de maquillaje y nail art con un toque romántico. Diseño funcional y decorati...',24000.00,25000.00,21000.00,50,'[\"products\\/porta-brochas-corazon-1040-a\\/1-6a1657bbb2d01.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(48,'1315',3,NULL,'Porta Brochas Doble','porta-brochas-doble-1315','[\"sin_graduacion\"]','Porta Brochas Doble',50000.00,55000.00,42000.00,50,'[\"products\\/porta-brochas-doble-1315\\/1-6a1657d0436d3.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(49,'1214',3,NULL,'Porta Brochas Redondo Premium','porta-brochas-redondo-premium-1214','[\"sin_graduacion\"]','Elegante organizador para tus pinceles. Mantiene tus herramientas protegidas y accesibles. ¡Calidad...',23000.00,25000.00,17000.00,50,'[\"products\\/porta-brochas-redondo-premium-1214\\/1-6a1657c6813d9.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(50,'1267',3,NULL,'Spider Gel - 8ml','spider-gel-8ml-1267','[\"sin_graduacion\"]','Gel elástico para nail art. Crea líneas finas y diseños geométricos únicos. ¡Textura y creativ...',13000.00,14000.00,10000.00,50,'[\"products\\/spider-gel-8ml-1267\\/1-6a1657cbb6732.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(51,'1031',3,NULL,'Stickers Adhesivo en Línea','stickers-adhesivo-en-linea-1031','[\"sin_graduacion\"]','Decora tus uñas con líneas finas en plata, dorado u oro rosa. Nail art fácil y preciso. ¡Diseño...',2000.00,NULL,1000.00,50,'[\"products\\/stickers-adhesivo-en-linea-1031\\/1-6a1657ba31796.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(52,'1030',3,NULL,'Stickers de Uñas con Diseño','stickers-de-unas-con-diseno-1030','[\"sin_graduacion\"]','Diseños tradicionales y de lujo para un nail art instantáneo. Fácil aplicación. ¡Personaliza tu...',2000.00,NULL,700.00,50,'[\"products\\/stickers-de-unas-con-diseno-1030\\/1-6a1657ba12e60.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(53,'1030-A',3,NULL,'Stickers de Uñas Luxury','stickers-de-unas-luxury-1030-a','[\"sin_graduacion\"]','Stickers de Uñas Luxury',3500.00,NULL,1500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(54,'2036',3,NULL,'Torre Decoración Jumbo Blue x6','torre-decoracion-jumbo-blue-x6-2036','[\"sin_graduacion\"]','Polvos decorativos en tonos azules vibrantes y profundos. Ideal para crear efectos únicos, contrast...',12000.00,13000.00,9500.00,50,'[\"products\\/torre-decoracion-jumbo-blue-x6-2036\\/1-6a1657d5a50ee.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(55,'2035',3,NULL,'Torre Decoración Jumbo Gold x6','torre-decoracion-jumbo-gold-x6-2035','[\"sin_graduacion\"]','Set de polvos dorados para efectos elegantes y llamativos en uñas acrílicas, gel o semipermanentes...',12000.00,13000.00,9500.00,50,'[\"products\\/torre-decoracion-jumbo-gold-x6-2035\\/1-6a1657d58834a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(56,'2034',3,NULL,'Torre Decoración Jumbo Pink x6','torre-decoracion-jumbo-pink-x6-2034','[\"sin_graduacion\"]','Polvos decorativos en acabado rosado, perfectos para looks delicados, femeninos y llenos de luz. For...',12000.00,13000.00,9500.00,50,'[\"products\\/torre-decoracion-jumbo-pink-x6-2034\\/1-6a1657d56f268.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(57,'2033',3,NULL,'Torre Decoración Jumbo Silver x6','torre-decoracion-jumbo-silver-x6-2033','[\"sin_graduacion\"]','Torre de polvos decorativos en tono plateado, ideal para agregar brillo intenso y detalles metálico...',12000.00,13000.00,9500.00,50,'[\"products\\/torre-decoracion-jumbo-silver-x6-2033\\/1-6a1657d556abc.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(58,'2037',3,NULL,'Torre Decoración Jumbo Tornasol x6','torre-decoracion-jumbo-tornasol-x6-2037','[\"sin_graduacion\"]','Polvos tornasolados con reflejos cambiantes que aportan un brillo multidimensional a cualquier dise�...',12000.00,13000.00,9500.00,50,'[\"products\\/torre-decoracion-jumbo-tornasol-x6-2037\\/1-6a1657d5bea1d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(59,'2028',3,NULL,'Torre Efecto Espejo X3','torre-efecto-espejo-x3-2028','[\"sin_graduacion\"]','Polvo de color ultra vibrante para nail art. Crea diseños llamativos y luminosos. ¡Uñas que brill...',7000.00,NULL,4200.00,50,'[\"products\\/torre-efecto-espejo-x3-2028\\/1-6a1657d48261f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(60,'1037',3,NULL,'Torre Efecto Espejo X6','torre-efecto-espejo-x6-1037','[\"sin_graduacion\"]','Polvo de color ultra vibrante para nail art. Crea diseños llamativos y luminosos. ¡Uñas que brill...',9000.00,9600.00,6500.00,50,'[\"products\\/torre-efecto-espejo-x6-1037\\/1-6a1657bae66da.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(61,'1251',3,NULL,'Torre Efecto Flaker','torre-efecto-flaker-1251','[\"sin_graduacion\"]','Hojuelas decorativas para nail art. Crea efectos iridiscentes y metálicos únicos. ¡Uñas con un a...',10000.00,11000.00,8000.00,50,'[\"products\\/torre-efecto-flaker-1251\\/1-6a1657ca24868.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(62,'1250',3,NULL,'Torre Efecto Glitter','torre-efecto-glitter-1250','[\"sin_graduacion\"]','Conjunto de cinco glitters de distintos tonos y texturas en formato apilable. Para brillo intenso en...',9500.00,10000.00,7500.00,50,'[\"products\\/torre-efecto-glitter-1250\\/1-6a1657ca07994.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(63,'1039-B',3,NULL,'Torre Efecto Glow x6','torre-efecto-glow-x6-1039-b','[\"sin_graduacion\"]','Polvos que brillan en la oscuridad para uñas. Crea diseños luminosos y divertidos. ¡Uñas que res...',7000.00,NULL,4500.00,50,'[\"products\\/torre-efecto-glow-x6-1039-b\\/1-6a1657bb705ed.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(64,'1039-C',3,NULL,'Torre Efecto Hollywood x6','torre-efecto-hollywood-x6-1039-c','[\"sin_graduacion\"]','Polvos iridiscentes para uñas. Crea un efecto aurora boreal mágico y tornasolado. ¡Brillo multidi...',7500.00,NULL,4500.00,50,'[\"products\\/torre-efecto-hollywood-x6-1039-c\\/1-6a1657bb8ed0e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(65,'1039-A',3,NULL,'Torre Efecto Micro Caviar x6','torre-efecto-micro-caviar-x6-1039-a','[\"sin_graduacion\"]','Set de microperlas para nail art. Añade textura y dimensión a tus uñas. ¡Diseños 3D únicos y s...',9500.00,10000.00,7500.00,50,'[\"products\\/torre-efecto-micro-caviar-x6-1039-a\\/1-6a1657bb4ff4e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(66,'1039',3,NULL,'Torre Pigmento Pastel Jumbo','torre-pigmento-pastel-jumbo-1039','[\"sin_graduacion\"]','Polvo de color suave y delicado para nail art. Crea diseños sutiles y elegantes. ¡Uñas con tonos ...',9500.00,10000.00,4500.00,50,'[\"products\\/torre-pigmento-pastel-jumbo-1039\\/1-6a1657bb30e0b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(67,'1249',3,NULL,'Torre Princesa','torre-princesa-1249','[\"sin_graduacion\"]','Set de cinco brillos finos tipo torre con colores suaves y luminosos. Ideales para destellos delicad...',9500.00,10000.00,6600.00,50,'[\"products\\/torre-princesa-1249\\/1-6a1657c9e3d29.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(68,'1018',4,NULL,'Cargador 24 Watts','cargador-24-watts-1018','[\"sin_graduacion\"]','Adaptador de reemplazo de alta calidad, compatible con equipos de manicure y pedicure de 24W. Carga ...',9500.00,10000.00,6600.00,50,'[\"products\\/cargador-24-watts-1018\\/1-6a1657b86c788.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(69,'1204',4,NULL,'Lámpara Corazón 120 Watts','lampara-corazon-120-watts-1204','[\"sin_graduacion\"]','Lámpara UV/LED de alta potencia. Diseño en tonos metálicos. Incluye guantes protectores y lima 10...',64000.00,70000.00,55000.00,50,'[\"products\\/lampara-corazon-120-watts-1204\\/1-6a1657c586a7c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(70,'1274',4,NULL,'Lámpara de Mesa Grande','lampara-de-mesa-grande-1274','[\"sin_graduacion\"]','Iluminación precisa y ajustable para tu estación de manicura. Disponible en rosa y lila. ¡Trabaja...',74000.00,80000.00,65000.00,50,'[\"products\\/lampara-de-mesa-grande-1274\\/1-6a1657cc7cfbc.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(71,'1274-A',4,NULL,'Lámpara de Mesa Pequeña','lampara-de-mesa-pequena-1274-a','[\"sin_graduacion\"]','Lámpara de Mesa Pequeña',51000.00,55000.00,45000.00,50,'[\"products\\/lampara-de-mesa-pequena-1274-a\\/1-6a1657cc97b7b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(72,'1275',4,NULL,'Lámpara Murano - 120W','lampara-murano-120w-1275','[\"sin_graduacion\"]','Potente lámpara UV/LED para un curado ultrarrápido de geles. Ideal para uso profesional. ¡Eficien...',64000.00,70000.00,55000.00,50,'[\"products\\/lampara-murano-120w-1275\\/1-6a1657ccb144f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(73,'1016-B',4,NULL,'Lámpara press on profesional Bicolor','lampara-press-on-profesional-bicolor-1016-b','[\"sin_graduacion\"]','Secado específico para uñas press-on. Fijación rápida y duradera con luz UV/LED. ¡Resultados im...',23000.00,25000.00,18000.00,50,'[\"products\\/lampara-press-on-profesional-bicolor-1016-b\\/1-6a1657b858116.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(74,'1013',4,NULL,'Lámpara UV/LED 48 watts matte','lampara-uvled-48-watts-matte-1013','[\"sin_graduacion\"]','Secado rápido y uniforme de esmaltes y geles. Diseño elegante y moderno. ¡Manicuras duraderas y r...',33000.00,35000.00,25000.00,50,'[\"products\\/lampara-uvled-48-watts-matte-1013\\/1-6a1657b83baee.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(75,'7077',4,NULL,'Motor Tool con Pedal','motor-tool-con-pedal-7077','[\"sin_graduacion\"]','Equipo profesional de alta potencia con control por pedal. Velocidad ajustable para trabajar todo ti...',121000.00,140000.00,95000.00,50,'[\"products\\/motor-tool-con-pedal-7077\\/1-6a1657d67c35e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(76,'1006',4,NULL,'Motor Tool Inalámbrico Bicolor + Bledo SG702','motor-tool-inalambrico-bicolor-bledo-sg702-1006','[\"sin_graduacion\"]','Set profesional portátil para manicuras y pedicuras. Potencia y comodidad sin cables. ¡Acabados im...',121000.00,140000.00,95000.00,50,'[\"products\\/motor-tool-inalambrico-bicolor-bledo-sg702-1006\\/1-6a1657b81ed92.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(77,'1273',4,NULL,'Motor tool Inalambrico BQ109','motor-tool-inalambrico-bq109-1273','[\"sin_graduacion\"]','Herramienta esencial para pulir y preparar uñas. Incluye bledo para limpieza. ¡Precisión y eficie...',130000.00,140000.00,115000.00,50,'[\"products\\/motor-tool-inalambrico-bq109-1273\\/1-6a1657cc66b71.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(78,'1309',4,NULL,'Motortull Hollywood','motortull-hollywood-1309','[\"sin_graduacion\"]','Motortull Hollywood',142000.00,155000.00,125000.00,50,'[\"products\\/motortull-hollywood-1309\\/1-6a1657d011275.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(79,'1108',4,NULL,'Olla Metálica x250','olla-metalica-x250-1108','[\"sin_graduacion\"]','Calentador de cera compacto de 250ml. Ideal para trabajos pequeños o retoques, manteniendo la cera ...',52000.00,NULL,37500.00,50,'[\"products\\/olla-metalica-x250-1108\\/1-6a1657c1cb4e2.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(80,'1107',4,NULL,'Olla Metálica x500','olla-metalica-x500-1107','[\"sin_graduacion\"]','Calentador de cera de mayor capacidad (500ml). Ideal para uso continuo en salones, manteniendo la ce...',64000.00,NULL,46000.00,50,'[\"products\\/olla-metalica-x500-1107\\/1-6a1657c1afa7a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(81,'1109',4,NULL,'Olla Pro Wax','olla-pro-wax-1109','[\"sin_graduacion\"]','Calentador de cera de diseño elegante y moderno. Ideal para un calentamiento rápido y eficaz de to...',29000.00,30000.00,24000.00,50,'[\"products\\/olla-pro-wax-1109\\/1-6a1657c1e5059.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(82,'1227',5,NULL,'Bledo Copa','bledo-copa-1227','[\"sin_graduacion\"]','Transforma tus uñas con Bledo Copa. Diseño elegante y durabilidad excepcional para una manicura pe...',17000.00,18000.00,13000.00,50,'[\"products\\/bledo-copa-1227\\/1-6a1657c7a6e1c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(83,'1258',5,NULL,'Bledo Flor','bledo-flor-1258','[\"sin_graduacion\"]','Dale un toque a tus uñas con Bledo Flor. Diseños vibrantes y fácil aplicación para manicuras cre...',16000.00,18000.00,10000.00,50,'[\"products\\/bledo-flor-1258\\/1-6a1657cad644c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(84,'7046',5,NULL,'Bledo Murano','bledo-murano-7046','[\"sin_graduacion\"]','Brilla como nunca con Bledo Murano. Lujo y sofisticación en cada uña, con un brillo deslumbrante y...',22000.00,24000.00,17000.00,50,'[\"products\\/bledo-murano-7046\\/1-6a1657d64bcd2.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(85,'7045',5,NULL,'Bledo Murano Diamond','bledo-murano-diamond-7045','[\"sin_graduacion\"]','Brilla como nunca con Bledo Murano Diamond. Lujo y sofisticación en cada uña, con un brillo deslum...',28000.00,30000.00,23000.00,50,'[\"products\\/bledo-murano-diamond-7045\\/1-6a1657d628969.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(86,'1226',5,NULL,'Bledo Profesional Bicolor','bledo-profesional-bicolor-1226','[\"sin_graduacion\"]','Elimina exceso de polvo de tus uñas. Diseño ergonómico y dual. ¡Higiene y limpieza en tu salón!...',11000.00,12000.00,9000.00,50,'[\"products\\/bledo-profesional-bicolor-1226\\/1-6a1657c78940b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(87,'1025',6,NULL,'Pincel Acrilico Duo #8 #10','pincel-acrilico-duo-8-10-1025','[\"sin_graduacion\"]','Pincel 2 en 1 ideal para la construcción de uñas acrílicas. Tamaños #8 y #10. ¡Ideal para escul...',7500.00,8000.00,5500.00,50,'[\"products\\/pincel-acrilico-duo-8-10-1025\\/1-6a1657b9b16c7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(88,'1205',6,NULL,'Pincel Acrílico Piedra X5','pincel-acrilico-piedra-x5-1205','[\"sin_graduacion\"]','Técnica, control y variedad en tus manos. El set de Pinceles Acrílico x5 incluye cinco unidades id...',14000.00,15000.00,11000.00,50,'[\"products\\/pincel-acrilico-piedra-x5-1205\\/1-6a1657c5a06e7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(89,'1020-C',6,NULL,'Pincel de Lujo Confite x3','pincel-de-lujo-confite-x3-1020-c','[\"sin_graduacion\"]','Set de tres pinceles profesionales con diseño inspirado en confites. Cerdas suaves y resistentes pa...',30000.00,32000.00,24000.00,50,'[\"products\\/pincel-de-lujo-confite-x3-1020-c\\/1-6a1657b9263b0.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(90,'1310',6,NULL,'Pincel Duo #2 y #12','pincel-duo-2-y-12-1310','[\"sin_graduacion\"]','Pincel Duo #2 y #12',8500.00,9000.00,6500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(91,'1019-10',6,NULL,'Pincel Kolinsky #10','pincel-kolinsky-10-1019-10','[\"sin_graduacion\"]','Calidad profesional para aplicación de acrílico. Precisión, durabilidad y control excepcionales. ...',50000.00,55000.00,42000.00,50,'[\"products\\/pincel-kolinsky-10-1019-10\\/1-6a1657b8e0997.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(92,'1019-12',6,NULL,'Pincel Kolinsky #12','pincel-kolinsky-12-1019-12','[\"sin_graduacion\"]','Calidad profesional para aplicación de acrílico. Precisión, durabilidad y control excepcionales. ...',64000.00,70000.00,55000.00,50,'[\"products\\/pincel-kolinsky-12-1019-12\\/1-6a1657b910108.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(93,'1019-02',6,NULL,'Pincel Kolinsky #2','pincel-kolinsky-2-1019-02','[\"sin_graduacion\"]','Calidad profesional para aplicación de acrílico. Precisión, durabilidad y control excepcionales. ...',26000.00,28000.00,20000.00,50,'[\"products\\/pincel-kolinsky-2-1019-02\\/1-6a1657b8936ed.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(94,'1019-08',6,NULL,'Pincel Kolinsky #8','pincel-kolinsky-8-1019-08','[\"sin_graduacion\"]','Calidad profesional para aplicación de acrílico. Precisión, durabilidad y control excepcionales. ...',42000.00,45000.00,33000.00,50,'[\"products\\/pincel-kolinsky-8-1019-08\\/1-6a1657b8c0db3.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(95,'1246',6,NULL,'Pincel Liner 15 mm Individual','pincel-liner-15-mm-individual-1246','[\"sin_graduacion\"]','Pincel individual de precisión para nail art. Crea líneas finas y detalles definidos. ¡Esencial p...',9500.00,10000.00,7000.00,50,'[\"products\\/pincel-liner-15-mm-individual-1246\\/1-6a1657c97bb9a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(96,'1247',6,NULL,'Pincel Liner 20 mm Individual','pincel-liner-20-mm-individual-1247','[\"sin_graduacion\"]','Pincel individual de precisión para nail art. Crea líneas finas y detalles definidos. ¡Esencial p...',10000.00,11000.00,7500.00,50,'[\"products\\/pincel-liner-20-mm-individual-1247\\/1-6a1657c99658a.webp\",\"products\\/pincel-liner-20-mm-individual-1247\\/2-6a1657c9af2b5.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(97,'1248',6,NULL,'Pincel Liner Escarcha x 5','pincel-liner-escarcha-x-5-1248','[\"sin_graduacion\"]','Set para nail art de precisión. Crea diseños finos y detalles brillantes. ¡Dale un toque único a...',13000.00,14000.00,10000.00,50,'[\"products\\/pincel-liner-escarcha-x-5-1248\\/1-6a1657c9c9b9a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(98,'2038',6,NULL,'Pincel Liner Tapa x3','pincel-liner-tapa-x3-2038','[\"sin_graduacion\"]','Pincel Liner Tapa x3',19000.00,20000.00,14000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(99,'1290',6,NULL,'Pincel Liner Tapa x5','pincel-liner-tapa-x5-1290','[\"sin_graduacion\"]','Incluye cinco unidades ideales para trazos finos, líneas artísticas y detalles delicados en manicu...',33000.00,35000.00,24000.00,50,'[\"products\\/pincel-liner-tapa-x5-1290\\/1-6a1657cdf3982.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(100,'1289',6,NULL,'Pincel Liner Unicornio x3','pincel-liner-unicornio-x3-1289','[\"sin_graduacion\"]','Set de tres pinceles con diseño vibrante y punta fina, ideales para líneas precisas, detalles art�...',10000.00,11000.00,8000.00,50,'[\"products\\/pincel-liner-unicornio-x3-1289\\/1-6a1657cdda079.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(101,'1021',6,NULL,'Pincel Liner x5 + Punzón','pincel-liner-x5-punzon-1021','[\"sin_graduacion\"]','Set completo de pinceles liner de alta precisión y punzones. Mango escarchado en colores vibrantes....',11000.00,12000.00,9500.00,50,'[\"products\\/pincel-liner-x5-punzon-1021\\/1-6a1657b9481ca.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(102,'1024',6,NULL,'Pincel Polygel de Lujo','pincel-polygel-de-lujo-1024','[\"sin_graduacion\"]','Herramienta doble profesional para Polygel. Aplica y moldea con facilidad. ¡Construye uñas perfect...',7500.00,8000.00,5000.00,50,'[\"products\\/pincel-polygel-de-lujo-1024\\/1-6a1657b994ce3.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(103,'1299',6,NULL,'Pincel Punta de Goma','pincel-punta-de-goma-1299','[\"sin_graduacion\"]','Pincel Punta de Goma',9500.00,10000.00,7000.00,50,'[\"products\\/pincel-punta-de-goma-1299\\/1-6a1657cf33ef3.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(104,'1023',6,NULL,'Punzón de Colores x5','punzon-de-colores-x5-1023','[\"sin_graduacion\"]','Herramientas ideales para crear puntos, flores, líneas y diseños precisos en nail art. Doble punta...',4500.00,NULL,3200.00,50,'[\"products\\/punzon-de-colores-x5-1023\\/1-6a1657b974f57.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(105,'2023',6,NULL,'Set Pinceladas x5','set-pinceladas-x5-2023','[\"sin_graduacion\"]','Set de cinco pinceles para pinceladas artísticas en nail art....',16000.00,17000.00,12000.00,50,'[\"products\\/set-pinceladas-x5-2023\\/1-6a1657d36db1c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(106,'1208',7,NULL,'Bloque Pulidor','bloque-pulidor-1208','[\"sin_graduacion\"]','Suaviza y da brillo a tus uñas. Ideal para preparar la superficie y lograr un acabado profesional i...',2000.00,NULL,1200.00,50,'[\"products\\/bloque-pulidor-1208\\/1-6a1657c5cea7f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(107,'1291',7,NULL,'Corta Cutícula Académico','corta-cuticula-academico-1291','[\"sin_graduacion\"]','Herramienta esencial para prácticas académicas. Precisión, seguridad y durabilidad en acero inoxi...',12000.00,13000.00,8900.00,50,'[\"products\\/corta-cuticula-academico-1291\\/1-6a1657ce1a3fb.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(108,'1292',7,NULL,'Corta Cutícula Máster','corta-cuticula-master-1292','[\"sin_graduacion\"]','Filo quirúrgico, acero inoxidable premium y acabado pulido. ¡Para profesionales exigentes que busc...',28000.00,30000.00,23000.00,50,'[\"products\\/corta-cuticula-master-1292\\/1-6a1657ce332e6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(109,'1293',7,NULL,'Corta Cutícula Pies','corta-cuticula-pies-1293','[\"sin_graduacion\"]','Elimina con precisión el exceso de cutícula en los pies. Diseño ergonómico en acero inoxidable. ...',19000.00,20000.00,15500.00,50,'[\"products\\/corta-cuticula-pies-1293\\/1-6a1657ce4df3d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(110,'1068',7,NULL,'Corta Cutícula Tornasol','corta-cuticula-tornasol-1068','[\"sin_graduacion\"]','Herramienta de precisión para cutículas. Acabado tornasol y corte limpio. ¡Manicura impecable y p...',13000.00,14000.00,10000.00,50,'[\"products\\/corta-cuticula-tornasol-1068\\/1-6a1657be9635f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(111,'1302',7,NULL,'Cortauñas','cortaunas-1302','[\"sin_graduacion\"]','Herramienta resistente y de corte preciso, fabricada en acero inoxidable. Disponible en punta recta ...',7000.00,NULL,3900.00,50,'[\"products\\/cortaunas-1302\\/1-6a1657cf8432e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(112,'2030',7,NULL,'Esponja de Difuminar','esponja-de-difuminar-2030','[\"sin_graduacion\"]','Cubos de espuma suave para difuminar cosméticos y esmaltes. Textura porosa para transiciones de col...',12000.00,13000.00,9500.00,50,'[\"products\\/esponja-de-difuminar-2030\\/1-6a1657d4b6ae5.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(113,'1049',7,NULL,'Estuche de Pusher x 2 Oro Rosa','estuche-de-pusher-x-2-oro-rosa-1049','[\"sin_graduacion\"]','Set de empujadores de cutícula con estilo. Disponibles en tornasol y oro rosa. ¡Diseño y funciona...',8500.00,NULL,6000.00,50,'[\"products\\/estuche-de-pusher-x-2-oro-rosa-1049\\/1-6a1657bc626df.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(114,'1051',7,NULL,'Guillotina Metálica','guillotina-metalica-1051','[\"sin_graduacion\"]','Corta tips y uñas artificiales con precisión. Herramienta resistente para uso profesional. ¡Corte...',5000.00,NULL,3000.00,50,'[\"products\\/guillotina-metalica-1051\\/1-6a1657bc7f763.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(115,'1285',7,NULL,'Imán 5 en 1 Profesional','iman-5-en-1-profesional-1285','[\"sin_graduacion\"]','Crea efectos magnéticos únicos con el Imán 5 en 1 Profesional. Ideal para esmaltes cat eye. Permi...',14000.00,15000.00,10000.00,50,'[\"products\\/iman-5-en-1-profesional-1285\\/1-6a1657cd74788.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(116,'1288',7,NULL,'Imán Flor','iman-flor-1288','[\"sin_graduacion\"]','Crea efectos magnéticos con forma de pétalo y profundidad única. Ideal para diseños cat eye deli...',3500.00,NULL,2200.00,50,'[\"products\\/iman-flor-1288\\/1-6a1657cdbe828.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(117,'1286',7,NULL,'Imán X1 Halo y Edge','iman-x1-halo-y-edge-1286','[\"sin_graduacion\"]','Diseñado para crear efectos cat eye definidos y centrados. Perfecto para estilos minimalistas o de ...',4500.00,NULL,3000.00,50,'[\"products\\/iman-x1-halo-y-edge-1286\\/1-6a1657cd8ccbd.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(118,'1287',7,NULL,'Imán X2 Heart Look','iman-x2-heart-look-1287','[\"sin_graduacion\"]','Permite generar efectos cat eye en dos direcciones, logrando profundidad y dinamismo en cada diseño...',4500.00,NULL,3000.00,50,'[\"products\\/iman-x2-heart-look-1287\\/1-6a1657cda5b3d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(119,'1300',7,NULL,'Katana de Lujo','katana-de-lujo-1300','[\"sin_graduacion\"]','Elegancia y precisión en tus manos. Combina diseño exclusivo y filo superior para cortes limpios y...',7500.00,8000.00,5500.00,50,'[\"products\\/katana-de-lujo-1300\\/1-6a1657cf4f06d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(120,'1067',7,NULL,'Palito de Naranjo x 100 Uds','palito-de-naranjo-x-100-uds-1067','[\"sin_graduacion\"]','Empuja cutículas y limpia uñas suavemente. Desechables para máxima higiene. ¡Imprescindible en t...',4500.00,NULL,3500.00,50,'[\"products\\/palito-de-naranjo-x-100-uds-1067\\/1-6a1657be7ada0.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(121,'1141-1',7,NULL,'Pinza Cónica Premium Tornasol','pinza-conica-premium-tornasol-1141-1','[\"sin_graduacion\"]','Pinza cónica profesional con diseño tornasol iridiscente para ondulaciones y rizos definidos de di...',64000.00,70000.00,55000.00,50,'[\"products\\/pinza-conica-premium-tornasol-1141-1\\/1-6a1657c4f3d34.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(122,'1043',7,NULL,'Pusher Metálico en Acero','pusher-metalico-en-acero-1043','[\"sin_graduacion\"]','Empuja cutículas con precisión y durabilidad. Herramienta profesional de acero inoxidable. ¡Manic...',2500.00,NULL,1300.00,50,'[\"products\\/pusher-metalico-en-acero-1043\\/1-6a1657bc0ac74.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(123,'1269',7,NULL,'Pusher Premium','pusher-premium-1269','[\"sin_graduacion\"]','Empuja cutículas con precisión y durabilidad. Herramienta profesional de acero inoxidable. ¡Manic...',9000.00,10000.00,6500.00,50,'[\"products\\/pusher-premium-1269\\/1-6a1657cbf2507.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(124,'1043-A',7,NULL,'Pusher Triángulo','pusher-triangulo-1043-a','[\"sin_graduacion\"]','Herramienta de diseño triangular para retirar producto sobre tus uñas. ¡Manicura detallada y efic...',4500.00,5000.00,1500.00,50,'[\"products\\/pusher-triangulo-1043-a\\/1-6a1657bc2aafc.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(125,'1066',7,NULL,'Raspa Callos en Madera','raspa-callos-en-madera-1066','[\"sin_graduacion\"]','Elimina durezas y callosidades suavemente. Diseño ergonómico para pies suaves. ¡Pedicura profesio...',2000.00,NULL,1450.00,50,'[\"products\\/raspa-callos-en-madera-1066\\/1-6a1657be61465.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(126,'1253',7,NULL,'Tijera Manicura Rusa','tijera-manicura-rusa-1253','[\"sin_graduacion\"]','Tijera de precisión para cortar cutículas. Ideal para manicura en seco. ¡Acabado impecable y deta...',24000.00,25000.00,20000.00,50,'[\"products\\/tijera-manicura-rusa-1253\\/1-6a1657ca55947.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(127,'1207',8,NULL,'Lima de Brillo Profesional','lima-de-brillo-profesional-1207','[\"sin_graduacion\"]','Consigue un brillo espejo natural sin esmalte. Pule y sella la uña. ¡Acabado profesional y durader...',3000.00,NULL,1500.00,50,'[\"products\\/lima-de-brillo-profesional-1207\\/1-6a1657c5b9020.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(128,'1026',8,NULL,'Lima Sponge 100/180','lima-sponge-100180-1026','[\"sin_graduacion\"]','Lima pulidora suave para preparación de uñas. Ideal para superficies delicadas y acabados finos. �...',2000.00,NULL,1200.00,50,'[\"products\\/lima-sponge-100180-1026\\/1-6a1657b9ca250.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(129,'1304',8,NULL,'Lima Sponge Estampada','lima-sponge-estampada-1304','[\"sin_graduacion\"]','Estilo y suavidad en cada pasada. Lima Sponge estampada con diseño llamativo para un limado delicad...',2500.00,NULL,1700.00,50,'[\"products\\/lima-sponge-estampada-1304\\/1-6a1657cfb865c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(130,'1027-A',8,NULL,'Lima Zebra con Regla','lima-zebra-con-regla-1027-a','[\"sin_graduacion\"]','Lima profesional con diferentes granos y regla integrada. Precisión para esculpir uñas acrílicas ...',2500.00,NULL,1600.00,50,'[\"products\\/lima-zebra-con-regla-1027-a\\/1-6a1657b9eb7e5.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(131,'1003',9,NULL,'Base Coat Miracle x15ml','base-coat-miracle-x15ml-1003','[\"sin_graduacion\"]','Base Coat Miracle x15ml',19000.00,20000.00,15000.00,50,'[\"products\\/base-coat-miracle-x15ml-1003\\/1-6a1657b78ca39.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(132,'1004',9,NULL,'Brillo Coat Miracle x15ml','brillo-coat-miracle-x15ml-1004','[\"sin_graduacion\"]','Brillo Coat Miracle x15ml',19000.00,20000.00,15000.00,50,'[\"products\\/brillo-coat-miracle-x15ml-1004\\/1-6a1657b7a3b87.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(133,'1036-V',9,NULL,'Caviar Vintage','caviar-vintage-1036-v','[\"sin_graduacion\"]','Caviar Vintage',5500.00,NULL,4000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(134,'2013',9,NULL,'Decoracion Aurora Diamond','decoracion-aurora-diamond-2013','[\"sin_graduacion\"]','Decoracion Aurora Diamond',7500.00,8000.00,5000.00,50,'[\"products\\/decoracion-aurora-diamond-2013\\/1-6a1657d274e82.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(135,'2023-M',9,NULL,'Decoracion Mariposa','decoracion-mariposa-2023-m','[\"sin_graduacion\"]','Decoracion Mariposa',5000.00,NULL,3500.00,50,'[\"products\\/decoracion-mariposa-2023-m\\/1-6a1657d38703c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(136,'7633',9,NULL,'Jabón en Espuma Limpiador','jabon-en-espuma-limpiador-7633','[\"sin_graduacion\"]','Jabón en Espuma Limpiador',11000.00,12000.00,8500.00,50,'[\"products\\/jabon-en-espuma-limpiador-7633\\/1-6a1657d722912.webp\",\"products\\/jabon-en-espuma-limpiador-7633\\/2-6a1657d75e513.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(137,'1032',9,NULL,'Llavero de Uñas x 50 Uds','llavero-de-unas-x-50-uds-1032','[\"sin_graduacion\"]','Exhibe tus diseños y muestras de color de forma práctica. Ideal para salones y clases. ¡Organiza ...',5000.00,NULL,3300.00,50,'[\"products\\/llavero-de-unas-x-50-uds-1032\\/1-6a1657ba4e57b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(138,'1056-30ml',9,NULL,'Polygel x 30 ml','polygel-x-30-ml-1056-30ml','[\"sin_graduacion\"]','Polygel x 30 ml',14000.00,15000.00,10500.00,50,'[\"products\\/polygel-x-30-ml-1056-30ml\\/1-6a1657bd53ef8.webp\",\"products\\/polygel-x-30-ml-1056-30ml\\/2-6a1657bda16ec.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(139,'1254',9,NULL,'Uñas Coffin Tips x 600','unas-coffin-tips-x-600-1254','[\"sin_graduacion\"]','Tips profesionales estilo coffin para extensiones de uñas. Gran cantidad para tu salón. ¡Diseño ...',17000.00,18000.00,15000.00,50,'[\"products\\/unas-coffin-tips-x-600-1254\\/1-6a1657ca70657.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(140,'1255',9,NULL,'Uñas Coffin Tips x 600 XXL','unas-coffin-tips-x-600-xxl-1255','[\"sin_graduacion\"]','Tips extralargos estilo coffin para extensiones dramáticas. Crea looks impactantes y modernos. ¡Vo...',23000.00,25000.00,18000.00,50,'[\"products\\/unas-coffin-tips-x-600-xxl-1255\\/1-6a1657ca8a19a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(141,'1212',9,NULL,'Uñas Coffin x 100','unas-coffin-x-100-1212','[\"sin_graduacion\"]','Tips estilo coffin para uñas acrílicas y de gel. Diseño moderno y sofisticado. ¡Crea manicuras a...',8500.00,9000.00,6000.00,50,'[\"products\\/unas-coffin-x-100-1212\\/1-6a1657c625130.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(142,'1211',9,NULL,'Uñas Curva C x 100','unas-curva-c-x-100-1211','[\"sin_graduacion\"]','Tips con curva C para uñas acrílicas y de gel. Diseño elegante y natural. ¡Logra extensiones res...',8500.00,9000.00,6000.00,50,'[\"products\\/unas-curva-c-x-100-1211\\/1-6a1657c60bf77.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(143,'1256',9,NULL,'Uñas Curva C x 600 XXL','unas-curva-c-x-600-xxl-1256','[\"sin_graduacion\"]','Tips extralargos con curva C para extensiones de uñas. Ideales para un look elegante y resistente. ...',23000.00,25000.00,18000.00,50,'[\"products\\/unas-curva-c-x-600-xxl-1256\\/1-6a1657caa3e44.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(144,'1210',9,NULL,'Uñas Dual System x 100','unas-dual-system-x-100-1210','[\"sin_graduacion\"]','Moldes reutilizables para construcción de uñas acrílicas y de gel. Fácil aplicación para manicu...',7500.00,8000.00,2500.00,50,'[\"products\\/unas-dual-system-x-100-1210\\/1-6a1657c5e6dd2.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(145,'1268',9,NULL,'Uñas Dual System x 100 Almendradas','unas-dual-system-x-100-almendradas-1268','[\"sin_graduacion\"]','Moldes reutilizables para uñas acrílicas y de gel con forma almendrada. ¡Facilita diseños elegan...',8000.00,9000.00,5500.00,50,'[\"products\\/unas-dual-system-x-100-almendradas-1268\\/1-6a1657cbdb221.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(146,'7055',9,NULL,'Uñas Dual System x 100 Coffin','unas-dual-system-x-100-coffin-7055','[\"sin_graduacion\"]','Moldes reutilizables para construir uñas acrílicas y gel con forma coffin. ¡Fácil aplicación y ...',8000.00,9000.00,5500.00,50,'[\"products\\/unas-dual-system-x-100-coffin-7055\\/1-6a1657d66312d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(147,'1213-A',9,NULL,'Uñas Soft Gel Almendra x240','unas-soft-gel-almendra-x240-1213-a','[\"sin_graduacion\"]','Tips Soft Gel marca Miracle con 240 unidades. Material flexible y ligero para adherencia óptima y a...',11000.00,12000.00,8000.00,50,'[\"products\\/unas-soft-gel-almendra-x240-1213-a\\/1-6a1657c64f29b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(148,'1213-S',9,NULL,'Uñas Soft Gel Stilleto x240','unas-soft-gel-stilleto-x240-1213-s','[\"sin_graduacion\"]','Uñas Soft Gel Stilleto x240',11000.00,12000.00,8000.00,50,'[\"products\\/unas-soft-gel-stilleto-x240-1213-s\\/1-6a1657c66804a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(149,'1213',9,NULL,'Uñas Técnica Avanzada Press Coffin x 240','unas-tecnica-avanzada-press-coffin-x-240-1213','[\"sin_graduacion\"]','Tips preformados para técnica press on. Aplicación rápida y resultados perfectos. ¡Ideal para ma...',11000.00,12000.00,8000.00,50,'[\"products\\/unas-tecnica-avanzada-press-coffin-x-240-1213\\/1-6a1657c63d9ed.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:11','2026-07-30 04:23:43'),(150,'2022',10,NULL,'Dispensador de Acetona','dispensador-de-acetona-2022','[\"sin_graduacion\"]','Dispensador de Acetona',4000.00,NULL,2500.00,50,'[\"products\\/dispensador-de-acetona-2022\\/1-6a1657d355f5f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(151,'1294',10,NULL,'Gel Sólido','gel-solido-1294','[\"sin_graduacion\"]','Gel Sólido',12000.00,13000.00,9000.00,50,'[\"products\\/gel-solido-1294\\/1-6a1657ce66378.webp\",\"products\\/gel-solido-1294\\/2-6a1657ce75b99.webp\",\"products\\/gel-solido-1294\\/3-6a1657ce85e5e.webp\",\"products\\/gel-solido-1294\\/4-6a1657ce96d62.webp\",\"products\\/gel-solido-1294\\/5-6a1657cea7ce7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(152,'1270',10,NULL,'Gel UV','gel-uv-1270','[\"sin_graduacion\"]','Gel transparente de construcción para uñas. Ofrece resistencia y durabilidad. ¡Ideal para extensi...',6000.00,NULL,4000.00,50,'[\"products\\/gel-uv-1270\\/1-6a1657cc1837a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(153,'1217',10,NULL,'Monómero x 120 ml','monomero-x-120-ml-1217','[\"sin_graduacion\"]','Líquido profesional para construcción de uñas acrílicas. Secado ideal y máxima adherencia. ¡Cr...',37000.00,38000.00,34000.00,50,'[\"products\\/monomero-x-120-ml-1217\\/1-6a1657c6cdd78.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(154,'1053',10,NULL,'Monómero x 60 ml','monomero-x-60-ml-1053','[\"sin_graduacion\"]','Líquido profesional para construcción de uñas acrílicas. Secado ideal y máxima adherencia. ¡Cr...',20000.00,21000.00,17600.00,50,'[\"products\\/monomero-x-60-ml-1053\\/1-6a1657bc9dcf8.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(155,'1044',10,NULL,'Mortero de Vidrio Transparente','mortero-de-vidrio-transparente-1044','[\"sin_graduacion\"]','Mortero de vidrio para manicure: mezcla pigmentos, acrílicos y tratamientos con precisión y estilo...',1500.00,NULL,750.00,50,'[\"products\\/mortero-de-vidrio-transparente-1044\\/1-6a1657bc46fa9.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(156,'1272',10,NULL,'Mortero Sencillo Color','mortero-sencillo-color-1272','[\"sin_graduacion\"]','Mortero de vidrio rosa y morado para manicure: mezcla pigmentos, acrílicos y tratamientos con preci...',2500.00,NULL,1500.00,50,'[\"products\\/mortero-sencillo-color-1272\\/1-6a1657cc4be66.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(157,'1271',10,NULL,'Mortero Strong','mortero-strong-1271','[\"sin_graduacion\"]','Firmeza que se nota. El Mortero Strong ofrece una textura espesa y moldeable, ideal para esculpir u�...',7000.00,NULL,4200.00,50,'[\"products\\/mortero-strong-1271\\/1-6a1657cc33dec.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(158,'1057',10,NULL,'Polvo Acrílico x 55 gr','polvo-acrilico-x-55-gr-1057','[\"sin_graduacion\"]','Polvo acrílico Miracle x 55 g en 5 tonos: Cover Glow, Cover, Clear, Pink y White. Alta adherencia, ...',19000.00,20000.00,15000.00,50,'[\"products\\/polvo-acrilico-x-55-gr-1057\\/1-6a1657bde1024.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(159,'1056-60ml',10,NULL,'Polygel x 60 ml','polygel-x-60-ml-1056-60ml','[\"sin_graduacion\"]','Excelente combinación entre el acrílico y gel para uñas. Fácil de moldear y resistente. ¡Crea e...',25000.00,27000.00,20000.00,50,'[\"products\\/polygel-x-60-ml-1056-60ml\\/1-6a1657bdbd4b9.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(160,'1061',10,NULL,'Rollo Gris Multiformas Grande','rollo-gris-multiformas-grande-1061','[\"sin_graduacion\"]','Moldes para la creación de sistemas artificiales en diferentes tipos de formas y tamaños. ¡Creati...',24000.00,25000.00,20000.00,50,'[\"products\\/rollo-gris-multiformas-grande-1061\\/1-6a1657be0b877.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(161,'1218',10,NULL,'Solución de Poly Gel','solucion-de-poly-gel-1218','[\"sin_graduacion\"]','Líquido modelador para Polygel. Facilita la aplicación y el esculpido sin adherirse al pincel. ¡C...',6500.00,7000.00,5000.00,50,'[\"products\\/solucion-de-poly-gel-1218\\/1-6a1657c6e8a7b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(162,'1076',11,NULL,'Bolsa Desechable de Manos x 100 Uds','bolsa-desechable-de-manos-x-100-uds-1076','[\"sin_graduacion\"]','Higiene sin complicaciones. Ideal para servicios de manicure con protocolo limpio y seguro. Fáciles...',4500.00,NULL,3100.00,50,'[\"products\\/bolsa-desechable-de-manos-x-100-uds-1076\\/1-6a1657bee1e71.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(163,'1075',11,NULL,'Bolsa Desechable de Pies x 50 Uds','bolsa-desechable-de-pies-x-50-uds-1075','[\"sin_graduacion\"]','Comodidad e higiene en cada servicio. Ideal para pedicure profesional con protocolo limpio y seguro....',8000.00,NULL,5800.00,50,'[\"products\\/bolsa-desechable-de-pies-x-50-uds-1075\\/1-6a1657bec9079.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(164,'1215',11,NULL,'Cepillo Manicure X2','cepillo-manicure-x2-1215','[\"sin_graduacion\"]','Set de cepillos para limpiar uñas y manos. Ideal para antes y después de la manicura. ¡Higiene y ...',1000.00,1200.00,750.00,50,'[\"products\\/cepillo-manicure-x2-1215\\/1-6a1657c69a1b0.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(165,'1216',11,NULL,'Cepillo Pedicure','cepillo-pedicure-1216','[\"sin_graduacion\"]','Cepillo ergonómico para limpiar pies y uñas. Suaviza la piel y elimina impurezas. ¡Prepara tus pi...',1500.00,NULL,950.00,50,'[\"products\\/cepillo-pedicure-1216\\/1-6a1657c6b35ee.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(166,'2020',11,NULL,'Dispensador de Cinta + Micropore','dispensador-de-cinta-micropore-2020','[\"sin_graduacion\"]','Incluye rollo de cinta adhesiva suave. Estuche protector con cortador serrado integrado. Diseño com...',9500.00,10000.00,4500.00,50,'[\"products\\/dispensador-de-cinta-micropore-2020\\/1-6a1657d3226eb.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(167,'1080-B',11,NULL,'Guantes de Nitrilo Lila x 100 Und','guantes-de-nitrilo-lila-x-100-und-1080-b','[\"sin_graduacion\"]','Máxima protección y flexibilidad. Resistentes y cómodos. ¡Perfectos para salones de belleza y pr...',27000.00,28000.00,25000.00,50,'[\"products\\/guantes-de-nitrilo-lila-x-100-und-1080-b\\/1-6a1657bf6c2b2.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(168,'1080',11,NULL,'Guantes de Nitrilo Negro x 100 Und','guantes-de-nitrilo-negro-x-100-und-1080','[\"sin_graduacion\"]','Durabilidad y estilo para uso profesional. Alta resistencia a químicos y desgarros. ¡Indispensable...',23000.00,25000.00,18000.00,50,'[\"products\\/guantes-de-nitrilo-negro-x-100-und-1080\\/1-6a1657bf36d4b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(169,'1080-A',11,NULL,'Guantes de Nitrilo Rosa x 100 Und','guantes-de-nitrilo-rosa-x-100-und-1080-a','[\"sin_graduacion\"]','Protección y estilo para profesionales. Alta resistencia y sensibilidad táctil. ¡Ideales para est...',27000.00,28000.00,25000.00,50,'[\"products\\/guantes-de-nitrilo-rosa-x-100-und-1080-a\\/1-6a1657bf4fb31.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(170,'1136',11,NULL,'Keratina Perla','keratina-perla-1136','[\"sin_graduacion\"]','Keratina Perla',7000.00,NULL,4900.00,50,'[\"products\\/keratina-perla-1136\\/1-6a1657c47ef8d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(171,'1064',11,NULL,'Paño de Algodón x 500 Uds','pano-de-algodon-x-500-uds-1064','[\"sin_graduacion\"]','Toallitas sin pelusa para limpieza de uñas. Ideales para remover esmalte y residuos. ¡Higiene y ef...',7500.00,8000.00,4500.00,50,'[\"products\\/pano-de-algodon-x-500-uds-1064\\/1-6a1657be4477b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(172,'1042',11,NULL,'Sanitizer 250 ml','sanitizer-250-ml-1042','[\"sin_graduacion\"]','Desinfectante para manos y herramientas de uñas. Elimina gérmenes y bacterias. ¡Higiene esencial ...',21000.00,22000.00,19500.00,50,'[\"products\\/sanitizer-250-ml-1042\\/1-6a1657bbd3ecf.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(173,'1078',11,NULL,'Toalla Desechable Wypall Cuadrada x 50 Uds','toalla-desechable-wypall-cuadrada-x-50-uds-1078','[\"sin_graduacion\"]','Toallas absorbentes y duraderas para uso profesional. Ideal para secado de cabello. ¡Higiene y efic...',48000.00,NULL,34500.00,50,'[\"products\\/toalla-desechable-wypall-cuadrada-x-50-uds-1078\\/1-6a1657bf1d591.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(174,'1077',11,NULL,'Toalla Desechable Wypall Redonda x 88 Uds','toalla-desechable-wypall-redonda-x-88-uds-1077','[\"sin_graduacion\"]','Altamente absorbente y resistente. Higiene superior para rostro, manos y tratamientos. ¡Uso profesi...',36000.00,NULL,26000.00,50,'[\"products\\/toalla-desechable-wypall-redonda-x-88-uds-1077\\/1-6a1657bf06055.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(175,'2525',12,NULL,'Gel Removedor de Cutícula y Callos','gel-removedor-de-cuticula-y-callos-2525','[\"sin_graduacion\"]','Fórmula de acción rápida diseñada para suavizar y desprender cutículas, callos y durezas sin es...',9500.00,10000.00,6600.00,50,'[\"products\\/gel-removedor-de-cuticula-y-callos-2525\\/1-6a1657d5d8537.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(176,'1055-130',12,NULL,'Removedor Líquido Multipropósito 130 ml','removedor-liquido-multiproposito-130-ml-1055-130','[\"sin_graduacion\"]','Elimina esmalte gel rápido y seguro. ¡Fácil de usar para un cambio de manicura eficiente!...',14000.00,15000.00,9900.00,50,'[\"products\\/removedor-liquido-multiproposito-130-ml-1055-130\\/1-6a1657bcd792e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(177,'1055-250',12,NULL,'Removedor Líquido Multipropósito 250 ml','removedor-liquido-multiproposito-250-ml-1055-250','[\"sin_graduacion\"]','Elimina esmalte gel rápido y seguro. ¡Fácil de usar para un cambio de manicura eficiente!...',23000.00,24000.00,18000.00,50,'[\"products\\/removedor-liquido-multiproposito-250-ml-1055-250\\/1-6a1657bcf3318.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(178,'1054',12,NULL,'Super Removedor en Gel 30 ml','super-removedor-en-gel-30-ml-1054','[\"sin_graduacion\"]','Gel disolvente para uñas acrílicas y de gel. Remueve de forma rápida y segura sin dañar la uña ...',12000.00,13000.00,10000.00,50,'[\"products\\/super-removedor-en-gel-30-ml-1054\\/1-6a1657bcbd6c6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(179,'1297',13,NULL,'Broca Cerámica 5 en 1','broca-ceramica-5-en-1-1297','[\"sin_graduacion\"]','Versatilidad y suavidad en una sola herramienta. Retira acrílico, limpia cutícula, pule superficie...',15000.00,16000.00,11000.00,50,'[\"products\\/broca-ceramica-5-en-1-1297\\/1-6a1657cf0155f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(180,'1219',13,NULL,'Broca Cónica','broca-conica-1219','[\"sin_graduacion\"]','Herramienta versátil para pulir y dar forma a uñas. Ideal para cutículas y zonas de difícil acce...',10000.00,12000.00,5000.00,50,'[\"products\\/broca-conica-1219\\/1-6a1657c70bd3f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(181,'1298',13,NULL,'Broca Mini Umbrella','broca-mini-umbrella-1298','[\"sin_graduacion\"]','Ideal para limpieza precisa de cutícula y zonas sensibles. Su forma tipo sombrilla permite trabajar...',14000.00,15000.00,10000.00,50,'[\"products\\/broca-mini-umbrella-1298\\/1-6a1657cf1b564.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(182,'1220',13,NULL,'Brocas 5 en 1 Carburo','brocas-5-en-1-carburo-1220','[\"sin_graduacion\"]','Herramienta multifuncional para manicura y pedicura. Corta, pule y limpia con una sola broca. ¡Efic...',21000.00,22000.00,16000.00,50,'[\"products\\/brocas-5-en-1-carburo-1220\\/1-6a1657c724f90.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(183,'1221',13,NULL,'Kit de Brocas Carburo x 8','kit-de-brocas-carburo-x-8-1221','[\"sin_graduacion\"]','Set profesional de brocas para drill. Máxima durabilidad y precisión en acrílicos y geles. ¡Herr...',46000.00,50000.00,40000.00,50,'[\"products\\/kit-de-brocas-carburo-x-8-1221\\/1-6a1657c73c921.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(184,'1222',13,NULL,'Kit de Brocas Cerámica x 8','kit-de-brocas-ceramica-x-8-1222','[\"sin_graduacion\"]','Set de brocas suaves y eficientes para drill. Ideales para remover geles sin dañar. ¡Calidad y seg...',33000.00,35000.00,27000.00,50,'[\"products\\/kit-de-brocas-ceramica-x-8-1222\\/1-6a1657c754aca.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(185,'1296',13,NULL,'Set Broca Manicura Rusa x 10','set-broca-manicura-rusa-x-10-1296','[\"sin_graduacion\"]','Incluye variedad de formas y texturas para limpieza de cutícula, preparación de superficie y detal...',17000.00,18000.00,13000.00,50,'[\"products\\/set-broca-manicura-rusa-x-10-1296\\/1-6a1657ceda59e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(186,'1295',13,NULL,'Set Broca Manicure Rusa Mix','set-broca-manicure-rusa-mix-1295','[\"sin_graduacion\"]','Precisión total en cada técnica. Incluye variedad de formas y texturas para limpieza profunda, pre...',26000.00,28000.00,20000.00,50,'[\"products\\/set-broca-manicure-rusa-mix-1295\\/1-6a1657cec19de.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(187,'1106',14,NULL,'Agua de Rosas x 1000ml','agua-de-rosas-x-1000ml-1106','[\"sin_graduacion\"]','La presentación de 1000ml es ideal para uso frecuente, ya sea para el cuidado personal diario o par...',10000.00,NULL,9200.00,50,'[\"products\\/agua-de-rosas-x-1000ml-1106\\/1-6a1657c196264.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(188,'1229',14,NULL,'Agua de Rosas x 250ml','agua-de-rosas-x-250ml-1229','[\"sin_graduacion\"]','Práctica y compacta, esta presentación es perfecta para llevar contigo y refrescar tu piel en cual...',6500.00,6600.00,5750.00,50,'[\"products\\/agua-de-rosas-x-250ml-1229\\/1-6a1657c82d485.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(189,'1105',14,NULL,'Agua de Rosas x 500ml + 250ml','agua-de-rosas-x-500ml-250ml-1105','[\"sin_graduacion\"]','Tonifica, hidrata y refresca la piel. Con propiedades calmantes y antiinflamatorias, es perfecto com...',12000.00,13000.00,10500.00,50,'[\"products\\/agua-de-rosas-x-500ml-250ml-1105\\/1-6a1657c17c538.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(190,'4048',14,NULL,'BELLOTA MARMOLEADA SWEET','bellota-marmoleada-sweet-4048','[\"sin_graduacion\"]','BELLOTA MARMOLEADA SWEET',4500.00,NULL,3200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(191,'4044',14,NULL,'Bellota X1','bellota-x1-4044','[\"sin_graduacion\"]','Bellota X1',1500.00,NULL,1100.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(192,'4045',14,NULL,'BELLOTA X3 SWEET','bellota-x3-sweet-4045','[\"sin_graduacion\"]','BELLOTA X3 SWEET',4500.00,NULL,3300.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(193,'7602',14,NULL,'Bombas Efervescentes Bath Salt','bombas-efervescentes-bath-salt-7602','[\"sin_graduacion\"]','Bombas Efervescentes Bath Salt',22000.00,23000.00,18000.00,50,'[\"products\\/bombas-efervescentes-bath-salt-7602\\/1-6a1657d6d8f94.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(194,'4042',14,NULL,'BORLA X2 SWEET','borla-x2-sweet-4042','[\"sin_graduacion\"]','BORLA X2 SWEET',2000.00,NULL,1500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(195,'4041',14,NULL,'BORLA X3 SWEET','borla-x3-sweet-4041','[\"sin_graduacion\"]','BORLA X3 SWEET',2000.00,NULL,1600.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(196,'1228',14,NULL,'Exfoliante 1000ml','exfoliante-1000ml-1228','[\"sin_graduacion\"]','Elimina células muertas, impurezas y suaviza la piel. Su tamaño de 1000ml es perfecto para uso fre...',19000.00,20000.00,16500.00,50,'[\"products\\/exfoliante-1000ml-1228\\/1-6a1657c7c1020.webp\",\"products\\/exfoliante-1000ml-1228\\/2-6a1657c7d2323.webp\",\"products\\/exfoliante-1000ml-1228\\/3-6a1657c7e357d.webp\",\"products\\/exfoliante-1000ml-1228\\/4-6a1657c8014df.webp\",\"products\\/exfoliante-1000ml-1228\\/5-6a1657c812b7b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(197,'2039',14,NULL,'Exfoliante 250ml','exfoliante-250ml-2039','[\"sin_graduacion\"]','Elimina células muertas, impurezas y suaviza la piel. Su tamaño de 1000ml es perfecto para uso fre...',10000.00,11000.00,7500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(198,'7640',14,NULL,'Jelly Spa Tratamiento Relajante Para Pies','jelly-spa-tratamiento-relajante-para-pies-7640','[\"sin_graduacion\"]','Jelly Spa Tratamiento Relajante Para Pies',24000.00,25000.00,19500.00,50,'[\"products\\/jelly-spa-tratamiento-relajante-para-pies-7640\\/1-6a1657d79e136.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(199,'1264',14,NULL,'Mantequilla Corporal','mantequilla-corporal-1264','[\"sin_graduacion\"]','Hidratación intensa para el cuerpo, nutre profundamente la piel, dejándola suave y sedosa. Ideal p...',20000.00,21000.00,15500.00,50,'[\"products\\/mantequilla-corporal-1264\\/1-6a1657cb48954.webp\",\"products\\/mantequilla-corporal-1264\\/2-6a1657cb5a08b.webp\",\"products\\/mantequilla-corporal-1264\\/3-6a1657cb6b0ba.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(200,'4053',14,NULL,'PINCEL CEJAS DUO SWEET','pincel-cejas-duo-sweet-4053','[\"sin_graduacion\"]','PINCEL CEJAS DUO SWEET',3500.00,NULL,2800.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(201,'4054',14,NULL,'PINCEL CEJAS TRADICIONAL SWEET','pincel-cejas-tradicional-sweet-4054','[\"sin_graduacion\"]','PINCEL CEJAS TRADICIONAL SWEET',1500.00,NULL,1000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(202,'4052',14,NULL,'SET BROCHAS PROFESIONAL X3 SWEET','set-brochas-profesional-x3-sweet-4052','[\"sin_graduacion\"]','SET BROCHAS PROFESIONAL X3 SWEET',9500.00,10000.00,8500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(203,'4050',14,NULL,'SET BROCHAS PROFESIONAL X5 SWEET','set-brochas-profesional-x5-sweet-4050','[\"sin_graduacion\"]','SET BROCHAS PROFESIONAL X5 SWEET',27000.00,28000.00,22000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(204,'4049',14,NULL,'SET DE BROCHAS MAS ENCRESPADOR SWEET','set-de-brochas-mas-encrespador-sweet-4049','[\"sin_graduacion\"]','SET DE BROCHAS MAS ENCRESPADOR SWEET',54000.00,60000.00,46000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(205,'1074',14,NULL,'Vaporizador Facial Profesional','vaporizador-facial-profesional-1074','[\"sin_graduacion\"]','Ideal para abrir poros, limpiar profundamente la piel y prepararla para tratamientos faciales. Ayuda...',51000.00,55000.00,37000.00,50,'[\"products\\/vaporizador-facial-profesional-1074\\/1-6a1657beb009d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(206,'1112',15,NULL,'Bajalenguas x100','bajalenguas-x100-1112','[\"sin_graduacion\"]','Aplicadores de cera desechables para una depilación segura y sin irritaciones. Su superficie lisa g...',7000.00,NULL,4900.00,50,'[\"products\\/bajalenguas-x100-1112\\/1-6a1657c23da51.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(207,'2032',15,NULL,'Bandas Depilatorias x100','bandas-depilatorias-x100-2032','[\"sin_graduacion\"]','Resistentes e ideales para depilación con cera fría o caliente. Ofrecen un agarre firme, retiro li...',7500.00,8000.00,5000.00,50,'[\"products\\/bandas-depilatorias-x100-2032\\/1-6a1657d53e7f1.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(208,'1110',15,NULL,'Cera en Perlas x100 Gramos','cera-en-perlas-x100-gramos-1110','[\"sin_graduacion\"]','Cera depilatoria en perlas para una depilación eficaz y sin bandas. Su fórmula se adhiere al vello...',6000.00,NULL,4000.00,50,'[\"products\\/cera-en-perlas-x100-gramos-1110\\/1-6a1657c20ca60.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(209,'1113',15,NULL,'Cerador en Rolón + Cartucho de Cera','cerador-en-rolon-cartucho-de-cera-1113','[\"sin_graduacion\"]','Simplifica tu depilación con nuestro Cerador en Rolón y cartucho de cera. Calentamiento rápido, a...',29000.00,30000.00,24000.00,50,'[\"products\\/cerador-en-rolon-cartucho-de-cera-1113\\/1-6a1657c2572f6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(210,'4046',15,NULL,'DEPILADOR ESCARCHA SWEET','depilador-escarcha-sweet-4046','[\"sin_graduacion\"]','DEPILADOR ESCARCHA SWEET',5000.00,NULL,3900.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(211,'4047',15,NULL,'DEPILADOR PASTEL SWEET','depilador-pastel-sweet-4047','[\"sin_graduacion\"]','DEPILADOR PASTEL SWEET',3000.00,NULL,2300.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(212,'1111',15,NULL,'Rollo de Lienzo Depilatorio x50 Metros','rollo-de-lienzo-depilatorio-x50-metros-1111','[\"sin_graduacion\"]','Material de alta calidad para la depilación con cera. Este rollo de 50 metros se puede cortar al ta...',14000.00,15000.00,12000.00,50,'[\"products\\/rollo-de-lienzo-depilatorio-x50-metros-1111\\/1-6a1657c225747.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(213,'1117',16,NULL,'Combo Tijera Lisa + Entresacar x2','combo-tijera-lisa-entresacar-x2-1117','[\"sin_graduacion\"]','Incluye tijera de corte lisa para cortes precisos y tijera de entresacar para dar volumen, textura y...',51000.00,55000.00,45000.00,50,'[\"products\\/combo-tijera-lisa-entresacar-x2-1117\\/1-6a1657c2a6a48.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(214,'1118',16,NULL,'Combo Tijera x3 Lisa + Entresacar + Microdentada','combo-tijera-x3-lisa-entresacar-microdentada-1118','[\"sin_graduacion\"]','El juego definitivo: tijera lisa para cortes limpios, tijera de entresacar para volumen y textura, y...',82000.00,90000.00,72000.00,50,'[\"products\\/combo-tijera-x3-lisa-entresacar-microdentada-1118\\/1-6a1657c2c12b4.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(215,'1230',16,NULL,'Tijera Académica Profesional','tijera-academica-profesional-1230','[\"sin_graduacion\"]','Excelente para peluqueros en formación y profesionales. Hojas afiladas para cortes precisos y fluid...',9500.00,10000.00,6600.00,50,'[\"products\\/tijera-academica-profesional-1230\\/1-6a1657c847de4.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(216,'1119',16,NULL,'Tijera Barrilito Estándar','tijera-barrilito-estandar-1119','[\"sin_graduacion\"]','Tijera de corte confiable y eficaz para uso diario en el salón. Diseño ergonómico y hojas afilada...',12000.00,NULL,10000.00,50,'[\"products\\/tijera-barrilito-estandar-1119\\/1-6a1657c2d9339.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(217,'1115',16,NULL,'Tijera Lisa Individual Tornasol','tijera-lisa-individual-tornasol-1115','[\"sin_graduacion\"]','Tijera de corte profesional con acabado tornasol. Hojas lisas y afiladas para cortes rectos, despunt...',29000.00,30000.00,25000.00,50,'[\"products\\/tijera-lisa-individual-tornasol-1115\\/1-6a1657c272182.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(218,'1116',16,NULL,'Tijera Microdentada Individual Tornasol','tijera-microdentada-individual-tornasol-1116','[\"sin_graduacion\"]','Precisión y estilo para cortes limpios. Evita que el cabello resbale. Herramienta esencial con dise...',29000.00,30000.00,25000.00,50,'[\"products\\/tijera-microdentada-individual-tornasol-1116\\/1-6a1657c28bdd0.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(219,'1125',17,NULL,'Gorro de Aluminio para Repolarización','gorro-de-aluminio-para-repolarizacion-1125','[\"sin_graduacion\"]','Optimiza tratamientos capilares. Retiene el calor, potencia la absorción y mejora la hidratación....',2000.00,NULL,1200.00,50,'[\"products\\/gorro-de-aluminio-para-repolarizacion-1125\\/1-6a1657c34d82f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(220,'1259',17,NULL,'Gorro de Baño Estampado','gorro-de-bano-estampado-1259','[\"sin_graduacion\"]','Protege tu cabello con estilo. Diseños modernos y ajuste cómodo. Ideal para piscina o ducha....',2000.00,NULL,1400.00,50,'[\"products\\/gorro-de-bano-estampado-1259\\/1-6a1657caed0d6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(221,'1122',17,NULL,'Gorro de Baño Unicolor','gorro-de-bano-unicolor-1122','[\"sin_graduacion\"]','Protección elegante para tu cabello. Diseño simple y ajuste perfecto. Ideal para nadar o ducharte ...',2000.00,NULL,1400.00,50,'[\"products\\/gorro-de-bano-unicolor-1122\\/1-6a1657c2f294d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(222,'1231',17,NULL,'Gorro de Malla','gorro-de-malla-1231','[\"sin_graduacion\"]','Elemento esencial para comodidad y practicidad en peluquería, especialmente en áreas de lavado o p...',1500.00,NULL,800.00,50,'[\"products\\/gorro-de-malla-1231\\/1-6a1657c864749.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(223,'1232',17,NULL,'Gorro de Media x2','gorro-de-media-x2-1232','[\"sin_graduacion\"]','Paquete de dos unidades diseñado para uso profesional en peluquerías y salones de belleza....',1500.00,NULL,800.00,50,'[\"products\\/gorro-de-media-x2-1232\\/1-6a1657c87c4c1.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(224,'1124',17,NULL,'Gorro de Satín','gorro-de-satin-1124','[\"sin_graduacion\"]','Protege tu cabello mientras duermes. Evita frizz, rotura y mantiene la hidratación....',6000.00,NULL,3000.00,50,'[\"products\\/gorro-de-satin-1124\\/1-6a1657c3328cb.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(225,'1260',17,NULL,'Gorro de Satín XL','gorro-de-satin-xl-1260','[\"sin_graduacion\"]','Protege cabello largo, rizado o voluminoso al dormir. Reduce frizz y rotura....',8500.00,9000.00,6000.00,50,'[\"products\\/gorro-de-satin-xl-1260\\/1-6a1657cb13165.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(226,'1126',17,NULL,'Gorro de Silicona para Mechas','gorro-de-silicona-para-mechas-1126','[\"sin_graduacion\"]','Ideal para extraer mechones con precisión y obtener reflejos perfectos. Reutilizable, resistente y ...',8500.00,9000.00,6000.00,50,'[\"products\\/gorro-de-silicona-para-mechas-1126\\/1-6a1657c367e22.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(227,'1126-A',17,NULL,'Gorro Mechas Plástico + Aguja','gorro-mechas-plastico-aguja-1126-a','[\"sin_graduacion\"]','Kit para estilistas: gorro plástico resistente con aguja especial para extraer mechones de manera u...',3000.00,NULL,2000.00,50,'[\"products\\/gorro-mechas-plastico-aguja-1126-a\\/1-6a1657c3830df.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(228,'1123',17,NULL,'Gorro Térmico Profesional','gorro-termico-profesional-1123','[\"sin_graduacion\"]','Utiliza calor suave y uniforme para abrir la cutícula del cabello, permitiendo penetración profund...',20000.00,NULL,18700.00,50,'[\"products\\/gorro-termico-profesional-1123\\/1-6a1657c318710.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(229,'1262',17,NULL,'Redecilla Gruesa x3','redecilla-gruesa-x3-1262','[\"sin_graduacion\"]','Máxima sujeción y durabilidad para moños y peinados. Ideal para cabello abundante....',3000.00,NULL,2000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(230,'1261',17,NULL,'Redecilla Invisible x3','redecilla-invisible-x3-1261','[\"sin_graduacion\"]','Redecillas ultrafinas y elásticas para mantener peinados, moños o recogidos impecables, seguros y ...',3000.00,NULL,1600.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(231,'4001',18,NULL,'CEPILLO ALPARGATA SWEET','cepillo-alpargata-sweet-4001','[\"sin_graduacion\"]','CEPILLO ALPARGATA SWEET',11000.00,12000.00,9500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(232,'1128',18,NULL,'Cepillo Anti-Frizz','cepillo-anti-frizz-1128','[\"sin_graduacion\"]','Diseñado para alisar y controlar el encrespamiento, dejando el cabello suave, brillante y sin efect...',6000.00,NULL,3500.00,50,'[\"products\\/cepillo-anti-frizz-1128\\/1-6a1657c39e403.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(233,'1234',18,NULL,'Cepillo Denman Black','cepillo-denman-black-1234','[\"sin_graduacion\"]','Define y estiliza rizos con el icónico Cepillo Denman Black. Cerdas de nailon y diseño ergonómico...',8500.00,9000.00,6000.00,50,'[\"products\\/cepillo-denman-black-1234\\/1-6a1657c8ae951.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(234,'1233',18,NULL,'Cepillo Denman Pink','cepillo-denman-pink-1233','[\"sin_graduacion\"]','Perfecto para dar forma a rizos, definir ondas y distribuir productos de manera uniforme. Un must-ha...',8500.00,9000.00,6000.00,50,'[\"products\\/cepillo-denman-pink-1233\\/1-6a1657c8950fb.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(235,'1130',18,NULL,'Cepillo Esqueleto','cepillo-esqueleto-1130','[\"sin_graduacion\"]','Seca tu cabello más rápido. Sus aberturas facilitan el paso del aire, reduciendo el tiempo de seca...',9500.00,10000.00,6900.00,50,'[\"products\\/cepillo-esqueleto-1130\\/1-6a1657c3b9021.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(236,'4018',18,NULL,'CEPILLO ESQUELETO SWEET','cepillo-esqueleto-sweet-4018','[\"sin_graduacion\"]','CEPILLO ESQUELETO SWEET',12000.00,NULL,10000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(237,'4017',18,NULL,'CEPILLO MAGICO SWEET','cepillo-magico-sweet-4017','[\"sin_graduacion\"]','CEPILLO MAGICO SWEET',11000.00,12000.00,9500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(238,'4014',18,NULL,'CEPILLO MASAJEADOR ESPIRAL SWEET','cepillo-masajeador-espiral-sweet-4014','[\"sin_graduacion\"]','CEPILLO MASAJEADOR ESPIRAL SWEET',6000.00,NULL,4800.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(239,'4003',18,NULL,'Cepillo masajeador ovalado duo','cepillo-masajeador-ovalado-duo-4003','[\"sin_graduacion\"]','Cepillo masajeador ovalado...',7000.00,7500.00,5900.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(240,'4013',18,NULL,'CEPILLO MASAJEADOR PALETA SWEET','cepillo-masajeador-paleta-sweet-4013','[\"sin_graduacion\"]','CEPILLO MASAJEADOR PALETA SWEET',6500.00,7000.00,5300.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(241,'1312',18,NULL,'Cepillo Mega Curls Ovalado','cepillo-mega-curls-ovalado-1312','[\"sin_graduacion\"]','Cepillo Mega Curls Ovalado',13000.00,14000.00,9500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(242,'7085',18,NULL,'Cepillo Mega Curls para Crespos','cepillo-mega-curls-para-crespos-7085','[\"sin_graduacion\"]','Rizos hidratados y definidos. Ideal para distribuir productos, estilizar y separar rizos sin dañarl...',13000.00,14000.00,9500.00,50,'[\"products\\/cepillo-mega-curls-para-crespos-7085\\/1-6a1657d696ea6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(243,'4019',18,NULL,'CEPILLO OVALADO SWEET','cepillo-ovalado-sweet-4019','[\"sin_graduacion\"]','CEPILLO OVALADO SWEET',12000.00,NULL,10000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(244,'1130-B',18,NULL,'Cepillo Plancha de Madera','cepillo-plancha-de-madera-1130-b','[\"sin_graduacion\"]','Materiales naturales que cuidan tu melena, desenredando suavemente y estimulando la circulación del...',14000.00,15000.00,11000.00,50,'[\"products\\/cepillo-plancha-de-madera-1130-b\\/1-6a1657c3ebd02.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(245,'1130-A',18,NULL,'Cepillo Plancha Plástico','cepillo-plancha-plastico-1130-a','[\"sin_graduacion\"]','Ideal para desenredar, alisar y dar forma mientras secas. Acabado profesional, sin frizz y con movim...',14000.00,15000.00,11000.00,50,'[\"products\\/cepillo-plancha-plastico-1130-a\\/1-6a1657c3d1851.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(246,'1148',18,NULL,'Cepillo Plástico Barbero','cepillo-plastico-barbero-1148','[\"sin_graduacion\"]','Ideal para desvanecidos, limpieza de cortes y aplicar polvos. Resistente y ergonómico. Imprescindib...',9000.00,10000.00,6000.00,50,'[\"products\\/cepillo-plastico-barbero-1148\\/1-6a1657c51a4a7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(247,'4016',18,NULL,'CEPILLO POINTS SWEET','cepillo-points-sweet-4016','[\"sin_graduacion\"]','CEPILLO POINTS SWEET',9500.00,10000.00,8000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(248,'4005',18,NULL,'CEPILLO PULPO REDONDO MASAJEADOR SWEET','cepillo-pulpo-redondo-masajeador-sweet-4005','[\"sin_graduacion\"]','CEPILLO PULPO REDONDO MASAJEADOR SWEET',4500.00,NULL,3600.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(249,'1131',18,NULL,'Combo Cepillo Térmico x4 Dorado','combo-cepillo-termico-x4-dorado-1131','[\"sin_graduacion\"]','Diferentes tamaños que distribuyen el calor para secado rápido, volumen y brillo sin frizz. Set es...',100000.00,110000.00,85000.00,50,'[\"products\\/combo-cepillo-termico-x4-dorado-1131\\/1-6a1657c412a3e.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(250,'1235',18,NULL,'Combo Cepillo Térmico x4 Pink Premium','combo-cepillo-termico-x4-pink-premium-1235','[\"sin_graduacion\"]','Dale un toque de color a tu rutina. 4 tamaños para cada necesidad: crea ondas, alisa o da cuerpo a ...',104000.00,115000.00,90000.00,50,'[\"products\\/combo-cepillo-termico-x4-pink-premium-1235\\/1-6a1657c8cb17c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(251,'4032',19,NULL,'BROCHA TINTE ESCARCHA SWEET','brocha-tinte-escarcha-sweet-4032','[\"sin_graduacion\"]','BROCHA TINTE ESCARCHA SWEET',2000.00,NULL,1400.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(252,'4031',19,NULL,'BROCHA TINTE NEGRA SWEET','brocha-tinte-negra-sweet-4031','[\"sin_graduacion\"]','BROCHA TINTE NEGRA SWEET',1000.00,NULL,500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(253,'4028',19,NULL,'CAIMAN BLANCO Y NEGRO SWEET','caiman-blanco-y-negro-sweet-4028','[\"sin_graduacion\"]','CAIMAN BLANCO Y NEGRO SWEET',5000.00,NULL,3900.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(254,'4027',19,NULL,'CAIMAN PASTEL SWEET','caiman-pastel-sweet-4027','[\"sin_graduacion\"]','CAIMAN PASTEL SWEET',5000.00,NULL,3900.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(255,'4024',19,NULL,'CEPILLO PULIDOR SWEET','cepillo-pulidor-sweet-4024','[\"sin_graduacion\"]','CEPILLO PULIDOR SWEET',2000.00,NULL,1600.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(256,'4012',19,NULL,'CHUMIS BIG SWEET','chumis-big-sweet-4012','[\"sin_graduacion\"]','CHUMIS BIG SWEET',9000.00,9500.00,7200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(257,'4011',19,NULL,'CHUMIS MEDIUM SWEET','chumis-medium-sweet-4011','[\"sin_graduacion\"]','CHUMIS MEDIUM SWEET',8500.00,9000.00,6900.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(258,'4010',19,NULL,'CHUMIS SMALL SWEET','chumis-small-sweet-4010','[\"sin_graduacion\"]','CHUMIS SMALL SWEET',8000.00,8500.00,6500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(259,'1279',19,NULL,'Clip Extensión Grande x15 U','clip-extension-grande-x15-u-1279','[\"sin_graduacion\"]','Agarre fuerte y cómodo para extensiones de cabello. Solución perfecta para cabello más largo y vo...',7000.00,7500.00,5200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(260,'1278',19,NULL,'Clip Extensión Pequeño x15 U','clip-extension-pequeno-x15-u-1278','[\"sin_graduacion\"]','Diseño compacto y resistente para sujeción invisible y duradera de extensiones de cabello....',5000.00,NULL,3500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(261,'4034',19,NULL,'DIFUSOR PARA SECADOR SWEET','difusor-para-secador-sweet-4034','[\"sin_graduacion\"]','DIFUSOR PARA SECADOR SWEET',14000.00,15000.00,12000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(262,'4022',19,NULL,'DONA SWEET','dona-sweet-4022','[\"sin_graduacion\"]','DONA SWEET',1500.00,NULL,1100.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(263,'1157',19,NULL,'Esponja Twiss Afro','esponja-twiss-afro-1157','[\"sin_graduacion\"]','Define tus rizos y crea un estilo Twiss Afro impecable. Ideal para dar forma y textura al cabello. L...',9000.00,10000.00,5000.00,50,'[\"products\\/esponja-twiss-afro-1157\\/1-6a1657c5511e3.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(264,'1236',19,NULL,'Gancho de Horquilla Negro','gancho-de-horquilla-negro-1236','[\"sin_graduacion\"]','Sujeción fuerte y confiable para peinados elegantes. Se camufla en cabello oscuro....',3000.00,NULL,1850.00,50,'[\"products\\/gancho-de-horquilla-negro-1236\\/1-6a1657c8e6ae6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(265,'1133',19,NULL,'Gancho Invisible Caja Miracle','gancho-invisible-caja-miracle-1133','[\"sin_graduacion\"]','Agarre firme y discreto para moños, recogidos y más. Tu peinado intacto por más tiempo....',1500.00,NULL,1300.00,50,'[\"products\\/gancho-invisible-caja-miracle-1133\\/1-6a1657c4479e6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(266,'1132',19,NULL,'Gancho Invisible Caja x Libra','gancho-invisible-caja-x-libra-1132','[\"sin_graduacion\"]','Diseño discreto que se camufla en el cabello, asegurando tu peinado con firmeza. Una caja de 1 libr...',14000.00,15000.00,10000.00,50,'[\"products\\/gancho-invisible-caja-x-libra-1132\\/1-6a1657c42d3c5.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(267,'1237',19,NULL,'Gancho Largo Profesional Dorado x20','gancho-largo-profesional-dorado-x20-1237','[\"sin_graduacion\"]','Longitud y acabado de lujo, indispensable para peinados que requieren sujeción fuerte y visible....',2000.00,NULL,1300.00,50,'[\"products\\/gancho-largo-profesional-dorado-x20-1237\\/1-6a1657c90e9a6.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(268,'1238',19,NULL,'Gancho Largo Profesional Negro x20','gancho-largo-profesional-negro-x20-1238','[\"sin_graduacion\"]','Ideal para moños altos, recogidos y trenzas. Accesorio invisible con sujeción firme....',2000.00,NULL,1300.00,50,'[\"products\\/gancho-largo-profesional-negro-x20-1238\\/1-6a1657c92b077.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(269,'1138-A',19,NULL,'Hilo Elástico 50 Mts','hilo-elastico-50-mts-1138-a','[\"sin_graduacion\"]','Hilo elástico resistente de 50 metros, ideal para trabajos de peinado, trenzas, extensiones y sujec...',5000.00,NULL,2500.00,50,'[\"products\\/hilo-elastico-50-mts-1138-a\\/1-6a1657c4d7a11.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(270,'1239',19,NULL,'Liga Blanco y Negro para Cabello','liga-blanco-y-negro-para-cabello-1239','[\"sin_graduacion\"]','Sujeción firme y estilo versátil. Perfecta para coletas, trenzas y recogidos diarios....',1500.00,NULL,1000.00,50,'[\"products\\/liga-blanco-y-negro-para-cabello-1239\\/1-6a1657c9475ab.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(271,'1240',19,NULL,'Liga Colores para Cabello','liga-colores-para-cabello-1240','[\"sin_graduacion\"]','Sujeción divertida y versátil. Ideales para colas, trenzas y dar un toque vibrante a tu estilo....',1500.00,NULL,1000.00,50,'[\"products\\/liga-colores-para-cabello-1240\\/1-6a1657c9622b7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(272,'1134',19,NULL,'Liga Negra para Cabello','liga-negra-para-cabello-1134','[\"sin_graduacion\"]','Sujeción fuerte y duradera para todo peinado. Ideal para colas de caballo, trenzas y recogidos....',1500.00,NULL,900.00,50,'[\"products\\/liga-negra-para-cabello-1134\\/1-6a1657c4632e9.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(273,'1137',19,NULL,'Microline Siliconado','microline-siliconado-1137','[\"sin_graduacion\"]','Microanillos con silicona interna para mejor sujeción y menor fricción. Se integra de forma natura...',12000.00,13000.00,9000.00,50,'[\"products\\/microline-siliconado-1137\\/1-6a1657c4996c4.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(274,'1138',19,NULL,'Pegante para Extensión 1 Onza','pegante-para-extension-1-onza-1138','[\"sin_graduacion\"]','Adhesivo profesional para extensiones capilares. Fijación fuerte, acabado limpio y larga duración....',5000.00,NULL,3500.00,50,'[\"products\\/pegante-para-extension-1-onza-1138\\/1-6a1657c4bcd4f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(275,'4040',19,NULL,'PEINILLA CABO METALICO SWEET','peinilla-cabo-metalico-sweet-4040','[\"sin_graduacion\"]','PEINILLA CABO METALICO SWEET',2000.00,NULL,1200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(276,'4039',19,NULL,'PEINILLA DE CORTE DOBLE SWEET','peinilla-de-corte-doble-sweet-4039','[\"sin_graduacion\"]','PEINILLA DE CORTE DOBLE SWEET',2000.00,NULL,1200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(277,'4038',19,NULL,'PEINILLA DE CORTE SWEET','peinilla-de-corte-sweet-4038','[\"sin_graduacion\"]','PEINILLA DE CORTE SWEET',2000.00,NULL,1200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(278,'4026',19,NULL,'PEINILLA DIENTE LEON SWEET','peinilla-diente-leon-sweet-4026','[\"sin_graduacion\"]','PEINILLA DIENTE LEON SWEET',2000.00,NULL,1200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(279,'4037',19,NULL,'PEINILLA MILIMETRICA MASTER SWEET','peinilla-milimetrica-master-sweet-4037','[\"sin_graduacion\"]','PEINILLA MILIMETRICA MASTER SWEET',2000.00,NULL,1400.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(280,'4056',19,NULL,'PEINILLA MOJARRA SWEET','peinilla-mojarra-sweet-4056','[\"sin_graduacion\"]','PEINILLA MOJARRA SWEET',1000.00,NULL,850.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(281,'4036',19,NULL,'PEINILLA WAHL SWEET','peinilla-wahl-sweet-4036','[\"sin_graduacion\"]','PEINILLA WAHL SWEET',2000.00,NULL,1200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(282,'1150',19,NULL,'Pinza Cocodrilo x6','pinza-cocodrilo-x6-1150','[\"sin_graduacion\"]','Sujeción firme para separar mechones o sostener secciones durante procesos de belleza. Diseño resi...',7000.00,NULL,4000.00,50,'[\"products\\/pinza-cocodrilo-x6-1150\\/1-6a1657c5369de.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(283,'4033',19,NULL,'PINZA ELITE METALICA SWEET','pinza-elite-metalica-sweet-4033','[\"sin_graduacion\"]','PINZA ELITE METALICA SWEET',4000.00,NULL,3200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(284,'4025',19,NULL,'PINZA ELITE PLASTICA SWEET','pinza-elite-plastica-sweet-4025','[\"sin_graduacion\"]','PINZA ELITE PLASTICA SWEET',5000.00,NULL,3900.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(285,'4006',19,NULL,'RULO ADHESIVO BIG SWEET','rulo-adhesivo-big-sweet-4006','[\"sin_graduacion\"]','RULO ADHESIVO BIG SWEET',9500.00,10000.00,7000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(286,'4007',19,NULL,'RULO ADHESIVO MEDIUM SWEET','rulo-adhesivo-medium-sweet-4007','[\"sin_graduacion\"]','RULO ADHESIVO MEDIUM SWEET',6500.00,7000.00,5500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(287,'4009',19,NULL,'RULO MAGNETICO M SWEET','rulo-magnetico-m-sweet-4009','[\"sin_graduacion\"]','RULO MAGNETICO M SWEET',4500.00,NULL,3100.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(288,'4008',19,NULL,'RULO MAGNETICO XL SWEET','rulo-magnetico-xl-sweet-4008','[\"sin_graduacion\"]','RULO MAGNETICO XL SWEET',4500.00,NULL,3300.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(289,'1097',20,NULL,'Anillo para pestañas x 100 uds','anillo-para-pestanas-x-100-uds-1097','[\"sin_graduacion\"]','Simplifica tu trabajo con nuestros Anillos para Pestañas. Mantén el adhesivo al alcance de la mano...',7000.00,NULL,4000.00,50,'[\"products\\/anillo-para-pestanas-x-100-uds-1097\\/1-6a1657c0a7419.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(290,'1092',20,NULL,'Cepillo para cejas y pestañas x 50 unidades','cepillo-para-cejas-y-pestanas-x-50-unidades-1092','[\"sin_graduacion\"]','Herramienta esencial para peinar, separar y aplicar productos. Ideal para extensiones. ¡Higiene y p...',6000.00,NULL,3500.00,50,'[\"products\\/cepillo-para-cejas-y-pestanas-x-50-unidades-1092\\/1-6a1657c02ab1b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(291,'1098',20,NULL,'Cinta micropore para pestañas','cinta-micropore-para-pestanas-1098','[\"sin_graduacion\"]','Sujeción suave y segura para aislar las pestañas inferiores. Hipoalergénica y fácil de remover. ...',3000.00,NULL,1500.00,50,'[\"products\\/cinta-micropore-para-pestanas-1098\\/1-6a1657c0c0b36.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(292,'1099',20,NULL,'Cinta transport para pestañas','cinta-transport-para-pestanas-1099','[\"sin_graduacion\"]','Mantiene las extensiones organizadas y listas para aplicar. Facilita el trabajo y agiliza el proceso...',3000.00,NULL,1400.00,50,'[\"products\\/cinta-transport-para-pestanas-1099\\/1-6a1657c0d941f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(293,'4060',20,NULL,'ENCRESPADOR CORAZON SWEET','encrespador-corazon-sweet-4060','[\"sin_graduacion\"]','ENCRESPADOR CORAZON SWEET',4500.00,NULL,3500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(294,'4061',20,NULL,'ENCRESPADOR MAS POMO SWEET','encrespador-mas-pomo-sweet-4061','[\"sin_graduacion\"]','ENCRESPADOR MAS POMO SWEET',6000.00,6500.00,5000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(295,'4051',20,NULL,'ENCRESPADOR PIÑA SWEET','encrespador-pina-sweet-4051','[\"sin_graduacion\"]','ENCRESPADOR PIÑA SWEET',5000.00,NULL,4000.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(296,'1094',20,NULL,'Estuche Pinzas para Pestañas X5','estuche-pinzas-para-pestanas-x5-1094','[\"sin_graduacion\"]','Set profesional de pinzas de precisión para aplicación de extensiones. Variedad para cada técnica...',38000.00,40000.00,30000.00,50,'[\"products\\/estuche-pinzas-para-pestanas-x5-1094\\/1-6a1657c074b0f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(297,'1102-A',20,NULL,'Kit de Laminación de Cejas','kit-de-laminacion-de-cejas-1102-a','[\"sin_graduacion\"]','Cejas definidas, elevadas y con carácter. El Kit de Laminación de Cejas alinea y fija los vellos p...',47000.00,50000.00,35000.00,50,'[\"products\\/kit-de-laminacion-de-cejas-1102-a\\/1-6a1657c148421.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(298,'1283',20,NULL,'Microbrush para Pestañas','microbrush-para-pestanas-1283','[\"sin_graduacion\"]','Aplicadores desechables para limpieza y tratamientos precisos. Ideal para extensiones. ¡Higiene y c...',6000.00,NULL,4000.00,50,'[\"products\\/microbrush-para-pestanas-1283\\/1-6a1657cd3f8ff.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(299,'1103',20,NULL,'Ondulado de Pestañas 99 Eye lash','ondulado-de-pestanas-99-eye-lash-1103','[\"sin_graduacion\"]','Curva perfecta, mirada impactante. El Kit Ondulado de Pestañas 99 Eyelash incluye todo lo necesario...',54000.00,60000.00,45000.00,50,'[\"products\\/ondulado-de-pestanas-99-eye-lash-1103\\/1-6a1657c16212c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(300,'1102',20,NULL,'Ondulado de Pestañas Lash Lift','ondulado-de-pestanas-lash-lift-1102','[\"sin_graduacion\"]','Mirada elevada sin extensiones. El Ondulado de Pestañas Lash Lift realza la curvatura natural de la...',42000.00,45000.00,30000.00,50,'[\"products\\/ondulado-de-pestanas-lash-lift-1102\\/1-6a1657c130372.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(301,'1101',20,NULL,'Parches de hidrogel','parches-de-hidrogel-1101','[\"sin_graduacion\"]','Diseñados para colocarse bajo los ojos durante la aplicación de extensiones, ayudan a aislar las p...',500.00,800.00,250.00,50,'[\"products\\/parches-de-hidrogel-1101\\/1-6a1657c117ed8.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(302,'1089',20,NULL,'Pegante ARRDELL mini','pegante-arrdell-mini-1089','[\"sin_graduacion\"]','Adhesivo de larga duración ideal para fijar segmentos o pestañas individuales. Proporciona sujeci�...',17000.00,NULL,11900.00,50,'[\"products\\/pegante-arrdell-mini-1089\\/1-6a1657c010cfb.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(303,'1086',20,NULL,'Pegante Lady Black 5gr','pegante-lady-black-5gr-1086','[\"sin_graduacion\"]','Adhesivo de secado rápido y alta retención, ideal para aplicaciones clásicas o de volumen. Ofrece...',48000.00,NULL,34000.00,50,'[\"products\\/pegante-lady-black-5gr-1086\\/1-6a1657bfebe72.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(304,'1084',20,NULL,'Pestaña en seda Profesional Nagaraku','pestana-en-seda-profesional-nagaraku-1084','[\"sin_graduacion\"]','Eleva tu trabajo con las Pestañas de Seda Profesional Nagaraku. Su textura sedosa y acabado natural...',9500.00,10000.00,8500.00,50,'[\"products\\/pestana-en-seda-profesional-nagaraku-1084\\/1-6a1657bfb8bb4.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(305,'1280',20,NULL,'Pestaña Nagaraku Fibras Tecnológica','pestana-nagaraku-fibras-tecnologica-1280','[\"sin_graduacion\"]','Pestañas Nagaraku Fibras Tecnológicas: Consigue un volumen espectacular y un acabado natural que d...',28000.00,30000.00,23000.00,50,'[\"products\\/pestana-nagaraku-fibras-tecnologica-1280\\/1-6a1657cd0ac18.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(306,'1083',20,NULL,'Pestaña Perfect Look 5D','pestana-perfect-look-5d-1083','[\"sin_graduacion\"]','Volumen espectacular y look multidimensional. Consigue una mirada intensa y deslumbrante....',4500.00,NULL,3000.00,50,'[\"products\\/pestana-perfect-look-5d-1083\\/1-6a1657bf9f202.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(307,'1082',20,NULL,'Pestaña Punto a Punto Miracle. 100% indonesia','pestana-punto-a-punto-miracle-100-indonesia-1082','[\"sin_graduacion\"]','Pestañas individuales 100% indonesias para volumen y longitud natural. Disponibles en short y mediu...',5500.00,NULL,3900.00,50,'[\"products\\/pestana-punto-a-punto-miracle-100-indonesia-1082\\/1-6a1657bf8655d.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(308,'1276',20,NULL,'Pestaña Rusa 20 Pelitos.','pestana-rusa-20-pelitos-1276','[\"sin_graduacion\"]','Maximiza el volumen de tus clientes con nuestras Pestañas Rusas de 20 pelitos. Disponibles en 10mm,...',6000.00,NULL,3900.00,50,'[\"products\\/pestana-rusa-20-pelitos-1276\\/1-6a1657cccc1c7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(309,'1308',20,NULL,'Pestaña Rusa 30 Pelitos','pestana-rusa-30-pelitos-1308','[\"sin_graduacion\"]','deales para lograr volumen intenso y un acabado esponjoso sin perder ligereza. Cada grupo viene perf...',6500.00,NULL,4200.00,50,'[\"products\\/pestana-rusa-30-pelitos-1308\\/1-6a1657cfeb760.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(310,'1082-A',20,NULL,'Pestaña Rusa natural 10 Pelitos','pestana-rusa-natural-10-pelitos-1082-a','[\"sin_graduacion\"]','Consigue un volumen natural y ligero con nuestras Pestañas Rusas de 10mm, 12mm y 14mm. Crea looks p...',6000.00,NULL,3500.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(311,'1301',20,NULL,'Pestaña Tira X3','pestana-tira-x3-1301','[\"sin_graduacion\"]','Set de 3 pares de pestañas postizas Miracle con tecnología 5D. Diseño multicapa de fibras súper ...',8500.00,9000.00,6500.00,50,'[\"products\\/pestana-tira-x3-1301\\/1-6a1657cf6a04c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(312,'1257',20,NULL,'Pestaña Volumen Ruso Mix - 10P','pestana-volumen-ruso-mix-10p-1257','[\"sin_graduacion\"]','Consigue miradas impactantes con nuestra Pestaña Volumen Ruso Mix. Crea abanicos perfectos y diseñ...',11000.00,12000.00,8500.00,50,'[\"products\\/pestana-volumen-ruso-mix-10p-1257\\/1-6a1657cabd93b.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(313,'1303',20,NULL,'Pestaña Volumen Ruso Mix - 20P y 30P','pestana-volumen-ruso-mix-20p-y-30p-1303','[\"sin_graduacion\"]','Set mixto ideal para crear volumen personalizado según cada mirada. Incluye grupos de 20 pelitos pa...',16000.00,17000.00,12000.00,50,'[\"products\\/pestana-volumen-ruso-mix-20p-y-30p-1303\\/1-6a1657cf9f3ed.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(314,'1093',20,NULL,'Pinza para Pestañas Curva Tornasol','pinza-para-pestanas-curva-tornasol-1093','[\"sin_graduacion\"]','Herramienta de precisión diseñada para el aislamiento y colocación exacta de pestañas. Su punta ...',7500.00,8000.00,5000.00,50,'[\"products\\/pinza-para-pestanas-curva-tornasol-1093\\/1-6a1657c043d3a.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(315,'1282',20,NULL,'Pinza para pestañas volumen ruso tornasol','pinza-para-pestanas-volumen-ruso-tornasol-1282','[\"sin_graduacion\"]','Precisión milimétrica para técnicas avanzadas. Nuestra pinza para pestañas volumen ruso permite ...',8500.00,9000.00,6000.00,50,'[\"products\\/pinza-para-pestanas-volumen-ruso-tornasol-1282\\/1-6a1657cd2552c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(316,'1085',20,NULL,'Primer banana','primer-banana-1085','[\"sin_graduacion\"]','Fórmula diseñada para limpiar y preparar las pestañas naturales antes de la aplicación de extens...',21000.00,NULL,15000.00,50,'[\"products\\/primer-banana-1085\\/1-6a1657bfd1c9f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(317,'1096-A',20,NULL,'Regla pie de rey para cejas Corta','regla-pie-de-rey-para-cejas-corta-1096-a','[\"sin_graduacion\"]','Herramienta de medición precisa para diseño de cejas. Simetría perfecta en microblading y depilac...',3000.00,NULL,1800.00,50,'[\"products\\/regla-pie-de-rey-para-cejas-corta-1096-a\\/1-6a1657c08e4c7.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(318,'1096-B',20,NULL,'Regla pie de rey para cejas Mediana','regla-pie-de-rey-para-cejas-mediana-1096-b','[\"sin_graduacion\"]','Regla pie de rey para cejas Mediana',4000.00,NULL,2200.00,50,'[]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(319,'1093-1',20,NULL,'Set de pinzas para pestañas X2 Miracle','set-de-pinzas-para-pestanas-x2-miracle-1093-1','[\"sin_graduacion\"]','Precisión profesional para la aplicación de extensiones. Ideal para aislar y colocar con facilidad...',11000.00,12000.00,9000.00,50,'[\"products\\/set-de-pinzas-para-pestanas-x2-miracle-1093-1\\/1-6a1657c05bc5c.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(320,'1100',20,NULL,'Shampoo para pestañas','shampoo-para-pestanas-1100','[\"sin_graduacion\"]','Limpieza profunda y suave para extensiones y pestañas naturales. Elimina residuos y prolonga la dur...',19000.00,20000.00,16500.00,50,'[\"products\\/shampoo-para-pestanas-1100\\/1-6a1657c0f301f.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(321,'1277',20,NULL,'Ventilador de cejas y pestañas','ventilador-de-cejas-y-pestanas-1277','[\"sin_graduacion\"]','Secado rápido y eficaz de adhesivos y productos. Minimiza la irritación y acelera el proceso. ¡Im...',19000.00,20000.00,14000.00,50,'[\"products\\/ventilador-de-cejas-y-pestanas-1277\\/1-6a1657cce4501.webp\"]',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,1,0,0,0,'2026-05-23 03:00:12','2026-07-30 04:23:43'),(322,'DEMO-VAR-001',21,NULL,'Esmalte Áureo Semipermanente','esmalte-aureo-semipermanente-demo','[\"sin_graduacion\"]','Esmalte semipermanente premium con acabado brillo o mate. Disponible en 4 tonos y 2 tamaños.',18000.00,22000.00,12000.00,100,'[]','Esmalte Áureo Semipermanente | Belleza Áurea','Esmalte semipermanente premium con acabado brillo o mate. Disponible en 4 tonos y 2 tamaños.',NULL,'esmalte semipermanente Wuhao',0,'[\"Acabado brillo o mate de larga duraci\\u00f3n\",\"Disponible en 4 tonos y 2 tama\\u00f1os\",\"F\\u00e1cil aplicaci\\u00f3n con pincel ergon\\u00f3mico\",\"Resiste hasta 21 d\\u00edas sin descamarse\",\"Sin tolueno, formaldeh\\u00eddo ni DBP\"]','1. Limpia y desengrasa la uña con preparador.\r\n2. Aplica una capa fina de base rubber y cura 60 segundos.\r\n3. Aplica el esmalte áureo y cura 60 segundos.\r\n4. Repite con una segunda capa y vuelve a curar.\r\n5. Sella con top coat y cura 90 segundos.','Di-HEMA Trimethylhexyl Dicarbamate, HEMA, HPMA, Hydroxypropyl Methacrylate, Pigments (CI 77891, CI 19140), Photo-initiators, Silica.','Manicuristas profesionales y uso doméstico avanzado · Cualquier tipo de uña','7701234567890','WUH-SP-AUR-15',15.00,'ml','Corea',1,1,1,1,0,0,'2026-05-23 03:21:51','2026-07-30 04:23:43');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_page_settings`
--

DROP TABLE IF EXISTS `quiz_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` varchar(255) DEFAULT NULL,
  `result_title` varchar(255) DEFAULT NULL,
  `result_cta_text` varchar(255) DEFAULT NULL,
  `default_reason` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `recommendation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommendation_rules`)),
  `default_product_id` bigint(20) unsigned DEFAULT NULL,
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`questions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_page_settings`
--

LOCK TABLES `quiz_page_settings` WRITE;
/*!40000 ALTER TABLE `quiz_page_settings` DISABLE KEYS */;
INSERT INTO `quiz_page_settings` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-06-16 08:27:50','2026-06-16 08:27:50');
/*!40000 ALTER TABLE `quiz_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_settings`
--

DROP TABLE IF EXISTS `seo_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `robots` varchar(50) NOT NULL DEFAULT 'index, follow',
  `og_type` varchar(50) NOT NULL DEFAULT 'website',
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `twitter_card` varchar(50) NOT NULL DEFAULT 'summary_large_image',
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(500) DEFAULT NULL,
  `custom_schema_markup` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seo_settings_page_key_unique` (`page_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_settings`
--

LOCK TABLES `seo_settings` WRITE;
/*!40000 ALTER TABLE `seo_settings` DISABLE KEYS */;
INSERT INTO `seo_settings` VALUES (1,'home','Belleza Áurea | Cosmética natural, elegante y atemporal','Insumos y cosmética profesional de belleza en Colombia: uñas, piel, maquillaje y cabello. Marcas originales al por mayor y al detal. Calidad garantizada.','skincare natural, cosmética botánica, perfume artesanal, ritual de belleza, belleza áurea, vitamina C, rosa mosqueta',NULL,'index, follow','website','Belleza Áurea | Tu ritual de belleza natural','Skincare y fragancias premium con ingredientes botánicos. Belleza natural, elegante y atemporal.',NULL,'summary_large_image','Belleza Áurea | Tu ritual de belleza natural','Skincare y fragancias premium con ingredientes botánicos. Belleza natural, elegante y atemporal.',NULL,NULL,1,'2026-05-23 00:28:29','2026-07-29 23:24:48'),(2,'blue-light',NULL,NULL,NULL,NULL,'index, follow','website',NULL,NULL,NULL,'summary_large_image',NULL,NULL,NULL,NULL,1,'2026-07-30 04:11:01','2026-07-30 04:11:01');
/*!40000 ALTER TABLE `seo_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('1UrAvqB6MG1jAw8kzCJsIW71WA7CgARQ9d434h5c',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoic3BSQURJQWJDYkVVaWpCcmNwNXJhTkFQNFM0U1Vrd2MzN1FDYnRraCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MiO3M6NToicm91dGUiO3M6MTQ6InByb2R1Y3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367071),('24Ku0n543xeZR94dywsyKXWCc3zL86BraoffCokn',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1N3T0JVeWNBR3pURVVQZnR1S0VycUpFd3NGZG9HNTNOY3dzQ0xaNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367070),('558Vz9F46ilKu2U0aNNybEnTdTA8vSwWUhReNZ2n',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoidHZjSllZaEVJa3o0Qjd4dkxsOHlzY0Q4TFlWczdyZXI1akJNNjg4bCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9xdWl6IjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1785366849),('6QmdTpS4zKN5c51LPrn5GYjD0nDyq83NwUzNHgWw',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2Y5VjRlWG5Gb2p5RW85SGszdG5MN0lIRHgzRUVSSUM5dWxYOTNSVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9yaXR1YWxlcyI7czo1OiJyb3V0ZSI7czoxMDoiYmx1ZS1saWdodCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785367068),('6RpN5MoxFNF2gb91ipuEURi8gxZTPCy7iwCGudXd',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU21peERyUGFVbHFuaFdwMUliQW1jT0xtYnpWTUlveXdIZGNZS3BqeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9yaXR1YWxlcyI7czo1OiJyb3V0ZSI7czoxMDoiYmx1ZS1saWdodCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785364728),('6U5UxR8rS8X0FKxh6VHxKY7amtAKbWmg48qWbCSV',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiclg0VGNhcVk1eWxlTHJNSDQzWXVlc1UwUzZqZDltakFxNm9FWGVhSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785365750),('72TkQLNqwM1xGpzpYSaZe5nSZbdB9KqUKZsIPUCn',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; es-CO) WindowsPowerShell/5.1.26100.8875','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmtxcVA4dnNla2RuR1hLTUFlbGQ0U2N4TnRpeHNDUnFIZ3NCdkRQRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9hZG1pbi9sb2dpbiI7czo1OiJyb3V0ZSI7czoxMToiYWRtaW4ubG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1785364559),('A6SGcP73RBQdq27KLywtPEYBn9DMgMPgf7TwjmSb',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoicU02UllqTVlUQmhpaURYMGQwb1FFWVRlM01OdElxS1haVWExZGNybiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9zaXRlbWFwLnhtbCI7czo1OiJyb3V0ZSI7czo3OiJzaXRlbWFwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366282),('A8Je3hod0MaKdBu7hhF1yDBGnjEf2K7uTZIqBrv7',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibzBMMFZjRUtBTzVoUWtYUXRKeDROenVkd0hGTWJISWdhN1A1MkpUciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wb2xpdGljYS1kZS1jb29raWVzIjtzOjU6InJvdXRlIjtzOjEzOiJsZWdhbC5jb29raWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366723),('awZLSCtTDoUwMBhvxAHSNMVsyMxic8R6IPRz3QAo',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoid0sxbWtDTlhtSGhqVk5semg0WkxLelJtSG9xa0lWQmd1RWx3cHpCVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367069),('BbIiSpLIwPvu5oi6mfvL3JlQjEAmta6ZiqMPvOQL',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWlpN3FzdWV2WldJbVRWeDRjRDFRVmpyT2U5RWNRTlg4M3FISlBoSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wb2xpdGljYS1kZS1jb29raWVzIjtzOjU6InJvdXRlIjtzOjEzOiJsZWdhbC5jb29raWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366723),('bqx7eFHDr6leYSju9uY2fh92rUmcl7ot9CUFXMdW',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSmRud0VQN0N3MktWWUxWTGZyMW5TaVgyQndCWHVadEI3VGY2WWlNcyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9jYXJyaXRvIjtzOjU6InJvdXRlIjtzOjEwOiJjYXJ0LmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367639),('cRsGqMjfxwFnGeVdptReMxTBvjFb6LoS00qpcm8A',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZnE1QW1kSXpWdHdtRmo4VFFsRE9ybjBuZ282ZDk5aEVYNkdDVGNBOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9ibG9nIjtzOjU6InJvdXRlIjtzOjEwOiJibG9nLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366281),('DkSF0bpjCLd1Ckv7GuY15VX2pESCb6W8VFpPWwrk',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoieGE0OVNSaEd1U3RxT1VpVGNEZGdldnBqRTBOcHd1NVJ1QkRUNHhtaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785360461),('dtwruBHRxPRi62NuaqySPV19jcyjhQXFMFNiJK4d',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTnRpU3hXdDExMnJtTm5taHJDTFd4MHlaeE5sWEdSM0hBNGdEOGYweSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9yaXR1YWxlcyI7czo1OiJyb3V0ZSI7czoxMDoiYmx1ZS1saWdodCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785364729),('ERZ12sR141apjqmAMUIUVS3PUH27RfQ3nbavk3cu',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWTZacUllYXBNU01VM1Bmb0JHY1Q2Y25oTGVRNlpJMjVPWjB4QjZKUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC90ZXJtaW5vcy15LWNvbmRpY2lvbmVzIjtzOjU6InJvdXRlIjtzOjExOiJsZWdhbC50ZXJtcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785366721),('FJ46gCG2ZDdikeGJwdy8GM87KO5387hlT3xpWuW1',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoidGR6cmVKY0RacXVxR1c3QTVSa25rWXRXSlBuMzFETnJma0N2VG1mMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MiO3M6NToicm91dGUiO3M6MTQ6InByb2R1Y3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366279),('g9eXMwtiMmWXUdBKO4Pboi5tejvPPMnShxdZflGO',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYzBtaEdDWlBJTHpHZWFjNEw0b0kzUlNTWHBua2FzejVXMjlHRHo5WSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367067),('gA6KUlzWuMqD9AyM2rQDlanEGI6HESd5KhZSBRKT',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZktZWlY2VGhpajVqWmdxS1Jrc213c255R1k3RU4zOHBGZ0gzbUhwQyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vbG9jYWxob3N0OjgxMjAiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1785366868),('Hoy0nsKLTbQSUiLpgQ4IIFtltw5k2AvRsVxzepjc',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiR2RvTTJjMzhJdGtQS3RuNEZ0RTVwZGxNa1RUb0VGRDZ4V0RFbzhGbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9yaXR1YWxlcyI7czo1OiJyb3V0ZSI7czoxMDoiYmx1ZS1saWdodCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785364620),('I1fspaB0TxpcEGkFn9cWsbe7bhHhhDD8w3ZLZPDj',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTTZVdllEUno0NHhNb3Jhb3hJNVVOM3ZrYlA1OE45a3dXRnIxdmFCaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785360461),('LiOBLVHgNfhrOUAVlw7pJ4WbIyqb3dYUwFo0l6z3',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ243M2dkQmpCYzQ1ZXEwVG1pTlRjZUVUZXZjQ245S2pnYmRaem5aMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366278),('LjiCsWmS7CkJQfL7IAHW1AX9eD7Y7LiO3BKa6xSU',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEE2bWtZT2RyWlVpMHFtZjY2eWlnWENtblBBeGlUcVAyWDJpbFBBcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9jYXJyaXRvIjtzOjU6InJvdXRlIjtzOjEwOiJjYXJ0LmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366281),('lJwWKB8Lw9irKYx2XcluXi6zY4HUxshkMCC6T7jy',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUkJaSWlqbFBRVlk1VUpQZGFtSzdnWXBKTGtFWExuWGF2OWRQd3NlcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367660),('MCAIYTI1K9KMaDJYePIwSarAKpSE02WekrGxcbs5',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS09FRkNkcXdld0dRM053ZGJlTnlMVlQ5MW5BZzc0YUFtU0czRDdsaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wb2xpdGljYS1kZS1wcml2YWNpZGFkIjtzOjU6InJvdXRlIjtzOjEzOiJsZWdhbC5wcml2YWN5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366722),('mSWKFNqVHfb4vZW5Gm8S6tydHWXLjWGIvQh6rfxH',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYllxWGxUYzd2VGlHb3NBYjY5clVCMFNKaVNieXdJYng4N0xHUUZXSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9zaXRlbWFwLnhtbCI7czo1OiJyb3V0ZSI7czo3OiJzaXRlbWFwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785365682),('oSWgLMP8FgGsz7rn3HgsLUSdCBoO7tyLMJgH0x0x',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOVpBbTYzYkdWdGxKRE1ScGlhNTVXS0g5SFdSb0hEdXp1aVBsd2pFOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC90ZXJtaW5vcy15LWNvbmRpY2lvbmVzIjtzOjU6InJvdXRlIjtzOjExOiJsZWdhbC50ZXJtcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785366721),('PaIa4OhOXZmmUuPpefmOcjeqSZ5COvhl6Ag2XOhw',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSTNqTEhXN3FTQ1Jsd1cyMlFKbWI3OEdPc3Jmb0IyOWNuaktnbHVRVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367641),('PeInBgy3HKF1agCR3Uqc900zPaNLUC88fncX2e7X',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoic0JaMVVYcTV6ckxPRmJ3YmZuNmdkd1NORlo5MmtWMUVzZDZkTXZxYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MiO3M6NToicm91dGUiO3M6MTQ6InByb2R1Y3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367639),('PkEfLgAA4cU612gh0L7DPwXtYemLtbpTgnRZPjov',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoidlNTUTN3VXNFbFE0MmR2Z1pGSUFZNHRMZW9yTW1PQ0JEVjZzWDdIRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wb2xpdGljYS1kZS1wcml2YWNpZGFkIjtzOjU6InJvdXRlIjtzOjEzOiJsZWdhbC5wcml2YWN5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366722),('qfVsT9FxqkL96gGE2GgJttpf39oxNiwaOPzo9TsJ',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZHp4eFpxOUhRTlpYaDBVbFBtMEZtY0Y4czZuZ1VEM2tzcDNFbVNWeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367707),('QYM7Q13Ggc2wxtu1zXxiEPHB9pYpaeAcNSEgAgpB',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibzNudGY1S240M2RYSnB5M1c3blBIaUQ5V0VhT2hYdmFqWDNoMVNUbiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9yaXR1YWxlcyI7czo1OiJyb3V0ZSI7czoxMDoiYmx1ZS1saWdodCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785366280),('sDc8xiugbYeCsdN3U1Kw4UbF0dGLpkUySEv7qIFJ',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzduMGF0UG4xQ245cnpubW9iM2FSRnRUTjBEcU9IZ2Zzb1BQcGk5bSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9wcm9kdWN0b3MvZXNtYWx0ZS1iYWJ5LWR1bGNlLTEzIjtzOjU6InJvdXRlIjtzOjEzOiJwcm9kdWN0cy5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367072),('tBdiPsx0CEBnLR99Up6wid7VUhRnEY7WQYTLi0HI',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOXFqcm1VSjUzQnVPWWV6bjVScWJPSzlaT29pZlpYSTFIZVNIb0I3VCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9jYXJyaXRvIjtzOjU6InJvdXRlIjtzOjEwOiJjYXJ0LmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367070),('tXvphJKS6kmQiBAieJbbE6H18LSG3LfViIfS8PWT',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaVFtZ2hJNVM2UEE5WjJkRjM0bHpLSjdNN0RtelpuS1MwVFE2V2Y5UyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367638),('wJH7pktXcBHPpWwF7FrlaLnwdjvxyf76KIIOfPFH',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUEJDSE5Cc0s5dW1od3I5Z0ZUcjZHbnFkZVBuMDhEWEFETGNEU1lZdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9tYXJjYXMiO3M6NToicm91dGUiO3M6MTI6ImJyYW5kcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785366850),('x0XWQBpBHHr1DOdUADrrZr0qmvaOEQ8BXvlUJIxT',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSm1maTcxak80dGhUZWhFdGFsRDMwV25GUmluS0VrYlhCOGVUNjZCWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785367640),('xjTCAU2vkxsFi69qjWotkQANacFiXPrWf7xXbcZx',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRFRoUjB6RndzR1VuNFByRFlraTJXZWg5bkVSTFhEd2dsRGdpTW51aCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9yaXR1YWxlcyI7czo1OiJyb3V0ZSI7czoxMDoiYmx1ZS1saWdodCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1785364621),('Y3HaYOlBK8rnZHL77vWxAEgDGABPz8GUbSPbjP6H',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3lXVHd4Z3NTUUY2OXBzZWlEcWJvMjBoOXRmbzZkR004c25jeTBoMCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9jb250YWN0byI7czo1OiJyb3V0ZSI7czo3OiJjb250YWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366282),('yFubtmk3Cp4CcJPBrqS3D5f6M8IkkjAEhXSRNozS',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWNuRExhY1ZlT2h3VDdKSlVWUURzM2lNYU5aYUtQTG82WGdtaEpFNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9sYW5kaW5nIjtzOjU6InJvdXRlIjtzOjEyOiJsYW5kaW5nLnF1aXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1785366849),('yI4kxtzcVhGXUhyy0JR4t4Cz8c1nWtIkrUzwt7C6',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRUt5MmI1YWVZczdZNTB6U09hY0R6QmltOGRRR1E0YmR0UzVyWHo5bCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9zaXRlbWFwLnhtbCI7czo1OiJyb3V0ZSI7czo3OiJzaXRlbWFwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366718),('z3blONAKvE72I6VOPoUlkSib9VXPeGbIjDgjP08N',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1dCWFpGVGhvUkFmN3JWSFVFYlZaRmVsaVJWSEswUmhuZkdhcjJ3YiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9zaXRlbWFwLnhtbCI7czo1OiJyb3V0ZSI7czo3OiJzaXRlbWFwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785365684),('ZEn6oHcHk6j3cxqOlppMPBNnNQEmbNiAKqsLzvtN',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZzNXRlVITXhabURrZEVxM2Rab2NBS0llNVVuYlNPNkpFWDdtRUNESSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9zaXRlbWFwLnhtbCI7czo1OiJyb3V0ZSI7czo3OiJzaXRlbWFwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785366718),('zfE0mOhvXzUrakikND8H4gLxPR9msTTU5shb6FhL',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibllUOGtxc0t2OFZtTFFUVEh6YzBaZ1MycEVMUEVnOFpENVl4TVhmSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9sYW5kaW5nIjtzOjU6InJvdXRlIjtzOjEyOiJsYW5kaW5nLnF1aXoiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1785366850),('ZgW2efyUBcUntdYsUkEp3GzQTfednQYlwy2xevGz',NULL,'127.0.0.1','curl/8.19.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibmN6dUIzQmMxTVcxVjlWWkpHVnlFeUIzR3E5Tk9QOThZamdIYTlnaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODEyMC9ibG9nIjtzOjU6InJvdXRlIjtzOjEwOiJibG9nLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1785365683);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_rates`
--

DROP TABLE IF EXISTS `shipping_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_rates_city_unique` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_rates`
--

LOCK TABLES `shipping_rates` WRITE;
/*!40000 ALTER TABLE `shipping_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_returns_page_settings`
--

DROP TABLE IF EXISTS `shipping_returns_page_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_returns_page_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `shipping_title` varchar(255) DEFAULT NULL,
  `shipping_content` text DEFAULT NULL,
  `returns_title` varchar(255) DEFAULT NULL,
  `returns_content` text DEFAULT NULL,
  `warranty_title` varchar(255) DEFAULT NULL,
  `warranty_content` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_returns_page_settings`
--

LOCK TABLES `shipping_returns_page_settings` WRITE;
/*!40000 ALTER TABLE `shipping_returns_page_settings` DISABLE KEYS */;
INSERT INTO `shipping_returns_page_settings` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-07-29 04:08:59','2026-07-29 04:08:59');
/*!40000 ALTER TABLE `shipping_returns_page_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_settings`
--

DROP TABLE IF EXISTS `shipping_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_settings`
--

LOCK TABLES `shipping_settings` WRITE;
/*!40000 ALTER TABLE `shipping_settings` DISABLE KEYS */;
INSERT INTO `shipping_settings` VALUES (1,'default_price','0','2026-05-23 00:28:28','2026-07-30 03:46:04'),(2,'free_shipping_threshold','0','2026-05-23 00:28:28','2026-07-30 03:46:04');
/*!40000 ALTER TABLE `shipping_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT 5,
  `avatar_color` varchar(255) NOT NULL DEFAULT '#378ADD',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador Belleza Áurea','admin@bellezaaurea.com',1,NULL,'$2y$12$2GYuYfEgg7t8YNE49I41vu7KpLGX3uy19FRz1iMOoqnwOGCzQhami',NULL,'2026-05-23 00:28:29','2026-07-29 20:41:09'),(2,'Mich','mich@aurea.local',1,NULL,'$2y$12$7dtUlPEKXnEN0GUmVU6C/.TK9Ffwz2RmKg4hAelvaMbJp.o2fQcGW',NULL,'2026-06-16 07:38:29','2026-06-16 07:38:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-29 18:58:01
