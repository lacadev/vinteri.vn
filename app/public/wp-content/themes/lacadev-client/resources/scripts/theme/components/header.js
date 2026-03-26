/**
 * Header
 * Ẩn/hiện header khi scroll, thêm class scrolled khi ra khỏi top.
 */

export function initHeaderScroll() {
	const header = document.getElementById( 'header' );
	if ( ! header ) return;

	// Set --header-h để wrapper bù chiều cao header fixed
	const setHeaderHeight = () => {
		document.documentElement.style.setProperty(
			'--header-h',
			header.offsetHeight + 'px'
		);
	};
	setHeaderHeight();
	new ResizeObserver( setHeaderHeight ).observe( header );

	let lastScrollTop = 0;
	const THRESHOLD = 100;

	window.addEventListener( 'scroll', () => {
		const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

		header.classList.toggle( 'header--scrolled', scrollTop > 50 );

		if ( scrollTop > THRESHOLD ) {
			header.classList.toggle( 'header--hidden', scrollTop > lastScrollTop );
		} else {
			header.classList.remove( 'header--hidden' );
		}

		lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
	}, { passive: true } );
}
