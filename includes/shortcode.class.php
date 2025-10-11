<?php

namespace FSA\Plugin;


defined('ABSPATH') || exit;

class Shortcode
{
  /**
   * The single instance of the class.
   *
   * @since 1.0
   */
  protected static $_instance = null;

  /**
   * Class constructor.
   *
   * @since 1.0.0
   */
  public function __construct()
  {
    add_shortcode('fsa_map', array($this, 'wpdocs_bartag_func'));
  }

  /**
   * Main Instance.
   * 
   * @since 1.0
   */
  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  function wpdocs_bartag_func($atts)
  {
    $atts = shortcode_atts(array(
      'foo' => 'no foo',
      'baz' => 'default baz'
    ), $atts, 'bartag');

    ob_start();

    // content
    return ob_get_clean();
  }
}
