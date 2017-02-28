<?php

namespace Drupal\open_graph_comments;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class OGCFetchTags {
  use StringTranslationTrait;

  /**
   * The http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * OGCFetchTags constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The http client.
   * @param  \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger factory.
   */
  public function __construct(ClientInterface $client, ModuleHandlerInterface $module_handler, LoggerChannelFactoryInterface $logger) {
    $this->httpClient = $client;
    $this->moduleHandler = $module_handler;
    $this->logger = $logger;
  }

  /**
   * Fetch the OG tags from the URL.
   *
   * @param string $url
   *   Url from where tags will be fetched.
   *
   * @return array
   *   The array of tags.
   */
  public function getTags($url) {
    $tags = [];

    try {
      $request_data = $this->httpClient->request('GET', $url);
      // Check for positive response code.
      if ($request_data->getStatusCode() == 200) {
        $html = new \DOMDocument();
        // Suppressing warnings as HTML response might contain special characters
        // which will generate warnings.
        @$html->loadHTML($request_data->getBody());

        // Get all meta tags and loop through them.
        foreach ($html->getElementsByTagName('meta') as $meta) {
          // If the property attribute of the meta tag contains og: then save
          // content to tags variable.
          if (strpos($meta->getAttribute('property'), 'og:') !== FALSE) {
            $tags[$meta->getAttribute('property')] = $meta->getAttribute('content');
          }
        }
      }
      else {
        // If unable to fetch data from source.
        $this->logger->get('open_graph_comments')->error($this->t('Unable to fetch data from url @url', ['@url' => $url]));
      }
    }
    catch (\Exception $e) {
      // Log error if any.
      $this->logger->get('open_graph_comments')->error('Error: ' . $e->getMessage() . ' URL: ' . $url);
    }

    // Allows user to alter tags before sending.
    $this->moduleHandler->alter('open_graph_comments_tags', $tags, $url);

    return $tags;
  }

}
