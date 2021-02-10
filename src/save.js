/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { useBlockProps } from '@wordpress/block-editor';

const pluralOrSingularUnit = ( props, index, unit ) => {
	let plural = props.attributes.ingredients[ index ].amount > 1;

	if ( ! plural ) {
		return unit;
	}

	switch ( unit ) {
		case 'nypa':
			return 'nypor';
		
		case 'matsked':
			return 'matskedar';

		case 'tesked':
			return 'teskedar';

		default:
			return unit;
	}
};

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export function ingredients( props ) {
	// return (
	// 	<p { ...useBlockProps.save() }>
	// 		{ __(
	// 			'Ingredients – hello from the saved content!',
	// 			'mf-food-blog'
	// 		) }
	// 	</p>
	// );

	const ingredientFields = props.attributes.ingredients.map( ( ingredient, index ) => {
		return <li>
			{ ingredient.amount } { pluralOrSingularUnit( props, index, ingredient.unit ) } { ingredient.name }{ ingredient.extra }
		</li>;
	} );

	return <ul { ...useBlockProps.save() }>{ ingredientFields }</ul>;
}
