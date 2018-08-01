<?php

namespace Drupal\swt_mapbox\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\geofield\Plugin\Field\FieldType\GeofieldItem;


/**
 * @FieldFormatter(
 *   id = "swt_mapbox_static_image",
 *   module = "swt_mapbox",
 *   label = @Translation("Mapbox Static Image"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */

class MapboxStaticImageFormatter extends FormatterBase {

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

    $parent_entity = $this->fieldDefinition->getTargetEntityTypeId();
    $parent_bundle = $this->fieldDefinition->getTargetBundle();

    foreach ($items as $delta => $item) {
      $map_request = $this->buildRequest($item);
      $element[$delta] = [
        '#theme' => 'swt_mapbox_formatter__static_image',
        '#image_url' => $this->buildUrl($map_request),
        '#parent_entity_type' => $parent_entity,
        '#parent_entity_bundle' => $parent_bundle,
        '#attributes' => [
          'class' => ['swt-bob'],
        ],
        '#wrapper_attributes' => [
          'class' => ['test-wrapper'],
        ]
      ];
    }

    return $element;
  }

  private function buildRequest( GeofieldItem $item){
    # with a custom marker overlay
    $request = str_replace(".","/",$this->getSetting('mapbox_style')).'/static/';

    if( $this->getSetting('marker') !== 'none'){
      $request .= $this->getSetting('marker').'-'.$this->getSetting('marker_label').'+'.$this->getSetting('marker_color');
      $request .='('.$item->get('lon')->getCastedValue().','.$item->get('lat')->getCastedValue().')/';
    }
    $request .= $item->get('lon')->getCastedValue().','.$item->get('lat')->getCastedValue().','.$this->getSetting('zoom').',0/';
    $request .= $this->getSetting('width').'x'.$this->getSetting('height').'@2x.png';

    return $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'mapbox_style' => 'mapbox.streets-v10',
        'height' => 400,
        'width' => 400,
        'zoom' => 10,
        'minPossibleZoom' => 0,
        'maxPossibleZoom' => 16,
        'marker' => 'pin-s',
        'marker_label' => '',
        'marker_color' => 'f3343d',
        'cache' => '',
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['mapbox_style'] = [
      '#title' => $this->t('Mapbox Style'),
      '#type' => 'select',
      '#options' => swt_mapbox_style_options(),
      '#default_value' => $this->getSetting('mapbox_style'),
      '#required' => TRUE,
    ];
    $zoom_options = [];
    for ($i = $this->getSetting('minPossibleZoom'); $i <= $this->getSetting('maxPossibleZoom'); $i++) {
      $zoom_options[$i] = $i;
    }
    $elements['zoom'] = [
      '#title' => $this->t('Zoom'),
      '#type' => 'select',
      '#options' => $zoom_options,
      '#default_value' => $this->getSetting('zoom'),
      '#required' => TRUE,
    ];
    $elements['width'] = [
      '#title' => $this->t('Map Width'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('width'),
      '#field_suffix' => $this->t('px'),
    ];
    $elements['height'] = [
      '#title' => $this->t('Map Height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('height'),
      '#field_suffix' => $this->t('px'),
    ];


    $elements['marker'] = [
      '#title' => $this->t('Marker'),
      '#description' => $this->t('Select a marker.'),
      '#type' => 'select',
      '#options' => swt_mapbox_marker_options(),
      '#default_value' => $this->getSetting('marker'),
    ];

    $elements['marker_color'] = [
      '#title' => $this->t('Marker Color'),
      '#description' => $this->t('Select a marker color. Only works for default markers'),
      '#type' => 'select',
      '#options' => swt_mapbox_marker_color_options(),
      '#default_value' => $this->getSetting('marker_color'),
    ];

    $elements['marker_label'] = [
      '#title' => $this->t('Marker Label'),
      '#description' => $this->t('Define a marker label. Only works for default markers'),
      '#type' => 'select',
      '#options' => swt_mapbox_marker_label_options(),
      '#default_value' => $this->getSetting('marker_label'),
    ];

    $elements['cache'] = [
      '#title' => $this->t('Cache'),
      '#description' => $this->t('Define a cache folder.'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('cache'),
      '#maxlength' => '128',
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Style @style', ['@style' => $this->getSetting('mapbox_style')]);
    $summary[] = $this->t('@width px X @height px X zoom @zoom', ['@width' => $this->getSetting('width'),'@height' => $this->getSetting('height'),'@zoom' => $this->getSetting('zoom')]);
    $summary[] = $this->t('Cache: @cache', ['@cache' => $this->getSetting('cache')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function buildUri($uri) {
    return "public://mapbox/cache/static-map/".$uri;
  }

  /**
   * {@inheritdoc}
   */
  public function buildUrl($path) {
    $uri = $this->buildUri($path);

    return file_create_url($uri);
  }
}