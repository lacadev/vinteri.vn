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
	RadioControl,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

// ── Inline Styles ────────────────────────────────────────────────────────────
const S = {
	wrap: { padding: '4rem 2rem', overflow: 'hidden' },
	inner: { maxWidth: '1536px', margin: '0 auto' },
	grid: {
		display: 'grid',
		gridTemplateColumns: 'repeat(12, 1fr)',
		gridTemplateRows: 'repeat(2, 600px)',
		gap: '2rem',
	},
	// Main panel: 8 cols / 2 rows
	mainPanel: {
		gridColumn: 'span 8',
		gridRow: 'span 2',
		position: 'relative',
		borderRadius: '1rem',
		overflow: 'hidden',
	},
	// Small panel: 4 cols / 1 row
	smallPanel: {
		gridColumn: 'span 4',
		gridRow: 'span 1',
		position: 'relative',
		borderRadius: '1rem',
		overflow: 'hidden',
	},
	panelImg: {
		width: '100%',
		height: '100%',
		objectFit: 'cover',
		display: 'block',
		transition: 'transform 1s',
	},
	panelOverlay: {
		position: 'absolute',
		inset: 0,
		background: 'rgba(28, 25, 23, 0.2)',
	},
	mainContent: {
		position: 'absolute',
		bottom: '4rem',
		left: '4rem',
	},
	curationLabel: {
		display: 'block',
		fontSize: '0.72rem',
		fontWeight: '500',
		letterSpacing: '0.2em',
		textTransform: 'uppercase',
		color: '#ffffff',
		marginBottom: '0.5rem',
	},
	mainTitle: {
		fontFamily: 'var(--font-headline, inherit)',
		fontSize: 'clamp(2.5rem, 4vw, 3.75rem)',
		fontWeight: '700',
		color: '#ffffff',
		margin: '0 0 2rem',
		lineHeight: 1.1,
	},
	ctaBtn: {
		background: '#ffffff',
		color: '#1c1917',
		padding: '1rem 2rem',
		borderRadius: '9999px',
		fontSize: '0.72rem',
		fontWeight: '500',
		letterSpacing: '0.15em',
		textTransform: 'uppercase',
		border: 'none',
		cursor: 'pointer',
		display: 'inline-block',
	},
	smallContent: {
		position: 'absolute',
		inset: 0,
		display: 'flex',
		flexDirection: 'column',
		alignItems: 'center',
		justifyContent: 'center',
		textAlign: 'center',
		padding: '2rem',
	},
	smallTitle: {
		fontFamily: 'var(--font-headline, inherit)',
		fontSize: '1.875rem',
		fontWeight: '700',
		color: '#ffffff',
		margin: '0 0 1rem',
	},
	smallLink: {
		fontSize: '0.72rem',
		fontWeight: '500',
		letterSpacing: '0.15em',
		textTransform: 'uppercase',
		color: '#ffffff',
		borderBottom: '1px solid #ffffff',
		paddingBottom: '2px',
	},
	placeholder: {
		padding: '3rem',
		textAlign: 'center',
		border: '2px dashed #ccc',
		borderRadius: '0.75rem',
		color: '#888',
	},
	imgPlaceholder: {
		width: '100%',
		height: '100%',
		display: 'flex',
		alignItems: 'center',
		justifyContent: 'center',
		backgroundColor: 'var(--color-surface-container-low, #e8e8e8)',
		fontSize: '3rem',
	},
};

// ── Helpers ──────────────────────────────────────────────────────────────────
function getTermImage( term ) {
	// Primary: REST field `image_url` registered by lacadev_register_term_image_rest_field (PHP)
	// Reads from: WooCommerce thumbnail_id, ACF term_image, or custom term_image_url meta
	if ( term?.image_url ) return term.image_url;
	return null;
}

function TermPanel( { term, isMain, curationLabel, setCurationLabel, ctaText, setCtaText } ) {
	const img = getTermImage( term );
	if ( isMain ) {
		return (
			<div style={ S.mainPanel }>
				{ img ? (
					<img src={ img } alt={ term.name } style={ S.panelImg } />
				) : (
					<div style={ S.imgPlaceholder }>🏠</div>
				) }
				<div style={ S.panelOverlay } />
				<div style={ S.mainContent }>
					<RichText
						tagName="span"
						style={ S.curationLabel }
						value={ curationLabel }
						onChange={ setCurationLabel }
						placeholder={ __( 'Curation No. 04', 'laca' ) }
					/>
					<h3 style={ S.mainTitle }>{ term.name }</h3>
					<button style={ S.ctaBtn } type="button">
						<RichText
							tagName="span"
							value={ ctaText }
							onChange={ setCtaText }
							placeholder={ __( 'Shop The Collection', 'laca' ) }
						/>
					</button>
				</div>
			</div>
		);
	}
	return (
		<div style={ S.smallPanel }>
			{ img ? (
				<img src={ img } alt={ term.name } style={ S.panelImg } />
			) : (
				<div style={ S.imgPlaceholder }>🛋</div>
			) }
			<div style={ { ...S.panelOverlay, background: 'rgba(28,25,23,0.1)' } } />
			<div style={ S.smallContent }>
				<h3 style={ S.smallTitle }>{ term.name }</h3>
				<span style={ S.smallLink }>{ __( 'Explore Collections', 'laca' ) }</span>
			</div>
		</div>
	);
}

// ── Main Component ───────────────────────────────────────────────────────────
export default function Edit( { attributes, setAttributes } ) {
	const { postType, taxonomy, termIds, mainTermId, curationLabel, ctaText, backgroundColor, isFullWidth } = attributes;

	// ── Block Preview (Inserter hover) ───────────────────────────────────────
	// Dual-detect: WP 6.3+ context API → fallback via attributes.__isPreview
	const { __unstableIsPreviewMode } = useBlockEditContext();
	const isPreview = ( __unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
	if ( isPreview ) {
		return (
			<div style={ { width: '100%', lineHeight: 0 } }>
				<img
					src={ previewImage }
					alt={ __( 'Bento Room Gallery Preview', 'laca' ) }
					style={ { width: '100%', height: 'auto', display: 'block' } }
				/>
			</div>
		);
	}

	// ── Fetch post types ─────────────────────────────────────────────────────
	const [ postTypes, setPostTypes ] = useState( [] );
	useEffect( () => {
		apiFetch( { path: '/wp/v2/types?per_page=100' } ).then( ( res ) => {
			const list = Object.values( res )
				.filter( ( pt ) => pt.rest_base && pt.slug !== 'attachment' )
				.map( ( pt ) => ({ label: pt.name, value: pt.slug }) );
			setPostTypes( list );
		} );
	}, [] );

	// ── Fetch taxonomies for post type ───────────────────────────────────────
	const [ taxonomies, setTaxonomies ] = useState( [] );
	useEffect( () => {
		if ( ! postType ) return;
		apiFetch( { path: `/wp/v2/taxonomies?type=${ postType }` } ).then( ( res ) => {
			const list = Object.values( res ).map( ( t ) => ({ label: t.name, value: t.slug }) );
			setTaxonomies( list );
			if ( list.length > 0 && ! list.find( ( t ) => t.value === taxonomy ) ) {
				setAttributes( { taxonomy: list[ 0 ].value, termIds: [], mainTermId: 0 } );
			}
		} );
	}, [ postType ] ); // eslint-disable-line react-hooks/exhaustive-deps

	// ── Fetch terms ──────────────────────────────────────────────────────────
	const terms = useSelect(
		( select ) => {
			if ( ! taxonomy ) return [];
			return select( 'core' ).getEntityRecords( 'taxonomy', taxonomy, { per_page: 50 } ) || [];
		},
		[ taxonomy ]
	);

	const toggleTerm = ( id ) => {
		const next = termIds.includes( id ) ? termIds.filter( ( t ) => t !== id ) : [ ...termIds, id ];
		setAttributes( { termIds: next } );
	};

	// Determine preview terms (max 3: 1 main + 2 small)
	const selectedTerms = terms.length
		? ( termIds.length ? terms.filter( ( t ) => termIds.includes( t.id ) ) : terms.slice( 0, 3 ) )
		: [];

	const mainTerm = mainTermId ? selectedTerms.find( ( t ) => t.id === mainTermId ) : selectedTerms[ 0 ];
	const smallTerms = selectedTerms.filter( ( t ) => t.id !== mainTerm?.id ).slice( 0, 2 );

	const blockProps = useBlockProps( {
		style: { ...S.wrap, ...( backgroundColor ? { backgroundColor } : {} ) },
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

				<PanelBody title={ __( 'Main Panel', 'laca' ) }>
					<TextControl
						label={ __( 'Curation Label (phía trên tiêu đề)', 'laca' ) }
						value={ curationLabel }
						onChange={ ( val ) => setAttributes( { curationLabel: val } ) }
					/>
					<TextControl
						label={ __( 'Text nút CTA', 'laca' ) }
						value={ ctaText }
						onChange={ ( val ) => setAttributes( { ctaText: val } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Nguồn dữ liệu', 'laca' ) }>
					<SelectControl
						label={ __( 'Post Type', 'laca' ) }
						value={ postType }
						options={ postTypes }
						onChange={ ( val ) => setAttributes( { postType: val, termIds: [], mainTermId: 0 } ) }
					/>
					<SelectControl
						label={ __( 'Taxonomy', 'laca' ) }
						value={ taxonomy }
						options={ taxonomies }
						onChange={ ( val ) => setAttributes( { taxonomy: val, termIds: [], mainTermId: 0 } ) }
					/>
					<p style={ { fontSize: '11px', color: '#666', margin: '8px 0', fontStyle: 'italic' } }>
						{ __( 'Chọn 1–3 danh mục. Danh mục đầu tiên (hoặc "Main") = panel lớn bên trái.', 'laca' ) }
					</p>
					{ ! terms && <Spinner /> }
					{ terms && terms.map( ( term ) => (
						<div key={ term.id } style={ { marginBottom: '6px' } }>
							<CheckboxControl
								label={ `${ term.name } (${ term.count })` }
								checked={ termIds.includes( term.id ) }
								onChange={ () => toggleTerm( term.id ) }
							/>
						</div>
					) ) }

					{ selectedTerms.length > 1 && (
						<>
							<hr style={ { margin: '12px 0' } } />
							<p style={ { fontSize: '12px', fontWeight: '600', margin: '0 0 8px' } }>
								{ __( 'Chọn panel CHÍNH (ô lớn):', 'laca' ) }
							</p>
							<RadioControl
								selected={ String( mainTermId || selectedTerms[ 0 ]?.id || 0 ) }
								options={ selectedTerms.map( ( t ) => ({ label: t.name, value: String( t.id ) }) ) }
								onChange={ ( val ) => setAttributes( { mainTermId: Number( val ) } ) }
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>

			{ /* ── Editor Canvas Preview ── */ }
			<section { ...blockProps }>
				<div style={ { ...S.inner, maxWidth: innerMaxWidth } }>
					{ selectedTerms.length === 0 && (
						<div style={ S.placeholder }>
							<p>{ __( 'Chọn Post Type + Taxonomy + ít nhất 1 danh mục ở sidebar.', 'laca' ) }</p>
						</div>
					) }

					{ selectedTerms.length > 0 && (
						<div style={ S.grid }>
							{ mainTerm && (
								<TermPanel
									term={ mainTerm }
									isMain={ true }
									curationLabel={ curationLabel }
									setCurationLabel={ ( v ) => setAttributes( { curationLabel: v } ) }
									ctaText={ ctaText }
									setCtaText={ ( v ) => setAttributes( { ctaText: v } ) }
								/>
							) }
							{ smallTerms.map( ( term ) => (
								<TermPanel key={ term.id } term={ term } isMain={ false } />
							) ) }
						</div>
					) }
				</div>
			</section>
		</>
	);
}
