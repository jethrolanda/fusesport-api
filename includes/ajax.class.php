<?php

namespace FSA\Plugin;

/**
 * Plugins custom settings page that adheres to wp standard
 * see: https://developer.wordpress.org/plugins/settings/custom-settings-page/
 *
 * @since   1.0
 */

defined('ABSPATH') || exit;

/**
 * WP Settings Class.
 */
class Ajax
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
    // Fetch fuel savings data items via ajax 
    add_action("wp_ajax_wppb_get_custom_block_patterns", array($this, 'wppb_get_custom_block_patterns'));
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

  /**
   * Get fuel savings data.
   * 
   * @since 1.0
   */
  public function wppb_get_custom_block_patterns()
  {

    if (!defined('DOING_AJAX') || !DOING_AJAX) {
      wp_die();
    }

    /**
     * Verify nonce
     */
    if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'settings_nonce')) {
      wp_die();
    }

    try {

      global $wpdb;

      $table_name = $wpdb->prefix . 'posts ORDER BY date DESC';
      $data = $wpdb->get_results("SELECT * FROM $table_name");


      error_log(print_r($data, true));

      wp_send_json(array(
        'status' => 'success',
        'data' => array(),
      ));
    } catch (\Exception $e) {

      wp_send_json(array(
        'status' => 'error',
        'message' => $e->getMessage()
      ));
    }
  }
}
