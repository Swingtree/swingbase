<?php
/**
 * Created by PhpStorm.
 * User: andry
 * Date: 13-04-18
 * Time: 11:32
 */

namespace Drupal\swt_mapbox\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * @FieldFormatter(
 *   id = "swt_mapbox_full",
 *   module = "swt_mapbox",
 *   label = @Translation("Mapbox full"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */

class MapboxFullFormatter extends FormatterBase {

  /**
   * Builds a renderable array for a field value.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field values to be rendered.
   * @param string $langcode
   *   The language that should be used to render the field.
   *
   * @return array
   *   A renderable array for $items, as an array of child elements keyed by
   *   consecutive numeric indexes starting from 0.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $entityType = $this->fieldDefinition->getTargetEntityTypeId();
      $dataTrail = null;
      $nid = null;
      if($entityType == "node") {
        $bundle = $this->fieldDefinition->getTargetBundle();
        $node = $items->getParent()->getValue();
        $nid = $node->get("nid")->getValue()[0]['value'];

        if($bundle == "hike") {
          $val = $node->get("field_trail_data")->getValue()[0]['value'];

          $dataTrail = \Drupal::service('swt_mapbox.data')->gpxToData($val);
        }
      }

      $lon = $item->getValue()["lon"];
      $lat = $item->getValue()["lat"];

      $element[$delta] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['swt-mapbox'],
          'style' => ['height:600px'],
          'data-mapbox' => $nid,
        ],
//        'content' => [
//          '#type' => 'container',
//          '#attributes' => [
//            'class' => ['swt-mapbox__filter-container'],
//          ],
//        ],
        '#attached' => [
          'library' => [
            'swt_mapbox/swt_mapbox.core',
          ],
//          'drupalSettings' => [
//            'trail_test' => $dataTrail,
//          ],
        ],
      ];

      if($nid !== null) {
        $element[$delta]['#attached']['drupalSettings'] = [
          'swt_mapbox' => [
            'nodes' => [
              $nid => [
                'trail_data' => $dataTrail,
                'lon' => floatval($lon),
                'lat' => floatval($lat),
              ]
            ],
          ]
        ];
      }
    }

    return $element;
  }
}