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

import {
	Button,
	IconButton,
	PanelBody,
	TextControl,
	__experimentalInputControl as InputControl,
	__experimentalNumberControl as NumberControl,
	SelectControl
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

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

const units = [
	{ label: 'stycken', value: 'st' },
	{ label: 'nypa', value: 'nypa' },
	{ label: 'milliliter', value: 'ml' },
	{ label: 'centiliter', value: 'cl' },
	{ label: 'deciliter', value: 'dl' },
	{ label: 'liter', value: 'l' },
	{ label: 'kryddmått', value: 'krm' },
	{ label: 'tesked', value: 'tsk' },
	{ label: 'matsked', value: 'msk' },
	{ label: 'milligram', value: 'mg' },
	{ label: 'gram', value: 'g' },
	{ label: 'kilo', value: 'kg' },
];

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export function ingredients( props ) {

	const handleAddIngredient = () => {
		const ingredients = [ ...props.attributes.ingredients ];
		ingredients.push( {
			amount: '',
			unit: '',
			name: '',
			extra: ''
		} );
		props.setAttributes( { ingredients } );

	};

	const handleRemoveIngredient = ( index ) => {
		const ingredients = [ ...props.attributes.ingredients ];
		ingredients.splice( index, 1 );
		props.setAttributes( { ingredients } );
	};

	const handleIngredientChange = ( key, value, index, e ) => {
		const ingredients = [ ...props.attributes.ingredients ];
		ingredients[ index ][ key ] = value;
		props.setAttributes( { ingredients } );
		maybeAddIngredient( key, e.event, ingredients );
	};

	const maybeAddIngredient = ( key, event, ingredients ) => {
		if ( [ 'name', 'extra' ].indexOf( key ) !== -1 && event.key === 'Enter' && ingredients[ ingredients.length - 1 ].amount !== '' ) {
			handleAddIngredient();
		}
	};

	let ingredientFields,
		ingredientDisplay;
		
	if ( props.attributes.ingredients.length ) {
		ingredientFields = props.attributes.ingredients.map( ( ingredient, index ) => {
			return [
				<div class="mfrb-ingredient">
					<NumberControl
						className="mfrb-ingredient-attr mfrb-ingredient-amount"
						placeholder="1"
						value={ props.attributes.ingredients[ index ].amount }
						min="0"
						onChange={ ( amount, e ) => handleIngredientChange( 'amount', amount, index, e ) }
						autoFocus={ true }
					/>
					<SelectControl
						className="mfrb-ingredient-attr mfrb-ingredient-unit"
						value={ props.attributes.ingredients[ index ].unit }
						options={
							units.map( unit => {
								return {
									label: pluralOrSingularUnit( props, index, unit.label ),
									value: unit.value
								}
							} )
						}
						onChange={ ( unit, e ) => handleIngredientChange( 'unit', unit, index, e ) }
					/>
					<InputControl
						className="mfrb-ingredient-attr mfrb-ingredient-name"
						placeholder="Ingredient"
						value={ props.attributes.ingredients[ index ].name }
						onChange={ ( name, e ) => handleIngredientChange( 'name', name, index, e ) }
						isPressEnterToChange={ true }
					/>
					<InputControl
						className="mfrb-ingredient-attr mfrb-ingredient-extra"
						placeholder="Extra"
						value={ props.attributes.ingredients[ index ].extra }
						onChange={ ( extra, e ) => handleIngredientChange( 'extra', extra, index, e ) }
						isPressEnterToChange={ true }
					/>
					<IconButton
						className="mfrb-remove-ingredient"
						icon="no-alt"
						label="Delete ingredient"
						onClick={ () => handleRemoveIngredient( index ) }
					/>
				</div>
			];
		} );

		ingredientDisplay = props.attributes.ingredients.map( ( ingredient, index ) => {
			return <div>{ ingredient.amount } { pluralOrSingularUnit( props, index, ingredient.unit ) } { ingredient.name }</div>;

		} );
	}

	return <div { ...useBlockProps() }>
		{ ingredientFields }
	
		<Button
			isDefault
			onClick={ handleAddIngredient.bind( this ) }
		>
			{ __( 'Add Ingredient' ) }
		</Button>
	</div>;
}
