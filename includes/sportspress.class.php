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
    // @set_time_limit(0);

    $options = get_option('fusesport_options');

    // REST API endpoint
    $url = $this->getSiteUrl() . '/wp-json/sportspress/v2/events';

    // Replace with your WordPress application credentials
    $username =  $options['sportspress_field_api_username'];
    $app_password = $options['sportspress_field_api_password'];
    $games = $data['rugby-schedule'][0]['competitions'][0]['games'];
    $leagueName = $data['rugby-schedule'][0]['competitions'][0]['name'];

    $sportspressSeasonId = $this->getTermSeasonIdByName('2025');
    $getLeagueId = $this->getTermLeagueIdByName($leagueName);

    foreach ($games as $key => $game) {
      $venueTermId = $this->createVenue($game['location']);

      $team_ids = $this->createTeams(
        array(
          array(
            'home_team_id' => $game['home_team_id'],
            'home_team_name' => $game['home_team_name'],
          ),
          array(
            'away_team_id' => $game['away_team_id'],
            'away_team_name' => $game['away_team_name'],
          )
        )
      );
      // Prepare event data (example: rugby match)
      $prepared_data = array(
        'title'        => $game['home_team_name'] . ' vs ' . $game['away_team_name'],
        'status'       => 'publish',
        'teams'        => $team_ids, // IDs of the teams
        'date'         => $game['GameDate'],
        'venue'        => $venueTermId, // venue id
        'competition'  => $getLeagueId, // competion id or league id??
        'season'       => $sportspressSeasonId // season id
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
        // Assign venue
        wp_set_object_terms($body['id'], $venueTermId, 'sp_venue');

        // Assign league
        wp_set_object_terms($body['id'], $getLeagueId, 'sp_league');

        // Assign season
        wp_set_object_terms($body['id'], $sportspressSeasonId, 'sp_season');

        // Mode
        update_post_meta($body['id'], 'sp_format', 'league');

        // Format
        update_post_meta($body['id'], 'sp_mode', 'team');

        // Results
        $result[$team_ids[0]] = array(
          "tries" => 0,
          "conversions" => 0,
          "pg" => 0,
          "dg" => 0,
          "points" => $game['home_team_score'],
          "outcome" => array(
            $game['home_team_score'] > $game['away_team_score'] ? "win" : "loss"
          )
        );
        $result[$team_ids[1]] = array(
          "tries" => 0,
          "conversions" => 0,
          "pg" => 0,
          "dg" => 0,
          "points" => $game['away_team_score'],
          "outcome" => array(
            $game['away_team_score'] > $game['home_team_score'] ? "win" : "loss"
          )
        );

        update_post_meta(
          $body['id'],
          'sp_results',
          $result
        );
      }
      // if ($key == 5) break;
    }
    // error_log(print_r($games, true));
  }

  public function createTeams($teams)
  {
    error_log(print_r($teams, true));

    $options = get_option('fusesport_options');
    $url = $this->getSiteUrl() . '/wp-json/sportspress/v2/teams';
    $username =  $options['sportspress_field_api_username'];
    $app_password = $options['sportspress_field_api_password'];
    $team_ids = array();
    foreach ($teams as $team) {

      $teamname = isset($team['away_team_name']) ? $team['away_team_name'] : $team['home_team_name'];

      // Check if team exist
      if ($team_id = $this->getTeamIdIfExist($teamname, 'sp_team')) {
        $team_ids[] = $team_id;
        continue;
      }

      $data = array(
        'title'       => $teamname,
        'status'      => 'publish',
        'description' => ''
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
        $team_ids[] = 0;
      } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        error_log('SportsPress Team Created: ' . $teamname);
        $team_ids[] = $body['id'];
      }
    }

    return $team_ids;
  }

  public function createVenue($venue)
  {

    if (empty(trim($venue))) return 0;

    $options = get_option('fusesport_options');
    $url = $this->getSiteUrl() . '/wp-json/sportspress/v2/venues';
    $username =  $options['sportspress_field_api_username'];
    $app_password = $options['sportspress_field_api_password'];

    $args = array(
      'headers' => array(
        'Authorization' => 'Basic ' . base64_encode("$username:$app_password"),
        'Content-Type'  => 'application/json'
      ),
      'body'    => wp_json_encode(array(
        'name'       => $venue,
        'status'      => 'publish',
        'description' => ''
      )),
      'method'  => 'POST',
      'timeout' => 60
    );

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
      error_log('SportsPress Venue Creation Failed: ' . $response->get_error_message());
      return 0;
    } else {
      $data = json_decode(wp_remote_retrieve_body($response));
      error_log('SportsPress Venue Created: ' . $venue);
      if (isset($data->code) && $data->code == 'term_exists')
        return $data->data->term_id;

      return $data->id;
    }
  }

  // HELPER FUNCTIONS
  public function getSiteUrl()
  {
    // If local env then use the docke host url else use regular url
    if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local') {
      if (defined('WP_DOCKER_HOST')) {
        $url = WP_DOCKER_HOST;
      } else {
        $url = site_url();
      }
    } else {
      $url = site_url();
    }

    return $url;
  }

  public function getTermSeasonIdByName($name)
  {
    $term = get_term_by('name', $name, 'sp_season');

    if ($term) {
      $term_id = $term->term_id;
      return $term_id;
    }

    return false;
  }

  public function getTermLeagueIdByName($name)
  {
    $term = get_term_by('name', $name, 'sp_league');

    if ($term) {
      $term_id = $term->term_id;
      return $term_id;
    }

    return false;
  }


  public function getTeamIdIfExist($title, $post_type)
  {
    global $wpdb;

    // Try to find the post by title (exact match)
    $post_id = $wpdb->get_var($wpdb->prepare(
      "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s AND post_status != 'trash' LIMIT 1",
      $title,
      $post_type
    ));

    if ($post_id) {
      return (int) $post_id;
    }
    return false;
  }
}
