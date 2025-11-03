<?php
namespace RunningRoutes\Core;

interface MapRendererInterface {
	public function render( array $track_data, array $options = [] ): string;
}