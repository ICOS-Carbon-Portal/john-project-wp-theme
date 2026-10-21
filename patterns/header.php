<?php
/**
 * Title: Header
 * Slug: john-project/header
 * Inserter: no
 */
?>
<!-- wp:group {"align":"full","className":"john-header","style":{"spacing":{"padding":{"top":"0","bottom":"0"}},"border":{"bottom":{"color":"var:preset|color|border","width":"1px","style":"solid"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull john-header has-white-background-color has-background" style="border-bottom-color:var(--wp--preset--color--border);border-bottom-style:solid;border-bottom-width:1px;padding-top:0;padding-bottom:0"><!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:image {"width":"210px","sizeSlug":"full","linkDestination":"custom","className":"john-logo"} -->
<figure class="wp-block-image size-full is-resized john-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/john-logo.svg' ) ); ?>" alt="JOHN Project home" style="width:210px"/></a></figure>
<!-- /wp:image -->

<!-- wp:group {"style":{"spacing":{"blockGap":"2.5rem"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:navigation {"className":"john-main-nav","overlayBackgroundColor":"white","overlayTextColor":"dark","style":{"spacing":{"blockGap":"2.625rem"}},"layout":{"type":"flex","justifyContent":"right"}} /-->
<?php /* Social links are hidden until the project's accounts exist.

<!-- wp:social-links {"iconColor":"dark","iconColorValue":"#414142","size":"has-small-icon-size","className":"is-style-logos-only john-header-social","style":{"spacing":{"blockGap":{"left":"0.75rem"}}}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only john-header-social"><!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"bluesky"} /--></ul>
<!-- /wp:social-links -->
*/ ?></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
