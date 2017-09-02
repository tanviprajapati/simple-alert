<?php 
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}
Class frontView{
    
    public function __construct() {
        add_shortcode( 'wp_sol_carousel_slider', array('frontView','get_carousel_slider' ));
    }
    
    /*
    * Get sc-slider
    */
    public function get_carousel_slider() { 
        
        $settings = unserialize(get_option("wp_sol_slider_settings"));
        $args = array('post_type' => 'sol_carousel_slider', 'posts_per_page' => -1, 'orderby' => 'meta_value','order' => 'Asc', 'meta_key' => 'sc-order',
            'meta_query' => array(
                                array('key' => 'sc-visisble',
                                      'value' => '1'
                                )
                            )
            );
            $loop = new WP_Query($args);
            if ($loop->have_posts()) {        ?>
            <h3 class="text-center"><?php _e( 'Solwin Carousel Slider', 'sol-carousel-slider' );?> </h3>
            <div id="<?php echo $settings['linkskin'];?>" class="myCarousel" data-ride="carousel">
                <!-- Wrapper for slides -->
                <?php while ($loop->have_posts()) : frontView::get_slide($loop->the_post()); ?>
                    
                <?php
                    $first = false; 
                    endwhile;
                ?>
                
            </div>
            <a id="mycss">Change</a>
            <script type="text/javascript">
                var sc_slider = jQuery("#<?php echo $settings['linkskin'];?>").solCarousel({
                    navigation : <?php echo ($settings['navigation'] == '1' ? "true" : "false" )?>,
                    singleItem:<?php echo ($settings['linkskin'] == 'skin3' ? "true" : "false" )?>,
                    <?php echo ($settings['linkskin'] != 'skin3' ? "items:".$settings['items']."," : "" )?>
                    <?php echo ( ($settings['linkskin'] !== 'skin3' && isset($settings['desktopitems'])  ) ? "itemsDesktop:[" .$settings['desktopwidth'] . ',' .  $settings['desktopitems'] . '],' : '' )?>
                    <?php echo ( ($settings['linkskin'] !== 'skin3' && isset($settings['tabletitems'])  ) ? "itemsTablet:[" .$settings['tabletwidth'] . ',' .  $settings['tabletitems'] . '],' : '' )?>
                    <?php echo ( ($settings['linkskin'] !== 'skin3' && isset($settings['mobileitems'])  ) ? "itemsMobile:[" .$settings['mobilewidth'] . ',' .  $settings['mobileitems'] . '],' : '' )?>    
                    slideSpeed:<?php echo (isset($settings['slide_speed']) ? $settings['slide_speed'] : 300)?>,
                    paginationSpeed:<?php echo (isset($settings['pagination_speed']) ? $settings['pagination_speed'] : 800)?>,
                    autoPlay:<?php echo ($settings['autoplay'] == '1' ? "true" : "false" )?>,
                    stopOnHover:<?php echo ($settings['stop_hover'] == '1' ? "true" : "false" )?>,
                    autoHeight:<?php echo ($settings['autoheight'] == '1' ? "true" : "false" )?>,
                    mouseDrag:<?php echo ($settings['mousedrag'] == '1' ? "true" : "false" )?>,
                    touchDrag:<?php echo ($settings['mousetouch'] == '1' ? "true" : "false" )?>,
                    lazyLoad:<?php echo ($settings['lazyload'] == '1' ? "true" : "false" )?>,
                    responsive:<?php echo ($settings['responsive'] == '1' ? "true" : "false" )?>,
                    <?php echo ( (strpos($settings['navigationtext'], 'fa') !== false)  ? "navigationText:[\"<i class='fa " . $settings['navigationtext'] ."-left ' ></i>\"" . ",\"<i class='fa " . $settings['navigationtext'] . "-right '></i>\"]," : 'navigationText:'. "['" . $settings['nav-next'] . " ',' " . $settings['nav-prev'] . "']" )?>
                    transitionStyle:'fade',
                             
                  });
                  jQuery("#mycss1").click(function() {
     sc_slider.trigger('sc.prev');
     sc_slider.on('moveEvents',function(e){
         alert('hi');
     });
});  
            </script>
        <?php }
    }        
    
    /*
    *  Get single slide
    */
    public function get_slide($post){ 
            
            $settings = unserialize(get_option("wp_sol_slider_settings"));
            $style = get_post_meta(get_the_ID(),'sc-transitionstyle',true);
        ?>
                        <div class="item <?php if($first) echo 'active'; ?>" data="<?php if($style) echo $style; ?>">
                            <div class="sc-content">
                             <?php
                                    if (has_post_thumbnail() ) {
                                        if(isset($settings['linkimage']) && ($settings['linkimage'] == 1)) { ?>
                                        <a href="<?php the_permalink(); ?>" ><?php the_post_thumbnail(); ?></a>
                                    <?php
                                    }else{
                                        the_post_thumbnail(); 
                                    } }else {?>
                                        <img alt="<?php echo get_the_title();?>" src="<?php echo SOLCAROUSELSLIDER_URL . '/images/no_image.png'?>" />
                                    <?php } if($settings['linkskin'] == 'skin3' ){?>
                                    <div class="carousel-caption">
                                        <?php if(isset($settings['linktitle']) && $settings['linktitle'] == 1) { ?> 
                                            <h4 class="sc-title"> <a href="<?php the_permalink(); ?>" > <?php the_title(); ?></a></h4>
                                        <?php }else{ ?>
                                            <h4 class="sc-title"> <?php the_title();?> </h4>
                                        <?php }?>
                                        <h7 class="sc- content hidden-xs pull-left"> <?php echo substr(get_the_content(), 0,60) . '...';?> </h7>
                                        <?php if(isset($settings['linkreadmore']) && $settings['linkreadmore'] == 1) { ?> 
                                            <a class=" cs-readmore pull-right" style="margin-right:60px"  href="<?php the_permalink(); ?>" ><?php _e( $settings['readmoretext'] , 'sol-carousel-slider' );?></a>
                                        <?php }?>
                                    </div>
                                    <?php }?>
                            </div>    
                            <?php if(isset($settings['linksocial']) && $settings['linksocial'] == 1) {  ?> 
                                <div class="sc-social">
                                    <a class="" href="" ><i class="fa fa-facebook"></i></a>
                                    <a class="" href="" ><i class="fa fa-google-plus"></i></a>
                                    <a class="" href="" ><i class="fa fa-twitter"></i></a>
                                </div>
                            <?php } ?> 
                        </div>

    <?php }

}// end of class

new frontView();

?>