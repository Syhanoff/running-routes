<?php
namespace RunningRoutes\Core;

class GPXParser implements TrackParserInterface {
	public function parse( string $file_path ): array {
		if ( ! file_exists( $file_path ) ) {
			return [];
		}
		$xml = simplexml_load_file( $file_path );
		if ( ! $xml ) {
			return [];
		}

		$points = [];
		foreach ( $xml->trk->trkseg->trkpt as $pt ) {
			$points[] = [
				'lat' => (float) $pt['lat'],
				'lng' => (float) $pt['lon'],
				'elevation' => isset( $pt->ele ) ? (float) $pt->ele : null,
			];
		}

		return [
			'points' => $points,
			'name'   => (string) ( $xml->trk->name ?? 'Unnamed Route' ),
		];
	}
}