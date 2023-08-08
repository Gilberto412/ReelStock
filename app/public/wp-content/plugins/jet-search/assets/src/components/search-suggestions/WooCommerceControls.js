import { useState, useEffect } from 'react';

const { __ } = wp.i18n;

const {
	ToggleControl,
	PanelBody,
} = wp.components;

const {
	Fragment
} = wp.element;

const WooCommerceControls = props => {
    const {
		attributes,
		setAttributes,
	} = props;

    const [isWooCommerceInstalled, setIsWooCommerceInstalled] = useState( false );

    useEffect( () => {
        const checkWooCommerceInstalled = async () => {
            try {
                const response       = await wp.apiFetch( { path: '/wc/v3/system_status' } );
                const activePlugins  = response['active_plugins'];
                const hasWooCommerce = activePlugins.some( plugin => plugin.name === 'WooCommerce' );
                setIsWooCommerceInstalled( !!hasWooCommerce );
            } catch ( error ) {
                console.error( 'Error checking WooCommerce installation:', error );
            }
        };

        checkWooCommerceInstalled();
    }, [] );

    return (
        <Fragment>
            { isWooCommerceInstalled && (
                <PanelBody
                    title={ __( 'WooCommerce' ) }
                >
                    <ToggleControl
                        label={ __( "Is Product Search" ) }
                        checked={ attributes.is_product_search }
                        onChange={ ( value ) => {
                            props.setAttributes( {
                                is_product_search: value
                            } );
                        } }
                    />
                </PanelBody>
                )
            }
        </Fragment>
    )
}

export default WooCommerceControls;