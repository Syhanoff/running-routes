<?php

/**
 * Plugin Name: Running Routes
 * Description: Display GPX tracks on interactive maps (OpenLayers, Mapy.cz) with support for Divi, Gutenberg, and ACF.
 * Version: 0.2.0
 * Author: Syhanoff
 * Text Domain: running-routes
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (! defined('ABSPATH')) {
  exit;
}

define('RUNNING_ROUTES_VERSION', '0.1.0');
define('RUNNING_ROUTES_PATH', plugin_dir_path(__FILE__));
define('RUNNING_ROUTES_URL', plugin_dir_url(__FILE__));

// Загрузка автозагрузчика (если будет composer)
if (file_exists(RUNNING_ROUTES_PATH . 'vendor/autoload.php')) {
  require RUNNING_ROUTES_PATH . 'vendor/autoload.php';
}

// Ядро
require RUNNING_ROUTES_PATH . 'core/Route.php';
require RUNNING_ROUTES_PATH . 'core/RouteManager.php';
require RUNNING_ROUTES_PATH . 'core/interfaces/TrackParserInterface.php';
require RUNNING_ROUTES_PATH . 'core/interfaces/MapRendererInterface.php';
require RUNNING_ROUTES_PATH . 'core/formats/GPXParser.php';

// Интеграции
require RUNNING_ROUTES_PATH . 'includes/shortcodes.php';
require RUNNING_ROUTES_PATH . 'includes/widget.php';
require RUNNING_ROUTES_PATH . 'includes/admin/menu.php';

// Админка
if (is_admin()) {
  require RUNNING_ROUTES_PATH . 'includes/admin/gpx-upload.php';
}

// Автоматическая загрузка шаблона из плагина
add_filter('single_template', function ($template) {
  global $post;
  if ($post && $post->post_type === 'running_route') {
    $custom = RUNNING_ROUTES_PATH . 'templates/single-running_route.php';
    if (file_exists($custom)) return $custom;
  }
  return $template;
}, 99);

// TinyMCE (классический редактор)
add_action('admin_enqueue_scripts', function () {
  if (in_array(get_current_screen()->base, ['post', 'page'])) {
    wp_enqueue_script(
      'running-routes-tinymce',
      RUNNING_ROUTES_URL . 'integrations/classic-editor/tinymce-button.js',
      ['jquery'],
      RUNNING_ROUTES_VERSION,
      true
    );
    wp_localize_script('running-routes-tinymce', 'rr_tinymce', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('rr_tinymce_nonce'),
    ]);
  }
});

// Gutenberg
add_action('enqueue_block_editor_assets', function () {
  wp_enqueue_script(
    'running-routes-block',
    RUNNING_ROUTES_URL . 'integrations/gutenberg/index.js',
    ['wp-blocks', 'wp-element', 'wp-editor'],
    RUNNING_ROUTES_VERSION,
    true
  );
});

// Divi Legacy
add_action('et_builder_ready', function () {
  if (class_exists('ET_Builder_Module')) {
    require RUNNING_ROUTES_PATH . 'integrations/divi/legacy/RunningRoutesDiviModule.php';
  }
});

// Регистрация Divi-модулей (и Legacy, и Divi 5)
// add_action('et_builder_ready', function () {
//   if (class_exists('ET_Builder_Module')) {
//     require_once RUNNING_ROUTES_PATH . 'includes/modules/RunningRoute/RunningRoute.php';
//   }
// });

// Активация
register_activation_hook(__FILE__, function () {
  // Здесь можно создать CPT или таблицы, если нужно
});

// Деактивация
register_deactivation_hook(__FILE__, function () {
  // Очистка кэшей и т.п.
});

// Отключить Divi Builder для running_route
// add_filter('et_builder_post_types', function ($post_types) {
//   return array_diff($post_types, ['running_route']);
// });

// Разрешить загрузку GPX-файлов
add_filter('upload_mimes', function ($mimes) {
  $mimes['gpx'] = 'application/gpx+xml';
  return $mimes;
});

// Проверка типа файла (для безопасности)
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
  if (empty($data['ext']) && preg_match('/\.gpx$/i', $filename)) {
    $data['ext'] = 'gpx';
    $data['type'] = 'application/gpx+xml';
  }
  return $data;
}, 10, 4);

// Добавляем enctype для загрузки файлов в CPT
add_action('admin_footer', function () {
  global $post;
  if ($post && in_array($post->post_type, ['post', 'running_route'])) {
    echo '<script>
            (function () {
                // Ждём, пока DOM загрузится
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", addEnctype);
                } else {
                    addEnctype();
                }

                function addEnctype() {
                    const form = document.getElementById("post");
                    if (form && !form.hasAttribute("enctype")) {
                        form.setAttribute("enctype", "multipart/form-data");
                        console.log("✅ enctype added for file upload");
                    }
                }
            })();
        </script>';
  }
});
