<?php

namespace MF_Food_Blog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Plugin Name:       MF Food Blog
 * Plugin URI: 		  https://nell.fi/mf-food-blog
 * Description:       Adds functionality and blocks for food blogs
 * Version:           0.1.0
 * Requires at least: 5.0
 * Requires PHP: 	  5.3
 * Author:            Markus Finell
 * Author URI: 		  https://nell.fi
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mf-food-blog
 * Domain Path: 	  languages
 *
 * @package           finell
 */

if ( ! defined( 'MF_FOOD_BLOG_DIR' ) ) {
	define( 'MF_FOOD_BLOG_DIR', __DIR__ );
}

if ( ! defined( 'MF_FOOD_BLOG_FILE' ) ) {
	define( 'MF_FOOD_BLOG_FILE', __FILE__ );
}

if ( ! class_exists( 'MF_Food_Blog\\Plugin' ) ) {
	class Plugin {

		const INGREDIENT_TAXONOMY = 'mffb_ingredient';

		public function __construct() {
			require_once MF_FOOD_BLOG_DIR . '/functions.php';

			$this->actions();
		}

		private function actions() {
			add_action( 'plugins_loaded', [ $this, 'load_language_files' ] );
			add_action( 'init', [ $this, 'register_blocks' ] );
			add_action( 'init', [ $this, 'register_taxonomies' ] );
			add_action( 'save_post', [ $this, 'save_post_hooks' ], 10, 3 );
			add_action( 'mffb_save_post', [ $this, 'set_ingredient_terms' ], 10, 3 );
		}

		/**
		 * Load language files
		 */
		public function load_language_files() {
			load_plugin_textdomain( 'mf-food-blog', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Registers all block assets so that they can be enqueued through the block editor
		 * in the corresponding context.
		 *
		 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
		 */
		public function register_blocks() {
			$dir = MF_FOOD_BLOG_DIR;

			$script_asset_path = "$dir/build/index.asset.php";
			if ( ! file_exists( $script_asset_path ) ) {
				throw new Error(
					'You need to run `npm start` or `npm run build` first.'
				);
			}
			$index_js     = 'build/index.js';
			$script_asset = require( $script_asset_path );
			wp_register_script(
				'finell-mf-food-blog-block-editor',
				plugins_url( $index_js, __FILE__ ),
				$script_asset['dependencies'],
				$script_asset['version']
			);
			wp_set_script_translations( 'finell-mf-food-blog-block-editor', 'mf-food-blog' );

			$editor_css = 'build/index.css';
			wp_register_style(
				'finell-mf-food-blog-block-editor',
				plugins_url( $editor_css, __FILE__ ),
				array(),
				filemtime( "$dir/$editor_css" )
			);

			$style_css = 'build/style-index.css';
			wp_register_style(
				'finell-mf-food-blog-block',
				plugins_url( $style_css, __FILE__ ),
				array(),
				filemtime( "$dir/$style_css" )
			);

			register_block_type(
				'finell/ingredients',
				[
					'editor_script' => 'finell-mf-food-blog-block-editor',
					'editor_style'  => 'finell-mf-food-blog-block-editor',
					'style'         => 'finell-mf-food-blog-block',
					'attributes'	=> [
						'ingredients' => [
							'type'    => 'array',
							'default' => []
						]
					]
				]
			);
		}

		/**
		 * Add save post hooks
		 */
		public function save_post_hooks( $post_id, $post, $update ) {
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}
			
			do_action( 'mffb_save_post', $post_id, $post, $update );
		}

		/**
		 * Register taxonomies
		 */
		public function register_taxonomies() {
			register_taxonomy(
				self::INGREDIENT_TAXONOMY,
				'post',
				[
					'labels' 				=> mffb_get_taxonomy_labels( __( 'Ingredient', 'mf-food-blog' ), __( 'Ingredients', 'mf-food-blog' ) ),
					'hierarchical'          => false,
					'show_ui'               => true,
					'update_count_callback' => '_update_post_term_count',
					'query_var'             => true,
					'public'				=> false,
				]
			);
		}

		/**
		 * Update terms on save post
		 * 
		 * @param int     $post_id
		 * @param WP_Post $post
		 * @param bool    $update
		 */
		public function set_ingredient_terms( $post_id, $post, $update ) {
			$ingredients = $this->get_post_ingredients( $post );

			foreach ( $ingredients as $key => $ingredient ) {
				if ( ! term_exists( $ingredient, self::INGREDIENT_TAXONOMY ) ) {
					$term = array_shift( get_terms(
						[
							'taxonomy' => self::INGREDIENT_TAXONOMY,
							'description__like' => $ingredient,
							'hide_empty' => false,
						]
					) );

					if ( $term ) {
						$ingredients[ $key ] = $term->name;
					}
				}
			}

			wp_set_post_terms( $post_id, $ingredients, self::INGREDIENT_TAXONOMY );
		}

		/**
		 * Get list of ingredients from post
		 * 
		 * @param int|WP_Post $post Optional. Post ID, post object. Defaults to global $post.
		 * @return array An array of ingredients
		 */
		private function get_post_ingredients( $post = null ) {
			$post = $post instanceof WP_Post ? $post : get_post( $post );

			return array_unique(
				call_user_func_array(
					'array_merge',
					array_filter(
						array_map(
							function ( $block ) {
								if ( $block['blockName'] !== 'finell/ingredients' ) {
									return false;
								}

								return array_map(
									function ( $ingredient ) {
										return $ingredient['name'];
									},
									$block['attrs']['ingredients']
								);
							},
							parse_blocks( $post->post_content )
						)
					)
				)
			);
		}
	}
	
	new Plugin();
}


