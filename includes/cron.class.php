<?php

namespace FSA\Plugin;


defined('ABSPATH') || exit;

class Cron
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
    add_filter('cron_schedules', array($this, 'add_custom_cron_schedules'));

    add_action('fusesport_schedule_update', array($this, 'fusesport_schedule_update'));
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

  function add_custom_cron_schedules($schedules)
  {
    // Adds once every 5 minutes
    if (! isset($schedules['every_five_minutes'])) {
      $schedules['every_five_minutes'] = array(
        'interval' => 5 * 60,
        'display'  => __('Every 5 Minutes'),
      );
    }

    // Example: add twice daily if you like
    if (! isset($schedules['twice_daily'])) {
      $schedules['twice_daily'] = array(
        'interval' => 12 * HOUR_IN_SECONDS,
        'display'  => __('Twice Daily'),
      );
    }

    return $schedules;
  }

  function activation_schedule_event()
  {
    if (! wp_next_scheduled('wce_every_five_minutes_event')) {
      wp_schedule_event(time(), 'every_five_minutes', 'wce_every_five_minutes_event');
    }
  }

  function deactivation_clear_event()
  {
    $timestamp = wp_next_scheduled('wce_every_five_minutes_event');
    if ($timestamp) {
      wp_unschedule_event($timestamp, 'wce_every_five_minutes_event');
    }
  }

  public function fusesport_schedule_update()
  {
    error_log('test cron');
    $new_post = array(
      'post_title'   => 'Test cron',
      'post_content' => 'This post was created using PHP!',
      'post_status'  => 'draft',  // Options: 'publish', 'draft', 'pending', etc.
      'post_author'  => 8,          // Usually the admin user ID
      'post_type'    => 'sp_event',     // You can also use custom post types
    );

    $post_id = wp_insert_post($new_post);

    if ($post_id && ! is_wp_error($post_id)) {
      echo "✅ Post created successfully with ID: $post_id";
    } else {
      echo "❌ Failed to create post.";
    }
  }
}
