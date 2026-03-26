import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	useBlockEditContext,
} from '@wordpress/block-editor';
import previewImage from './preview.png';
import {
	PanelBody,
	TextControl,
	RangeControl,
	SelectControl,
	ToggleControl,
	Spinner,
	ColorPalette,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';

// ── Editor inline styles ───────────────────────────────────────────────────────
const S = {
	wrap: { padding: '2rem', background: '#faf9f7', fontFamily: 'inherit' },
	grid: {
		display: 'grid',
		gridTemplateColumns: '1fr 1fr',
		gap: '1.5rem',
		minHeight: '26rem',
	},
	leftCard: {
		position: 'relative',
		background: 'var(--color-surface-container-low, #f0eeee)',
		borderRadius: '0.5rem',
		overflow: 'hidden',
		display: 'flex',
		alignItems: 'center',
		padding: '3rem',
	},
	rightGrid: {
		display: 'grid',
		gridTemplateRows: '1fr 1fr',
		gap: '1.5rem',
	},
	topCard: {
		background: 'var(--color-surface-container-low, #f0eeee)',
		borderRadius: '0.5rem',
		display: 'flex',
		alignItems: 'center',
		padding: '2rem',
		position: 'relative',
		overflow: 'hidden',
	},
	bottomGrid: {
		display: 'grid',
		gridTemplateColumns: '1fr 1fr',
		gap: '1.5rem',
	},
	smallCard: {
		background: 'var(--color-surface-container-low, #f0eeee)',
		borderRadius: '0.5rem',
		padding: '2rem',
		display: 'flex',
		flexDirection: 'column',
		justifyContent: 'space-between',
	},
	label: {
		fontSize: '0.65rem',
		letterSpacing: '0.2em',
		textTransform: 'uppercase',
		color: 'var(--color-on-surface-variant, #888)',
		display: 'block',
		marginBottom: '0.5rem',
	},
	h2: { fontFamily: 'inherit', fontSize: '1.8rem', margin: '0 0 1.5rem 0', lineHeight: 1.2 },
	h3: { fontFamily: 'inherit', fontSize: '1.4rem', margin: '0 0 1rem 0', lineHeight: 1.2 },
	h4: { fontFamily: 'inherit', fontSize: '1rem', margin: 0, lineHeight: 1.3 },
	btn: {
		display: 'inline-block',
		padding: '0.5rem 1.5rem',
		border: '1px solid #2f3331',
		background: 'transparent',
		fontSize: '0.7rem',
		letterSpacing: '0.15em',
		textTransform: 'uppercase',
		cursor: 'pointer',
	},
	imgRight: {
		position: 'absolute',
		right: 0,
		top: '50%',
		transform: 'translateY(-50%)',
		width: '50%',
		height: '80%',
		objectFit: 'contain',
		mixBlendMode: 'multiply',
	},
	catCount: { fontSize: '0.75rem', color: 'var(--color-on-surface-variant,#888)', marginTop: '0.25rem' },
	spinner: { display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '2rem', color: '#888' },
	catThumb: {
		position: 'absolute',
		right: 0, top: '50%',
		transform: 'translateY(-50%)',
		width: '50%',
		height: '80%',
		objectFit: 'contain',
		mixBlendMode: 'multiply',
	},
};

const ORDERBY_OPTIONS = [
	{ label: __( 'Số sản phẩm (nhiều → ít)', 'laca' ), value: 'count' },
	{ label: __( 'Tên A → Z', 'laca' ), value: 'name' },
	{ label: __( 'Slug', 'laca' ), value: 'slug' },
	{ label: __( 'Thứ tự menu', 'laca' ), value: 'menu_order' },
	{ label: __( 'Ngẫu nhiên', 'laca' ), value: 'rand' },
];

// ── Main Edit Component ────────────────────────────────────────────────────────
export default function Edit( { attributes, setAttributes } ) {
	const {
		featuredCtaText, promoCtaText,
		numberOfCategories, orderby, hideEmpty, parentCategoryId,
		backgroundColor, containerLayout,
		__isPreview,
	} = attributes;

	// ── Preview Mode ────────────────────────────────────────────────────────
	const { __unstableIsPreviewMode } = useBlockEditContext();
	const isPreview = ( __unstableIsPreviewMode ?? false ) || ( __isPreview ?? false );
	if ( isPreview ) {
		return (
			<div style={ { width: '100%', lineHeight: 0 } }>
				<img
					src={ previewImage }
					alt="Block Preview"
					style={ { width: '100%', height: 'auto', display: 'block' } }
				/>
			</div>
		);
	}

	// ── Load all product categories for parent selector ─────────────────────
	const allCategories = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'product_cat', {
			per_page: 100,
			_fields: [ 'id', 'name' ],
			hide_empty: false,
		} );
	}, [] );

	// ── Load preview categories for editor ──────────────────────────────────
	const previewCategories = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'product_cat', {
			per_page: Math.max( 4, numberOfCategories ),
			_fields: [ 'id', 'name', 'description', 'count', 'cat_image_url' ],
			hide_empty: hideEmpty,
			orderby,
			parent: parentCategoryId || undefined,
		} );
	}, [ numberOfCategories, orderby, hideEmpty, parentCategoryId ] );

	const cats = previewCategories || [];
	const c0 = cats[ 0 ];
	const c1 = cats[ 1 ];
	const smallCats = cats.slice( 2 );

	const blockProps = useBlockProps( {
		style: backgroundColor ? { backgroundColor } : {},
	} );

	// Parent category options
	const parentOptions = [
		{ label: __( 'Tất cả (không lọc theo cha)', 'laca' ), value: 0 },
		...( allCategories || [] ).map( ( c ) => ( { label: c.name, value: c.id } ) ),
	];

	// ── Render ──────────────────────────────────────────────────────────────
	return (
		<>
			{ /* ── Inspector Controls ── */ }
			<InspectorControls>

				{ /* Danh mục */ }
				<PanelBody title={ __( '📂 Danh mục WooCommerce', 'laca' ) } initialOpen={ true }>
					<RangeControl
						label={ __( 'Số danh mục hiển thị', 'laca' ) }
						value={ numberOfCategories }
						onChange={ ( v ) => setAttributes( { numberOfCategories: v } ) }
						min={ 3 }
						max={ 10 }
						help={ __( 'Tối thiểu 3 (1 featured + 1 promo + ≥1 small cards).', 'laca' ) }
					/>
					<SelectControl
						label={ __( 'Thứ tự hiển thị', 'laca' ) }
						value={ orderby }
						options={ ORDERBY_OPTIONS }
						onChange={ ( v ) => setAttributes( { orderby: v } ) }
					/>
					<ToggleControl
						label={ __( 'Chỉ hiện danh mục có sản phẩm', 'laca' ) }
						checked={ hideEmpty }
						onChange={ ( v ) => setAttributes( { hideEmpty: v } ) }
					/>
					{ ! allCategories ? (
						<div style={ S.spinner }><Spinner />{ __( 'Đang tải…', 'laca' ) }</div>
					) : (
						<SelectControl
							label={ __( 'Chỉ lấy danh mục con của:', 'laca' ) }
							value={ parentCategoryId }
							options={ parentOptions }
							onChange={ ( v ) => setAttributes( { parentCategoryId: Number( v ) } ) }
						/>
					) }
				</PanelBody>

				{ /* Nút CTA */ }
				<PanelBody title={ __( '🔗 Nút CTA', 'laca' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Text nút — Card lớn', 'laca' ) }
						value={ featuredCtaText }
						onChange={ ( v ) => setAttributes( { featuredCtaText: v } ) }
					/>
					<TextControl
						label={ __( 'Text nút — Card promo', 'laca' ) }
						value={ promoCtaText }
						onChange={ ( v ) => setAttributes( { promoCtaText: v } ) }
					/>
				</PanelBody>

				{ /* Layout & Styles */ }
				<PanelBody title={ __( '🎨 Layout & Styles', 'laca' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Container Layout', 'laca' ) }
						value={ containerLayout }
						options={ [
							{ label: __( 'Boxed (max-width)', 'laca' ), value: 'container' },
							{ label: __( 'Full Width', 'laca' ), value: 'container-fluid' },
						] }
						onChange={ ( v ) => setAttributes( { containerLayout: v } ) }
					/>
					<p>{ __( 'Màu nền section:', 'laca' ) }</p>
					<ColorPalette
						value={ backgroundColor }
						onChange={ ( v ) => setAttributes( { backgroundColor: v || '' } ) }
						clearable
					/>
				</PanelBody>

			</InspectorControls>

			{ /* ── Editor Preview ── */ }
			<section { ...blockProps }>
				<div style={ S.wrap }>
					{ ! previewCategories ? (
						<div style={ S.spinner }><Spinner />{ __( 'Đang tải danh mục…', 'laca' ) }</div>
					) : (
						<div style={ S.grid }>

							{ /* Left — Featured large card */ }
							<div style={ S.leftCard }>
								<div style={ { zIndex: 10, width: '50%' } }>
									<span style={ S.label }>
										{ c0 ? `${ c0.count } sản phẩm` : 'Danh mục' }
									</span>
									<h2 style={ S.h2 }>{ c0 ? c0.name : '(Danh mục 1)' }</h2>
									<button style={ S.btn }>{ featuredCtaText }</button>
								</div>
								<div style={ S.catThumb }>
									{ c0?.cat_image_url
										? <img src={ c0.cat_image_url } alt={ c0?.name ?? '' } style={ { width: '100%', height: '100%', objectFit: 'contain' } } />
										: <div style={ { width: '100%', height: '100%', background: '#e8e8e8', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '3rem' } }>🛍️</div>
									}
								</div>
							</div>

							{ /* Right — split grid */ }
							<div style={ S.rightGrid }>

								{ /* Top promo card */ }
								<div style={ S.topCard }>
									<div style={ { zIndex: 10, width: '50%' } }>
										<span style={ S.label }>
											{ c1 ? `${ c1.count } sản phẩm` : 'Danh mục' }
										</span>
										<h3 style={ S.h3 }>{ c1 ? c1.name : '(Danh mục 2)' }</h3>
										<button style={ S.btn }>{ promoCtaText }</button>
									</div>
									<div style={ { position: 'absolute', right: 0, top: '50%', transform: 'translateY(-50%)', width: '50%', height: '100%', padding: '1rem', overflow: 'hidden' } }>
										{ c1?.cat_image_url
											? <img src={ c1.cat_image_url } alt={ c1?.name ?? '' } style={ { width: '100%', height: '100%', objectFit: 'contain' } } />
											: <div style={ { width: '100%', height: '100%', background: '#e8e8e8', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '2rem' } }>🪑</div>
										}
									</div>
								</div>

								{ /* Bottom small cards */ }
								<div style={ S.bottomGrid }>
									{ ( smallCats.length > 0 ? smallCats : [ null, null ] ).map( ( cat, i ) => (
										<div key={ i } style={ S.smallCard }>
											<div>
												<span style={ S.label }>{ cat ? `${ cat.count } sản phẩm` : 'Danh mục' }</span>
												<h4 style={ S.h4 }>{ cat ? cat.name : `(Danh mục ${ i + 3 })` }</h4>
											</div>
											<div style={ { height: '5rem', borderRadius: '0.25rem', overflow: 'hidden', marginTop: '0.75rem', background: '#e8e8e8', display: 'flex', alignItems: 'center', justifyContent: 'center' } }>
												{ cat?.cat_image_url
													? <img src={ cat.cat_image_url } alt={ cat.name } style={ { width: '100%', height: '100%', objectFit: 'contain' } } />
													: <span style={ { fontSize: '1.5rem' } }>🪑</span>
												}
											</div>
										</div>
									) ) }
								</div>

							</div>
						</div>
					) }
					<p style={ { textAlign: 'center', color: '#aaa', fontSize: '0.75rem', marginTop: '1rem' } }>
						{ __( '⚙️ Cấu hình trong sidebar → Danh mục WooCommerce', 'laca' ) }
					</p>
				</div>
			</section>
		</>
	);
}
