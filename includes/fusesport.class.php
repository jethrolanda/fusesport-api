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
    // add_action('init', array($this, 'request'));
    add_action('wp_ajax_fusesport_api', array($this, 'request_data'));
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

  public function request_token()
  {

    try {
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
        return array(
          'status' => 'success',
          'token' => $data['token'],
        );
      } else {
        return array(
          'status' => 'error',
          'message' => $response->get_error_message(),
        );
      }
    } catch (\Exception $e) {
      return array(
        'status' => 'error',
        'message' => $e->getMessage()
      );
    }
  }

  public function request_data()
  {
    $requested_token = $this->request_token();
    if ($requested_token['status'] === 'success') {

      // You can get scheduling, scores and ladder info from https://rugbyresults.fusesport.com/api/rugby/main_detail/
      // 1313 is the season_id for the whole 2025 Premiership Rugby season.
      // It’s recommended to filter the results by adding ?competitionID=1688636 for Chikarovski Cup or ?competitionID=1688637 for Women's Div 2

      $url = 'https://rugbyresults.fusesport.com/api/rugby/main_detail/1313';
      $token = $requested_token['token'];

      $response = wp_remote_get($url, array(
        'headers' => array(
          'Authorization' => 'Bearer ' . $token,
          'Accept'        => 'application/json',
        ),
      ));

      if (is_wp_error($response)) {
        wp_send_json(array(
          'status' => 'error',
          'message' => $response->get_error_message(),
        ));
      } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        wp_send_json(array(
          'status' => 'success',
          'data' => $data,
        ));
      }
    }
  }
}
