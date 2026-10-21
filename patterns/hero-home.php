<?php
/**
 * Title: Home hero
 * Slug: john-project/hero-home
 * Inserter: no
 */
?>
<!-- wp:group {"align":"full","className":"john-home-hero","style":{"spacing":{"padding":{"top":"5.5rem","bottom":"5.75rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull john-home-hero" style="padding-top:5.5rem;padding-bottom:5.75rem"><!-- wp:columns {"verticalAlignment":"top","align":"wide","style":{"spacing":{"blockGap":{"left":"5rem"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-top"><!-- wp:column {"verticalAlignment":"top","width":"51%"} -->
<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:51%"><!-- wp:paragraph {"className":"is-style-eyebrow"} -->
<p class="is-style-eyebrow">Horizon Europe project &middot; 2026&ndash;2030</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"lineHeight":"1.074"}},"fontSize":"display"} -->
<h1 class="wp-block-heading has-display-font-size" style="line-height:1.074">Joint operations for monitoring anthropogenic and natural greenhouse gas emissions and sinks</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"john-intro"} -->
<p class="john-intro">JOHN is a Horizon Europe funded project (2026&ndash;2030) that will assess and improve the global greenhouse gas observing system, explore emerging technologies, and develop priorities for future investment and monitoring.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"2.25rem"},"blockGap":"1rem"}}} -->
<div class="wp-block-buttons" style="margin-top:2.25rem"><!-- wp:button {"metadata":{"bindings":{"url":{"source":"john-project/links","args":{"key":"page","slug":"about"}}}}} -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">About the project &rarr;</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline","metadata":{"bindings":{"url":{"source":"john-project/links","args":{"key":"page","slug":"objectives"}}}}} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Our objectives &rarr;</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"top","width":"42.33%"} -->
<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:42.33%"><!-- wp:image {"aspectRatio":"157/134","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"john-home-hero__image"} -->
<figure class="wp-block-image size-full john-home-hero__image"><img src="<?php echo esc_url( add_query_arg( 'ver', filemtime( get_theme_file_path( 'assets/images/hero-home.webp' ) ), get_theme_file_uri( 'assets/images/hero-home.webp' ) ) ); ?>" alt="A measurement mast above pine forest at an ICOS station" style="aspect-ratio:157/134;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
