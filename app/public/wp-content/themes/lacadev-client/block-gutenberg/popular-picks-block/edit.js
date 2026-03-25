import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	useBlockEditContext,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	CheckboxControl,
	ColorPalette,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import previewImage from './preview.png';

// ── Inline Styles (Editor preview only) ──────────────────────────────────────
const S = {
	section: {
		padding: '4rem 2rem',
	},
	inner: {
		maxWidth: '1536px',
		margin: '0 auto',
	},
	heading: {
		fontFamily: 'var(--font-headline, serif)',
		fontSize: '3rem',
		fontWeight: '700',
		textAlign: 'center',
		marginBottom: '2rem',
	},
	tabRow: {
		display: 'flex',
		justifyContent: 'center',
		gap: '2rem',
		marginBottom: '3rem',
		flexWrap: 'wrap',
	},
	tabActive: {
		fontSize: '0.72rem',
		fontWeight: '700',
		letterSpacing: '0.2em',
		textTransform: 'uppercase',
		borderBottom: '2px solid currentColor',
		paddingBottom: '0.5rem',
		cursor: 'pointer',
		background: 'none',
		border: 'none',
		borderBottom: '2px solid #1c1917',
		color: '#1c1917',
	},
	grid: {
		display: 'grid',
		gridTemplateColumns: 'repeat(4, 1fr)',
		gap: '2.5rem',
	},
	card: {
		cursor: 'default',
	},
	imgWrap: {
		position: 'relative',
		aspectRatio: '3/4',
		borderRadius: '0.5rem',
		overflow: 'hidden',
		marginBottom: '1.5rem',
		backgroundColor: '#f5f4f0',
	},
	img: {
		width: '100%',
		height: '100%',
		objectFit: 'cover',
		display: 'block',
	},
	imgPlaceholder: {
		width: '100%',
		height: '100%',
		display: 'flex',
		alignItems: 'center',
		justifyContent: 'center',
		fontSize: '3rem',
		color: '#ccc',
		backgroundColor: '#f5f4f0',
	},
	productName: {
		fontFamily: 'var(--font-headline, serif)',
		fontSize: '1.25rem',
		marginBottom: '0.25rem',
	},
	price: {
		color: 'var(--color-on-surface-variant, #6b7280)',
		fontSize: '0.95rem',
	},
	placeholder: {
		padding: '3rem',
		textAlign: 'center',
		border: '2px dashed #ccc',
		borderRadius: '0.75rem',
		color: '#888',
	},
};

// ── Format WC price ───────────────────────────────────────────────────────────
function formatPrice( product ) {
	if ( product?.price_html ) {
		// strip HTML tags
		return product.price_html.replace( /<[^>]+>/g, '' );
	}
	if ( product?.price ) return '$' + parseFloat( product.price ).toFixed( 2 );
	return '';
}

// ── Product Card Preview ──────────────────────────────────────────────────────
function ProductCard( { product } ) {
	const img = product?.images?.[ 0 ]?.src || null;
	const name = product?.name || 'Sản phẩm';
	const price = formatPrice( product );

	return (
		<div style={ S.card }>
			<div style={ S.imgWrap }>
				{ img
					? <img src={ img } alt={ name } style={ S.img } />
					: <div style={ S.imgPlaceholder }>🛋</div>
				}
			</div>
			<div>
				<h3 style={ S.productName }>{ name }</h3>
				<p style={ S.price }>{ price }</p>
			</div>
		</div>
	);
}

// ── Main Component ────────────────────────────────────────────────────────────
export default function Edit( { attributes, setAttributes } ) {
	const {
		sectionTitle,
		numberOfProducts,
		orderby,
		productCategoryIds,
		showCategoryFilter,
		backgroundColor,
		containerLayout,
	} = attributes;

	// ── Block Preview (Inserter hover) ────────────────────────────────────────
	const { __unstableIsPreviewMode } = useBlockEditContext();
	const isPreview = ( __unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
	if ( isPreview ) {
		return (
			<div style={ { width: '100%', lineHeight: 0 } }>
				<img
					src={ previewImage }
					alt={ __( 'Popular Picks Preview', 'laca' ) }
					style={ { width: '100%', height: 'auto', display: 'block' } }
				/>
			</div>
		);
	}

	// ── Fetch WC Categories ───────────────────────────────────────────────────
	const productCategories = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'product_cat', {
			per_page: 50,
			hide_empty: true,
		} ) || [];
	}, [] );

	// ── Fetch Products ────────────────────────────────────────────────────────
	const products = useSelect( ( select ) => {
		const query = {
			per_page: numberOfProducts,
			orderby,
			order: orderby === 'price' ? 'asc' : 'desc',
			status: 'publish',
		};
		if ( productCategoryIds.length > 0 ) {
			query.product_cat = productCategoryIds.join( ',' );
		}
		return select( 'core' ).getEntityRecords( 'postType', 'product', query );
	}, [ numberOfProducts, orderby, productCategoryIds ] );

	const isLoading = products === undefined;

	// ── Toggle category ───────────────────────────────────────────────────────
	const toggleCategory = ( id ) => {
		const next = productCategoryIds.includes( id )
			? productCategoryIds.filter( ( c ) => c !== id )
			: [ ...productCategoryIds, id ];
		setAttributes( { productCategoryIds: next } );
	};

	const blockProps = useBlockProps( {
		style: {
			...S.section,
			backgroundColor: backgroundColor || undefined,
		},
	} );

	const innerMaxWidth = containerLayout === 'container-fluid' ? '100%' : '1536px';

	return (
		<>
			{ /* ── Inspector Controls ── */ }
			<InspectorControls>
				<PanelBody title={ __( 'Nội dung', 'laca' ) } initialOpen={ true }>
					<RangeControl
						label={ __( 'Số sản phẩm hiển thị', 'laca' ) }
						value={ numberOfProducts }
						onChange={ ( val ) => setAttributes( { numberOfProducts: val } ) }
						min={ 2 }
						max={ 12 }
						step={ 1 }
					/>
					<SelectControl
						label={ __( 'Sắp xếp theo', 'laca' ) }
						value={ orderby }
						options={ [
							{ label: __( 'Mới nhất', 'laca' ), value: 'date' },
							{ label: __( 'Phổ biến nhất', 'laca' ), value: 'popularity' },
							{ label: __( 'Giá tăng dần', 'laca' ), value: 'price' },
							{ label: __( 'Ngẫu nhiên', 'laca' ), value: 'rand' },
							{ label: __( 'Thứ tự menu', 'laca' ), value: 'menu_order' },
						] }
						onChange={ ( val ) => setAttributes( { orderby: val } ) }
					/>
					<ToggleControl
						label={ __( 'Hiển thị tab lọc danh mục', 'laca' ) }
						checked={ showCategoryFilter }
						onChange={ ( val ) => setAttributes( { showCategoryFilter: val } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Lọc danh mục', 'laca' ) } initialOpen={ false }>
					<p style={ { fontSize: '11px', color: '#666', margin: '0 0 8px', fontStyle: 'italic' } }>
						{ __( 'Để trống = hiển thị tất cả danh mục', 'laca' ) }
					</p>
					{ ! productCategories.length && <Spinner /> }
					{ productCategories.map( ( cat ) => (
						<CheckboxControl
							key={ cat.id }
							label={ `${ cat.name } (${ cat.count })` }
							checked={ productCategoryIds.includes( cat.id ) }
							onChange={ () => toggleCategory( cat.id ) }
						/>
					) ) }
				</PanelBody>

				<PanelBody title={ __( 'Layout & Màu nền', 'laca' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Container', 'laca' ) }
						value={ containerLayout }
						options={ [
							{ label: __( 'Boxed (max-w-screen-2xl)', 'laca' ), value: 'container' },
							{ label: __( 'Full Width', 'laca' ), value: 'container-fluid' },
						] }
						onChange={ ( val ) => setAttributes( { containerLayout: val } ) }
					/>
					<p style={ { fontSize: '11px', color: '#666', margin: '8px 0 4px' } }>
						{ __( 'Màu nền section', 'laca' ) }
					</p>
					<ColorPalette
						value={ backgroundColor }
						onChange={ ( val ) => setAttributes( { backgroundColor: val || '' } ) }
					/>
				</PanelBody>
			</InspectorControls>

			{ /* ── Editor Canvas ── */ }
			<section { ...blockProps }>
				<div style={ { ...S.inner, maxWidth: innerMaxWidth } }>

					{ /* Title */ }
					<RichText
						tagName="h2"
						style={ S.heading }
						value={ sectionTitle }
						onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
						placeholder={ __( 'Popular Picks', 'laca' ) }
					/>

					{ /* Filter Tabs (editor preview — static) */ }
					{ showCategoryFilter && productCategories.length > 0 && (
						<div style={ S.tabRow }>
							<span style={ S.tabActive }>
								{ __( 'All Items', 'laca' ) }
							</span>
							{ productCategories.slice( 0, 5 ).map( ( cat ) => (
								<span
									key={ cat.id }
									style={ {
										...S.tabActive,
										borderBottom: '2px solid transparent',
										fontWeight: '400',
										color: '#6b7280',
									} }
								>
									{ cat.name }
								</span>
							) ) }
						</div>
					) }

					{ /* Product Grid */ }
					{ isLoading && (
						<div style={ { textAlign: 'center', padding: '3rem' } }>
							<Spinner />
							<p>{ __( 'Đang tải sản phẩm...', 'laca' ) }</p>
						</div>
					) }

					{ ! isLoading && ( ! products || products.length === 0 ) && (
						<div style={ S.placeholder }>
							<p>{ __( 'Chưa có sản phẩm. Hãy kiểm tra WooCommerce đã được kích hoạt và có sản phẩm published.', 'laca' ) }</p>
						</div>
					) }

					{ ! isLoading && products && products.length > 0 && (
						<div style={ { ...S.grid, gridTemplateColumns: `repeat(${ Math.min( numberOfProducts, 4 ) }, 1fr)` } }>
							{ products.map( ( product ) => (
								<ProductCard key={ product.id } product={ product } />
							) ) }
						</div>
					) }

				</div>
			</section>
		</>
	);
}
