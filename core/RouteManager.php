<?php
namespace RunningRoutes\Core;

class RouteManager {
	public static function get_route( int $id ): ?Route {
		// Здесь можно брать из post_meta, attachment или кэша
		// Для MVP — заглушка
		return new Route( $id, [
			'name' => 'Demo Route',
			'points' => [
				['lat' => 55.751244, 'lng' => 37.618423],
				['lat' => 55.755814, 'lng' => 37.617666],
			]
		] );
	}
}