import SearchInputControls from './components/ajax-search/SearchInputControls';
import SearchSubmitControls from './components/ajax-search/SearchSubmitControls';
import SearchQueryControls from './components/ajax-search/SearchQueryControls';
import SearchQueryIncExcControls from './components/ajax-search/SearchQueryIncExcControls';
import ResultsAreaControls from './components/ajax-search/ResultsAreaControls';
import NotificationsControls from './components/ajax-search/NotificationsControls';
import CustomFieldsControls from './components/ajax-search/CustomFieldsControls';

const {
	InspectorControls,
} = wp.blockEditor;

const { __ } = wp.i18n;

const {
	ToolbarGroup,
	ToolbarButton,
	Disabled,
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

const ajaxSearch = function( props ) {

	const {
		className,
		attributes,
		setAttributes,
	} = props;

	const [ previewResults, setPreviewResults ] = useState( false );

	const taxonomies     = window.JetSearchData.taxonomiesList;
	const postTypes      = window.JetSearchData.postTypesList;
	const thumbSizes     = window.JetSearchData.thumbSizes;
	const placeholderImg = window.JetSearchData.placeholderImgUrl;
	const arrowsTypeList = window.JetSearchData.arrowsType;
	const metaCallbacks  = window.JetSearchData.metaCallbacks;

	if ( ! attributes.thumbnail_placeholder['url'] ) {
		setAttributes({ thumbnail_placeholder: { url: placeholderImg } });
	}

	const multipleSelector = ( list, controlName ) => {
		let selectedElements = [];

		list.forEach( ( element, i = 1 ) => {
			selectedElements[i] = element.value;
			i++;
		} );

		props.setAttributes({ [controlName]: selectedElements });
		props.setAttributes({ [controlName + '_list']: list });
	}

	return (
		<Fragment>
			<BlockControls
				key={ className + '-preview-results' }
			>
				<ToolbarGroup>
					<ToolbarButton
						label="Preview results"
						isActive={ previewResults }
						onClick={ () => {
							setPreviewResults( ! previewResults );
						} }
					>
						<span
							style={ { padding: '0 10px', display: 'inline-flex' } }
						>{ 'Preview results' }</span>
					</ToolbarButton>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls
				key={ className + '-inspector' }
			>
				<SearchInputControls attributes={ attributes } setAttributes={ setAttributes } taxonomies={ taxonomies }/>

				<SearchSubmitControls attributes={ attributes } setAttributes={ setAttributes } />

				<SearchQueryControls attributes={ attributes } setAttributes={ setAttributes } postTypes={ postTypes } multipleSelector={ multipleSelector } taxonomies={ taxonomies }/>

				{ ! attributes.current_query &&
					<SearchQueryIncExcControls attributes={ attributes } setAttributes={ setAttributes } multipleSelector={ multipleSelector } />
				}

				<ResultsAreaControls attributes={ attributes } setAttributes={ setAttributes } thumbSizes={ thumbSizes } arrowsTypeList={ arrowsTypeList } />

				<CustomFieldsControls attributes={ attributes } setAttributes={ setAttributes } metaCallbacks={ metaCallbacks } />

				<NotificationsControls attributes={ attributes } setAttributes={ setAttributes } />

			</InspectorControls>
			<Disabled>
				<ServerSideRender
					block="jet-search/ajax-search"
					httpMethod="POST"
					attributes={ attributes }
					urlQueryArgs={ {
						previewResults: previewResults,
					} }
				/>
			</Disabled>
		</Fragment>
	);
}

export default ajaxSearch;