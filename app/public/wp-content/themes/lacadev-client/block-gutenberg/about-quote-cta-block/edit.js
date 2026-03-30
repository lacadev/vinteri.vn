import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
    useBlockEditContext,
} from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    TextareaControl,
    SelectControl,
    ColorPalette,
} from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
    // ── Preview Image (Block Inserter) ──────────────────────────────────────
    const { __unstableIsPreviewMode } = useBlockEditContext();
    const isPreview = ( __unstableIsPreviewMode ?? false ) || ( attributes.__isPreview ?? false );
    if ( isPreview ) {
        return (
            <div style={ { background: '#faf9f7', padding: '3rem', borderRadius: 8, textAlign: 'center', minHeight: 300 } }>
                <span style={ { fontSize: '3rem', opacity: 0.3, display: 'block' } }>&ldquo;</span>
                <blockquote style={ { fontFamily: 'Georgia,serif', fontSize: '1.5rem', fontStyle: 'italic', fontWeight: 300, color: '#2f3331', marginBottom: '1rem' } }>
                    Design is not just what it looks like and feels like. Design is how it works.
                </blockquote>
                <p style={ { fontSize: '0.7rem', letterSpacing: '0.4em', textTransform: 'uppercase', opacity: 0.6 } }>— VINTERI PHILOSOPHY</p>
                <div style={ { marginTop: '2rem', padding: '1.5rem', background: '#e6e9e6', borderRadius: 8, display: 'flex', justifyContent: 'space-between', alignItems: 'center' } }>
                    <strong style={ { fontFamily: 'Georgia,serif' } }>Begin your journey</strong>
                    <span style={ { padding: '0.6rem 1.5rem', background: '#5f5e5e', color: '#fff', borderRadius: 9999, fontSize: '0.7rem', letterSpacing: '0.1em', textTransform: 'uppercase' } }>Explore Collection</span>
                </div>
            </div>
        );
    }

    const {
        quoteText, quoteAuthor,
        ctaHeading, ctaSubtext, ctaButtonText, ctaButtonUrl,
        containerLayout, backgroundColor,
    } = attributes;

    const blockProps = useBlockProps( {
        style: backgroundColor ? { backgroundColor } : {},
    } );

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Quote Section', 'laca' ) } initialOpen={ true }>
                    <TextareaControl
                        label={ __( 'Nội dung trích dẫn', 'laca' ) }
                        value={ quoteText }
                        onChange={ ( val ) => setAttributes( { quoteText: val } ) }
                    />
                    <TextControl
                        label={ __( 'Tên tác giả / chức danh', 'laca' ) }
                        value={ quoteAuthor }
                        onChange={ ( val ) => setAttributes( { quoteAuthor: val } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'CTA Section', 'laca' ) } initialOpen={ true }>
                    <TextControl
                        label={ __( 'Tiêu đề CTA', 'laca' ) }
                        value={ ctaHeading }
                        onChange={ ( val ) => setAttributes( { ctaHeading: val } ) }
                    />
                    <TextControl
                        label={ __( 'Mô tả CTA', 'laca' ) }
                        value={ ctaSubtext }
                        onChange={ ( val ) => setAttributes( { ctaSubtext: val } ) }
                    />
                    <TextControl
                        label={ __( 'Text nút', 'laca' ) }
                        value={ ctaButtonText }
                        onChange={ ( val ) => setAttributes( { ctaButtonText: val } ) }
                    />
                    <TextControl
                        label={ __( 'URL nút', 'laca' ) }
                        value={ ctaButtonUrl }
                        onChange={ ( val ) => setAttributes( { ctaButtonUrl: val } ) }
                    />
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
                { /* Quote Section */ }
                <div style={ { padding: '5rem 0', background: '#faf9f7' } }>
                    <div className={ containerLayout }>
                        <div style={ { maxWidth: '56rem', margin: '0 auto', textAlign: 'center' } }>
                            <span style={ { fontSize: '2.5rem', opacity: 0.3, display: 'block', marginBottom: '2rem' } }>"</span>
                            <blockquote style={ {
                                fontFamily: 'Georgia, serif',
                                fontSize: 'clamp(1.3rem, 3vw, 2.2rem)',
                                fontStyle: 'italic',
                                fontWeight: 300,
                                lineHeight: 1.4,
                                marginBottom: '2rem',
                                color: '#2f3331',
                            } }>
                                { quoteText }
                            </blockquote>
                            <p style={ {
                                fontSize: '0.75rem',
                                letterSpacing: '0.4em',
                                textTransform: 'uppercase',
                                opacity: 0.6,
                            } }>
                                { quoteAuthor }
                            </p>
                        </div>
                    </div>
                </div>

                { /* CTA Section */ }
                <div style={ { padding: '3rem 0', background: '#e6e9e6' } }>
                    <div className={ containerLayout }>
                        <div style={ {
                            display: 'flex',
                            flexDirection: 'row',
                            justifyContent: 'space-between',
                            alignItems: 'center',
                            gap: '2rem',
                        } }>
                            <div>
                                <h2 style={ {
                                    fontFamily: 'Georgia, serif',
                                    fontSize: 'clamp(1.5rem, 3vw, 2.2rem)',
                                    fontWeight: 400,
                                    marginBottom: '0.5rem',
                                } }>
                                    { ctaHeading }
                                </h2>
                                <p style={ { opacity: 0.7, fontSize: '0.95rem' } }>{ ctaSubtext }</p>
                            </div>
                            <a
                                href={ ctaButtonUrl || '#' }
                                style={ {
                                    display: 'inline-block',
                                    padding: '1rem 2.5rem',
                                    background: '#5f5e5e',
                                    color: '#faf7f6',
                                    borderRadius: '9999px',
                                    fontSize: '0.75rem',
                                    letterSpacing: '0.1em',
                                    textTransform: 'uppercase',
                                    textDecoration: 'none',
                                    fontWeight: 500,
                                    whiteSpace: 'nowrap',
                                } }
                            >
                                { ctaButtonText }
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
