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
import previewImage from './preview.png';

export default function Edit( { attributes, setAttributes } ) {
    // ── Preview Image (Block Inserter) ──────────────────────────────────────
    const { __unstableIsPreviewMode } = useBlockEditContext();
    const isPreview = ( __unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
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

    const {
        subTitle, title, titleItalic, description,
        buttonText, buttonUrl,
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
                <PanelBody title={ __( 'Nội dung', 'laca' ) } initialOpen={ true }>
                    <TextControl
                        label={ __( 'Sub Title (badge nhỏ)', 'laca' ) }
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
                    <TextControl
                        label={ __( 'Text nút CTA', 'laca' ) }
                        value={ buttonText }
                        onChange={ ( val ) => setAttributes( { buttonText: val } ) }
                    />
                    <TextControl
                        label={ __( 'URL nút CTA', 'laca' ) }
                        value={ buttonUrl }
                        onChange={ ( val ) => setAttributes( { buttonUrl: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Ảnh Hero', 'laca' ) } initialOpen={ true }>
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
                                            ? __( 'Thay ảnh', 'laca' )
                                            : __( 'Chọn ảnh', 'laca' ) }
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

            { /* ── Editor Preview — dùng inline styles vì Tailwind không load trong WP editor ── */ }
            <section { ...blockProps }>
                <div className={ containerLayout }>
                    <div style={ { padding: '3rem 0' } }>
                        <div style={ {
                            display: 'grid',
                            gridTemplateColumns: '5fr 7fr',
                            gap: '2rem',
                            alignItems: 'center',
                        } }>
                            { /* Text Column */ }
                            <div>
                                <span style={ {
                                    display: 'block',
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.15em',
                                    fontSize: '0.75rem',
                                    marginBottom: '1rem',
                                    opacity: 0.6,
                                } }>
                                    { subTitle }
                                </span>
                                <h1 style={ {
                                    fontSize: 'clamp(2.5rem, 5vw, 4.5rem)',
                                    lineHeight: 1.1,
                                    marginBottom: '1.5rem',
                                    fontFamily: 'Georgia, serif',
                                    fontWeight: 400,
                                } }>
                                    { title }
                                    { titleItalic && (
                                        <><br /><span style={ { fontStyle: 'italic', fontWeight: 300 } }>{ titleItalic }</span></>
                                    ) }
                                </h1>
                                <p style={ {
                                    fontSize: '1.05rem',
                                    lineHeight: 1.7,
                                    maxWidth: '28rem',
                                    marginBottom: '2rem',
                                    opacity: 0.75,
                                } }>
                                    { description }
                                </p>
                                <span className="hero-btn" style={ { cursor: 'default' } }>
                                    { buttonText }
                                </span>
                            </div>

                            { /* Image Column */ }
                            <div style={ { position: 'relative' } }>
                                <div className="hero-image-wrapper" style={ {
                                    width: '100%',
                                    borderRadius: '0.75rem',
                                    overflow: 'hidden',
                                    background: '#f0efec',
                                } }>
                                    { imageUrl ? (
                                        <img
                                            src={ imageUrl }
                                            alt={ imageAlt }
                                            style={ { width: '100%', height: '100%', objectFit: 'cover', display: 'block' } }
                                        />
                                    ) : (
                                        <div style={ {
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            height: '24rem',
                                            color: '#888',
                                            fontSize: '0.9rem',
                                        } }>
                                            { __( '← Chọn ảnh hero trong sidebar', 'laca' ) }
                                        </div>
                                    ) }
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
