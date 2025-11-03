<?php
class RunningRoute_Module extends ET_Builder_Module {
	public $slug       = 'running_route';
	public $vb_support = 'on';

	public function init() {
		$this->name = esc_html__( 'Running Route', 'running-routes' );
	}

	public function get_fields() {
		return [
			'route_id' => [
				'label' => esc_html__( 'Route ID', 'running-routes' ),
				'type'  => 'text',
			],
			'height' => [
				'label' => esc_html__( 'Height', 'running-routes' ),
				'type'  => 'range',
				'default' => '500',
			],
		];
	}

	public function render( $attrs, $content = null, $render_slug ) {
		return do_shortcode( "[running_route id='{$this->props['route_id']}' height='{$this->props['height']}']" );
	}
}
new RunningRoute_Module;