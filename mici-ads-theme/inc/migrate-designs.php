<?php
/**
 * Admin migration tool: Import Designs
 *
 * Registers a Tools > Import Designs admin page.
 * On form submit inserts all 53 portfolio designs as 'design' CPT posts,
 * skipping duplicates by title.
 *
 * @package MiciAds
 */

defined( 'ABSPATH' ) || exit;

// Register admin menu under Tools.
add_action( 'admin_menu', 'mici_register_migrate_designs_page' );

function mici_register_migrate_designs_page() {
	add_management_page(
		__( 'Import Designs', 'mici-ads' ),
		__( 'Import Designs', 'mici-ads' ),
		'manage_options',
		'mici-import-designs',
		'mici_render_migrate_designs_page'
	);
}

/**
 * Returns the full 53-item portfolio dataset.
 * Mirrors portfolioItems from js/main.js.
 */
function mici_get_design_data() {
	return [
		// --- Nail Salon Designs ---
		[ 'id' => 1,  'name' => 'Olivia Nail Art Salon',    'industry' => 'nail',       'category' => 'logo',         'style' => 'minimal',      'theme' => 'card-theme-nail-2',    'logo' => 'OLIVIA',       'sub' => 'Nail Art Salon',        'detail' => 'Logo Design',           'colors' => [ '#fff5f5', '#fce4ec', '#f06292', '#ad1457' ] ],
		[ 'id' => 2,  'name' => 'Licera & Co Studio',       'industry' => 'nail',       'category' => 'logo',         'style' => 'elegant',      'theme' => 'card-theme-nail-1',    'logo' => 'Licera & Co',  'sub' => 'Nail Art Studio',       'detail' => 'Brand Identity',        'colors' => [ '#fce4ec', '#f4c2c2', '#e91e63', '#880e4f' ] ],
		[ 'id' => 3,  'name' => 'Nails Salon Price List',   'industry' => 'nail',       'category' => 'menu',         'style' => 'feminine',     'theme' => 'card-theme-nail-4',    'logo' => 'Nails Salon',  'sub' => 'Price List',            'detail' => 'Manicure & Pedicure',   'colors' => [ '#fdf2f8', '#fbcfe8', '#ec4899', '#831843' ] ],
		[ 'id' => 4,  'name' => 'Beauty Studio Services',   'industry' => 'nail',       'category' => 'flyer',        'style' => 'professional', 'theme' => 'card-theme-nail-3',    'logo' => 'Beauty Studio','sub' => 'Nail Services',         'detail' => '50% Off Promotion',     'colors' => [ '#faf5f0', '#f5ebe0', '#d4a373', '#6b4226' ] ],
		[ 'id' => 5,  'name' => 'Nail Lounge Loyalty',      'industry' => 'nail',       'category' => 'loyalty-card', 'style' => 'luxury',       'theme' => 'card-theme-nail-5',    'logo' => 'Nail Lounge',  'sub' => 'Loyalty Card',          'detail' => 'Collect 10 Get 1 Free', 'colors' => [ '#f9f5f0', '#ede0d4', '#bc8a5f', '#5c4033' ] ],
		[ 'id' => 6,  'name' => 'Anna Wilson Studio',       'industry' => 'nail',       'category' => 'flyer',        'style' => 'clean',        'theme' => 'card-theme-nail-2',    'logo' => 'Anna Wilson',  'sub' => 'Nail Studio',           'detail' => 'Business Card',         'colors' => [ '#fff5f5', '#fce4ec', '#e57373', '#c62828' ] ],
		[ 'id' => 7,  'name' => 'Larana Pure Polish',       'industry' => 'nail',       'category' => 'flyer',        'style' => 'modern',       'theme' => 'card-theme-nail-3',    'logo' => 'LARANA',       'sub' => 'Pure Polish',           'detail' => 'Grand Opening Flyer',   'colors' => [ '#faf5f0', '#f5ebe0', '#c9a87c', '#8d6e4c' ] ],
		[ 'id' => 8,  'name' => 'Mani & Pedi Gift Card',    'industry' => 'nail',       'category' => 'voucher',      'style' => 'beautiful',    'theme' => 'card-theme-nail-1',    'logo' => 'Gift Card',    'sub' => 'Mani & Pedi',           'detail' => '$50 Value',             'colors' => [ '#fce4ec', '#f8bbd0', '#e91e63', '#ad1457' ] ],
		[ 'id' => 9,  'name' => 'Nail Art Website',         'industry' => 'nail',       'category' => 'website',      'style' => 'aesthetic',    'theme' => 'card-theme-nail-5',    'logo' => 'NailArt.co',   'sub' => 'Website Design',        'detail' => 'Landing Page',          'colors' => [ '#f9f5f0', '#ede0d4', '#c4a882', '#7c5e42' ] ],
		// --- Restaurant Designs ---
		[ 'id' => 10, 'name' => 'Rosa Maria Restaurant',    'industry' => 'restaurant', 'category' => 'logo',         'style' => 'classic',      'theme' => 'card-theme-rest-1',    'logo' => 'Rosa Maria',   'sub' => 'Filipino Restaurant',   'detail' => 'Since 1990',            'colors' => [ '#fefae0', '#dda15e', '#bc6c25', '#283618' ] ],
		[ 'id' => 11, 'name' => 'Main Courses Menu',        'industry' => 'restaurant', 'category' => 'menu',         'style' => 'minimalist',   'theme' => 'card-theme-rest-3',    'logo' => 'Main Courses', 'sub' => 'Restaurant Menu',       'detail' => 'Seasonal Edition',      'colors' => [ '#fefae0', '#e9edc9', '#588157', '#344e41' ] ],
		[ 'id' => 12, 'name' => 'Rimberio Restaurant',      'industry' => 'restaurant', 'category' => 'logo',         'style' => 'bold',         'theme' => 'card-theme-rest-2',    'logo' => 'Rimberio',     'sub' => 'Restaurant',            'detail' => 'Brand Identity',        'colors' => [ '#fefae0', '#dda15e', '#5b0e0e', '#2c1810' ] ],
		[ 'id' => 13, 'name' => 'Kitchen Delicious Logo',   'industry' => 'restaurant', 'category' => 'logo',         'style' => 'simple',       'theme' => 'card-theme-rest-4',    'logo' => 'KITCHEN',      'sub' => 'Delicious',             'detail' => 'Minimal Logo',          'colors' => [ '#f5f0e8', '#d5c4a1', '#a68a64', '#4a3728' ] ],
		[ 'id' => 14, 'name' => 'Food Menu Borcelle',       'industry' => 'restaurant', 'category' => 'menu',         'style' => 'elegant',      'theme' => 'card-theme-rest-5',    'logo' => 'FOOD MENU',    'sub' => 'Borcelle Restaurant',   'detail' => 'Full Menu Layout',      'colors' => [ '#1a1a2e', '#16213e', '#0f3460', '#e2e8f0' ] ],
		[ 'id' => 15, 'name' => 'Tasty Food Flyer',         'industry' => 'restaurant', 'category' => 'flyer',        'style' => 'vibrant',      'theme' => 'card-theme-rest-1',    'logo' => 'Tasty Food',   'sub' => 'Western Cuisine',       'detail' => 'Promotional Flyer',     'colors' => [ '#fefae0', '#faedcd', '#d4a373', '#bc6c25' ] ],
		[ 'id' => 16, 'name' => 'Restaurant Loyalty Card',  'industry' => 'restaurant', 'category' => 'loyalty-card', 'style' => 'gold',         'theme' => 'card-theme-rest-4',    'logo' => 'Dine & Earn',  'sub' => 'Loyalty Program',       'detail' => 'Stamp Card',            'colors' => [ '#f5f0e8', '#e6d5b8', '#b8956a', '#3a2a1a' ] ],
		[ 'id' => 17, 'name' => 'Chef Special Voucher',     'industry' => 'restaurant', 'category' => 'voucher',      'style' => 'creative',     'theme' => 'card-theme-rest-2',    'logo' => 'Gift Voucher', 'sub' => "Chef's Special",        'detail' => 'Dining Experience',     'colors' => [ '#fefae0', '#c9a96e', '#7a3b2e', '#2c1810' ] ],
		[ 'id' => 18, 'name' => 'Restaurant Website',       'industry' => 'restaurant', 'category' => 'website',      'style' => 'clean',        'theme' => 'card-theme-rest-3',    'logo' => 'ThynkKitchen', 'sub' => 'Website Design',        'detail' => 'Responsive Site',       'colors' => [ '#fefae0', '#e9edc9', '#588157', '#2d4032' ] ],
		// --- Beauty Designs ---
		[ 'id' => 19, 'name' => 'Glow Spa Studio',          'industry' => 'beauty',     'category' => 'logo',         'style' => 'elegant',      'theme' => 'card-theme-beauty-1',  'logo' => 'GLOW',         'sub' => 'Spa & Wellness',        'detail' => 'Brand Identity',        'colors' => [ '#ede9fe', '#ddd6fe', '#8b5cf6', '#5b21b6' ] ],
		[ 'id' => 20, 'name' => 'Hair Salon Promo',          'industry' => 'beauty',     'category' => 'flyer',        'style' => 'modern',       'theme' => 'card-theme-beauty-2',  'logo' => 'Luxe Hair',    'sub' => 'Hair Studio',           'detail' => 'Summer Special',        'colors' => [ '#faf5ff', '#e9d5ff', '#a855f7', '#7c3aed' ] ],
		[ 'id' => 21, 'name' => 'Skincare Treatment Menu',  'industry' => 'beauty',     'category' => 'menu',         'style' => 'minimalist',   'theme' => 'card-theme-beauty-3',  'logo' => 'Derma Skin',   'sub' => 'Treatment Menu',        'detail' => 'Facial & Body Care',    'colors' => [ '#fdf4ff', '#f5d0fe', '#d946ef', '#a21caf' ] ],
		[ 'id' => 22, 'name' => 'Beauty Rewards Card',      'industry' => 'beauty',     'category' => 'loyalty-card', 'style' => 'luxury',       'theme' => 'card-theme-beauty-1',  'logo' => 'Belle Card',   'sub' => 'Loyalty Program',       'detail' => 'Earn Points',           'colors' => [ '#ede9fe', '#c4b5fd', '#7c3aed', '#4c1d95' ] ],
		[ 'id' => 23, 'name' => 'Spa Gift Voucher',         'industry' => 'beauty',     'category' => 'voucher',      'style' => 'beautiful',    'theme' => 'card-theme-beauty-2',  'logo' => 'Gift Card',    'sub' => 'Spa Experience',        'detail' => '$100 Value',            'colors' => [ '#faf5ff', '#e9d5ff', '#c084fc', '#7e22ce' ] ],
		// --- Cafe Designs ---
		[ 'id' => 24, 'name' => 'Brew & Bean Cafe',         'industry' => 'cafe',       'category' => 'logo',         'style' => 'minimal',      'theme' => 'card-theme-cafe-1',    'logo' => 'BREW',         'sub' => 'Coffee House',          'detail' => 'Est. 2020',             'colors' => [ '#fef9ee', '#f5e6c8', '#b8860b', '#5c3a1e' ] ],
		[ 'id' => 25, 'name' => 'Coffee Menu Board',        'industry' => 'cafe',       'category' => 'menu',         'style' => 'clean',        'theme' => 'card-theme-cafe-3',    'logo' => 'Daily Brew',   'sub' => 'Coffee Menu',           'detail' => 'Hot & Cold Drinks',     'colors' => [ '#3b2314', '#5c3a1e', '#d4a373', '#f5e6c8' ] ],
		[ 'id' => 26, 'name' => 'Bakery Grand Opening',     'industry' => 'cafe',       'category' => 'flyer',        'style' => 'creative',     'theme' => 'card-theme-cafe-2',    'logo' => 'Sweet Crust',  'sub' => 'Artisan Bakery',        'detail' => 'Opening Day Deals',     'colors' => [ '#f0fdf4', '#dcfce7', '#4ade80', '#166534' ] ],
		[ 'id' => 27, 'name' => 'Cafe Stamp Card',          'industry' => 'cafe',       'category' => 'loyalty-card', 'style' => 'simple',       'theme' => 'card-theme-cafe-1',    'logo' => '10th Free',    'sub' => 'Coffee Loyalty',        'detail' => 'Buy 9 Get 1 Free',      'colors' => [ '#fef9ee', '#e8d5b0', '#92702a', '#3b2314' ] ],
		// --- Others ---
		[ 'id' => 28, 'name' => 'Focus Photography',        'industry' => 'others',     'category' => 'logo',         'style' => 'professional', 'theme' => 'card-theme-other-1',   'logo' => 'FOCUS',        'sub' => 'Photography Studio',    'detail' => 'Brand Identity',        'colors' => [ '#f0f9ff', '#dbeafe', '#3b82f6', '#1e40af' ] ],
		[ 'id' => 29, 'name' => 'FitZone Gym Flyer',        'industry' => 'others',     'category' => 'flyer',        'style' => 'bold',         'theme' => 'card-theme-other-3',   'logo' => 'FIT ZONE',     'sub' => 'Fitness Center',        'detail' => 'Join Today 50% Off',    'colors' => [ '#1e293b', '#334155', '#f97316', '#ea580c' ] ],
		[ 'id' => 30, 'name' => 'Pet Grooming Voucher',     'industry' => 'others',     'category' => 'voucher',      'style' => 'beautiful',    'theme' => 'card-theme-other-2',   'logo' => 'Pawfect',      'sub' => 'Pet Grooming',          'detail' => '$30 Gift Card',         'colors' => [ '#f8fafc', '#e2e8f0', '#60a5fa', '#2563eb' ] ],
		[ 'id' => 31, 'name' => 'Yoga Studio Website',      'industry' => 'others',     'category' => 'website',      'style' => 'aesthetic',    'theme' => 'card-theme-other-1',   'logo' => 'ZenFlow',      'sub' => 'Yoga & Meditation',     'detail' => 'Landing Page',          'colors' => [ '#f0f9ff', '#bae6fd', '#0ea5e9', '#0369a1' ] ],
		// --- Additional Nail ---
		[ 'id' => 32, 'name' => 'Luxe Nails Price Menu',    'industry' => 'nail',       'category' => 'menu',         'style' => 'elegant',      'theme' => 'card-theme-nail-5',    'logo' => 'LUXE NAILS',   'sub' => 'Price Menu',            'detail' => 'Full Service List',     'colors' => [ '#f9f5f0', '#ede0d4', '#c4a882', '#5c4033' ] ],
		[ 'id' => 33, 'name' => 'Pink Blossom Nail Promo',  'industry' => 'nail',       'category' => 'flyer',        'style' => 'feminine',     'theme' => 'card-theme-nail-4',    'logo' => 'Pink Blossom', 'sub' => 'Nail Studio',           'detail' => '50% Off Opening',       'colors' => [ '#fdf2f8', '#fce7f3', '#f472b6', '#be185d' ] ],
		[ 'id' => 34, 'name' => 'Diamond Nails VIP Card',   'industry' => 'nail',       'category' => 'loyalty-card', 'style' => 'luxury',       'theme' => 'card-theme-nail-1',    'logo' => 'DIAMOND',      'sub' => 'VIP Loyalty',           'detail' => 'Premium Member',        'colors' => [ '#fce4ec', '#f8bbd0', '#c2185b', '#880e4f' ] ],
		[ 'id' => 35, 'name' => 'Gel Polish Studio Logo',   'industry' => 'nail',       'category' => 'logo',         'style' => 'modern',       'theme' => 'card-theme-nail-3',    'logo' => 'GEL STUDIO',   'sub' => 'Polish & Care',         'detail' => 'Brand Mark',            'colors' => [ '#faf5f0', '#f5ebe0', '#d4a373', '#8d6e4c' ] ],
		// --- Additional Restaurant ---
		[ 'id' => 36, 'name' => 'Catering Delicious Menu',  'industry' => 'restaurant', 'category' => 'menu',         'style' => 'classic',      'theme' => 'card-theme-rest-2',    'logo' => 'CATERING',     'sub' => 'Delicious Food',        'detail' => 'Full Course Menu',      'colors' => [ '#fefae0', '#dda15e', '#7a3b2e', '#2c1810' ] ],
		[ 'id' => 37, 'name' => 'Seafood Feast Flyer',      'industry' => 'restaurant', 'category' => 'flyer',        'style' => 'bold',         'theme' => 'card-theme-rest-1',    'logo' => 'SEAFOOD',      'sub' => 'Fresh Catch',           'detail' => 'Weekend Special',       'colors' => [ '#fefae0', '#faedcd', '#bc6c25', '#283618' ] ],
		[ 'id' => 38, 'name' => 'Italian Cuisine Menu',     'industry' => 'restaurant', 'category' => 'menu',         'style' => 'minimalist',   'theme' => 'card-theme-rest-5',    'logo' => 'CUCINA',       'sub' => 'Italian Fine Dining',   'detail' => 'Seasonal Menu',         'colors' => [ '#1a1a2e', '#16213e', '#0f3460', '#e2e8f0' ] ],
		[ 'id' => 39, 'name' => 'BBQ Grill House Logo',     'industry' => 'restaurant', 'category' => 'logo',         'style' => 'bold',         'theme' => 'card-theme-rest-4',    'logo' => 'BBQ GRILL',    'sub' => 'Smokehouse',            'detail' => 'Est. 2018',             'colors' => [ '#f5f0e8', '#d5c4a1', '#8b5e3c', '#3a2a1a' ] ],
		[ 'id' => 40, 'name' => 'Fast Food Restaurant',     'industry' => 'restaurant', 'category' => 'flyer',        'style' => 'vibrant',      'theme' => 'card-theme-rest-1',    'logo' => 'FAST FOOD',    'sub' => 'Quick Bites',           'detail' => 'Combo Deals',           'colors' => [ '#fefae0', '#faedcd', '#e76f51', '#bc6c25' ] ],
		// --- Additional Beauty ---
		[ 'id' => 41, 'name' => 'Eyelash Studio Promo',     'industry' => 'beauty',     'category' => 'flyer',        'style' => 'elegant',      'theme' => 'card-theme-beauty-3',  'logo' => 'LASH BAR',     'sub' => 'Eyelash Studio',        'detail' => 'Extension Special',     'colors' => [ '#fdf4ff', '#f5d0fe', '#c026d3', '#86198f' ] ],
		[ 'id' => 42, 'name' => 'Keithston Beauty Logo',    'industry' => 'beauty',     'category' => 'logo',         'style' => 'professional', 'theme' => 'card-theme-beauty-1',  'logo' => 'KEITHSTON',    'sub' => 'Beauty Salon',          'detail' => 'Brand Identity',        'colors' => [ '#ede9fe', '#ddd6fe', '#7c3aed', '#4c1d95' ] ],
		[ 'id' => 43, 'name' => 'Beauty Room Services',     'industry' => 'beauty',     'category' => 'menu',         'style' => 'feminine',     'theme' => 'card-theme-beauty-2',  'logo' => 'Beauty Room',  'sub' => 'Hair Makeup Nails',     'detail' => 'Service Menu',          'colors' => [ '#faf5ff', '#e9d5ff', '#a855f7', '#7e22ce' ] ],
		[ 'id' => 44, 'name' => 'Glow Spa Website',         'industry' => 'beauty',     'category' => 'website',      'style' => 'aesthetic',    'theme' => 'card-theme-beauty-1',  'logo' => 'GLOW SPA',     'sub' => 'Wellness Center',       'detail' => 'Landing Page',          'colors' => [ '#ede9fe', '#c4b5fd', '#8b5cf6', '#5b21b6' ] ],
		[ 'id' => 45, 'name' => 'Hair Salon Gift Card',     'industry' => 'beauty',     'category' => 'voucher',      'style' => 'luxury',       'theme' => 'card-theme-beauty-3',  'logo' => 'Gift Card',    'sub' => 'Hair Treatment',        'detail' => '$75 Value',             'colors' => [ '#fdf4ff', '#e9d5ff', '#d946ef', '#a21caf' ] ],
		// --- Additional Cafe ---
		[ 'id' => 46, 'name' => 'Borcelle Cafe Menu',       'industry' => 'cafe',       'category' => 'menu',         'style' => 'elegant',      'theme' => 'card-theme-cafe-3',    'logo' => 'BORCELLE',     'sub' => 'Cafe & Bakery',         'detail' => 'Full Menu',             'colors' => [ '#3b2314', '#5c3a1e', '#c9a87c', '#f5e6c8' ] ],
		[ 'id' => 47, 'name' => 'Iced Coffee Promo',        'industry' => 'cafe',       'category' => 'flyer',        'style' => 'creative',     'theme' => 'card-theme-cafe-2',    'logo' => 'ICED',         'sub' => 'Coffee Special',        'detail' => '$2.99 Cups',            'colors' => [ '#f0fdf4', '#bbf7d0', '#22c55e', '#166534' ] ],
		[ 'id' => 48, 'name' => 'Coffee Shop Price List',   'industry' => 'cafe',       'category' => 'menu',         'style' => 'simple',       'theme' => 'card-theme-cafe-1',    'logo' => 'COFFEE',       'sub' => 'Price List',            'detail' => 'Hot & Cold Drinks',     'colors' => [ '#fef9ee', '#f5e6c8', '#92702a', '#5c3a1e' ] ],
		[ 'id' => 49, 'name' => 'Artisan Roast Logo',       'industry' => 'cafe',       'category' => 'logo',         'style' => 'minimal',      'theme' => 'card-theme-cafe-3',    'logo' => 'ARTISAN',      'sub' => 'Roast Coffee',          'detail' => 'Since 2019',            'colors' => [ '#3b2314', '#6b4a2e', '#d4a373', '#f5e6c8' ] ],
		[ 'id' => 50, 'name' => 'Cafe Gift Voucher',        'industry' => 'cafe',       'category' => 'voucher',      'style' => 'beautiful',    'theme' => 'card-theme-cafe-1',    'logo' => 'Gift Card',    'sub' => 'Coffee Lovers',         'detail' => '$25 Value',             'colors' => [ '#fef9ee', '#e8d5b0', '#b8860b', '#3b2314' ] ],
		// --- Additional Others ---
		[ 'id' => 51, 'name' => 'Fitness Studio Loyalty',   'industry' => 'others',     'category' => 'loyalty-card', 'style' => 'bold',         'theme' => 'card-theme-other-3',   'logo' => 'FIT CLUB',     'sub' => 'Gym Membership',        'detail' => '10 Sessions Card',      'colors' => [ '#1e293b', '#334155', '#f97316', '#ea580c' ] ],
		[ 'id' => 52, 'name' => 'Photography Portfolio',    'industry' => 'others',     'category' => 'website',      'style' => 'minimalist',   'theme' => 'card-theme-other-1',   'logo' => 'LENS',         'sub' => 'Photo Portfolio',       'detail' => 'Gallery Website',       'colors' => [ '#f0f9ff', '#dbeafe', '#3b82f6', '#1e40af' ] ],
		[ 'id' => 53, 'name' => 'Pet Care Service Menu',    'industry' => 'others',     'category' => 'menu',         'style' => 'clean',        'theme' => 'card-theme-other-2',   'logo' => 'PawCare',      'sub' => 'Pet Grooming',          'detail' => 'Service Prices',        'colors' => [ '#f8fafc', '#e2e8f0', '#60a5fa', '#2563eb' ] ],
	];
}

/**
 * Renders the Import Designs admin page and handles the import form.
 */
function mici_render_migrate_designs_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorized.', 'mici-ads' ) );
	}

	$results = [];

	if ( isset( $_POST['mici_import_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mici_import_nonce'] ) ), 'mici_import_designs' ) ) {
		$designs  = mici_get_design_data();
		$inserted = 0;
		$skipped  = 0;
		$errors   = 0;

		foreach ( $designs as $design ) {
			// Skip if a post with this title already exists.
			$existing = get_page_by_title( $design['name'], OBJECT, 'design' );
			if ( $existing ) {
				$skipped++;
				continue;
			}

			$post_id = wp_insert_post( [
				'post_title'  => sanitize_text_field( $design['name'] ),
				'post_status' => 'publish',
				'post_type'   => 'design',
			], true );

			if ( is_wp_error( $post_id ) ) {
				$errors++;
				continue;
			}

			// Store all meta fields.
			update_post_meta( $post_id, 'design_id',       absint( $design['id'] ) );
			update_post_meta( $post_id, 'design_industry', sanitize_text_field( $design['industry'] ) );
			update_post_meta( $post_id, 'design_category', sanitize_text_field( $design['category'] ) );
			update_post_meta( $post_id, 'design_style',    sanitize_text_field( $design['style'] ) );
			update_post_meta( $post_id, 'design_theme',    sanitize_text_field( $design['theme'] ) );
			update_post_meta( $post_id, 'design_logo',     sanitize_text_field( $design['logo'] ) );
			update_post_meta( $post_id, 'design_sub',      sanitize_text_field( $design['sub'] ) );
			update_post_meta( $post_id, 'design_detail',   sanitize_text_field( $design['detail'] ) );
			update_post_meta( $post_id, 'design_colors',   array_map( 'sanitize_hex_color', $design['colors'] ) );

			$inserted++;
		}

		$results = compact( 'inserted', 'skipped', 'errors' );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Import Designs', 'mici-ads' ); ?></h1>
		<p><?php esc_html_e( 'Imports all 53 portfolio designs as Design CPT posts. Duplicates (matched by title) are skipped.', 'mici-ads' ); ?></p>

		<?php if ( ! empty( $results ) ) : ?>
			<div class="notice notice-success">
				<p>
					<?php
					printf(
						/* translators: 1: inserted count 2: skipped count 3: error count */
						esc_html__( 'Import complete: %1$d inserted, %2$d skipped (duplicates), %3$d errors.', 'mici-ads' ),
						(int) $results['inserted'],
						(int) $results['skipped'],
						(int) $results['errors']
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'mici_import_designs', 'mici_import_nonce' ); ?>
			<?php submit_button( __( 'Run Import (53 Designs)', 'mici-ads' ), 'primary', 'submit', true ); ?>
		</form>

		<h2><?php esc_html_e( 'Preview (53 designs)', 'mici-ads' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'mici-ads' ); ?></th>
					<th><?php esc_html_e( 'Name', 'mici-ads' ); ?></th>
					<th><?php esc_html_e( 'Industry', 'mici-ads' ); ?></th>
					<th><?php esc_html_e( 'Category', 'mici-ads' ); ?></th>
					<th><?php esc_html_e( 'Style', 'mici-ads' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( mici_get_design_data() as $d ) : ?>
					<tr>
						<td><?php echo absint( $d['id'] ); ?></td>
						<td><?php echo esc_html( $d['name'] ); ?></td>
						<td><?php echo esc_html( $d['industry'] ); ?></td>
						<td><?php echo esc_html( $d['category'] ); ?></td>
						<td><?php echo esc_html( $d['style'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
