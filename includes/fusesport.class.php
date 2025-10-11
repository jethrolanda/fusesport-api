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
    $url = 'https://rugbyresults.fusesport.com/api/oauth2/token';

    $body = [
      'grant-type' => 'client_credentials',
      'client_id' => '79FFD7AC-CB2A-44CA-B0D9-9A82CDD3D427',
      'client_secret' => 'LoJeZCfOuokbOMNNmJya9D19DrYb0pnM',
    ];

    $response = wp_remote_post($url, [
      'headers' => [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
      'body' => $body, // note: NOT json encoded, since it's form data
    ]);

    if (!is_wp_error($response)) {
      $data = json_decode(wp_remote_retrieve_body($response), true);
      error_log(print_r($data, true));
    } else {
      error_log(print_r($response->get_error_message(), true));
    }
  }
}
