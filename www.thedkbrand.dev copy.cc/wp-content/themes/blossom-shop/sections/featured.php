<?php
/**
 * Feature Section
 * 
 * @package Blossom_Shop
 */
$ed_featured         = get_theme_mod( 'ed_featured_section', false );
$featured_page_one   = get_theme_mod( 'featured_content_one' );
$featured_page_two   = get_theme_mod( 'featured_content_two' );
$featured_page_three = get_theme_mod( 'featured_content_three' );

$image_size = '';
$featured_pages      = array( $featured_page_one, $featured_page_two, $featured_page_three );
$featured_pages      = array_diff( array_unique( $featured_pages), array( '' ) );

$args = array(
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'post__in'       => $featured_pages,
    'orderby'        => 'post__in'   
);

$qry = new WP_Query( $args );
                    
if( $ed_featured && $featured_pages && $qry->have_posts() ){ ?>
    <section id="featured_section" class="featured-section style-one feat_page">
		<div class="container">
		<?php while( $qry->have_posts() ){ $qry->the_post(); ?>
			<div class="section-block">
                <figure class="block-img">
                    <?php 
                        if( has_post_thumbnail() ){
                            the_post_thumbnail( 'blossom-shop-featured', array( 'itemprop' => 'image' ) );
                        }else{
                        	blossom_shop_get_fallback_svg( 'blossom-shop-featured' );
                        }
                    ?>                                   
                </figure>
                <div class="block-content">
        			<?php the_title( '<h4 class="block-title"><a href="'. esc_url( get_permalink() ) .'">', '</a></h4>' ); ?>
				</div>
			</div>
		<?php }
        wp_reset_postdata();                                    
        ?>
		</div>
	</section>
<?php
}    