<?php

namespace FSA\Plugin;

/** 
 * @since   1.0
 */

defined('ABSPATH') || exit;

/**
 * WP Settings Class.
 */
class Settings
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
    /**
     * Register our settings_init to the admin_init action hook.
     */
    add_action('admin_init', array($this, 'settings_init'));

    /**
     * Register our register_options_page to the admin_menu action hook.
     */
    add_action('admin_menu', array($this, 'register_options_page'));
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
   * custom option and settings
   */
  public function settings_init()
  {
    // Register a new setting for "fusesport" page.
    register_setting('fusesport', 'fusesport_options');

    // Register a new section in the "fusesport" page.
    add_settings_section(
      'fusesport_section_developers',
      '',
      '',
      'fusesport'
    );

    // Register a new field in the "fusesport_section_developers" section, inside the "fusesport" page.
    add_settings_field(
      'fusesport_field_api_username', // As of WP 4.6 this value is used only internally.
      // Use $args' label_for to populate the id inside the callback.
      __('FuseSport API Username', 'fusesport'),
      array($this, 'fusesport_field_api_username_cb'),
      'fusesport',
      'fusesport_section_developers',
      array(
        'label_for'         => 'fusesport_field_api_username',
        'class'             => 'warroom_row',
      )
    );
    add_settings_field(
      'fusesport_field_api_password', // As of WP 4.6 this value is used only internally.
      // Use $args' label_for to populate the id inside the callback.
      __('FuseSport API Password', 'fusesport'),
      array($this, 'fusesport_field_api_password_cb'),
      'fusesport',
      'fusesport_section_developers',
      array(
        'label_for'         => 'fusesport_field_api_password',
        'class'             => 'warroom_row',
      )
    );
  }



  /**
   * Pill field callbakc function.
   *
   * WordPress has magic interaction with the following keys: label_for, class.
   * - the "label_for" key value is used for the "for" attribute of the <label>.
   * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
   * Note: you can add custom key value pairs to be used inside your callbacks.
   *
   * @param array $args
   */
  public function fusesport_field_api_username_cb($args)
  {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option('fusesport_options'); ?>
    <input style="width: 400px" type="text" placeholder="Username" id="<?php echo esc_attr($args['label_for']); ?>" name="fusesport_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?>" />

  <?php
  }

  public function fusesport_field_api_password_cb($args)
  {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option('fusesport_options');
  ?>
    <input style="width: 400px" type="password" placeholder="Password" id="<?php echo esc_attr($args['label_for']); ?>" name="fusesport_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?>" />

  <?php
  }


  /**
   * Add the top level menu page.
   */
  public function register_options_page()
  {
    add_menu_page(
      'FuseSport Settings',
      'FuseSport',
      'manage_options',
      'fusesport',
      array($this, 'options_page')
    );
  }

  /**
   * Top level menu callback function
   */
  public function options_page()
  {
    // check user capabilities
    if (! current_user_can('manage_options')) {
      return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
      // add settings saved message with the class of "updated"
      add_settings_error('fusesport_messages', 'fusesport_message', __('Settings Saved', 'fusesport'), 'updated');
    }

    // show error/update messages
    settings_errors('fusesport_messages');
  ?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "fusesport"
        settings_fields('fusesport');
        // output setting sections and their fields
        // (sections are registered for "fusesport", each field is registered to a specific section)
        do_settings_sections('fusesport');
        // output save settings button
        submit_button('Save Settings');
        ?>
      </form>
    </div>

    <div id="fusesport"></div>
<?php
  }
}
