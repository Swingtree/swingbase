<?php
/**
 * Created by PhpStorm.
 * User: andry
 * Date: 11-04-18
 * Time: 16:22
 */

namespace Drupal\swt_field_duration\Plugin\Field\FieldFormatter;


use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * @FieldFormatter(
 *   id = "swt_field_duration_text",
 *   module = "swt_field_duration",
 *   label = @Translation("Duration text"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */

class TextDurationFormatter extends FormatterBase {

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
      $value = $item->value;

      $d = floor($value / 1440 );
      $h = floor(($value%1440) / 60 );
      $m = $value % 60;

      if($d > 0) {
        $str = $this->t(":day day :hourh",[":day"=>$d, ":hour"=>$h],['context'=> "TextDurationFormatter"]);
      }
      else if($h > 0) {
        $str = $this->t(":hourh:minute",[":hour"=>$h, ":minute"=>$m],['context'=> "TextDurationFormatter"]);
      }
      else {
        $str = $this->t(":minutem",[":minute"=>$m],['context'=> "TextDurationFormatter"]);
      }

      $element[$delta] = [
        '#type' => 'processed_text',
        '#text' => $str,
//        '#theme' => 'geolocation_latlng_formatter',
//        '#hour' => 2,
//        '#minute' => $item->,
      ];
    }

    return $element;
  }
}