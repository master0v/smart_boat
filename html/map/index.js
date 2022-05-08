
// https://openlayers.org/en/latest/doc/

function iterate(item, index) {
  //console.log(points_matrix[index][0]);
  opacity = .25+(index+1)/4000 // number of datapoints returned by the query * 4
  // will make it between .25 and .5
  
  // gradually fade from yellow to white
  //var color = 255-Math.floor(index / 3.91);
  if ( points_matrix[index][2] && points_matrix[index][1] ) {
  
    // convert coordinates from Lon, Lat
    var coordinates = ol.proj.fromLonLat([points_matrix[index][2], points_matrix[index][1]]);
  
    // construct an icon with these coordinates
    var iconFeature = new ol.Feature({
      geometry: new ol.geom.Point(coordinates),
      name: points_matrix[index][0]
    });
  
    // construct icon's style (point)
    var fill = new ol.style.Fill({
      color: "rgba(8,56,92," + opacity + ")"
    });
    var stroke = new ol.style.Stroke({
      color: '#3399CC',
      width: 0.001
    });
    iconFeature.setStyle(new ol.style.Style({
        image: new ol.style.Circle({
          fill: fill,
          stroke: stroke,
          radius: 1.5
        }),
        fill: fill,
        stroke: stroke
    }));  
  
    // add icon to the vector
    vectorSource.addFeature(iconFeature);
  
    // if it is not the first line
    if (start_point != null) {
      // construct a line
      var points = [start_point, coordinates];
      var featureLine = new ol.Feature({
          geometry: new ol.geom.LineString(points)
      });
      // add it to Vector
      vectorSource.addFeature(featureLine);
    };
    // start the line at the previous point's coordinates
    start_point = coordinates;
  } else {
    //console.log(points_matrix[index][2], points_matrix[index][1]);
  };
};

// === BEGIN ===

var center = ol.proj.fromLonLat([points_matrix[0][2], points_matrix[0][1]]);
//console.log(center);

// construct the icon vector
var vectorSource = new ol.source.Vector();

var start_point = null;
// add all the points first
// .reverse().
points_matrix.reverse().forEach(iterate);

// add ship last so it stays on top of all the points
var iconFeature = new ol.Feature({
  geometry: new ol.geom.Point(center),
  name: 'eXpeditious'
});

// set icon style
var iconStyle = new ol.style.Style({
  image: new ol.style.Icon({
    anchor: [0.35, 21.5],
    anchorXUnits: 'fraction',
    anchorYUnits: 'pixels',
    src: 'exp.png'
  })
});

// apply style to the icon
iconFeature.setStyle(iconStyle);
// insert ship into the vector
vectorSource.addFeature(iconFeature);

// Create the "icons layer"
var vectorLayer = new ol.layer.Vector({
  source: vectorSource
});


// Create the "land" layer
var openCycleMapLayer = new ol.layer.Tile({
  source: new ol.source.OSM({
    attributions: [
      '<a href="https://www.opencyclemap.org/">OpenCycleMap</a>',
      ol.source.ATTRIBUTION
    ],
    url: 'https://{a-c}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png' +
        '?apikey=aa612cc5aafc47f8a3a29bfc0e48b4ef'
  })
});

// Create the "sea" layer
var openSeaMapLayer = new ol.layer.Tile({
  source: new ol.source.OSM({
    attributions: [
      '<a href="http://www.openseamap.org/">OpenSeaMap</a>',
      ol.source.ATTRIBUTION
    ],
    opaque: false,
    url: 'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png'
  })
});

// create the map object
// =====================================
var map = new ol.Map({
  layers: [
    openCycleMapLayer,
    openSeaMapLayer,
    vectorLayer
  ],
  target: document.getElementById('map'),
  view: new ol.View({
    maxZoom: 20,
    center: center,
    zoom: 18
  })
});
// =====================================

var element = document.getElementById('popup');

var popup = new ol.Overlay({
  element: element,
  positioning: 'bottom-center',
  stopEvent: false,
  offset: [0, -50]
});
map.addOverlay(popup);

// display popup on click
map.on('click', function(evt) {
  var feature = map.forEachFeatureAtPixel(evt.pixel,
    function(feature) {
      return feature;
    });
  if (feature) {
    var coordinates = feature.getGeometry().getCoordinates();
    popup.setPosition(coordinates);
    $(element).popover({
      placement: 'top',
      html: true,
      content: feature.get('name')
    });
    $(element).popover('show');
  } else {
    $(element).popover('destroy');
  }
});

// change mouse cursor when over marker
map.on('pointermove', function(e) {
  if (e.dragging) {
    $(element).popover('destroy');
    return;
  }
  var pixel = map.getEventPixel(e.originalEvent);
  var hit = map.hasFeatureAtPixel(pixel);
  map.getTarget().style.cursor = hit ? 'pointer' : '';
});
