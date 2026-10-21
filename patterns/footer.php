<?php
/**
 * Title: Footer
 * Slug: john-project/footer
 * Inserter: no
 */

$newsletter_url              = john_project_page_url( 'newsletter' ) ?: '#';
$privacy_policy_url          = get_privacy_policy_url() ?: '#';
$accessibility_statement_url = john_project_page_url( 'accessibility-statement' ) ?: '#';

$explore_links = [];
foreach ( [ 'about' => 'About', 'objectives' => 'Objectives', 'results' => 'Results', 'news-events' => 'News & Events', 'newsletter' => 'Newsletter' ] as $slug => $label ) {
	$url = john_project_page_url( $slug );
	if ( $url ) {
		$explore_links[ $label ] = $url;
	}
}
?>
<!-- wp:group {"align":"full","className":"john-footer","style":{"spacing":{"padding":{"top":"4.5rem","bottom":"3rem"},"margin":{"top":"0"}},"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"primary","textColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull john-footer has-white-color has-primary-background-color has-text-color has-background has-link-color" style="margin-top:0;padding-top:4.5rem;padding-bottom:3rem"><!-- wp:columns {"align":"wide","className":"john-footer-columns","style":{"spacing":{"blockGap":{"left":"3rem"}}}} -->
<div class="wp-block-columns alignwide john-footer-columns"><!-- wp:column {"width":"29.7%"} -->
<div class="wp-block-column" style="flex-basis:29.7%"><!-- wp:image {"width":"200px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/john-logo-on-dark.svg' ) ); ?>" alt="JOHN" style="width:200px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Joint operations for monitoring anthropogenic and natural greenhouse gas emissions and sinks</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"16.3%"} -->
<div class="wp-block-column" style="flex-basis:16.3%"><!-- wp:paragraph {"className":"john-footer-heading"} -->
<p class="john-footer-heading">Explore</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"john-footer-links"} -->
<ul class="wp-block-list john-footer-links"><?php foreach ( $explore_links as $label => $url ) : ?><!-- wp:list-item -->
<li><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"20.8%"} -->
<div class="wp-block-column" style="flex-basis:20.8%"><!-- wp:paragraph {"className":"john-footer-heading"} -->
<p class="john-footer-heading">Follow us</p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"white","iconColorValue":"#FFFFFF","showLabels":true,"size":"has-small-icon-size","className":"is-style-logos-only john-footer-social","style":{"spacing":{"blockGap":{"top":"0.75rem"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<ul class="wp-block-social-links has-small-icon-size has-visible-labels has-icon-color is-style-logos-only john-footer-social"><?php /* LinkedIn and Bluesky are hidden until the project's accounts exist.
<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"bluesky"} /-->
*/ ?><!-- wp:social-link {"url":"<?php echo esc_url( $newsletter_url ); ?>","service":"mail","label":"Subscribe to the newsletter"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"21.2%"} -->
<div class="wp-block-column" style="flex-basis:21.2%"><!-- wp:image {"width":"250px","sizeSlug":"full","linkDestination":"none","className":"john-eu-emblem"} -->
<figure class="wp-block-image size-full is-resized john-eu-emblem"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/eu-funded-white.png' ) ); ?>" alt="Funded by the European Union" style="width:250px"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"x-small"} -->
<p class="has-x-small-font-size">JOHN project received funding from the European Union's Horizon Europe programme under grant agreement no. 101295913.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"align":"wide","className":"john-footer-rule","style":{"spacing":{"margin":{"top":"2.5rem","bottom":"2.25rem"}}}} -->
<hr class="wp-block-separator alignwide has-alpha-channel-opacity john-footer-rule" style="margin-top:2.5rem;margin-bottom:2.25rem"/>
<!-- /wp:separator -->

<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"fontSize":"x-small"} -->
<p class="has-x-small-font-size">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> JOHN project</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"john-footer-legal","fontSize":"x-small"} -->
<p class="john-footer-legal has-x-small-font-size"><a href="<?php echo esc_url( $privacy_policy_url ); ?>">Privacy policy</a> <a href="<?php echo esc_url( $accessibility_statement_url ); ?>">Accessibility statement</a> <a href="#">Cookie settings</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
