// The sidebar only sticks when all of it fits in the window (see style.css).
// Its column stretches to the height of the text column, so measure what's in it.
( function () {
	var stickyTop = 32;
	var sidebars = document.querySelectorAll( '.john-with-sidebar__sidebar' );

	function contentHeight( sidebar ) {
		var first = sidebar.firstElementChild;
		var last = sidebar.lastElementChild;
		return first ? last.getBoundingClientRect().bottom - first.getBoundingClientRect().top : 0;
	}

	function update() {
		sidebars.forEach( function ( sidebar ) {
			sidebar.classList.toggle( 'fits-in-window', contentHeight( sidebar ) + stickyTop <= window.innerHeight );
		} );
	}

	update();
	window.addEventListener( 'resize', update );
} )();
