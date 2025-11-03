<?php
namespace RunningRoutes\Core;

class Route {
	public $id;
	public $name;
	public $points = [];
	public $meta = [];

	public function __construct( int $id, array $data ) {
		$this->id   = $id;
		$this->name = $data['name'] ?? '';
		$this->points = $data['points'] ?? [];
		$this->meta = $data['meta'] ?? [];
	}
}