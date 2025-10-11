<?php
if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly


class WP_Plugin_Boilerplate
{

	/*
    |------------------------------------------------------------------------------------------------------------------
    | Class Members
    |------------------------------------------------------------------------------------------------------------------
     */
	private static $_instance;

	public $scripts;
	public $blocks;
	public $ajax;
	public $shortcode;
	public $rest;
	public $fusesport;

	const VERSION = '1.0';

	/*
  |------------------------------------------------------------------------------------------------------------------
  | Mesc Functions
  |------------------------------------------------------------------------------------------------------------------
  */

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{

		$this->scripts = FSA\Plugin\Scripts::instance();
		$this->blocks = FSA\Plugin\Blocks::instance();
		$this->ajax = FSA\Plugin\Ajax::instance();
		$this->shortcode = FSA\Plugin\Shortcode::instance();
		$this->rest = FSA\Plugin\Rest::instance();
		$this->fusesport = FSA\Plugin\Fusesport::instance();


		// Register Activation Hook
		register_activation_hook(FSA_PLUGIN_DIR . 'wp-plugin-boilerplate.php', array($this, 'activate'));

		// Register Deactivation Hook
		register_deactivation_hook(FSA_PLUGIN_DIR . 'wp-plugin-boilerplate.php', array($this, 'deactivate'));
	}

	/**
	 * Singleton Pattern.
	 *
	 * @since 1.0.0
	 */
	public static function instance()
	{

		if (!self::$_instance instanceof self) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}


	/**
	 * Trigger on activation
	 *
	 * @since 1.0.0
	 */
	public function activate() {}

	/**
	 * Trigger on deactivation
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {}
}
