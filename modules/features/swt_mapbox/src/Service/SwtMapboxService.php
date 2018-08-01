<?php

namespace Drupal\swt_mapbox\Service;

class SwtMapboxService {
  private function object2array($object) { return @json_decode(@json_encode($object),1); }
  public function gpxToData($dataGpx) {
    $data2 = [];
    if(!empty($dataGpx) && substr($dataGpx, 0, 1) == "<") {
      $data = simplexml_load_string($dataGpx);
      $arr =  $this->object2array($data);
      $trkseg = NULL;
      if(!empty($arr["trk"])) {
        if(!empty($arr["trk"]["trkseg"])) {
          $trkseg = $arr["trk"]["trkseg"];
        }
        else {
          $trkseg = $arr["trk"][0]["trkseg"];
        }

        if(!empty($trkseg) && !empty($trkseg["trkpt"])) {
          $pts = $trkseg["trkpt"];
          //        $pts = $arr["trk"]["trkseg"]["trkpt"];

          foreach($pts as $d) {
            $data2[] = $d["@attributes"];
          }
        }
      }
    }

    return $data2;
  }
}