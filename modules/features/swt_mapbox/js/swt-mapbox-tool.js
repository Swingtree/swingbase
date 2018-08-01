
mapboxgl.accessToken = 'pk.eyJ1Ijoic3dpbmd0cmVlLXN0dWRpbyIsImEiOiJjamR5ZXI1MDYxMzF0MnZvaHAxeGhtcWU2In0.xpY4V8dZ3Jj6T6Do-Fl0Qg';

// alert(drupalSettings.trail_test);

function fakeConfig() {
  return {
    zoom: 13,
    // lon: 3.43,
    // lat: 50.13,
    lon: 5.067895,
    lat: 49.203994,
    bounds: [[-10, 39], [16, 56]],
  }
}

(function ($) {
  Drupal.behaviors.swtMapbox = {
    attach: function(context, settings) {
      $('.swt-mapbox', context).once('swtMapbox').each(function () {
        var $elem = $(this);
        var node = null;
        var nid;
        if(drupalSettings.swt_mapbox) {
          if(drupalSettings.swt_mapbox.nodes) {
            nid = $elem.attr("data-mapbox");
            node = drupalSettings.swt_mapbox.nodes[nid];
          }
        }

        var config = fakeConfig();
        if(node) {
          config.lon = node.lon;
          config.lat = node.lat;
        }

        var map = swtMapbox.buildMap(config, $elem[0]);

        if(node && node.trail_data) {
          map.drawGpx(node.trail_data);
          var bounds = swtMapbox.gpxToBounds(node.trail_data, 0.0003);
          map.fitBounds(bounds);

          if(window.peb_mainmap) {
            var hike = getId(window.peb_mainmap.hikes, nid);
            var pois = getPoisFRomIds(window.peb_mainmap.pois, hike.pois);

            map.setSubject(nid, bounds, true);
            map.drawPois(pois, undefined, 1, 10);
            map.drawPois([hike], "pinpoint--hike.svg");

            if(hike.poisBonus) {
              var poisBonus = getPoisFRomIds(window.peb_mainmap.pois, hike.poisBonus);
              map.drawPois(poisBonus, undefined, 0.7, 10);
            }
          }
        }
        map.updateZoomVisibilityWhenLoaded();
      });
    }
  };

  function getPoisFRomIds(pois, ids) {
    var list = [];
    var i;for(i=0; i< ids.length; i++) {
      list.push(getId(pois, ids[i]));
    }

    return list;
  }
  function getId(list, id) {
    var i;for(i=0; i< list.length; i++) {
      if(list[i].id === id) {
        return list[i];
      }
    }
    return null;
  }

}(jQuery));

/*window.pebData = (function() {
  this.getId = function(list, id) {
    var i;for(i=0; i< list.length; i++) {
      if(list[i].id === id) {
        return list[i];
      }
    }
    return null;
  }
}());*/


window.swtMapbox = (function() {
  this._maps = {};
  var _layers = [];
  var pathToPicto = "/themes/custom/gavias_tico/images/maps/";

  this.buildMap = function (config, container, data, id) {
    var zoom = config.zoom;
    var lon = config.lon;
    var lat = config.lat;
    var bounds = config.bounds;
    // var lon = 3.43;
    // var lat = 50.13;
    // var lon = -116.078968;
    // var lat = 43.802190;
    if(data !== undefined && data.rect !== undefined) {
      if(data.rect.zoom !== undefined) zoom = data.rect.zoom;
      if(data.rect.lon !== undefined) lon = data.rect.lon;
      if(data.rect.lat !== undefined) lat = data.rect.lat;
    }

    var props = {
      container: container,
      // style: drupalSettings.swt_mapbox.map_style,
      // style: 'mapbox://styles/swingtree-studio/cjh81iwza1ojj2smwhrf9x9v0',
      style: 'mapbox://styles/swingtree-studio/cjh81iwza1ojj2smwhrf9x9v0?no-cache='+Math.floor(Math.random()*10000),
      attributionControl: false,
      zoom: zoom,
      center: [lon, lat],
      maxBounds: bounds,
    };
    // if(bounds) {
    //   props.maxBounds = bounds;
    // }
    var mapbox = new mapboxgl.Map(props);

    mapbox.on('load', function () {
    });

    var map = new Map(mapbox);
    if(id !== undefined) {
      _maps[id] = map;
    }

    // mapbox.doubleClickZoom.disable();

    return map;
  };

  function dataToFeature(transformater, list, icon, weight) {
    if(icon === undefined) {
      icon = "pinpoint--default.svg";
    }

    var features = [];
    var i=0;
    for(i=0; i < list.length; i++) {
      var poi = list[i];
      features[i] = transformater(list[i], icon, weight);
    }
    return features;
  }

  function getId(list, id) {
    var i;for(i=0; i< list.length; i++) {
      if(list[i].id === id) {
        return list[i];
      }
    }
    return null;
  }

  function poiToFeature(poi, icon, weight) {
    if(icon === undefined) {
      // icon = "marker";
      icon = "pinpoint--default.svg";
    }
    if(weight === undefined) {
      weight = 1;
    }

    var themeStyle = "theme--default";
    var themes = window.peb_mainmap.themes;
    var theme = getId(themes, poi.theme);
    if(theme) {
      themeStyle = theme.style;
    }
    // var themeStyle = "theme--field-of-ruins";


    return {
      "type": "Feature",
      "geometry": {
        "type": "Point",
        "coordinates": [poi.lon, poi.lat]
      },
      "properties": {
        "id": poi.id,
        "title": poi.title,
        "page": poi.page,
        "type": poi.type,
        "icon": icon,
        "iconSize": [60, 60],
        "weight": weight,
        "themeStyle": themeStyle,
        "data": poi,
      }
    };
  }
  this.gpxToBounds = function(data, border) {
    var bounds;
    if(data.length > 0) {
      bounds = [[Number(data[0].lon), Number(data[0].lat)], [Number(data[0].lon), Number(data[0].lat)]];
      var i;for(i=0; i < data.length; i++) {
        var d = data[i];
        if(bounds[0][0] > Number(d.lon)) { bounds[0][0] = Number(d.lon); }
        if(bounds[1][0] < Number(d.lon)) { bounds[1][0] = Number(d.lon); }
        if(bounds[0][1] > Number(d.lat)) { bounds[0][1] = Number(d.lat); }
        if(bounds[1][1] < Number(d.lat)) { bounds[1][1] = Number(d.lat); }
      }

      bounds[0][0] -= border;
      bounds[0][1] -= border;
      bounds[1][0] += border;
      bounds[1][1] += border;
    }
    return bounds;
  };

  function gpxToGeometry(data) {
    var coords = [];
    var i;for(i=0; i < data.length; i++) {
      var d = data[i];
      coords.push([d.lon, d.lat]);
    }

    return {
      "type": "LineString",
      "coordinates": coords,
    };
  }

  this.Map = function(mapbox) {

    var _self = this;
    var _langcode = "en";
    var _layerIndex = 0;
    var _pois = [];
    // var mapbox = mapbox;

    function setLayerVisible(layer, bool) {
      if(layer.visible === bool) return;
      layer.visible = bool;

      var mode = "hidden";
      if(bool) {
        mode = "visible";
      }
      var i;for(i=0; i<layer.markers.length; i++) {
        layer.markers[i].style["visibility"] = mode;
      }
    }

    this.updateZoomVisibilityWhenLoaded = function() {
      mapbox.on('load', function () {
        _self.updateZoomVisibility();
      });
    };
    this.updateZoomVisibility = function() {
      var zoom = mapbox.getZoom();
      if(_self._lastZoom === undefined || zoom !== _self._lastZoom) {
        var i;for(i=0; i<_self._layers.length; i++) {
          if(_self._layers[i].minzoom !== undefined && zoom < _self._layers[i].minzoom) {
            setLayerVisible(_self._layers[i], false);
          }
          else if(_self._layers[i].maxzoom !== undefined && zoom > _self._layers[i].maxzoom) {
            setLayerVisible(_self._layers[i], false);
          }
          else {
            setLayerVisible(_self._layers[i], true);
          }
        }
        // console.log("mvt "+zoom);
      }
      _self._lastZoom = zoom;
    };


    var _filterTheme = "-1";
    var _filterTag = "-1";
    this.setFilterTheme = function(id) {
      _filterTheme = id;
      _self.updateFilter();
    };
    this.setFilterTag = function(id) {
      _filterTag = id;
      _self.updateFilter();
    };
    this.updateFilter = function() {
      var i; for(i=0; i < _pois.length; i++) {
        var testTheme = _filterTheme === "-1";
        var testTag = _filterTag === "-1";
        if(_pois[i].data.type === "poi") {
          if(_pois[i].data.theme === _filterTheme) {
            testTheme = true;
          }
          if(_pois[i].data.tags.indexOf(_filterTag) !== -1) {
            testTag = true;
          }
        }
        else {
          var j; for(j=0; j<_pois[i].data.pois.length; j++) {
            var poi = getId(window.peb_mainmap.pois, _pois[i].data.pois[j]);
            if(poi.theme === _filterTheme) {
              testTheme = true;
            }
            if(poi.tags.indexOf(_filterTag) !== -1) {
              testTag = true;
            }

            if(testTheme && testTag) {
              break;
            }
            else {
              testTheme = _filterTheme === "-1";
              testTag = _filterTag === "-1";
            }
          }
          for(j=0; j<_pois[i].data.poisBonus.length; j++) {
            var poi = getId(window.peb_mainmap.pois, _pois[i].data.poisBonus[j]);
            if(poi.theme === _filterTheme) {
              testTheme = true;
            }
            if(poi.tags.indexOf(_filterTag) !== -1) {
              testTag = true;
            }

            if(testTheme && testTag) {
              break;
            }
            else {
              testTheme = _filterTheme === "-1";
              testTag = _filterTag === "-1";
            }
          }
        }

        if(testTheme && testTag) {
          _pois[i].el.style["opacity"] = 1;
          _pois[i].el.style["color"] = null;
        }
        else {
          _pois[i].el.style["opacity"] = 0.5;
          _pois[i].el.style["color"] = "#888888";
        }
      }
    };

    mapbox.addControl(new mapboxgl.NavigationControl());
    // mapbox.on('moveend', function (e) { console.log("mvt"); });
    mapbox.on('move', function (e) {
      _self.updateZoomVisibility();
    });

    this.fitBounds = function(bounds) {
      mapbox.fitBounds(bounds);
    };
    this.fitToSubject = function() {
      _self.fitBounds(_self.subjectBounds);
    };

    this.getGroups = function(pois, hikes) {
      var groups = [];
      var i;
      for(i=0; i<hikes.length;i++) {
        var group = {
          id: hikes[i].id,
          type: "group",
          title: "",
          lat: hikes[i].lat,
          lon: hikes[i].lon,
          bounds: hikes[i].bounds
        };
        groups.push(group);
      }
      return groups;
    };
    this.splitDataPois = function(pois, hikes) {
      var border = 0.002;
      var poisOfHike = [];
      var poisOfFree = [];
      var i; for(i=0; i<hikes.length;i++) {
        var i2;
        var bounds = [
            [
              Number(hikes[i].lon), Number(hikes[i].lat)
            ],
            [
              Number(hikes[i].lon), Number(hikes[i].lat)
            ]
        ];
        hikes[i].bounds = bounds;

        for(i2=0; i2<hikes[i].pois.length;i2++) {
          var poi = getId(pois, hikes[i].pois[i2]);
          if(poi.hikes === undefined) {
            poi.hikes = [];
          }
          poi.hikes.push(hikes[i]);
          if(poi.lon < bounds[0][0]) { bounds[0][0] = poi.lon; }
          else if(poi.lon > bounds[1][0]) { bounds[1][0] = poi.lon; }
          if(poi.lat < bounds[0][1]) { bounds[0][1] = poi.lat; }
          else if(poi.lat > bounds[1][1]) { bounds[1][1] = poi.lat; }
        }
        for(i2=0; i2<hikes[i].poisBonus.length;i2++) {
          var poi = getId(pois, hikes[i].poisBonus[i2]);
          if(poi.hikes === undefined) {
            poi.hikes = [];
          }
          poi.hikes.push(hikes[i]);
          if(poi.lon < bounds[0][0]) { bounds[0][0] = poi.lon; }
          else if(poi.lon > bounds[1][0]) { bounds[1][0] = poi.lon; }
          if(poi.lat < bounds[0][1]) { bounds[0][1] = poi.lat; }
          else if(poi.lat > bounds[1][1]) { bounds[1][1] = poi.lat; }
        }
        // for(i2=0; i2<hikes[i].poisBonus.length;i2++) {
        //   hikes[i].poisBonus.hikes.push(hikes[i]);
        // }
        // console.log(bounds);
        bounds[0][0] = Number(bounds[0][0]) - border;
        bounds[0][1] = Number(bounds[0][1]) - border;
        bounds[1][0] = Number(bounds[1][0]) + border;
        bounds[1][1] = Number(bounds[1][1]) + border;
      }

      for(i=0;i<pois.length;i++) {
        if(pois[i].hikes.length === 0) {
          poisOfFree.push(pois[i]);
        }
        else {
          poisOfHike.push(pois[i]);
        }
      }
      return {free:poisOfFree, hiked:poisOfHike};
    };

    this.subject = null;
    this.subjectBounds = null;
    this.subjectFix = false;
    this.setSubject = function(id, bounds, isFix) {
      _self.subject = id;
      _self.subjectBounds = bounds;
      if(isFix === true) {
        _self.subjectFix = true;
      }
    }
    this.drawPois = function (list, icon, weight, minzoom, maxzoom) {
      if(weight === undefined) weight = 1;

      _layerIndex++;
      var layerId = "poi-"+_layerIndex;
      mapbox.on('load', function () {
        var features = dataToFeature(poiToFeature, list, icon, weight);

        var layer = {
          "id": layerId,
          "type": "symbol",
          "minzoom": 14,
          "maxzoom": 24,
          "source": {
            "type": "geojson",
            "data": {
              "type": "FeatureCollection",
              "features": features
            }
          },
          "layout": {
            "icon-image": "{icon}-15",
            "text-field": "{title}",
            "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
            "text-offset": [0, 0.6],
            "text-anchor": "top"
          }
        };
        // mapbox.addLayer(layer);
        _self.initMarker(features, minzoom, maxzoom);
        // mapbox.setLayoutProperty('country-label-lg', 'text-field', ['get', 'name_' + _langcode]);
      });
      _layers.push(layerId);
    };

    this.currentPopup = null;
    this.currentPopupID = null;
    this.currentPopupEl = null;
    this.hasFirstDown = false;

    function clearPopups() {
      /*if(_self.currentPopupEl) {
        _self.currentPopupEl.style['z-index'] = 0;
      }*/
      _self.currentPopupID = null;
      _self.currentPopupEl = null;

      if(_self.currentPopup) {
        var hold = _self.currentPopup;
        _self.currentPopup = null;
        hold.remove();
      }
    }

    this._layers = [];
    this._lastZoom = undefined;
    // var pictoName ="pinpoint--default.svg";
    this.initMarker = function(features, minzoom, maxzoom) {
      var markers = [];

      function showPoiTitle(id, marker, el) {
        clearPopups();
        if(marker.properties.title === "") {
          return;
        }

        var content = document.createElement('div');
        content.innerHTML = '<h6>'+marker.properties.title+'</h6>';

        // _self.currentPopup = new mapboxgl.Popup({closeOnClick: false})
        _self.currentPopup = new mapboxgl.Popup()
            .setLngLat(marker.geometry.coordinates)
            .setDOMContent(content)
            // .setHTML('<h6>'+marker.properties.title+'</h6>')
            .addTo(mapbox);

        // content.parentNode.parentNode.className += ' no-but-close';
        content.parentNode.parentNode.className += ' no-event';
      }
      function showPoiTeaser(id, marker, el) {
        clearPopups();
        _self.currentPopupID = id;
        _self.currentPopupEl = el;
        _self.currentPopup = new mapboxgl.Popup({closeOnClick: false})
            .setLngLat(marker.geometry.coordinates)
            .setHTML('<h6>'+marker.properties.title+' ...</h6>')
            .addTo(mapbox);
        _self.currentPopup.on('close', function(e) {
          clearPopups();
        });
        jQuery.ajax({url: '/ajax/poi/teaser/'+id, success: function(result){
          // var $teaser = jQuery(".swt-mapbox__selected-teaser");
          if(_self.currentPopup && _self.currentPopupID === id) {
            _self.currentPopup.setHTML(result.html);
          }
        }});
      }
      features.forEach(function(marker) {
        var path = pathToPicto + marker.properties.icon;

        var factSize = marker.properties.weight;

        // create a DOM element for the marker
        var el = document.createElement('div');
        // el.className = 'mapbox-marker theme--monuments';
        el.className = 'mapbox-marker '+marker.properties.themeStyle;

        // var iconId = marker.properties.type === 'hike' ? 'pinpoint--hike' : 'pinpoint';
        var iconId = 'pinpoint';
        if(marker.properties.type === 'hike') {
          iconId = 'pinpoint--hike';
        }
        else if(marker.properties.type === 'group') {
          iconId = 'pinpoint--group';
        }
        el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 48"><use href="#'+iconId+'" x="0" y="0" width="24" height="48"/></svg>';

        // el.style.backgroundImage = 'url('+path+')';
        el.style.width = factSize*60 + 'px';
        el.style.height = factSize*120 + 'px';


        el.addEventListener('mouseover', function(e) {
          //el.style['z-index'] = 2;
          if(!_self.currentPopupID) {
            showPoiTitle(marker.properties.id, marker, el);
          }
        });
        el.addEventListener('mouseout', function(e) {
          if(marker.properties.id !== _self.currentPopupID) {
            // el.style['z-index'] = 0;
          }
          if(!_self.currentPopupID) {
            clearPopups();
          }
        });
        el.addEventListener('mousedown', function(e) {
          if(_self.hasFirstDown) {
            e.preventDefault();
            e.stopPropagation();
          }
        });
        el.addEventListener('click', function(e) {
          e.preventDefault();
        });
        el.addEventListener('mouseup', function(e) {
          e.preventDefault();
          if(e.which===2){
            // window.open('/node/'+marker.properties.id, "_blank");
            window.open(marker.properties.page, "_blank");
          }
          else if(e.which===1) {
            if(_self.subjectFix && _self.subject !== null && _self.subject === marker.properties.id) {
              clearPopups();
              setTimeout(_self.fitToSubject, 100);
            }
            else if(marker.properties.type === 'group') {
              clearPopups();
              _self.setSubject(marker.properties.id, marker.properties.data.bounds);
              setTimeout(_self.fitToSubject, 100);
            }
            else {
              if(marker.properties.id === _self.currentPopupID) {
                clearPopups();
              }
              else {
                showPoiTeaser(marker.properties.id, marker, el);

                mapbox.flyTo({
                  center: [
                    marker.properties.data.lon,
                    Number(marker.properties.data.lat) + 0.0005
                  ],
                  zoom: 15
                });
              }
            }
          }
        });

        markers.push(el);
        _pois.push({el:el, data:marker.properties.data});

        // add marker to map
        new mapboxgl.Marker(el)
            .setLngLat(marker.geometry.coordinates)
            .addTo(mapbox);
      });

      var layer = {};
      layer.markers = markers;
      layer.minzoom = minzoom;
      layer.maxzoom = maxzoom;
      _self._layers.push(layer);

      return layer;
    };


    this.drawGpx = function (dataGpx) {
      _layerIndex++;
      var layerId = "gpx-"+_layerIndex;

      mapbox.on('load', function () {
        var geometry = gpxToGeometry(dataGpx);
        var layer = {
          "id": layerId,
          "type": "line",
          "source": {
            "type": "geojson",
            "data": {
              "type": "Feature",
              "geometry": geometry,
              "properties": {},
            }
          },
          "layout": {
            // 'visibility': 'visible',
            "line-join": "round",
            "line-cap": "round"
          },
          "paint": {
            "line-color": "#497f8a",
            // "line-opacity": 0.5,
            "line-width": 4
          }
        };
        mapbox.addLayer(layer);
        // mapbox.setLayoutProperty('country-label-lg', 'text-field', ['get', 'name_' + _langcode]);
      });
      _layers.push(layerId);
    };

    mapbox.on('mousedown', function (e) {
      clearPopups();
      _self.hasFirstDown = true;
    });

    this.setLanguage = function(langcode) {
      _langcode = langcode;
      mapbox.on('load', function () {
        var layers = mapbox.getStyle().layers;
        var i;for(i=0; i < layers.length; i++) {
          // console.log(layers[i]);
          if(layers[i].layout && layers[i].layout['text-field']) {
            mapbox.setLayoutProperty(layers[i].id, 'text-field', ['get', 'name_' + _langcode]);
          }
        }
      });
    };


    return this;
  };

  return this;
}());





// style: 'mapbox://styles/mapbox/streets-v9'
// style: 'mapbox://styles/mapbox/outdoors-v9'
// style: 'mapbox://styles/mapbox/dark-v9'
// style: 'mapbox://styles/mapbox/light-v9'
// style: 'mapbox://styles/swingtree-studio/cjfy2am0h63h52sqc8bfojh67'
// style: 'mapbox://styles/swingtree-studio/cjfy4m00h6vmu2rohgmjbtrnn',
// style: drupalSettings.mapStyleTest,
