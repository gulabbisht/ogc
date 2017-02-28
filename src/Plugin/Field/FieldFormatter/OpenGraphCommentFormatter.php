<?php

namespace Drupal\open_graph_comments\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\BasicStringFormatter;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\open_graph_comments\OGCFetchTags;
use Drupal\Component\Utility\UrlHelper;

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
    $elements = [];

    foreach ($items as $delta => $item) {
      $value = $item->value;

      // Match and filter the url from the comment.
      preg_match("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", $value, $matches);

      $meta_data = [];

      if (isset($matches[0])) {
        // Get OG tags from the url.
        $og_tags = $this->ogc->getTags($matches[0]);
        // Prepare meta data array for output.
        $meta_data = $this->prepareMetaData($og_tags);
      }

      $elements[$delta] = [
        '#theme' => 'open_graph_comments_template',
        '#value' => $value,
        '#meta_data' => $meta_data,
      ];
    }

    return $elements;
  }

  /**
   * Prepare meta data array.
   *
   * @param array $og_tags
   *   OG tags.
   *
   * @return array
   *   Meta data array.
   */
  protected function prepareMetaData(array $og_tags = []) {
    $meta_data = [];

    if (!empty($og_tags)) {
      if (isset($og_tags['og:url'])) {
        $meta_data['url'] = UrlHelper::filterBadProtocol($og_tags['og:url']);
      }
      if (isset($og_tags['og:title'])) {
        $meta_data['title'] = $og_tags['og:title'];
      }
      if (isset($og_tags['og:image'])) {
        $meta_data['img'] = UrlHelper::filterBadProtocol($og_tags['og:image']);
      }
      if (isset($og_tags['og:description'])) {
        $meta_data['desc'] = $og_tags['og:description'];
      }
    }

    return $meta_data;
  }

}
