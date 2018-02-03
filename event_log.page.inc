<?php

/**
 * @file
 * Contains event_log.page.inc.
 *
 * Page callback for Event log entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Event log templates.
 *
 * Default template: event_log.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_event_log(array &$variables) {
  // Fetch EventLog Entity Object.
  $event_log = $variables['elements']['#event_log'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
