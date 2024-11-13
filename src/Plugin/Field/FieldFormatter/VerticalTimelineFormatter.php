<?php

namespace Drupal\radicati_component_timeline\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'vertical_timeline_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "vertical_timeline_formatter",
 *   label = @Translation("Vertical Timeline Formatter"),
 *   field_types = {
 *     "entity_reference_revisions"
 *   }
 * )
 */
class VerticalTimelineFormatter extends FormatterBase {
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return parent::isApplicable($field_definition) && strpos($field_definition->id(), "timeline");
  }


  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [
      '#theme' => 'radicati_vertical_timeline',
      '#items' => [],
    ];
    $entities = $items->referencedEntities();
    $current_group = null;

    // For rendering the paragraph entities.
    $view_mode = 'default';
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('paragraph');
    $storage = \Drupal::entityTypeManager()->getStorage('paragraph');

    foreach ($entities as $key => $item) {
      $label = $item->field_timeline_item_label->value;

      if($current_group == null) {
        $current_group = [
          '#theme' => 'radicati_vertical_timeline_group',
          '#label' => $label,
          '#items' => [],
        ];
      }

      $current_group['#items'][] = $view_builder->view($item, $view_mode);

      if(array_key_exists($key, $entities) && !empty($entities[$key+1]) && $label != $entities[$key+1]->field_timeline_item_label->value) {
        $elements['#items'][] = $current_group;
        $current_group = null;
      }
    }
    
    return $elements;
  }
}
