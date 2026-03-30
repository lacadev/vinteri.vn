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
            <div style={ { background: '#faf9f7', minHeight: 400, display: 'flex', flexDirection: 'row', gap: '3rem', alignItems: 'center', padding: '3rem', borderRadius: 8 } }>
                <div style={ { flex: '0 0 45%', aspectRatio: '4/5', background: '#e0ddd8', borderRadius: 8 } } />
                <div style={ { flex: 1 } }>
                    <h2 style={ { fontFamily: 'Georgia,serif', fontSize: '2.5rem', fontWeight: 300, color: '#2f3331', marginBottom: '1rem' } }>The Foundation<br /><em>of Craft</em></h2>
                    <p style={ { color: '#5e6562', lineHeight: 1.7, fontSize: '0.9rem' } }>Every piece in our collection passes through a rigorous curation process.</p>
                </div>
            </div>
        );
    }

    const {
        sectionLabel, specText,
        heading, paragraph1, paragraph2,
        ctaText, ctaUrl,
        imageId, imageUrl, imageAlt,
        containerLayout, backgroundColor,
    } = attributes;

    const blockProps = useBlockProps( {
        style: backgroundColor ? { backgroundColor } : {},
    } );

    return (
        <>
            { /* ── Sidebar Controls ───────────────────────────────────────── */ }
            <InspectorControls>
                <PanelBody title={ __( 'Nội dung chính', 'laca' ) } initialOpen={ true }>
                    <TextControl
                        label={ __( 'Tiêu đề section', 'laca' ) }
                        value={ heading }
                        onChange={ ( val ) => setAttributes( { heading: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Đoạn văn 1', 'laca' ) }
                        value={ paragraph1 }
                        onChange={ ( val ) => setAttributes( { paragraph1: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Đoạn văn 2', 'laca' ) }
                        value={ paragraph2 }
                        onChange={ ( val ) => setAttributes( { paragraph2: val } ) }
                    />
                    <TextControl
                        label={ __( 'Text nút CTA', 'laca' ) }
                        value={ ctaText }
                        onChange={ ( val ) => setAttributes( { ctaText: val } ) }
                    />
                    <TextControl
                        label={ __( 'URL nút CTA', 'laca' ) }
                        value={ ctaUrl }
                        onChange={ ( val ) => setAttributes( { ctaUrl: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Glass Card (Specification)', 'laca' ) } initialOpen={ false }>
                    <TextControl
                        label={ __( 'Label specification', 'laca' ) }
                        value={ sectionLabel }
                        onChange={ ( val ) => setAttributes( { sectionLabel: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Nội dung specification', 'laca' ) }
                        value={ specText }
                        onChange={ ( val ) => setAttributes( { specText: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Ảnh editorial', 'laca' ) } initialOpen={ true }>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={ ( media ) => setAttributes( {
                                imageId: media.id,
                                imageUrl: media.url,
                                imageAlt: media.alt || '',
                            } ) }
                            allowedTypes={ [ 'image' ] }
                            value={ imageId }
                            render={ ( { open } ) => (
                                <>
                                    { imageUrl && (
                                        <img
                                            src={ imageUrl }
                                            alt={ imageAlt }
                                            style={ { width: '100%', marginBottom: '8px', borderRadius: '4px' } }
                                        />
                                    ) }
                                    <Button onClick={ open } variant="secondary" style={ { width: '100%' } }>
                                        { imageUrl ? __( 'Thay ảnh', 'laca' ) : __( 'Chọn ảnh', 'laca' ) }
                                    </Button>
                                    { imageUrl && (
                                        <Button
                                            onClick={ () => setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } ) }
                                            variant="tertiary"
                                            isDestructive
                                            style={ { width: '100%', marginTop: '4px' } }
                                        >
                                            { __( 'Xóa ảnh', 'laca' ) }
                                        </Button>
                                    ) }
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

            { /* ── Editor Preview ──────────────────────────────────────────── */ }
            <section { ...blockProps } style={ { ...(blockProps.style || {}), padding: '3rem 0' } }>
                <div className={ containerLayout }>
                    <div style={ {
                        display: 'flex',
                        flexDirection: 'row',
                        gap: '5rem',
                        alignItems: 'center',
                    } }>
                        { /* Image Column */ }
                        <div style={ { flex: '0 0 50%', position: 'relative' } }>
                            <div style={ {
                                aspectRatio: '4/5',
                                background: '#f0efec',
                                borderRadius: '0.5rem',
                                overflow: 'hidden',
                            } }>
                                { imageUrl ? (
                                    <img
                                        src={ imageUrl }
                                        alt={ imageAlt }
                                        style={ { width: '100%', height: '100%', objectFit: 'cover', display: 'block' } }
                                    />
                                ) : (
                                    <div style={ {
                                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                                        height: '100%', color: '#888', fontSize: '0.85rem',
                                    } }>
                                        { __( '← Chọn ảnh editorial', 'laca' ) }
                                    </div>
                                ) }
                            </div>
                            { /* Glass Card Preview */ }
                            <div style={ {
                                position: 'absolute',
                                bottom: '-2.5rem',
                                right: '-2.5rem',
                                width: '16rem',
                                padding: '1.5rem',
                                background: 'rgba(255,255,255,0.85)',
                                borderRadius: '0.5rem',
                                border: '1px solid rgba(175,179,176,0.3)',
                                boxShadow: '0 20px 40px rgba(47,51,49,0.12)',
                            } }>
                                <p style={ { fontSize: '0.7rem', letterSpacing: '0.15em', textTransform: 'uppercase', opacity: 0.6, marginBottom: '0.75rem' } }>
                                    { sectionLabel }
                                </p>
                                <p style={ { fontFamily: 'Georgia, serif', fontSize: '1rem', lineHeight: 1.5 } }>
                                    { specText }
                                </p>
                            </div>
                        </div>

                        { /* Text Column */ }
                        <div style={ { flex: 1 } }>
                            <h2 style={ {
                                fontFamily: 'Georgia, serif',
                                fontSize: 'clamp(2rem, 4vw, 3.5rem)',
                                fontWeight: 300,
                                lineHeight: 1.2,
                                marginBottom: '2rem',
                            } }>
                                { heading }
                            </h2>
                            <div style={ { display: 'flex', flexDirection: 'column', gap: '1rem', marginBottom: '2rem', opacity: 0.75 } }>
                                { paragraph1 && <p style={ { lineHeight: 1.75, fontSize: '1rem' } }>{ paragraph1 }</p> }
                                { paragraph2 && <p style={ { lineHeight: 1.75, fontSize: '1rem' } }>{ paragraph2 }</p> }
                            </div>
                            { ctaText && (
                                <div>
                                    <span style={ {
                                        fontSize: '0.8rem',
                                        letterSpacing: '0.1em',
                                        textTransform: 'uppercase',
                                        borderBottom: '1px solid currentColor',
                                        paddingBottom: '2px',
                                        cursor: 'default',
                                    } }>
                                        { ctaText } →
                                    </span>
                                </div>
                            ) }
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
