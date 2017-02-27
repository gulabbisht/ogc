<?php

namespace Drupal\open_graph_comments\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\BasicStringFormatter;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\open_graph_comments\OGCFetchTags;

/**
 * @FieldFormatter(
 *   id = "open_graph_comments",
 *   label = @Translation("Open graph comments"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class OpenGraphCommentFormatter extends BasicStringFormatter implements ContainerFactoryPluginInterface {

  /**
   * The open graph comments.
   *
   * @var \Drupal\open_graph_comments\OGCFetchTags
   */
  protected $ogc;

  /**
   * Constructs a new OpenGraphCommentFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\open_graph_comments\OGCFetchTags $ogc
   *   The open graph comment service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, OGCFetchTags $ogc) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->ogc = $ogc;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('open_graph_comments.fetch_tags')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    return $elements;
  }

}
