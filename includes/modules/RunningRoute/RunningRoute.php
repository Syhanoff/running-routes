<?php
class RunningRoute_Module extends ET_Builder_Module
{
  public $slug       = 'running_route';
  public $vb_support = 'on';

  public function init()
  {
    $this->name = esc_html__('Running Route', 'running-routes');
  }

  public function get_fields()
  {
    return [
      'route_id' => [
        'label' => esc_html__('Route ID', 'running-routes'),
        'type'  => 'text',
      ],
      'height' => [
        'label' => esc_html__('Height', 'running-routes'),
        'type'  => 'range',
        'default' => '500',
      ],
    ];
  }

  public function render($attrs, $content = null, $render_slug)
  {
    $route_id = $this->props['route_id'];
    $height   = $this->props['height'];

    if (empty($route_id)) {
      return '<p>' . esc_html__('Please select a route ID. (DIVI5)', 'running-routes') . '</p>';
    }

    $route = \RunningRoutes\Core\RouteManager::get_route((int) $route_id);
    if (! $route) {
      return '<p>' . esc_html__('Route not found or GPX file is missing.', 'running-routes') . '</p>';
    }

    return do_shortcode("[running_route id='{$route_id}' height='{$height}']");
  }
}
new RunningRoute_Module;
