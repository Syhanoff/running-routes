<?php
class RR_Divi_Running_Routes_Module extends ET_Builder_Module {
	public $slug       = 'rr_running_routes';
	public $vb_support = 'on';

	protected $module_credits = [
		'module_uri' => '#',
		'author'     => 'Your Name',
		'author_uri' => '#',
	];

	public function init() {
		$this->name = esc_html__( 'Running Route', 'running-routes' );
	}

	public function get_fields() {
		return [
			'route_id' => [
				'label'           => esc_html__( 'Route ID', 'running-routes' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter route ID.', 'running-routes' ),
			],
			'height' => [
				'label'           => esc_html__( 'Map Height', 'running-routes' ),
				'type'            => 'range',
				'default'         => '500',
				'range_settings'  => [
					'min'  => '200',
					'max'  => '1000',
					'step' => '10',
				],
			],
			'map_layer' => [
				'label'            => esc_html__( 'Map Layer', 'running-routes' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => [
					'opentopomap' => esc_html__( 'OpenTopoMap', 'running-routes' ),
					'osm'         => esc_html__( 'OpenStreetMap', 'running-routes' ),
					'mapycz'      => esc_html__( 'Mapy.cz', 'running-routes' ),
				],
				'default'          => 'opentopomap',
			],
		];
	}

	public function render( $attrs, $content = null, $render_slug ) {
		$route_id = $this->props['route_id'];
		$height   = $this->props['height'];
		$layer    = $this->props['map_layer'];

		if ( empty( $route_id ) ) {
			return '<p>' . esc_html__( 'Please select a route ID.', 'running-routes' ) . '</p>';
		}

		return do_shortcode( "[running_route id='{$route_id}' height='{$height}' layer='{$layer}']" );
	}
}

new RR_Divi_Running_Routes_Module;