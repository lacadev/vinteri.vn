import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	PanelColorSettings,
	useBlockEditContext,
} from '@wordpress/block-editor';
import previewImage from './preview.png';
import {
	PanelBody,
	TextControl,
	SelectControl,
	ToggleControl,
	CheckboxControl,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

// ── Inline Styles (editor preview) ──────────────────────────────────────────
const S = {
	wrap: { padding: '8rem 2rem', overflow: 'hidden' },
	inner: { maxWidth: '1536px', margin: '0 auto' },
	header: { display: 'flex', justifyContent: 'space-between', alignItems: 'flex-end', marginBottom: '3rem' },
	headerLeft: {},
	subTitle: {
		display: 'block',
		fontSize: '0.75rem',
		fontWeight: '700',
		letterSpacing: '0.2em',
		textTransform: 'uppercase',
		color: 'var(--color-secondary, #6b7280)',
		marginBottom: '0.5rem',
	},
	title: {
		fontFamily: 'var(--font-headline, inherit)',
		fontSize: 'clamp(1.8rem, 3vw, 2.5rem)',
		fontWeight: '600',
		margin: 0,
	},
	browseLink: {
		fontSize: '0.75rem',
		fontWeight: '500',
		letterSpacing: '0.1em',
		textTransform: 'uppercase',
		color: 'var(--color-on-surface-variant, #6b7280)',
		borderBottom: '1px solid var(--color-outline-variant, #ddd)',
		paddingBottom: '4px',
		textDecoration: 'none',
		whiteSpace: 'nowrap',
	},
	grid: {
		display: 'grid',
		gridTemplateColumns: 'repeat(6, 1fr)',
		gap: '1.5rem',
	},
	item: { cursor: 'pointer' },
	imgWrap: {
		aspectRatio: '4 / 5',
		borderRadius: '0.75rem',
		overflow: 'hidden',
		backgroundColor: 'var(--color-surface-container-low, #f5f5f5)',
		marginBottom: '1rem',
	},
	img: { width: '100%', height: '100%', objectFit: 'cover', display: 'block' },
	imgPlaceholder: {
		width: '100%',
		height: '100%',
		display: 'flex',
		alignItems: 'center',
		justifyContent: 'center',
		backgroundColor: 'var(--color-surface-container-low, #f0f0f0)',
		fontSize: '2rem',
	},
	termName: {
		fontFamily: 'var(--font-headline, inherit)',
		fontSize: '1.1rem',
		fontWeight: '500',
		margin: '0 0 0.25rem',
	},
	termCount: {
		fontSize: '0.72rem',
		fontWeight: '600',
		letterSpacing: '0.15em',
		textTransform: 'uppercase',
		color: 'var(--color-on-surface-variant, #6b7280)',
	},
	placeholder: {
		padding: '3rem',
		textAlign: 'center',
		border: '2px dashed #ccc',
		borderRadius: '0.75rem',
		color: '#888',
	},
};

// ── Helpers ──────────────────────────────────────────────────────────────────
function getTermImage( term ) {
	// Primary: REST field registered by lacadev_register_term_image_rest_field (PHP)
	// Supports WooCommerce thumbnail_id, ACF, custom term_image_url meta
	if ( term?.image_url ) return term.image_url;

	// Fallback: embedded featured media (if _embed requested)
	return term?._embedded?.[ 'wp:featuredmedia' ]?.[ 0 ]?.source_url || null;
}

// ── Main Component ───────────────────────────────────────────────────────────
export default function Edit( { attributes, setAttributes } ) {
	const { subTitle, title, browseAllText, browseAllUrl, postType, taxonomy, termIds, backgroundColor, isFullWidth } = attributes;

	// ── Block Preview (Inserter hover) ──────────────────────────────────────
	// WP 6.3+: __unstableIsPreviewMode via context. Fallback: __isPreview injected via example.attributes.
	const blockEditContext = useBlockEditContext();
	const isPreview = ( blockEditContext.__unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
	if ( isPreview ) {
		return (
			<div style={ { width: '100%', aspectRatio: '16/9', overflow: 'hidden', lineHeight: 0 } }>
				<img
					src={ previewImage }
					alt={ __( 'Categories Gallery Preview', 'laca' ) }
					style={ { width: '100%', height: '100%', objectFit: 'cover', display: 'block' } }
				/>
			</div>
		);
	}

	// ── Fetch public post types ──────────────────────────────────────────────
	const [ postTypes, setPostTypes ] = useState( [] );
	useEffect( () => {
		apiFetch( { path: '/wp/v2/types?per_page=100' } ).then( ( res ) => {
			const list = Object.values( res )
				.filter( ( pt ) => pt.rest_base && pt.slug !== 'attachment' )
				.map( ( pt ) => ({ label: pt.name, value: pt.slug }) );
			setPostTypes( list );
		} );
	}, [] );

	// ── Fetch taxonomies for selected post type ──────────────────────────────
	const [ taxonomies, setTaxonomies ] = useState( [] );
	useEffect( () => {
		if ( ! postType ) return;
		apiFetch( { path: `/wp/v2/taxonomies?type=${ postType }` } ).then( ( res ) => {
			const list = Object.values( res ).map( ( t ) => ({ label: t.name, value: t.slug }) );
			setTaxonomies( list );
			// Reset taxonomy if it's no longer valid for the new post type
			if ( list.length > 0 && ! list.find( ( t ) => t.value === taxonomy ) ) {
				setAttributes( { taxonomy: list[ 0 ].value, termIds: [] } );
			}
		} );
	}, [ postType ] ); // eslint-disable-line react-hooks/exhaustive-deps

	// ── Fetch terms for selected taxonomy ───────────────────────────────────
	const terms = useSelect(
		( select ) => {
			if ( ! taxonomy ) return [];
			return select( 'core' ).getEntityRecords( 'taxonomy', taxonomy, { per_page: 50, _embed: true } ) || [];
		},
		[ taxonomy ]
	);

	// Toggle term selection
	const toggleTerm = ( id ) => {
		const next = termIds.includes( id )
			? termIds.filter( ( t ) => t !== id )
			: [ ...termIds, id ];
		setAttributes( { termIds: next } );
	};

	// Determine which terms to preview
	const previewTerms = terms.length
		? ( termIds.length ? terms.filter( ( t ) => termIds.includes( t.id ) ) : terms.slice( 0, 6 ) )
		: null;

	// Block props
	const blockProps = useBlockProps( {
		style: {
			...S.wrap,
			...( backgroundColor ? { backgroundColor } : {} ),
		},
	} );

	const innerMaxWidth = isFullWidth ? '100%' : '1536px';

	return (
		<>
			{ /* ── Inspector Controls ── */ }
			<InspectorControls>
				<PanelColorSettings
					title={ __( 'Màu nền', 'laca' ) }
					initialOpen={ true }
					colorSettings={ [
						{
							value: backgroundColor,
							onChange: ( val ) => setAttributes( { backgroundColor: val || '' } ),
							label: __( 'Background color', 'laca' ),
						},
					] }
				/>

				<PanelBody title={ __( 'Layout', 'laca' ) } initialOpen={ false }>
					<ToggleControl
						label={ __( 'Full Width (không có container)', 'laca' ) }
						checked={ isFullWidth }
						onChange={ ( val ) => setAttributes( { isFullWidth: val } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Header', 'laca' ) }>
					<TextControl
						label={ __( 'Sub tiêu đề', 'laca' ) }
						value={ subTitle }
						onChange={ ( val ) => setAttributes( { subTitle: val } ) }
					/>
					<TextControl
						label={ __( 'Tiêu đề chính', 'laca' ) }
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
					/>
					<TextControl
						label={ __( 'Text nút "Xem tất cả"', 'laca' ) }
						value={ browseAllText }
						onChange={ ( val ) => setAttributes( { browseAllText: val } ) }
					/>
					<TextControl
						label={ __( 'URL nút "Xem tất cả"', 'laca' ) }
						value={ browseAllUrl }
						onChange={ ( val ) => setAttributes( { browseAllUrl: val } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Nguồn dữ liệu', 'laca' ) }>
					<SelectControl
						label={ __( 'Post Type', 'laca' ) }
						value={ postType }
						options={ postTypes }
						onChange={ ( val ) => setAttributes( { postType: val, termIds: [] } ) }
					/>
					<SelectControl
						label={ __( 'Taxonomy', 'laca' ) }
						value={ taxonomy }
						options={ taxonomies }
						onChange={ ( val ) => setAttributes( { taxonomy: val, termIds: [] } ) }
					/>

					<p style={ { fontSize: '11px', color: '#666', margin: '8px 0', fontStyle: 'italic' } }>
						{ __( 'Để trống = hiển thị tất cả. Tick để chọn danh mục cụ thể:', 'laca' ) }
					</p>

					{ ! terms && <Spinner /> }

					{ terms && terms.map( ( term ) => {
						const img = getTermImage( term );
						return (
							<div key={ term.id } style={ { display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '6px' } }>
								{ img && (
									<img
										src={ img }
										alt={ term.name }
										style={ { width: '32px', height: '32px', objectFit: 'cover', borderRadius: '4px', flexShrink: 0 } }
									/>
								) }
								{ ! img && (
									<div style={ { width: '32px', height: '32px', background: '#eee', borderRadius: '4px', flexShrink: 0 } } />
								) }
								<CheckboxControl
									label={ `${ term.name } (${ term.count })` }
									checked={ termIds.includes( term.id ) }
									onChange={ () => toggleTerm( term.id ) }
								/>
							</div>
						);
					} ) }
				</PanelBody>
			</InspectorControls>

			{ /* ── Editor Canvas Preview ── */ }
			<section { ...blockProps }>
				<div style={ { ...S.inner, maxWidth: innerMaxWidth } }>

					{ /* Header */ }
					<div style={ S.header }>
						<div style={ S.headerLeft }>
							<RichText
								tagName="span"
								style={ S.subTitle }
								value={ subTitle }
								onChange={ ( v ) => setAttributes( { subTitle: v } ) }
								placeholder={ __( 'Departments…', 'laca' ) }
							/>
							<RichText
								tagName="h2"
								style={ S.title }
								value={ title }
								onChange={ ( v ) => setAttributes( { title: v } ) }
								placeholder={ __( 'Tiêu đề…', 'laca' ) }
							/>
						</div>
						{ browseAllText && (
							<span style={ S.browseLink }>{ browseAllText }</span>
						) }
					</div>

					{ /* Grid preview */ }
					{ ! previewTerms && (
						<div style={ S.placeholder }>
							<p>{ __( 'Chọn Post Type và Taxonomy ở sidebar để hiển thị danh mục.', 'laca' ) }</p>
						</div>
					) }

					{ previewTerms && previewTerms.length === 0 && (
						<div style={ S.placeholder }>
							<p>{ __( 'Chưa có danh mục nào. Hãy tạo taxonomy terms trước.', 'laca' ) }</p>
						</div>
					) }

					{ previewTerms && previewTerms.length > 0 && (
						<div style={ S.grid }>
							{ previewTerms.map( ( term ) => {
								const img = getTermImage( term );
								// Try embedded featured image
								const embedImg = term?._embedded?.['wp:featuredmedia']?.[ 0 ]?.source_url || null;
								const finalImg = img || embedImg;

								return (
									<div key={ term.id } style={ S.item }>
										<div style={ S.imgWrap }>
											{ finalImg ? (
												<img src={ finalImg } alt={ term.name } style={ S.img } />
											) : (
												<div style={ S.imgPlaceholder }>🖼</div>
											) }
										</div>
										<p style={ S.termName }>{ term.name }</p>
										<p style={ S.termCount }>{ term.count } { __( 'Items', 'laca' ) }</p>
									</div>
								);
							} ) }
						</div>
					) }
				</div>
			</section>
		</>
	);
}
