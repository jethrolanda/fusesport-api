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
    add_shortcode('wppb_map', array($this, 'wpdocs_bartag_func'));
    add_shortcode('wppb_form', array($this, 'hubspot_form'));
    add_shortcode('wppb_zipcode', array($this, 'zipcode'));
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


    // echo '<iframe
    //         width="600"
    //         height="450"
    //         style="border:0"
    //         loading="lazy"
    //         allowfullscreen
    //         referrerpolicy="no-referrer-when-downgrade"
    //         src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBwHHnwIFReFPErGaAagIu881jQPRMFS8w
    //           &q=Space+Needle,Seattle+WA">
    //       </iframe>';
    return ob_get_clean();
  }

  function hubspot_form($atts)
  {
    $atts = shortcode_atts(array(
      'foo' => 'no foo',
      'baz' => 'default baz'
    ), $atts, 'bartag');

    ob_start();

    echo 'asdasd';
    return ob_get_clean();
  }

  function zipcode($atts)
  {
    ob_start();

    echo '<span data-wp-interactive="service-area" data-wp-text="state.urlZipcode"></span>';
    return ob_get_clean();
  }
}
