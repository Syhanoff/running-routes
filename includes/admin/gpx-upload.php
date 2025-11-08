<?php

/**
 * GPX Upload Meta Box for Running Routes
 */
function rr_add_gpx_meta_box()
{
  add_meta_box(
    'rr_gpx_upload',
    __('GPX File', 'running-routes'),
    'rr_render_gpx_meta_box',
    'running_route',
    'side',
    'high'
  );
}
add_action('add_meta_boxes', 'rr_add_gpx_meta_box');

function rr_render_gpx_meta_box($post)
{
  $attachment_id = get_post_meta($post->ID, '_rr_gpx_attachment_id', true);
  $url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
  $filename = $attachment_id ? basename(get_attached_file($attachment_id)) : '';

  wp_nonce_field('rr_save_gpx', 'rr_gpx_nonce');

?>
  <div class="running-routes-gpx-metabox">
    <div class="gpx-upload-from-device-button">
      <button type="button" class="button button-primary upload-from-device">
        <?php _e('Загрузить с устройства', 'running-routes'); ?>
      </button>
    </div>
    <div class="gpx-select-from-library-button">
      <button type="button" class="button button-secondary select-from-library">
        <?php _e('Из медиабиблиотеки', 'running-routes'); ?>
      </button>
    </div>

    <?php if ($url && $filename): ?>
      <div class="gpx-file-info" style="margin-top:15px; padding:12px; background:#f8f9f9; border-radius:4px; border:1px solid #e2e4e7;">
        <p><strong><?php _e('Ссылка на файл:', 'running-routes'); ?></strong></p>
        <div class="gpx-url-container" style="position:relative; margin:8px 0;">
          <input type="text" class="gpx-url widefat" value="<?php echo esc_url($url); ?>" readonly style="padding-right:30px; font-family: Consolas, Monaco, monospace; font-size: 13px;">
          <span class="dashicons dashicons-admin-links copy-url" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer; color:#666;" title="<?php _e('Копировать в буфер', 'running-routes'); ?>"></span>
        </div>

        <p><strong><?php _e('Шорткод:', 'running-routes'); ?></strong></p>
        <div class="shortcode-container" style="position:relative; margin:8px 0;">
          <input type="text" class="shortcode widefat" value="[running_route id=&quot;<?php echo esc_attr($post->ID); ?>&quot;]" readonly style="padding-right:30px; font-family: Consolas, Monaco, monospace; font-size: 13px;">
          <span class="dashicons dashicons-admin-links copy-shortcode" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); cursor:pointer; color:#666;" title="<?php _e('Копировать в буфер', 'running-routes'); ?>"></span>
        </div>

        <div class="gpx-download-button">
          <a href="<?php echo esc_url($url); ?>" class="button button-primary" download="<?php echo esc_attr($filename); ?>">
            <?php _e('Скачать файл', 'running-routes'); ?>
          </a>
        </div>

        <div class="gpx-detach-button"">
                    <button type=" button" class="button button-secondary detach-gpx-url">
          <?php _e('Открепить файл', 'running-routes'); ?>
          </button>
        </div>

        <div class="gpx-remove-link">
          <a href="#" class="remove-gpx-url">
            <?php _e('Удалить файл', 'running-routes'); ?>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <input type="file" id="rr-gpx-device-upload" name="rr_gpx_file" accept=".gpx" style="display:none;">
    <input type="hidden" name="_rr_gpx_attachment_id" id="_rr_gpx_attachment_id" value="<?php echo esc_attr($attachment_id); ?>">
  </div>
<?php
}

// AJAX handler для сохранения attachment ID
add_action('wp_ajax_rr_save_attachment_id', function () {
  check_ajax_referer('running_routes_admin_nonce', 'nonce');

  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;

  if (!$post_id || !current_user_can('edit_post', $post_id)) {
    wp_send_json_error(__('Недостаточно прав', 'running-routes'));
  }

  if ($attachment_id) {
    update_post_meta($post_id, '_rr_gpx_attachment_id', $attachment_id);
  } else {
    delete_post_meta($post_id, '_rr_gpx_attachment_id');
  }

  $url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
  $filename = $attachment_id ? basename(get_attached_file($attachment_id)) : '';
  $shortcode = $attachment_id ? '[running_route id="' . $post_id . '"]' : '';

  wp_send_json_success([
    'attachment_id' => $attachment_id,
    'url' => $url,
    'filename' => $filename,
    'shortcode' => $shortcode
  ]);
});

// AJAX handler для удаления файла
add_action('wp_ajax_rr_remove_gpx_file', function () {
  check_ajax_referer('running_routes_admin_nonce', 'nonce');

  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  if (!$post_id || !current_user_can('edit_post', $post_id)) {
    wp_send_json_error(__('Недостаточно прав', 'running-routes'));
  }

  $attachment_id = get_post_meta($post_id, '_rr_gpx_attachment_id', true);
  if ($attachment_id) {
    wp_delete_attachment($attachment_id, true);
    delete_post_meta($post_id, '_rr_gpx_attachment_id');
    wp_send_json_success();
  }

  wp_send_json_error(__('Файл не найден', 'running-routes'));
});

// AJAX handler для открепления файла
add_action('wp_ajax_rr_detach_gpx_file', function () {
  check_ajax_referer('running_routes_admin_nonce', 'nonce');

  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  if (!$post_id || !current_user_can('edit_post', $post_id)) {
    wp_send_json_error(__('Недостаточно прав', 'running-routes'));
  }

  delete_post_meta($post_id, '_rr_gpx_attachment_id');
  wp_send_json_success();
});

// AJAX handler для загрузки файла с устройства
add_action('wp_ajax_rr_upload_gpx_file', function () {
  check_ajax_referer('running_routes_admin_nonce', 'nonce');

  $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
  if (!$post_id || !current_user_can('edit_post', $post_id)) {
    wp_send_json_error(__('Недостаточно прав', 'running-routes'));
  }

  if (!isset($_FILES['gpx_file']) || empty($_FILES['gpx_file']['name'])) {
    wp_send_json_error(__('Нет файла для загрузки', 'running-routes'));
  }

  // Validate file type
  $file_name = $_FILES['gpx_file']['name'];
  $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

  if ($file_ext !== 'gpx') {
    wp_send_json_error(__('Разрешены только GPX файлы', 'running-routes'));
  }

  require_once ABSPATH . 'wp-admin/includes/media.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';

  $attachment_id = media_handle_upload('gpx_file', $post_id);

  if (is_wp_error($attachment_id)) {
    wp_send_json_error($attachment_id->get_error_message());
  }

  $url = wp_get_attachment_url($attachment_id);
  $filename = basename(get_attached_file($attachment_id));
  $shortcode = '[running_route id="' . $post_id . '"]';

  update_post_meta($post_id, '_rr_gpx_attachment_id', $attachment_id);

  wp_send_json_success([
    'attachment_id' => $attachment_id,
    'url' => $url,
    'filename' => $filename,
    'shortcode' => $shortcode
  ]);
});
