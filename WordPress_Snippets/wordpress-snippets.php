<?php 
/***
** Enqueue Google Fonts
***/ 

$google_fonts_args = array(

	'family' => 'Open Sans',
);


// WP Register Style

wp_register_style( 'test_theme-google-fonts', add_query_arg( $google_fonts_args, 'https://fonts.googleapis.com/css' ), array(), null );
wp_enqueue_style( 'test_theme-google-fonts' );




/***
** Make the href value of a main nav link equal to a custom field (ACF) value
***/

 add_filter( 'nav_menu_link_attributes', 'dynamic_menu_item_href', 10, 3 );
    function dynamic_menu_item_href( $atts, $item, $args ) {
		$menu_item = array(9);
	    global $post;
	    $lo_apply = get_field('lo_apply', $post->ID);
	    if (in_array($item->ID, $menu_item)) {
	    	$atts['href'] = $lo_apply;
		}
	return $atts;
    }


/***
** ACF and Gravity forms integration (dynamically populated fields)
***/ 

// // send form notification email to value of lo_email custom field on the page
// add_filter('gform_field_value_lo_email', 'populate_lo_email');
// function populate_lo_email($value){
//     global $post;

//     $lo_email = get_field('lo_email', $post->ID);

//     return $lo_email;
// }

// // include lo_name custom field in notification field options
// add_filter('gform_field_value_lo_name', 'populate_lo_name');
// function populate_lo_name($value){
//     global $post;

//     $lo_name = get_field('lo_name', $post->ID);

//     return $lo_name;
// } 

// // include loan product custom field in notification field options
// add_filter('gform_field_value_loan_product', 'populate_loan_product');
// function populate_loan_product($value){
//     global $post;

//     $loan_product = get_field('loan-product', $post->ID);

//     return $loan_product;
// } 

/*** Multiple fields at once ***/

add_filter( 'gform_field_value', 'populate_fields', 10, 3 );
function populate_fields( $value, $field, $name ) {
global $post;
    $values = array(
        'lo_email'   => get_field('lo_email', $post->ID),
        'lo_name'   => get_field('lo_name', $post->ID),
        'loan_product' => get_field('loan-product', $post->ID)
    );

    return isset( $values[ $name ] ) ? $values[ $name ] : $value;
}



/***
** Create blog breadcrumb navigation 
***/ 

function post_breadcrumb() {
    $postCategories= get_the_category();
        echo '<ul id="breadcrumb">';
        if (!is_home() && is_singular( 'post' )) {
        echo '<li><a href="' . site_url( "/blog/" );
        echo '">';
        echo 'Blog >';
        echo '</a></li><li>';
            if ( ! empty( $postCategories ) ) {
                echo '<a href="' . esc_url( get_category_link( $postCategories[0]->term_id ) ) . '">' . esc_html( $postCategories[0]->name ) . ' >' . '</a>';
                echo '</li><li>';
             }
                the_title();
                echo '</li>';
            }
         echo '</ul>';
    } ?>

<div class="breadcrumb-container"><?php post_breadcrumb(); ?></div>

<!--  Create 'news' custom post type breadcrumb navigation  -->
<?php 
function news_breadcrumb() {
        $currentPost = get_the_ID ();
        $terms = wp_get_post_terms( $currentPost, 'topic' ); 
        echo '<ul id="breadcrumb">';
        if (!is_home() && is_singular( 'news' )) {
        echo '<li><a href="' . site_url( "/news/" );
        echo '">';
        echo 'News >';
        echo '</a></li><li>';
            if ( ! empty( $terms ) ) {
                echo '<a href="' . esc_url( get_category_link( $terms[0]->term_id ) ) . '">' . esc_html( $terms[0]->name ) . ' >' . '</a>';
                echo '</li><li>';
             }
                the_title();
                echo '</li>';
            }
         echo '</ul>';
    }    

?>
<?php

/***
** A Blog Sidebar
***/
 

if ( ! is_active_sidebar( 'blogside' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area col-md-3 col-md-push-1 columns" role="complementary">

	<h5 class="widget-title">Learn More</h5>
	<hr>
	<h5 class="widget-title">Follow Atlantic Bay</h5>
		<?php dynamic_sidebar( 'blogside' ); ?>
	
<!-- This section will display a short list of recent posts within the current category, excluding the current post -->
	
 	<?php 
 		global $post;
		$currentPost = get_the_ID ();
		$categories = get_the_category();
		foreach ($categories as $category) :
	?>
	<hr>
	<h5 class="widget-title">Recent Posts In This Category</h5>
	<ul class="recentnav">
		<?php
		/* get_posts, simply references a new WP_Query object, and therefore does not affect or alter the main loop.
		If you would just like to call an array of posts based on a small and simple set of parameters within a page, 
		then get_posts is your best option. */
		$args = array(
			'numberposts' => 3,
	        'category' => $category->term_id, 
	        'exclude' => array($currentPost), 
		);
		$posts = get_posts($args);
		foreach($posts as $post) :
		?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
		<?php endforeach; ?>

		<?php endforeach; ?>
	</ul> 

</div><!-- #secondary -->

/***
** Add Author Meta 
***/

<!-- https://github.com/mor10/popperscores/commit/cdb556ff41e98b256a0119ebb21199cb8036bdb8 -->
