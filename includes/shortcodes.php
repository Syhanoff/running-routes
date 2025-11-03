<?php
add_shortcode( 'running_route', function ( $atts ) {
	$atts = shortcode_atts( [
		'id'     => '',
		'height' => '500',
		'layer'  => 'opentopomap',
		'theme'  => 'light',
	], $atts );

	if ( empty( $atts['id'] ) ) {
		return '<p>' . esc_html__( 'Route ID is required.', 'running-routes' ) . '</p>';
	}

	$route = \RunningRoutes\Core\RouteManager::get_route( (int) $atts['id'] );
	if ( ! $route ) {
		return '<p>' . esc_html__( 'Route not found.', 'running-routes' ) . '</p>';
	}

	// Подключаем JS/CSS
	wp_enqueue_style( 'running-routes-frontend', RUNNING_ROUTES_URL . 'assets/css/frontend.css', [], RUNNING_ROUTES_VERSION );
	wp_enqueue_script( 'running-routes-map', RUNNING_ROUTES_URL . 'assets/js/openlayers-map.js', [], RUNNING_ROUTES_VERSION, true );
	wp_localize_script( 'running-routes-map', 'rr_map_data', [
		'route' => [
			'id'     => $route->id,
			'name'   => $route->name,
			'points' => $route->points,
		],
		'options' => $atts,
	] );

	$map_id = 'rr-map-' . uniqid();
	return '<div id="' . esc_attr( $map_id ) . '" class="running-routes-map" style="height:' . esc_attr( $atts['height'] ) . 'px;"></div>';
} );