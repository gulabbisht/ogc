<?php

namespace Drupal\open_graph_comments;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

class OGCFetchTags {

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
   * OGCFetchTags constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The http client.
   * @param  \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(ClientInterface $client, ModuleHandlerInterface $module_handler) {
    $this->httpClient = $client;
    $this->moduleHandler = $module_handler;
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

    // Allows user to alter tags before sending.
    $this->moduleHandler->alter('open_graph_comments_tags', $tags, $url);

    return $tags;
  }

}
