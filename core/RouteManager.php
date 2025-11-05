<?php
namespace RunningRoutes\Core;

class RouteManager {
    public static function get_route( int $id ): ?Route {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'running_route' ) {
            return null;
        }

        $attachment_id = get_post_meta( $id, '_rr_gpx_attachment_id', true );
        if ( ! $attachment_id ) {
            return null;
        }

        $file_path = get_attached_file( $attachment_id );
        if ( ! $file_path || ! file_exists( $file_path ) ) {
            return null;
        }

        $parser = new GPXParser();
        $data = $parser->parse( $file_path );

        return new Route( $id, [
            'name'   => $post->post_title,
            'points' => $data['points'] ?? [],
            'meta'   => [
                'distance' => $data['distance'] ?? 0,
                'elevation' => $data['elevation'] ?? 0,
            ]
        ]);
    }
}