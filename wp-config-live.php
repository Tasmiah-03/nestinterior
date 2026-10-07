<?php
define( 'DB_NAME', 'if0_41950541_nestinterior' );
define( 'DB_USER', 'if0_41950541' );
define( 'DB_PASSWORD', 'Amayra003' );
define( 'DB_HOST', 'sql113.infinityfree.com' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

define( 'AUTH_KEY',         'p3w8r9t7y6u5i4o3p2q1w9e8r7t6y5u4i3o2p1q0w9e8r7t6y5u4i3o2p1q0w9e8' );
define( 'SECURE_AUTH_KEY',  'o9i8u7y6t5r4e3w2q1p0o9i8u7y6t5r4e3w2q1p0o9i8u7y6t5r4e3w2q1p0o9i8' );
define( 'LOGGED_IN_KEY',    'a1s2d3f4g5h6j7k8l9z0a1s2d3f4g5h6j7k8l9z0a1s2d3f4g5h6j7k8l9z0a1s2' );
define( 'NONCE_KEY',        'q1w2e3r4t5y6u7i8o9p0q1w2e3r4t5y6u7i8o9p0q1w2e3r4t5y6u7i8o9p0q1w2' );
define( 'AUTH_SALT',        'z1x2c3v4b5n6m7q8w9e0z1x2c3v4b5n6m7q8w9e0z1x2c3v4b5n6m7q8w9e0z1x2' );
define( 'SECURE_AUTH_SALT', 'm1n2b3v4c5x6z7l8k9j0m1n2b3v4c5x6z7l8k9j0m1n2b3v4c5x6z7l8k9j0m1n2' );
define( 'LOGGED_IN_SALT',   'p1o2i3u4y5t6r7e8w9q0p1o2i3u4y5t6r7e8w9q0p1o2i3u4y5t6r7e8w9q0p1o2' );
define( 'NONCE_SALT',       'l1k2j3h4g5f6d7s8a9p0l1k2j3h4g5f6d7s8a9p0l1k2j3h4g5f6d7s8a9p0l1k2' );

$table_prefix = 'wp_';
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
