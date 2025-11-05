document.addEventListener('DOMContentLoaded', function () {
  const mapContainers = document.querySelectorAll('.running-routes-map');
  mapContainers.forEach((container) => {
    const mapId = container.id;
    const mapData = window.rr_map_data;

    if (!mapData || !mapData.route) return;

    const points = mapData.route.points;
    if (!points.length) return;

    const map = new ol.Map({
      target: mapId,
      layers: [
        new ol.layer.Tile({
          source: new ol.source.XYZ({
            url: 'https://tile.opentopomap.org/{z}/{x}/{y}.png',
          }),
        }),
      ],
      view: new ol.View({
        center: ol.proj.fromLonLat([points[0].lng, points[0].lat]),
        zoom: 12,
      }),
    });

    const lineFeature = new ol.Feature({
      geometry: new ol.geom.LineString(
        points.map((p) => ol.proj.fromLonLat([p.lng, p.lat]))
      ),
    });

    const vectorSource = new ol.source.Vector({
      features: [lineFeature],
    });

    const vectorLayer = new ol.layer.Vector({
      source: vectorSource,
      style: new ol.style.Style({
        stroke: new ol.style.Stroke({
          color: 'blue',
          width: 3,
        }),
      }),
    });

    map.addLayer(vectorLayer);
  });
});
