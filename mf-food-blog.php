<?php
/**
 * Plugin Name:     MF Food Blog
 * Description:     Adds functionality and blocks for food blogs
 * Version:         0.1.0
 * Author:          Markus Finell
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     mf-food-blog
 *
 * @package         finell
 */

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function finell_mf_food_blog_block_init() {
	$dir = __DIR__;

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
		array(
			'editor_script' => 'finell-mf-food-blog-block-editor',
			'editor_style'  => 'finell-mf-food-blog-block-editor',
			'style'         => 'finell-mf-food-blog-block',
			'attributes'	=> [
				'ingredients' => [
					'type'    => 'array',
					'default' => []
				]
			]
		)
	);
}
add_action( 'init', 'finell_mf_food_blog_block_init' );
