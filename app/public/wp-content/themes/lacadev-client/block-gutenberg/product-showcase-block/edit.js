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
	RadioControl,
	Spinner,
	ColorPalette,
	Button,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

// ── Styles inline cho Editor preview ──────────────────────────────────────────
const S = {
	wrap: { padding: '1.5rem', background: 'var(--wp-admin-theme-color-darker-20,#faf9f7)', fontFamily: 'inherit' },
	header: { display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: '1.5rem' },
	title: { margin: 0, fontFamily: 'inherit', fontSize: '1.6rem' },
	viewAll: { fontSize: '0.75rem', color: '#888', textDecoration: 'none', borderBottom: '1px solid #ccc', lineHeight: 1.8 },
	grid: ( cols ) => ( {
		display: 'grid',
		gridTemplateColumns: `repeat(${ Math.min( cols, 4 ) }, 1fr)`,
		gap: '2rem',
	} ),
	card: {
		cursor: 'pointer',
	},
	imgWrap: {
		aspectRatio: '4/5',
		overflow: 'hidden',
		borderRadius: '0.5rem',
		marginBottom: '1rem',
		background: 'var(--color-surface-container-low,#F5F5F5)',
		display: 'flex',
		alignItems: 'center',
		justifyContent: 'center',
	},
	img: { width: '100%', height: '100%', objectFit: 'cover' },
	placeholder: { fontSize: '2rem', color: '#bbb' },
	cardMeta: { display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' },
	cardTitle: { margin: 0, fontFamily: 'inherit', fontSize: '1rem', fontWeight: '600' },
	cardVariation: { color: '#888', fontSize: '0.8rem', marginTop: '0.25rem' },
	price: { fontSize: '0.9rem', whiteSpace: 'nowrap' },
	spinner: { display: 'flex', alignItems: 'center', gap: '0.5rem', padding: '2rem', color: '#888' },
	hint: { textAlign: 'center', color: '#aaa', fontSize: '0.75rem', marginTop: '1rem' },
	badge: {
		display: 'inline-block',
		margin: '0.25rem 0.25rem 0 0',
		padding: '0.2rem 0.5rem',
		background: '#e8e8e8',
		borderRadius: '0.25rem',
		fontSize: '0.75rem',
		color: '#444',
	},
	searchBox: { position: 'relative', marginBottom: '0.75rem' },
	searchResults: {
		maxHeight: '12rem',
		overflowY: 'auto',
		border: '1px solid #e0e0e0',
		borderRadius: '0.25rem',
		background: '#fff',
	},
	searchItem: {
		display: 'flex',
		alignItems: 'center',
		gap: '0.5rem',
		padding: '0.5rem 0.75rem',
		cursor: 'pointer',
		borderBottom: '1px solid #f0f0f0',
		fontSize: '0.85rem',
	},
	searchItemImg: { width: '2.5rem', height: '2.5rem', objectFit: 'cover', borderRadius: '0.25rem', background: '#f5f5f5', flexShrink: 0 },
};

const AUTO_ORDERBY_OPTIONS = [
	{ label: __( 'Mới nhất', 'laca' ), value: 'date' },
	{ label: __( 'Xem nhiều nhất', 'laca' ), value: 'popularity' },
	{ label: __( 'Ngẫu nhiên', 'laca' ), value: 'rand' },
	{ label: __( 'Giá thấp → cao', 'laca' ), value: 'price' },
	{ label: __( 'Giá cao → thấp', 'laca' ), value: 'price-desc' },
	{ label: __( 'Đánh giá cao nhất', 'laca' ), value: 'rating' },
];

const COLS_OPTIONS = [
	{ label: '2 cột', value: 2 },
	{ label: '3 cột', value: 3 },
	{ label: '4 cột', value: 4 },
];

// ── ProductSearch Component (Manual mode) ──────────────────────────────────────
function ProductSearch( { manualProductIds, setAttributes, selectedProducts } ) {
	const [ searchInput, setSearchInput ] = useState( '' );
	const [ query, setQuery ] = useState( '' );

	const searchResults = useSelect( ( select ) => {
		if ( ! query || query.length < 2 ) {
			return null;
		}
		return select( 'core' ).getEntityRecords( 'postType', 'product', {
			search: query,
			per_page: 12,
			status: 'publish',
			_fields: [ 'id', 'title', 'featured_media', '_links' ],
		} );
	}, [ query ] );

	const handleSearch = ( val ) => {
		setSearchInput( val );
		const trimmed = val.trim();
		if ( trimmed.length >= 2 ) {
			setQuery( trimmed );
		}
	};

	const toggleProduct = ( id ) => {
		const current = manualProductIds || [];
		const updated = current.includes( id )
			? current.filter( ( pid ) => pid !== id )
			: [ ...current, id ];
		setAttributes( { manualProductIds: updated } );
	};

	const selectedIds = manualProductIds || [];

	return (
		<div>
			<p style={ { fontSize: '0.8rem', color: '#666', margin: '0 0 0.5rem' } }>
				{ __( 'Tìm và chọn sản phẩm muốn hiển thị:', 'laca' ) }
			</p>

			{ /* Search input */ }
			<div style={ S.searchBox }>
				<TextControl
					placeholder={ __( 'Nhập tên sản phẩm…', 'laca' ) }
					value={ searchInput }
					onChange={ handleSearch }
					style={ { marginBottom: 0 } }
				/>
			</div>

			{ /* Search results */ }
			{ query.length >= 2 && (
				<div style={ S.searchResults }>
					{ ! searchResults ? (
						<div style={ S.spinner }><Spinner />{ __( 'Đang tìm…', 'laca' ) }</div>
					) : searchResults.length === 0 ? (
						<p style={ { padding: '0.75rem', color: '#888', fontSize: '0.85rem' } }>
							{ __( 'Không tìm thấy sản phẩm.', 'laca' ) }
						</p>
					) : (
						searchResults.map( ( product ) => {
							const isSelected = selectedIds.includes( product.id );
							return (
								<div
									key={ product.id }
									style={ {
										...S.searchItem,
										background: isSelected ? '#f0f7ff' : '#fff',
									} }
									onClick={ () => toggleProduct( product.id ) }
								>
									<input
										type="checkbox"
										checked={ isSelected }
										onChange={ () => toggleProduct( product.id ) }
										style={ { flexShrink: 0 } }
									/>
									<span>{ product.title?.rendered || `#${ product.id }` }</span>
								</div>
							);
						} )
					) }
				</div>
			) }

			{ /* Selected products list */ }
			{ selectedIds.length > 0 && (
				<div style={ { marginTop: '1rem' } }>
					<p style={ { fontSize: '0.8rem', color: '#666', margin: '0 0 0.5rem', fontWeight: 600 } }>
						{ __( 'Đã chọn:', 'laca' ) }
					</p>
					{ selectedIds.map( ( id ) => {
						const product = ( selectedProducts || [] ).find( ( p ) => p.id === id );
						return (
							<div
								key={ id }
								style={ { display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: '0.5rem', marginBottom: '0.35rem' } }
							>
								<span style={ { fontSize: '0.82rem', color: '#333' } }>
									{ product ? product.title?.rendered : `#${ id }` }
								</span>
								<Button
									isDestructive
									isSmall
									variant="link"
									onClick={ () => toggleProduct( id ) }
									style={ { padding: 0, minHeight: 0 } }
								>
									✕
								</Button>
							</div>
						);
					} ) }
				</div>
			) }
		</div>
	);
}

// ── Main Edit Component ────────────────────────────────────────────────────────
export default function Edit( { attributes, setAttributes } ) {
	const {
		mode,
		numberOfProducts,
		columns,
		sectionTitle,
		viewAllText,
		viewAllUrl,
		showViewAll,
		autoOrderby,
		autoCategoryId,
		manualProductIds,
		backgroundColor,
		containerLayout,
		__isPreview,
	} = attributes;

	// ── Preview Mode ────────────────────────────────────────────────────────────
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

	// ── Load all product categories ──────────────────────────────────────────────
	const categories = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords( 'taxonomy', 'product_cat', {
			per_page: 100,
			_fields: [ 'id', 'name', 'count' ],
			hide_empty: false,
		} );
	}, [] );

	// ── Auto mode: load products for preview ─────────────────────────────────────
	const autoProductsQuery = useSelect( ( select ) => {
		if ( mode !== 'auto' ) {
			return null;
		}
		const base = {
			per_page: numberOfProducts,
			status: 'publish',
			_fields: [ 'id', 'title', 'price', 'images', 'short_description' ],
		};
		if ( autoCategoryId > 0 ) {
			base.product_cat = autoCategoryId;
		}
		// Note: popularity & rating require WC REST endpoint, fallback to WP posts endpoint
		const orderMap = {
			date: { orderby: 'date', order: 'desc' },
			rand: { orderby: 'rand', order: 'desc' },
			price: { orderby: 'price', order: 'asc' },
			'price-desc': { orderby: 'price', order: 'desc' },
			popularity: { orderby: 'popularity', order: 'desc' },
			rating: { orderby: 'rating', order: 'desc' },
		};
		const od = orderMap[ autoOrderby ] || orderMap.date;
		return select( 'core' ).getEntityRecords( 'postType', 'product', {
			...base,
			orderby: od.orderby,
			order: od.order,
		} );
	}, [ mode, numberOfProducts, autoCategoryId, autoOrderby ] );

	// ── Manual mode: load selected products for preview ──────────────────────────
	const selectedProducts = useSelect( ( select ) => {
		if ( mode !== 'manual' || ! manualProductIds?.length ) {
			return [];
		}
		return select( 'core' ).getEntityRecords( 'postType', 'product', {
			include: manualProductIds,
			per_page: manualProductIds.length,
			status: 'publish',
			_fields: [ 'id', 'title', 'price', 'images', 'short_description' ],
		} );
	}, [ mode, manualProductIds ] );

	// ── Determine display products ───────────────────────────────────────────────
	const displayProducts = mode === 'manual'
		? ( selectedProducts || [] )
		: ( autoProductsQuery || [] );

	// ── Category options ─────────────────────────────────────────────────────────
	const catOptions = [
		{ label: __( 'Tất cả danh mục', 'laca' ), value: 0 },
		...( categories || [] ).map( ( c ) => ( { label: `${ c.name } (${ c.count })`, value: c.id } ) ),
	];

	const blockProps = useBlockProps( {
		style: backgroundColor ? { backgroundColor } : {},
	} );

	const isLoading = mode === 'auto' ? ! autoProductsQuery : ! selectedProducts;

	// ── Render ──────────────────────────────────────────────────────────────────
	return (
		<>
			{ /* ── Inspector Controls ── */ }
			<InspectorControls>

				{ /* Chế độ hiển thị */ }
				<PanelBody title={ __( '⚙️ Chế độ hiển thị', 'laca' ) } initialOpen={ true }>
					<RadioControl
						label={ __( 'Chọn chế độ', 'laca' ) }
						selected={ mode }
						options={ [
							{ label: __( '🔄 Tự động', 'laca' ), value: 'auto' },
							{ label: __( '✋ Thủ công', 'laca' ), value: 'manual' },
						] }
						onChange={ ( v ) => setAttributes( { mode: v } ) }
					/>
				</PanelBody>

				{ /* Tự động */ }
				{ mode === 'auto' && (
					<PanelBody title={ __( '🔄 Cài đặt Tự động', 'laca' ) } initialOpen={ true }>
						<RangeControl
							label={ __( 'Số sản phẩm hiển thị', 'laca' ) }
							value={ numberOfProducts }
							onChange={ ( v ) => setAttributes( { numberOfProducts: v } ) }
							min={ 2 }
							max={ 24 }
						/>
						<SelectControl
							label={ __( 'Sắp xếp theo', 'laca' ) }
							value={ autoOrderby }
							options={ AUTO_ORDERBY_OPTIONS }
							onChange={ ( v ) => setAttributes( { autoOrderby: v } ) }
						/>
						{ ! categories ? (
							<div style={ S.spinner }><Spinner />{ __( 'Đang tải danh mục…', 'laca' ) }</div>
						) : (
							<SelectControl
								label={ __( 'Lọc theo danh mục', 'laca' ) }
								value={ autoCategoryId }
								options={ catOptions }
								onChange={ ( v ) => setAttributes( { autoCategoryId: Number( v ) } ) }
								help={ __( 'Để trống để lấy từ tất cả danh mục.', 'laca' ) }
							/>
						) }
					</PanelBody>
				) }

				{ /* Thủ công */ }
				{ mode === 'manual' && (
					<PanelBody title={ __( '✋ Chọn sản phẩm thủ công', 'laca' ) } initialOpen={ true }>
						<ProductSearch
							manualProductIds={ manualProductIds }
							setAttributes={ setAttributes }
							selectedProducts={ selectedProducts }
						/>
						<p style={ { fontSize: '0.8rem', color: '#999', marginTop: '0.75rem' } }>
							{ __( 'Đã chọn:', 'laca' ) } { ( manualProductIds || [] ).length } { __( 'sản phẩm', 'laca' ) }
						</p>
					</PanelBody>
				) }

				{ /* Nội dung Header */ }
				<PanelBody title={ __( '📝 Tiêu đề & Link', 'laca' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Tiêu đề section', 'laca' ) }
						value={ sectionTitle }
						onChange={ ( v ) => setAttributes( { sectionTitle: v } ) }
					/>
					<ToggleControl
						label={ __( 'Hiện nút "Xem tất cả"', 'laca' ) }
						checked={ showViewAll }
						onChange={ ( v ) => setAttributes( { showViewAll: v } ) }
					/>
					{ showViewAll && (
						<>
							<TextControl
								label={ __( 'Text nút', 'laca' ) }
								value={ viewAllText }
								onChange={ ( v ) => setAttributes( { viewAllText: v } ) }
							/>
							<TextControl
								label={ __( 'URL liên kết', 'laca' ) }
								value={ viewAllUrl || '' }
								onChange={ ( v ) => setAttributes( { viewAllUrl: v } ) }
								type="url"
								placeholder="https://"
								help={ __( 'Để trống sẽ dùng URL trang Shop.', 'laca' ) }
							/>
						</>
					) }
				</PanelBody>

				{ /* Layout */ }
				<PanelBody title={ __( '🎨 Layout & Styles', 'laca' ) } initialOpen={ false }>
					<SelectControl
						label={ __( 'Số cột', 'laca' ) }
						value={ columns }
						options={ COLS_OPTIONS.map( ( o ) => ( { label: o.label, value: o.value } ) ) }
						onChange={ ( v ) => setAttributes( { columns: Number( v ) } ) }
					/>
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

					{ /* Header */ }
					<div style={ S.header }>
						<h2 style={ S.title }>{ sectionTitle || __( 'Tiêu đề section', 'laca' ) }</h2>
						{ showViewAll && (
							<a href="#" style={ S.viewAll }>{ viewAllText }</a>
						) }
					</div>

					{ /* Products grid */ }
					{ isLoading ? (
						<div style={ S.spinner }><Spinner />{ __( 'Đang tải sản phẩm…', 'laca' ) }</div>
					) : displayProducts.length === 0 ? (
						<div style={ { padding: '2rem', textAlign: 'center', color: '#888', border: '2px dashed #ddd', borderRadius: '0.5rem' } }>
							{ mode === 'manual'
								? __( '👆 Tìm và chọn sản phẩm trong sidebar.', 'laca' )
								: __( '📦 Chưa có sản phẩm nào. Kiểm tra cài đặt trong sidebar.', 'laca' )
							}
						</div>
					) : (
						<div style={ S.grid( columns ) }>
							{ displayProducts.map( ( product ) => {
								const imgSrc = product.images?.[ 0 ]?.src || null;
								return (
									<div key={ product.id } style={ S.card }>
										<div style={ S.imgWrap }>
											{ imgSrc ? (
												<img
													src={ imgSrc }
													alt={ product.title?.rendered || '' }
													style={ S.img }
												/>
											) : (
												<span style={ S.placeholder }>🛒</span>
											) }
										</div>
										<div style={ S.cardMeta }>
											<div>
												<h3 style={ S.cardTitle }>
													{ product.title?.rendered || `#${ product.id }` }
												</h3>
											</div>
										</div>
									</div>
								);
							} ) }
						</div>
					) }

					<p style={ S.hint }>
						{ mode === 'auto'
							? __( '⚙️ Tự động — cấu hình trong sidebar', 'laca' )
							: __( '✋ Thủ công — tìm chọn sản phẩm trong sidebar', 'laca' )
						}
					</p>
				</div>
			</section>
		</>
	);
}
