<?php
class RR_Running_Route_Widget extends WP_Widget
{
  public function __construct()
  {
    parent::__construct(
      'rr_running_route_widget',
      __('Running Route Widget', 'running-routes'),
      array('description' => __('Displays the latest running route.', 'running-routes'))
    );
  }

  public function widget($args, $instance)
  {
    echo $args['before_widget'];
    if (! empty($instance['title'])) {
      echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
    }

    // Здесь выводим информацию о последнем маршруте
    $latest_route = get_posts([
      'post_type' => 'running_route',
      'posts_per_page' => 1,
      'post_status' => 'publish'
    ]);

    if ($latest_route) {
      $route = $latest_route[0];
      echo '<p>' . esc_html($route->post_title) . '</p>';
      echo '<p>' . esc_html(get_post_meta($route->ID, '_rr_distance', true)) . ' km</p>';


      echo '<p><strong>' . esc_html($route->post_title) . '</strong></p>';
      echo '<p>ID: ' . esc_html($route->ID) . '</p>';
      echo '<p><a href="' . esc_url(get_permalink($route->ID)) . '">View route</a></p>';
    }

    echo $args['after_widget'];
  }

  public function form($instance)
  {
    $title = ! empty($instance['title']) ? $instance['title'] : __('New title', 'running-routes');
?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
        <?php esc_attr_e('Title:', 'running-routes'); ?>
      </label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
        name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
        value="<?php echo esc_attr($title); ?>">
    </p>
<?php
  }
}

function rr_register_widgets()
{
  register_widget('RR_Running_Route_Widget');
}
add_action('widgets_init', 'rr_register_widgets');
