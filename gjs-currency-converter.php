<?php if(!defined('ABSPATH')) { die(); }
/**
 * Plugin Name: Currency Converter Shortcodes
 * Description: shortcodes to provide an accurate conversion between various physical and digital currencies. Powered by <a href="https://www.cryptocompare.com/">Crypto Compare</a>
 * Version: 0.1.0
 * Author: Greg Schoppe
 * Author URI: https://gschoppe.com
 * Text Domain: gjs-currency-converter
 **/


if( !class_exists('GJSCurrencyConverter') ) {
	class GJSCurrencyConverter {
		private $version = '0.1.0';
		private $dir;
		private $uri;

		public static function Instance() {
			static $instance = null;
			if ($instance === null) {
				$instance = new self();
			}
			return $instance;
		}

		private function __construct() {
			$this->dir = dirname( __FILE__ );
			$this->uri = plugin_dir_url( __FILE__ );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'init', array( $this, 'init') );
		}
		// your functions go here
		public function enqueue_scripts() {
			wp_enqueue_script( 'gjs-currency-converter', $this->uri . '/js/gjs-currency-converter.js', array('jquery'), $this->version, true );
		}

		public function init() {
			add_shortcode( 'currency-converter', array( $this, 'currency_converter_shortcode' ) );
			add_shortcode( 'currency-converter-attribution', array( $this, 'currency_converter_attribution' ) );
		}

		public function currency_converter_shortcode( $atts ){
			$defaults = apply_filters( '', array(
				'value'    => 1,
				'from'     => 'USD',
				'to'       => 'USD',
				'round'    => false,
				'format'   => '',
				'decimal'  => '',
				'separator'=> '',
				'showunit' => 'true',
			));
			$atts = shortcode_atts( $defaults, $atts );
			$value = filter_var( $atts['value'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			if( !$value ) {
				$value = 1;
			}
			$template  = '<span class="gjs-currency"';
			$template .=' data-convert-from="' . esc_attr( $atts['from'] ) . '"';
			$template .=' data-convert-to="' . esc_attr( $atts['to'] ) . '"';
			$template .=' data-value="' . esc_attr( $value ) . '"';
			if( $atts['round'] !== false ) {
				$template .=' data-round="' . esc_attr( $atts['round'] ) . '"';
			}
			if( $atts['format'] ) {
				$template .=' data-format="' . esc_attr( $atts['format'] ) . '"';
			}
			if( $atts['decimal'] ) {
				$template .=' data-decimal="' . esc_attr( $atts['decimal'] ) . '"';
			}
			if( $atts['separator'] ) {
				$template .=' data-separator="' . esc_attr( $atts['separator'] ) . '"';
			}
			if( $atts['showunit'] ) {
				$template .=' data-showunit="true"';
			}
			$template .= '><span class="gjs-currency-loading">Loading</span></span>';
			return $template;
		}
		public function currency_converter_attribution( $atts ) {
			$attribution  = '<span class="gjs-currency-attribution">';
			$attribution .= 	'Powered by ';
			$attribution .='	<a href="https://www.cryptocompare.com/" target="_blank">';
			$attribution .= 		'Crypto Compare';
			$attribution .= 	'</a>';
			$attribution .= '</span>';
			return $attribution;
		}
	}
	GJSCurrencyConverter::Instance();
}
