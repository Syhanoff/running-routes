<?php
add_shortcode('running_route', function ($atts) {
  $atts = shortcode_atts([
    'id'     => '',
    'height' => '500',
    'layer'  => 'opentopomap',
    'theme'  => 'light',
  ], $atts);

  if (empty($atts['id'])) {
    return '<p>' . esc_html__('Route ID is required.', 'running-routes') . '</p>';
  }

  $route = \RunningRoutes\Core\RouteManager::get_route((int) $atts['id']);
  if (! $route) {
    return '<p>' . esc_html__('Route not found.', 'running-routes') . '</p>';
  }


  // Подключаем OpenLayers
  wp_enqueue_style('openlayers-css', 'https://cdn.jsdelivr.net/npm/ol@7.5.2/ol.css', [], '7.5.2');
  wp_enqueue_script('openlayers', 'https://cdn.jsdelivr.net/npm/ol@7.5.2/dist/ol.js', [], '7.5.2', true);
  wp_enqueue_script('running-routes-map', RUNNING_ROUTES_URL . 'assets/js/openlayers-map.js', ['openlayers'], RUNNING_ROUTES_VERSION, true);
  wp_enqueue_style('running-routes-frontend', RUNNING_ROUTES_URL . 'assets/css/frontend.css', [], RUNNING_ROUTES_VERSION);

  wp_localize_script('running-routes-map', 'rr_map_data', [
    'route' => [
      'id'     => $route->id,
      'name'   => $route->name,
      'points' => $route->points,
    ],
    'options' => $atts,
  ]);

  $map_id = 'rr-map-' . uniqid();
  return '<div id="' . esc_attr($map_id) . '" class="running-routes-map" style="height:' . esc_attr($atts['height']) . 'px;"></div>';
});










add_shortcode('test_map', function () {
  wp_enqueue_style('openlayers-css', 'https://cdn.jsdelivr.net/npm/ol@v10.7.0/dist/ol.css');
  wp_enqueue_script('openlayers', 'https://cdn.jsdelivr.net/npm/ol@v10.7.0/dist/ol.js', [], '10.7.0', true);
  wp_add_inline_script('openlayers', '
        document.addEventListener("DOMContentLoaded", function() {
            const map = new ol.Map({
                target: "test-map",
                layers: [
                    new ol.layer.Tile({ source: new ol.source.OSM() })
                ],
                view: new ol.View({ center: ol.proj.fromLonLat([37.618423, 55.751244]), zoom: 12 })
            });
        });
    ');
  return '<div id="test-map" style="height:400px;"></div>';
});
