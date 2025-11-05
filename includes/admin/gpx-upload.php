<?php
/**
 * GPX Upload Meta Box for 'running_route' only
 */

function rr_add_gpx_meta_box() {
    // Только для running_route
    add_meta_box(
        'rr_gpx_upload',
        __( 'GPX File', 'running-routes' ),
        'rr_render_gpx_meta_box',
        'running_route',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'rr_add_gpx_meta_box' );

function rr_render_gpx_meta_box( $post ) {
    $attachment_id = get_post_meta( $post->ID, '_rr_gpx_attachment_id', true );
    $url = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
    $filename = $attachment_id ? basename( get_attached_file( $attachment_id ) ) : '';

    wp_nonce_field( 'rr_save_gpx', 'rr_gpx_nonce' );
    ?>
    <input type="file" name="rr_gpx_file" accept=".gpx" style="width:100%;" />
    
    <?php if ( $url && $filename ) : ?>
        <p style="margin-top:10px; padding:8px; background:#f8f8f8; border-radius:4px;">
            <strong><?php echo esc_html( $filename ); ?></strong><br>
            <a href="<?php echo esc_url( $url ); ?>" target="_blank" style="display:inline-block; margin-top:4px;">
                <?php esc_html_e( 'Download', 'running-routes' ); ?>
            </a>
            <button type="button" class="button-link rr-remove-gpx" style="color:#a00; margin-left:10px;">
                <?php esc_html_e( 'Remove', 'running-routes' ); ?>
            </button>
            <input type="hidden" name="rr_gpx_current" value="<?php echo esc_attr( $attachment_id ); ?>" />
        </p>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const removeBtn = document.querySelector('.rr-remove-gpx');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    const input = document.querySelector('input[name="rr_gpx_current"]');
                    if (input) input.value = '';
                    const container = this.closest('p');
                    if (container) container.style.display = 'none';
                    // Отметим, что файл нужно удалить
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'rr_gpx_remove';
                    hidden.value = '1';
                    document.getElementById('post').appendChild(hidden);
                });
            }
        });
        </script>
    <?php endif;
}

function rr_save_gpx_file( $post_id ) {
    // Безопасность
    if ( ! isset( $_POST['rr_gpx_nonce'] ) || ! wp_verify_nonce( $_POST['rr_gpx_nonce'], 'rr_save_gpx' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Удаление файла
    if ( isset( $_POST['rr_gpx_remove'] ) && $_POST['rr_gpx_remove'] ) {
        delete_post_meta( $post_id, '_rr_gpx_attachment_id' );
        return;
    }

    // Загрузка нового файла
    if ( ! empty( $_FILES['rr_gpx_file']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_handle_upload( 'rr_gpx_file', $post_id );
        if ( ! is_wp_error( $attachment_id ) ) {
            update_post_meta( $post_id, '_rr_gpx_attachment_id', $attachment_id );
        }
    }
}
add_action( 'save_post_running_route', 'rr_save_gpx_file' );