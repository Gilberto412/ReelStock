import IconControl from '../../controls/icon-control';

const { __ } = wp.i18n;

const {
	TextControl,
	ToggleControl,
	SelectControl,
	PanelBody,
    RangeControl,
    TextareaControl,
} = wp.components;

const {
	Fragment
} = wp.element;

const FormControls = props => {
    const {
		taxonomies,
        settingsPageLink,
		attributes,
		setAttributes,
	} = props;

    return (
        <PanelBody
            title={ __( 'Search Form' ) }
        >

            <TextControl
                label={ __( 'Placeholder' ) }
                help={ __( 'Placeholder text for the search input' ) }
                value={ attributes.search_placeholder_text }
                onChange={ ( value ) => {
                    props.setAttributes( {
                        search_placeholder_text: value
                    } )
                } }
            />

            <ToggleControl
                label={ __( "Show preloader" ) }
                checked={ attributes.show_search_suggestions_list_on_focus_preloader }
                onChange={ ( value ) => {
                    props.setAttributes( {
                        show_search_suggestions_list_on_focus_preloader: value
                    } );
                } }
            />

            <ToggleControl
                label={ __( "Highlight Searched Text" ) }
                checked={ attributes.highlight_searched_text }
                onChange={ ( value ) => {
                    props.setAttributes( {
                        highlight_searched_text: value
                    } );
                } }
            />

            <RangeControl
                label={ __( 'Number of Suggestions' ) }
                value={ attributes.search_suggestions_quantity_limit }
                onChange={ value => {
                    props.setAttributes({ search_suggestions_quantity_limit: value })
                } }
                min={ 1 }
                max={ 50 }
            />

            <div className="jet-search-block-line-separator"></div>

            <ToggleControl
                label={ __( 'Show Submit Button' ) }
                checked={ attributes.show_search_submit }
                onChange={ () => {
                    props.setAttributes( {
                        show_search_submit: ! attributes.show_search_submit
                    } );
                } }
            />
            { attributes.show_search_submit && <Fragment>
                <TextControl
                    label={ __( 'Button Label' ) }
                    value={ attributes.search_submit_label }
                    onChange={ ( value ) => {
                        props.setAttributes( {
                            search_submit_label: value
                        } )
                    } }
                />
                <p>Button Icon</p>
                <IconControl
                    icon={ attributes.selected_search_submit_icon }
                    defaultIcon
                    onChange={ ( value ) => {
                        props.setAttributes( {
                            selected_search_submit_icon: value
                        } );
                    } }
                />
            </Fragment> }

            <div className="jet-search-block-line-separator"></div>

            <ToggleControl
                label={ __( "Show Categories List" ) }
                className="jet-search-block-separator__before"
                checked={ attributes.show_search_category_list }
                onChange={ ( value ) => {
                    props.setAttributes( {
                        show_search_category_list: value
                    } );
                } }
            />

            { attributes.show_search_category_list &&
                <Fragment>

                    <SelectControl
                        label= { __( "Taxonomy" ) }
                        value={ attributes.search_taxonomy }
                        options={ taxonomies }
                        onChange={ value => {
                            props.setAttributes({ search_taxonomy: value });
                        }}
                    />

                    <TextControl
                        type="text"
                        label={ __("Select Placeholder") }
                        value={ attributes.search_category_select_placeholder }
                        onChange={ ( value ) => {
                            props.setAttributes( {
                                search_category_select_placeholder: value
                            } );
                        } }
                    />
                </Fragment>
            }

            <div className="jet-search-block-line-separator"></div>

            {/* Inline Suggestions*/}

            <ToggleControl
                label={ __( "Show Suggestions Below Search Form" ) }
                checked={ attributes.show_search_suggestions_list_inline }
                onChange={ ( value ) => {
                    props.setAttributes( {
                        show_search_suggestions_list_inline: value
                    } );
                } }
            />

            { attributes.show_search_suggestions_list_inline &&
                <Fragment>
                    <SelectControl
                        label= { __( "Suggestions List" ) }
                        value={ attributes.search_suggestions_list_inline }
                        options={ [
                            {
                                value: 'popular',
                                label: __( 'Most popular' ),
                            },
                            {
                                value: 'latest',
                                label: __( 'Latest' ),
                            },
                            {
                                value: 'manual',
                                label: __( 'Manual' ),
                            }
                        ] }
                        onChange={ value => {
                            props.setAttributes({ search_suggestions_list_inline: value });
                        } }
                    />

                    { 'manual' != attributes.search_suggestions_list_inline &&
                        <Fragment>
                            <RangeControl
                                label={ __( 'Number of Suggestions' ) }
                                value={ attributes.search_suggestions_list_inline_quantity }
                                onChange={ value => {
                                    props.setAttributes({ search_suggestions_list_inline_quantity: value })
                                } }
                                min={ 1 }
                                max={ 50 }
                            />
                        </Fragment>
                    }

                    { 'manual' === attributes.search_suggestions_list_inline &&
                        <Fragment>
                            <TextareaControl
                                label="List of Manual Suggestions"
                                value={ attributes.search_suggestions_list_inline_manual }
                                onChange={ value => {
                                    props.setAttributes({ search_suggestions_list_inline_manual: value });
                                } }
                            />
                            <p className="control-help">Write multiple suggestions by semicolon separated with "," sign.</p>
                        </Fragment>
                    }
                </Fragment>
            }

            <div className="jet-search-block-line-separator"></div>

            {/* Focus Suggestions */}

            <ToggleControl
                label={ __( "Show Suggestions on Input Focus" ) }
                checked={ attributes.show_search_suggestions_list_on_focus }
                onChange={ ( value ) => {
                    props.setAttributes( {
                        show_search_suggestions_list_on_focus: value
                    } );
                } }
            />

            { attributes.show_search_suggestions_list_on_focus &&
                <Fragment>
                    <SelectControl
                        label= { __( "Suggestions List" ) }
                        value={ attributes.search_suggestions_list_on_focus }
                        options={ [
                            {
                                value: 'popular',
                                label: __( 'Most popular' ),
                            },
                            {
                                value: 'latest',
                                label: __( 'Latest' ),
                            },
                            {
                                value: 'manual',
                                label: __( 'Manual' ),
                            }
                        ] }
                        onChange={ value => {
                            props.setAttributes({ search_suggestions_list_on_focus: value });
                        } }
                    />

                    { 'manual' != attributes.search_suggestions_list_on_focus &&
                        <Fragment>
                            <RangeControl
                                label={ __( 'Number of Suggestions' ) }
                                value={ attributes.search_suggestions_list_on_focus_quantity }
                                onChange={ value => {
                                    props.setAttributes({ search_suggestions_list_on_focus_quantity: value })
                                } }
                                min={ 1 }
                                max={ 50 }
                            />
                        </Fragment>
                    }

                    { 'manual' === attributes.search_suggestions_list_on_focus &&
                        <Fragment>
                            <TextareaControl
                                label="List of Manual Suggestions"
                                value={ attributes.search_suggestions_list_on_focus_manual }
                                onChange={ value => {
                                    props.setAttributes({ search_suggestions_list_on_focus_manual: value });
                                } }
                            />
                            <p className="control-help">Write multiple suggestions by semicolon separated with "," sign.</p>
                        </Fragment>
                    }
                </Fragment>
            }

            <div className="jet-search-block-line-separator"></div>

            <p>Manage Saved Suggestions <a target="_blank" href={ settingsPageLink }><strong>here</strong></a></p>
        </PanelBody>
    )
}

export default FormControls;