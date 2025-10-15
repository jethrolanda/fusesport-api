<?php

namespace FSA\Plugin;

/**
 * @since   1.0
 */

defined('ABSPATH') || exit;

/**
 * WP Settings Class.
 */
class Sportspress
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
  public function __construct() {}

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

  public function createEvents($data)
  {
    $options = get_option('fusesport_options');

    // REST API endpoint
    $url = 'http://host.docker.internal:8006/wp-json/sportspress/v2/events';

    // Replace with your WordPress application credentials
    $username =  $options['sportspress_field_api_username']; //'api-dev';
    $app_password = $options['sportspress_field_api_password']; //'iFgA 6lDQ qZrJ 4YQF FbaA uXym';
    $games = $data['rugby-schedule'][0]['competitions'][0]['games'];

    foreach ($games as $key => $game) {

      // Prepare event data (example: rugby match)
      $prepared_data = array(
        'title'        => $game['home_team_name'] . ' vs ' . $game['away_team_name'],
        'status'       => 'publish',
        'teams'        => array(12, 15), // IDs of the teams
        'date'         => $game['GameDate'],
        'venue'        => 7,
        'competition'  => 3,
        'season'       => 19
      );
      error_log(print_r($prepared_data, true));
      $args = array(
        'headers' => array(
          'Authorization' => 'Basic ' . base64_encode("$username:$app_password"),
          'Content-Type'  => 'application/json'
        ),
        'body'    => wp_json_encode($prepared_data),
        'method'  => 'POST',
        'timeout' => 300,
      );

      // Send the request
      $response = wp_remote_post($url, $args);

      if (is_wp_error($response)) {
        error_log('SportsPress Event Creation Failed: ' . $response->get_error_message());
      } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        error_log('SportsPress Event Created: ' . print_r($prepared_data['title'], true));
      }
      if ($key == 1) break;
    }
    // error_log(print_r($games, true));
  }

  public function createTeam()
  {
    $url = 'http://host.docker.internal:8006/wp-json/sportspress/v2/teams';
    $username = 'your_username';
    $app_password = 'your_app_password';

    $data = array(
      'title'       => 'Rugby United',
      'status'      => 'publish',
      'description' => 'Official rugby club of the Northern League'
    );

    $args = array(
      'headers' => array(
        'Authorization' => 'Basic ' . base64_encode("$username:$app_password"),
        'Content-Type'  => 'application/json'
      ),
      'body'    => wp_json_encode($data),
      'timeout' => 60 // increase timeout for Docker or slow connections
    );

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
      error_log('SportsPress Team Creation Failed: ' . $response->get_error_message());
    } else {
      $body = json_decode(wp_remote_retrieve_body($response), true);
      error_log('SportsPress Team Created: ' . print_r($body, true));
    }
  }
}
