import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
    MediaUpload,
    MediaUploadCheck,
    useBlockEditContext,
} from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    TextareaControl,
    SelectControl,
    ColorPalette,
    Button,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
    // ── Preview Image (Block Inserter) ──────────────────────────────────────
    const { __unstableIsPreviewMode } = useBlockEditContext();
    const isPreview = ( __unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
    if ( isPreview ) {
        return (
            <div style={ { background: '#f4f5f3', padding: '2rem', borderRadius: 8, minHeight: 350 } }>
                <h2 style={ { fontFamily: 'Georgia,serif', fontSize: '2rem', fontWeight: 300, textAlign: 'center', marginBottom: '1.5rem', color: '#2f3331' } }>Our Philosophy</h2>
                <div style={ { display: 'grid', gridTemplateColumns: 'repeat(2, 1fr)', gap: '1rem', height: '240px' } }>
                    <div style={ { background: '#fff', borderRadius: 8, padding: '1.5rem', gridColumn: 'span 1' } }><strong style={ { fontFamily: 'Georgia,serif' } }>Intentional</strong></div>
                    <div style={ { background: '#5f5e5e', borderRadius: 8, padding: '1.5rem', color: '#fff' } }><strong style={ { fontFamily: 'Georgia,serif' } }>Timeless</strong></div>
                    <div style={ { background: '#f4dfcb', borderRadius: 8, padding: '1.5rem' } }><strong style={ { fontFamily: 'Georgia,serif', color: '#5e4f40' } }>Material</strong></div>
                    <div style={ { background: '#e0e3e0', borderRadius: 8, padding: '1.5rem' } }><strong style={ { fontFamily: 'Georgia,serif' } }>Domestic</strong></div>
                </div>
            </div>
        );
    }

    const {
        sectionTitle,
        item1Title, item1Text, item1ImageId, item1ImageUrl, item1ImageAlt,
        item2Title, item2Text,
        item3Title, item3Text,
        item4Title, item4ImageId, item4ImageUrl, item4ImageAlt,
        containerLayout, backgroundColor,
    } = attributes;

    const blockProps = useBlockProps( {
        style: backgroundColor ? { backgroundColor } : {},
    } );

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Section Title', 'laca' ) } initialOpen={ true }>
                    <TextControl
                        label={ __( 'Tiêu đề section', 'laca' ) }
                        value={ sectionTitle }
                        onChange={ ( val ) => setAttributes( { sectionTitle: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Bento 1 — Intentional (8/12)', 'laca' ) } initialOpen={ true }>
                    <TextControl
                        label={ __( 'Tiêu đề', 'laca' ) }
                        value={ item1Title }
                        onChange={ ( val ) => setAttributes( { item1Title: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Mô tả', 'laca' ) }
                        value={ item1Text }
                        onChange={ ( val ) => setAttributes( { item1Text: val } ) }
                    />
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={ ( m ) => setAttributes( { item1ImageId: m.id, item1ImageUrl: m.url, item1ImageAlt: m.alt || '' } ) }
                            allowedTypes={ [ 'image' ] }
                            value={ item1ImageId }
                            render={ ( { open } ) => (
                                <>
                                    { item1ImageUrl && <img src={ item1ImageUrl } alt="" style={ { width: '100%', marginBottom: '6px', borderRadius: '4px' } } /> }
                                    <Button onClick={ open } variant="secondary" style={ { width: '100%' } }>
                                        { item1ImageUrl ? __( 'Thay ảnh', 'laca' ) : __( 'Chọn ảnh', 'laca' ) }
                                    </Button>
                                </>
                            ) }
                        />
                    </MediaUploadCheck>
                </PanelBody>

                <PanelBody title={ __( 'Bento 2 — Timeless (4/12)', 'laca' ) } initialOpen={ false }>
                    <TextControl
                        label={ __( 'Tiêu đề', 'laca' ) }
                        value={ item2Title }
                        onChange={ ( val ) => setAttributes( { item2Title: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Mô tả', 'laca' ) }
                        value={ item2Text }
                        onChange={ ( val ) => setAttributes( { item2Text: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Bento 3 — Material (4/12)', 'laca' ) } initialOpen={ false }>
                    <TextControl
                        label={ __( 'Tiêu đề', 'laca' ) }
                        value={ item3Title }
                        onChange={ ( val ) => setAttributes( { item3Title: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Mô tả', 'laca' ) }
                        value={ item3Text }
                        onChange={ ( val ) => setAttributes( { item3Text: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Bento 4 — Domestic (8/12)', 'laca' ) } initialOpen={ false }>
                    <TextControl
                        label={ __( 'Tiêu đề overlay', 'laca' ) }
                        value={ item4Title }
                        onChange={ ( val ) => setAttributes( { item4Title: val } ) }
                    />
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={ ( m ) => setAttributes( { item4ImageId: m.id, item4ImageUrl: m.url, item4ImageAlt: m.alt || '' } ) }
                            allowedTypes={ [ 'image' ] }
                            value={ item4ImageId }
                            render={ ( { open } ) => (
                                <>
                                    { item4ImageUrl && <img src={ item4ImageUrl } alt="" style={ { width: '100%', marginBottom: '6px', borderRadius: '4px' } } /> }
                                    <Button onClick={ open } variant="secondary" style={ { width: '100%' } }>
                                        { item4ImageUrl ? __( 'Thay ảnh', 'laca' ) : __( 'Chọn ảnh', 'laca' ) }
                                    </Button>
                                </>
                            ) }
                        />
                    </MediaUploadCheck>
                </PanelBody>

                <PanelBody title={ __( 'Layout & Styles', 'laca' ) } initialOpen={ false }>
                    <SelectControl
                        label={ __( 'Container Layout', 'laca' ) }
                        value={ containerLayout }
                        options={ [
                            { label: 'Boxed (container)', value: 'container' },
                            { label: 'Full Width (container-fluid)', value: 'container-fluid' },
                        ] }
                        onChange={ ( val ) => setAttributes( { containerLayout: val } ) }
                    />
                    <p>{ __( 'Màu nền section', 'laca' ) }</p>
                    <ColorPalette
                        value={ backgroundColor }
                        onChange={ ( val ) => setAttributes( { backgroundColor: val ?? '' } ) }
                    />
                </PanelBody>
            </InspectorControls>

            { /* ── Editor Preview (simplified bento grid) ─────────────────── */ }
            <section { ...blockProps } style={ { ...(blockProps.style || {}), padding: '3rem 0' } }>
                <div className={ containerLayout }>
                    <div style={ { textAlign: 'center', marginBottom: '3rem' } }>
                        <h2 style={ { fontFamily: 'Georgia, serif', fontSize: '2.5rem', fontWeight: 300 } }>{ sectionTitle }</h2>
                        <div style={ { height: '1px', width: '6rem', background: '#afb3b0', margin: '1rem auto 0' } } />
                    </div>
                    <div style={ { display: 'grid', gridTemplateColumns: 'repeat(12, 1fr)', gap: '2rem' } }>
                        { /* Bento 1 — 8 cols */ }
                        <div style={ {
                            gridColumn: 'span 8',
                            background: '#fff',
                            borderRadius: '0.75rem',
                            padding: '3rem',
                            display: 'flex',
                            flexDirection: 'column',
                            gap: '2rem',
                        } }>
                            <div>
                                <h3 style={ { fontFamily: 'Georgia, serif', fontSize: '1.6rem', marginBottom: '0.75rem' } }>{ item1Title }</h3>
                                <p style={ { opacity: 0.7, lineHeight: 1.7 } }>{ item1Text }</p>
                            </div>
                            { item1ImageUrl && (
                                <div style={ { borderRadius: '0.5rem', overflow: 'hidden', aspectRatio: '21/9' } }>
                                    <img src={ item1ImageUrl } alt={ item1ImageAlt } style={ { width: '100%', height: '100%', objectFit: 'cover', display: 'block' } } />
                                </div>
                            ) }
                        </div>
                        { /* Bento 2 — 4 cols */ }
                        <div style={ {
                            gridColumn: 'span 4',
                            background: '#5f5e5e',
                            color: '#faf7f6',
                            borderRadius: '0.75rem',
                            padding: '3rem',
                            display: 'flex',
                            flexDirection: 'column',
                            justifyContent: 'center',
                            alignItems: 'center',
                            textAlign: 'center',
                        } }>
                            <span style={ { fontSize: '3rem', marginBottom: '1.5rem' } }>♻</span>
                            <h3 style={ { fontFamily: 'Georgia, serif', fontSize: '1.6rem', marginBottom: '0.75rem' } }>{ item2Title }</h3>
                            <p style={ { opacity: 0.8, lineHeight: 1.7, fontSize: '0.95rem' } }>{ item2Text }</p>
                        </div>
                        { /* Bento 3 — 4 cols */ }
                        <div style={ {
                            gridColumn: 'span 4',
                            background: '#f4dfcb',
                            borderRadius: '0.75rem',
                            padding: '3rem',
                            display: 'flex',
                            flexDirection: 'column',
                            justifyContent: 'flex-end',
                            minHeight: '25rem',
                        } }>
                            <h3 style={ { fontFamily: 'Georgia, serif', fontSize: '1.6rem', marginBottom: '0.75rem', color: '#5e4f40' } }>{ item3Title }</h3>
                            <p style={ { opacity: 0.8, lineHeight: 1.7, fontSize: '0.95rem', color: '#5e4f40' } }>{ item3Text }</p>
                        </div>
                        { /* Bento 4 — 8 cols */ }
                        <div style={ {
                            gridColumn: 'span 8',
                            borderRadius: '0.75rem',
                            overflow: 'hidden',
                            position: 'relative',
                            minHeight: '25rem',
                            background: '#e0e3e0',
                        } }>
                            { item4ImageUrl ? (
                                <img src={ item4ImageUrl } alt={ item4ImageAlt } style={ { width: '100%', height: '100%', objectFit: 'cover', display: 'block', position: 'absolute', inset: 0 } } />
                            ) : (
                                <div style={ { display: 'flex', alignItems: 'center', justifyContent: 'center', height: '100%', minHeight: '25rem', color: '#888', fontSize: '0.85rem' } }>
                                    { __( '← Chọn ảnh Bento 4', 'laca' ) }
                                </div>
                            ) }
                            <div style={ {
                                position: 'absolute', inset: 0,
                                background: 'linear-gradient(to top, rgba(0,0,0,0.6), transparent)',
                                display: 'flex',
                                alignItems: 'flex-end',
                                padding: '3rem',
                            } }>
                                <h3 style={ { fontFamily: 'Georgia, serif', fontSize: '1.6rem', color: '#fff' } }>{ item4Title }</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
