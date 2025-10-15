<?php

namespace FSA\Plugin;

/**
 * Scripts class
 *
 * @since   1.0
 */

defined('ABSPATH') || exit;

class Scripts
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

    // Load Backend CSS and JS
    add_action('admin_enqueue_scripts', array($this, 'backend_script_loader'));

    // Load Frontend CSS and JS
    add_action('wp_enqueue_scripts', array($this, 'frontend_script_loader'));
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
   * Load wp admin backend scripts
   *
   * @since 1.0
   * @return bool
   */
  public function backend_script_loader()
  {
    $asset_file = FSA_JS_ROOT_DIR . 'fusesport/build/index.asset.php';

    if (file_exists($asset_file) && isset($_GET['page']) && $_GET['page'] == "fusesport") {
      $asset = include $asset_file;
      wp_enqueue_script('fusesport-js', FSA_JS_ROOT_URL . 'fusesport/build/index.js', $asset['dependencies'], $asset['version'], true);
      wp_localize_script('fusesport-js', 'fusesport_params', array(
        'rest_url'   => esc_url_raw(get_rest_url()),
        'nonce' => wp_create_nonce('wp_rest'),
        'ajax_url' => admin_url('admin-ajax.php'),
      ));
      wp_enqueue_style('fusesport-css', FSA_JS_ROOT_URL . 'fusesport/build/style-index.css');

      wp_enqueue_style('fusesport-css');
      wp_enqueue_script('fusesport-js');
    }
  }

  /**
   * Load wp frontend scripts
   *
   * @since 1.0
   * @return bool
   */
  public function frontend_script_loader() {}
}
