<?php 
/**
 * Get array of labels for a taxonomy
 * 
 * @param string $singular
 * @param string $plural
 * 
 * @return array
 */
if ( ! function_exists( 'mffb_get_taxonomy_labels' ) ) {
	function mffb_get_taxonomy_labels( $singular, $plural ) {
		$lc_singular = strtolower( $singular );
		$lc_plural = strtolower( $plural );

		return [
			'name'                       => $plural,
			'singular_name'              => $singular,
			'search_items'               => sprintf( __( 'Search %s', 'mf-food-blog' ), $lc_plural ),
			'popular_items'              => sprintf( __( 'Popular %s', 'mf-food-blog' ), $lc_plural ),
			'all_items'                  => sprintf( __( 'All %s', 'mf-food-blog' ), $lc_plural ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'mf-food-blog' ), $lc_singular ),
			'update_item'                => sprintf( __( 'Update %s', 'mf-food-blog' ), $singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'mf-food-blog' ), $singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'mf-food-blog' ), $lc_singular ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'mf-food-blog' ), $lc_plural ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'mf-food-blog' ), $lc_plural ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'mf-food-blog' ), $lc_plural ),
			'not_found'                  => sprintf( __( 'No %s found.', 'mf-food-blog' ), $lc_plural ),
			'menu_name'                  => $plural,
		];
	}
}