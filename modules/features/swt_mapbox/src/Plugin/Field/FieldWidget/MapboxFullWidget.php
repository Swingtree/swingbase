<?php
/**
 * Created by PhpStorm.
 * User: andry
 * Date: 24-04-18
 * Time: 15:33
 */

namespace Drupal\swt_mapbox\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * A widget bar.
 *
 * @FieldWidget(
 *   id = "swt_mapbox_full_widget",
 *   label = @Translation("Mapbox"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */

class MapboxFullWidget extends WidgetBase implements WidgetInterface {
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $latitude = isset($items[$delta]->value) ? $items[$delta]->value : '';

    $element += [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['swt-mapbox'],
        'style' => ['height:600px']
      ],
      '#attached' => [
        'library' => [
          'swt_mapbox/swt_mapbox.core',
        ],
      ],
//      'content' => [
//        '#type' => 'geofield_latlon',
//
//      ],
      '#element_validate' => [
        [static::class, 'validate'],
      ],
    ];

    return ['value' => $element];
  }

  public static function validate($element, FormStateInterface $form_state) {
//    $form_state->setError($element, "Need to be done");
  }
}