<?php

namespace FSA\Plugin;

/**
 * @since   1.0
 */

defined('ABSPATH') || exit;

class Fusesport
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
    add_action('init', array($this, 'request'));
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

  public function request()
  {

    $options = get_option('fusesport_options');
    $url = 'https://rugbyresults.fusesport.com/api/oauth2/token';
    $username = $options['fusesport_field_api_username'];
    $password = $options['fusesport_field_api_password'];
    $auth = base64_encode("$username:$password");

    $body = [
      'grant-type' => 'client_credentials',
    ];

    $response = wp_remote_post($url, array(
      'headers' => array(
        'Authorization' => 'Basic ' . $auth,
      ),
      'body' => $body, // note: NOT json encoded, since it's form data
    ));

    if (!is_wp_error($response)) {
      $data = json_decode(wp_remote_retrieve_body($response), true);
      error_log(print_r($data, true));
    } else {
      error_log(print_r($response->get_error_message(), true));
    }
  }
}
