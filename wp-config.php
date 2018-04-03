<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'cthemes_test');

/** Имя пользователя MySQL */
define('DB_USER', 'cthemes_test');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'YgPuK9H8nb1o1R4J');

/** Имя сервера MySQL */
define('DB_HOST', 'cthemes.mysql.tools');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Bm=%,IF^5YfO[=+:0m*|Fjb)|+!|=1d=7)O|$,x7K{i^$xK4W^nkm8Sh7=,JCSAT');
define('SECURE_AUTH_KEY',  'xAyG81Qb;A)$m&Q-mi|-t&<X&7EL>lS(C4$9|q!}M/aot#6j~F1`^!3bjy#!:@UD');
define('LOGGED_IN_KEY',    '5]xKwkuBUl3D]|)vgQ! HKvZspJ[p%4vq{kUpQW s;FB=Iy;fdBlD`p?WW[aE|:u');
define('NONCE_KEY',        'GiCNRb=;Jq!mxeOZPKNZFFmH3x:kBIak<oTwEqXg7, Uq@e4Ky3@_!,F>/|69-*O');
define('AUTH_SALT',        'd(f~B84WGD@mR]QZ-Gw Ne]|D3?5nm6@h]Wn+mx-8qXh{H_M +p_JN9>W`OqTT|m');
define('SECURE_AUTH_SALT', 'S7+QYDc-JwQ J;g*WLgw:!S0Z-/Jr;YVqS_tavuL44<$PA+e|b<&J.xaAewZQnB2');
define('LOGGED_IN_SALT',   'd}U$P:-}b+[IG|%d%5sk;|b#dI{?PIHUy@~p<#<+&G^NdY*/kka =,2 oxaiKVsA');
define('NONCE_SALT',       '8KgPl@c!(rre[vT!zsMC!%P9R45yZ>GU2}{_{V=v^{s)Q<Bc|6A$3$1[m-.l4xDY');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_dfgtst';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
