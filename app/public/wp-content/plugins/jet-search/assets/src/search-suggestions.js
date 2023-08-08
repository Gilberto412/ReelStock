import FormControls from './components/search-suggestions/FormControls';
import WooCommerceControls from './components/search-suggestions/WooCommerceControls';

const {
	InspectorControls,
} = wp.blockEditor;

const { __ } = wp.i18n;

const {
	ToolbarGroup,
	ToolbarButton,
	ToggleGroupControl,
	ToggleGroupControlOption,
	Disabled
} = wp.components;

const {
	serverSideRender: ServerSideRender
} = wp;

const {
	BlockControls,
} = wp.blockEditor;

const {
	Fragment,
	useState
} = wp.element;

const searchSuggestions = function( props ) {

	const {
		className,
		attributes,
		setAttributes,
	} = props;

	const [ previewFocusItems, setpreviewFocusItems ]   = useState( false );
	const [ previewInlineItems, setpreviewInlineItems ] = useState( false );

	const taxonomies       = window.JetSearchData.taxonomiesList;
	const settingsPageLink = window.JetSearchData.settingsPageLink;

	const previewFocusItemsState = () => {
		setpreviewFocusItems( !previewFocusItems );

		if ( true === previewInlineItems ) {
			setpreviewInlineItems( false );
		}
	}

	const previewInlineItemsState = () => {
		setpreviewInlineItems( !previewInlineItems );

		if ( true === previewFocusItems ) {
			setpreviewFocusItems( false );
		}
	}

	return (
		<Fragment>
			<BlockControls
				key={ className + '-previews' }
			>
				<ToolbarGroup>
					<ToolbarButton
						label="Preview focus items"
						isActive={ previewFocusItems }
						onClick={ previewFocusItemsState }
					>
						<span
							style={ { padding: '0 10px', display: 'inline-flex' } }
						>{ 'Preview focus items' }</span>
					</ToolbarButton>
					<ToolbarButton
						label="Preview inline items"
						isActive={ previewInlineItems }
						onClick={ previewInlineItemsState }
					>
						<span
							style={ { padding: '0 10px', display: 'inline-flex' } }
						>{ 'Preview inline items' }</span>
					</ToolbarButton>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls
				key={ className + '-inspector' }
			>
				<FormControls attributes={ attributes } setAttributes={ setAttributes } taxonomies={ taxonomies } settingsPageLink={ settingsPageLink }/>

				<WooCommerceControls attributes={ attributes } setAttributes={ setAttributes } />

			</InspectorControls>
			<Disabled>
				<ServerSideRender
					block="jet-search/search-suggestions"
					httpMethod="POST"
					attributes={ attributes }
					urlQueryArgs={ {
						previewFocusItems:        previewFocusItems,
						previewFocusItemsNumber:  attributes.search_suggestions_list_on_focus_quantity,
						previewFocusManualItems:  attributes.search_suggestions_list_on_focus_manual,
						previewInlineItems:       previewInlineItems,
						previewInlineItemsNumber: attributes.search_suggestions_list_inline_quantity,
						previewInlineManualItems: attributes.search_suggestions_list_inline_manual,
					} }
				/>
			</Disabled>
		</Fragment>
	);
}

export default searchSuggestions;