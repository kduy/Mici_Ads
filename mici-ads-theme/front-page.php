<?php
/**
 * Mici Ads Theme — Front Page Template
 *
 * Orchestrates all landing page template parts in sequence.
 *
 * @package MiciAds
 */

get_header();

get_template_part( 'template-parts/landing', 'hero' );
get_template_part( 'template-parts/landing', 'trust-bar' );
get_template_part( 'template-parts/landing', 'services' );
get_template_part( 'template-parts/landing', 'tagline' );
get_template_part( 'template-parts/landing', 'portfolio-marquee' );
get_template_part( 'template-parts/landing', 'benefits' );
get_template_part( 'template-parts/landing', 'testimonials' );
get_template_part( 'template-parts/landing', 'faq' );
get_template_part( 'template-parts/landing', 'cta-footer' );
get_template_part( 'template-parts/landing', 'contact' );

get_footer();
