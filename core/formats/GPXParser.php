<?php

namespace RunningRoutes\Core;

class GPXParser implements TrackParserInterface
{
  public function parse(string $file_path): array
  {
    if (! file_exists($file_path)) {
      return [];
    }

    $xml = simplexml_load_file($file_path);
    if (! $xml) {
      return [];
    }

    $points = [];
    $total_distance = 0;

    // Получаем первую точку
    $prev_point = null;

    foreach ($xml->trk->trkseg->trkpt as $pt) {
      $lat = (float) $pt['lat'];
      $lng = (float) $pt['lon'];

      $point = [
        'lat' => $lat,
        'lng' => $lng,
        'elevation' => isset($pt->ele) ? (float) $pt->ele : null,
      ];

      // Вычисляем дистанцию между точками
      if ($prev_point) {
        $distance = $this->calculateDistance(
          $prev_point['lat'],
          $prev_point['lng'],
          $lat,
          $lng
        );
        $total_distance += $distance;
      }

      $points[] = $point;
      $prev_point = $point;
    }

    return [
      'points' => $points,
      'distance' => round($total_distance / 1000, 2), // в километрах
      'elevation' => $this->getElevationRange($points),
    ];
  }

  private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
  {
    // Формула Haversine
    $earth_radius = 6371000; // метры

    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
      sin($dLng / 2) * sin($dLng / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c;
  }

  private function getElevationRange(array $points): array
  {
    $elevations = array_filter(array_column($points, 'elevation'));
    if (empty($elevations)) {
      return ['min' => 0, 'max' => 0];
    }

    return [
      'min' => min($elevations),
      'max' => max($elevations),
    ];
  }
}
