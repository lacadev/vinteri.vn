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
    RangeControl,
    Button,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
    // ── Preview Image (Block Inserter) ──────────────────────────────────────
    const { __unstableIsPreviewMode } = useBlockEditContext();
    const isPreview = ( __unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
    if ( isPreview ) {
        return (
            <div style={ { background: 'linear-gradient(135deg,#2f3331 0%,#4a4f4d 100%)', minHeight: 400, display: 'flex', flexDirection: 'column', alignItems: 'flex-start', justifyContent: 'flex-end', padding: '3rem', borderRadius: 8 } }>
                <span style={ { color: 'rgba(255,255,255,0.6)', fontSize: '0.7rem', letterSpacing: '0.3em', textTransform: 'uppercase', marginBottom: '1rem', display: 'block' } }>ESTABLISHED 2024</span>
                <h1 style={ { color: '#faf7f6', fontFamily: 'Georgia,serif', fontWeight: 300, fontSize: '3.5rem', lineHeight: 1.05, margin: 0 } }>The Art of<br /><em>Curation</em></h1>
            </div>
        );
    }

    const {
        subTitle, title, titleItalic, description,
        imageId, imageUrl, imageAlt,
        overlayOpacity,
        containerLayout, backgroundColor,
    } = attributes;

    const blockProps = useBlockProps( {
        style: backgroundColor ? { backgroundColor } : {},
    } );

    return (
        <>
            { /* ── Sidebar Controls ───────────────────────────────────────── */ }
            <InspectorControls>
                <PanelBody title={ __( 'Nội dung', 'laca' ) } initialOpen={ true }>
                    <TextControl
                        label={ __( 'Sub Title (label nhỏ)', 'laca' ) }
                        value={ subTitle }
                        onChange={ ( val ) => setAttributes( { subTitle: val } ) }
                    />
                    <TextControl
                        label={ __( 'Tiêu đề chính', 'laca' ) }
                        value={ title }
                        onChange={ ( val ) => setAttributes( { title: val } ) }
                    />
                    <TextControl
                        label={ __( 'Phần tiêu đề in nghiêng', 'laca' ) }
                        value={ titleItalic }
                        onChange={ ( val ) => setAttributes( { titleItalic: val } ) }
                    />
                    <TextareaControl
                        label={ __( 'Mô tả', 'laca' ) }
                        value={ description }
                        onChange={ ( val ) => setAttributes( { description: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Ảnh nền Hero', 'laca' ) } initialOpen={ true }>
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
                                        { imageUrl
                                            ? __( 'Thay ảnh nền', 'laca' )
                                            : __( 'Chọn ảnh nền', 'laca' ) }
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
                    <div style={ { marginTop: '12px' } }>
                        <RangeControl
                            label={ __( 'Độ tối overlay (%)', 'laca' ) }
                            value={ overlayOpacity }
                            onChange={ ( val ) => setAttributes( { overlayOpacity: val } ) }
                            min={ 0 }
                            max={ 80 }
                            step={ 5 }
                        />
                    </div>
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
            <section { ...blockProps }>
                <div style={ {
                    position: 'relative',
                    minHeight: '600px',
                    display: 'flex',
                    alignItems: 'center',
                    overflow: 'hidden',
                } }>
                    { /* Background Image */ }
                    { imageUrl ? (
                        <>
                            <div style={ {
                                position: 'absolute', inset: 0,
                            } }>
                                <img
                                    src={ imageUrl }
                                    alt={ imageAlt }
                                    style={ { width: '100%', height: '100%', objectFit: 'cover' } }
                                />
                                <div style={ {
                                    position: 'absolute', inset: 0,
                                    backgroundColor: `rgba(0,0,0,${ overlayOpacity / 100 })`,
                                } } />
                            </div>
                        </>
                    ) : (
                        <div style={ {
                            position: 'absolute', inset: 0,
                            background: 'linear-gradient(135deg, #f0efec 0%, #e0ddd8 100%)',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: '#888',
                            fontSize: '0.9rem',
                        } }>
                            { __( '← Chọn ảnh nền trong sidebar', 'laca' ) }
                        </div>
                    ) }

                    { /* Text Content */ }
                    <div className={ containerLayout } style={ { position: 'relative', zIndex: 10, padding: '3rem 0' } }>
                        <div style={ { maxWidth: '48rem' } }>
                            { subTitle && (
                                <span style={ {
                                    display: 'block',
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.3em',
                                    fontSize: '0.8rem',
                                    marginBottom: '1.5rem',
                                    opacity: 0.7,
                                } }>
                                    { subTitle }
                                </span>
                            ) }
                            <h1 style={ {
                                fontSize: 'clamp(2.5rem, 6vw, 5.5rem)',
                                fontFamily: 'Georgia, serif',
                                fontWeight: 300,
                                lineHeight: 1.05,
                                marginBottom: '2rem',
                            } }>
                                { title }
                                { titleItalic && (
                                    <><br /><span style={ { fontStyle: 'italic' } }>{ titleItalic }</span></>
                                ) }
                            </h1>
                            { description && (
                                <p style={ {
                                    fontSize: '1.1rem',
                                    lineHeight: 1.7,
                                    maxWidth: '36rem',
                                    opacity: 0.85,
                                    fontWeight: 300,
                                } }>
                                    { description }
                                </p>
                            ) }
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
