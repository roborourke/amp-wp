/**
 * External dependencies
 */
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __, _x } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { SimplePanel } from './panel';
import { InputGroup, getCommonValue } from './elements';

function SizePanel( { selectedElements, onSetProperties } ) {
	const width = getCommonValue( selectedElements, 'width' );
	const height = getCommonValue( selectedElements, 'height' );
	const isFullbleed = getCommonValue( selectedElements, 'isFullbleed' );
	const [ state, setState ] = useState( { width, height } );
	const [ lockRatio, setLockRatio ] = useState( true );
	useEffect( () => {
		setState( { width, height } );
	}, [ width, height ] );
	const handleSubmit = ( evt ) => {
		onSetProperties( state );
		evt.preventDefault();
	};
	return (
		<SimplePanel title={ __( 'Size', 'amp' ) } onSubmit={ handleSubmit }>
			<InputGroup
				label={ __( 'Width', 'amp' ) }
				value={ state.width }
				isMultiple={ width === '' }
				onChange={ ( value ) => {
					const ratio = width / height;
					const newWidth = isNaN( value ) || value === '' ? '' : parseFloat( value );
					setState( {
						...state,
						width: newWidth,
						height: typeof newWidth === 'number' && lockRatio ? newWidth / ratio : height,
					} );
				} }
				postfix={ _x( 'px', 'pixels, the measurement of size', 'amp' ) }
				disabled={ isFullbleed }
			/>
			<InputGroup
				label={ __( 'Height', 'amp' ) }
				value={ state.height }
				isMultiple={ height === '' }
				onChange={ ( value ) => {
					const ratio = width / height;
					const newHeight = isNaN( value ) || value === '' ? '' : parseFloat( value );
					setState( {
						...state,
						height: newHeight,
						width: typeof newHeight === 'number' && lockRatio ? newHeight * ratio : width,
					} );
				} }
				postfix={ _x( 'px', 'pixels, the measurement of size', 'amp' ) }
				disabled={ isFullbleed }
			/>
			<InputGroup
				type="checkbox"
				label={ __( 'Keep ratio', 'amp' ) }
				value={ lockRatio }
				isMultiple={ false }
				onChange={ ( value ) => {
					setLockRatio( value );
				} }
				disabled={ isFullbleed }
			/>
		</SimplePanel>
	);
}

SizePanel.propTypes = {
	selectedElements: PropTypes.array.isRequired,
	onSetProperties: PropTypes.func.isRequired,
};

export default SizePanel;
