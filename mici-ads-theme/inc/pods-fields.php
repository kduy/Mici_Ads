<?php
/**
 * Mici Ads Theme — Pods Field Groups for Front Page
 *
 * Registers all landing page field groups using Pods API.
 * Fields are stored as flat post meta with prefixed names (e.g. hero_item_1_image).
 * The pods-compat-get-field.php wrapper assembles group sub-fields for templates.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register all front-page field groups with Pods.
 */
function mici_register_pods_fields() {
	if ( ! function_exists( 'pods_register_group' ) ) {
		return;
	}

	// Extend the built-in 'page' post type so Pods manages its custom fields.
	pods_register_type( 'post_type', 'page', array(
		'label' => 'Pages',
	) );

	// ① Hero Section.
	$hero_fields = array(
		'proof_text'         => array( 'name' => 'proof_text',         'label' => 'Proof Text (e.g. "500+ khách hàng hài lòng")', 'type' => 'text' ),
		'hero_title'         => array( 'name' => 'hero_title',         'label' => 'Hero Title (supports <br> <em>)',               'type' => 'paragraph' ),
		'hero_subtitle'      => array( 'name' => 'hero_subtitle',      'label' => 'Hero Subtitle',                                 'type' => 'paragraph' ),
		'cta_primary_text'   => array( 'name' => 'cta_primary_text',   'label' => 'CTA Primary — Button Text',                     'type' => 'text' ),
		'cta_primary_url'    => array( 'name' => 'cta_primary_url',    'label' => 'CTA Primary — URL',                             'type' => 'website' ),
		'cta_secondary_text' => array( 'name' => 'cta_secondary_text', 'label' => 'CTA Secondary — Button Text',                   'type' => 'text' ),
		'cta_secondary_url'  => array( 'name' => 'cta_secondary_url',  'label' => 'CTA Secondary — URL',                           'type' => 'website' ),
	);

	// Hero gallery items (4 groups, flattened to individual fields).
	for ( $i = 1; $i <= 4; $i++ ) {
		$prefix = 'hero_item_' . $i;
		$hero_fields[ $prefix . '_heading' ] = array( 'name' => $prefix . '_heading', 'label' => 'Gallery Item ' . $i, 'type' => 'heading' );
		$hero_fields[ $prefix . '_image' ]   = array( 'name' => $prefix . '_image',   'label' => 'Image (leave empty for text card)', 'type' => 'file', 'file_type' => 'images' );
		$hero_fields[ $prefix . '_label' ]   = array( 'name' => $prefix . '_label',   'label' => 'Card Label (e.g. "OLIVIA")',        'type' => 'text' );
		$hero_fields[ $prefix . '_sublabel' ]   = array( 'name' => $prefix . '_sublabel',   'label' => 'Card Sublabel',                    'type' => 'text' );
		$hero_fields[ $prefix . '_background' ] = array( 'name' => $prefix . '_background', 'label' => 'Card Background CSS',              'type' => 'text' );
		$hero_fields[ $prefix . '_text_color' ] = array( 'name' => $prefix . '_text_color', 'label' => 'Card Text Color',                  'type' => 'text' );
	}

	pods_register_group(
		array( 'name' => 'mici_hero', 'label' => '① Hero Section', 'weight' => 0 ),
		'page',
		$hero_fields
	);

	// ② Trust Bar.
	pods_register_group(
		array( 'name' => 'mici_trust', 'label' => '② Trust Bar', 'weight' => 1 ),
		'page',
		array(
			'trust_text'         => array( 'name' => 'trust_text',         'label' => 'Trust Statement',            'type' => 'text' ),
			'trust_clients_list' => array( 'name' => 'trust_clients_list', 'label' => 'Client Names (one per line)', 'type' => 'paragraph', 'paragraph_max_length' => 0 ),
		)
	);

	// ③ Services Section (4 items).
	$service_fields = array(
		'section_title' => array( 'name' => 'section_title', 'label' => 'Section Title (supports <em>)', 'type' => 'text' ),
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$prefix = 'service_' . $i;
		$service_fields[ $prefix . '_heading' ]     = array( 'name' => $prefix . '_heading',     'label' => 'Service ' . $i,            'type' => 'heading' );
		$service_fields[ $prefix . '_image' ]       = array( 'name' => $prefix . '_image',       'label' => 'Image',                    'type' => 'file', 'file_type' => 'images' );
		$service_fields[ $prefix . '_title' ]       = array( 'name' => $prefix . '_title',       'label' => 'Title',                    'type' => 'text' );
		$service_fields[ $prefix . '_description' ] = array( 'name' => $prefix . '_description', 'label' => 'Description',              'type' => 'paragraph' );
		$service_fields[ $prefix . '_tags' ]        = array( 'name' => $prefix . '_tags',        'label' => 'Tags (comma separated)',    'type' => 'text' );
	}
	pods_register_group(
		array( 'name' => 'mici_services', 'label' => '③ Services Section', 'weight' => 2 ),
		'page',
		$service_fields
	);

	// ④ Tagline.
	pods_register_group(
		array( 'name' => 'mici_tagline', 'label' => '④ Tagline', 'weight' => 3 ),
		'page',
		array(
			'tagline_text' => array( 'name' => 'tagline_text', 'label' => 'Tagline (supports <em>)', 'type' => 'text' ),
		)
	);

	// ⑤ Portfolio Marquee (12 individual image fields).
	$portfolio_fields = array();
	for ( $i = 1; $i <= 12; $i++ ) {
		$col = ( ( $i - 1 ) % 3 ) + 1;
		$portfolio_fields[ 'portfolio_image_' . $i ] = array(
			'name'      => 'portfolio_image_' . $i,
			'label'     => 'Image ' . $i . ' (Column ' . $col . ')',
			'type'      => 'file',
			'file_type' => 'images',
		);
	}
	pods_register_group(
		array( 'name' => 'mici_portfolio', 'label' => '⑤ Portfolio Marquee', 'weight' => 4 ),
		'page',
		$portfolio_fields
	);

	// ⑥ Benefits Section (6 items).
	$benefit_fields = array(
		'benefits_section_title' => array( 'name' => 'benefits_section_title', 'label' => 'Section Title (supports <em>)', 'type' => 'text' ),
	);
	for ( $i = 1; $i <= 6; $i++ ) {
		$prefix = 'benefit_' . $i;
		$benefit_fields[ $prefix . '_heading' ]     = array( 'name' => $prefix . '_heading',     'label' => 'Benefit ' . $i,  'type' => 'heading' );
		$benefit_fields[ $prefix . '_title' ]       = array( 'name' => $prefix . '_title',       'label' => 'Title',          'type' => 'text' );
		$benefit_fields[ $prefix . '_description' ] = array( 'name' => $prefix . '_description', 'label' => 'Description',    'type' => 'paragraph' );
	}
	pods_register_group(
		array( 'name' => 'mici_benefits', 'label' => '⑥ Benefits Section', 'weight' => 5 ),
		'page',
		$benefit_fields
	);

	// ⑦ Testimonials Section (4 items).
	$testimonial_fields = array(
		'testimonials_section_title' => array( 'name' => 'testimonials_section_title', 'label' => 'Section Title (supports <em>)', 'type' => 'text' ),
	);
	for ( $i = 1; $i <= 4; $i++ ) {
		$prefix = 'testimonial_' . $i;
		$testimonial_fields[ $prefix . '_heading' ]        = array( 'name' => $prefix . '_heading',        'label' => 'Testimonial ' . $i,          'type' => 'heading' );
		$testimonial_fields[ $prefix . '_quote' ]          = array( 'name' => $prefix . '_quote',          'label' => 'Quote',                      'type' => 'paragraph' );
		$testimonial_fields[ $prefix . '_name' ]           = array( 'name' => $prefix . '_name',           'label' => 'Name',                       'type' => 'text' );
		$testimonial_fields[ $prefix . '_role' ]           = array( 'name' => $prefix . '_role',           'label' => 'Role / Location',            'type' => 'text' );
		$testimonial_fields[ $prefix . '_avatar_initial' ] = array( 'name' => $prefix . '_avatar_initial', 'label' => 'Avatar Initial (1 letter)',  'type' => 'text' );
		$testimonial_fields[ $prefix . '_rating' ]         = array( 'name' => $prefix . '_rating',         'label' => 'Rating (1-5)',               'type' => 'number', 'number_min' => 1, 'number_max' => 5 );
	}
	pods_register_group(
		array( 'name' => 'mici_testimonials', 'label' => '⑦ Testimonials Section', 'weight' => 6 ),
		'page',
		$testimonial_fields
	);

	// ⑧ FAQ Section (8 items).
	$faq_fields = array(
		'faq_section_title' => array( 'name' => 'faq_section_title', 'label' => 'Section Title (supports <em>)', 'type' => 'text' ),
	);
	for ( $i = 1; $i <= 8; $i++ ) {
		$prefix    = 'faq_' . $i;
		$opt_label = $i > 5 ? ' (optional)' : '';
		$faq_fields[ $prefix . '_heading' ]  = array( 'name' => $prefix . '_heading',  'label' => 'FAQ ' . $i . $opt_label, 'type' => 'heading' );
		$faq_fields[ $prefix . '_question' ] = array( 'name' => $prefix . '_question', 'label' => 'Question',               'type' => 'text' );
		$faq_fields[ $prefix . '_answer' ]   = array( 'name' => $prefix . '_answer',   'label' => 'Answer',                 'type' => 'paragraph' );
	}
	pods_register_group(
		array( 'name' => 'mici_faq', 'label' => '⑧ FAQ Section', 'weight' => 7 ),
		'page',
		$faq_fields
	);

	// ⑨ CTA Footer.
	pods_register_group(
		array( 'name' => 'mici_cta_footer', 'label' => '⑨ CTA Footer Section', 'weight' => 8 ),
		'page',
		array(
			'cta_title'    => array( 'name' => 'cta_title',    'label' => 'CTA Title (supports <em> <br>)', 'type' => 'text' ),
			'cta_subtitle' => array( 'name' => 'cta_subtitle', 'label' => 'CTA Button Text',                'type' => 'text' ),
		)
	);

	// ⑩ Contact Section.
	pods_register_group(
		array( 'name' => 'mici_contact', 'label' => '⑩ Contact Section', 'weight' => 9 ),
		'page',
		array(
			'contact_title'    => array( 'name' => 'contact_title',    'label' => 'Contact Title',                             'type' => 'text' ),
			'contact_subtitle' => array( 'name' => 'contact_subtitle', 'label' => 'Contact Subtitle',                          'type' => 'text' ),
			'wpforms_id'       => array( 'name' => 'wpforms_id',       'label' => 'WPForms ID (leave empty for default form)',  'type' => 'number' ),
		)
	);
}
add_action( 'init', 'mici_register_pods_fields', 15 );
