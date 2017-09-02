<?php 
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}
Class scSlider{
    
    public function __construct() {
        include_once( SOLCAROUSELSLIDER_DIR.'includes/common-function.php' );
        include_once( SOLCAROUSELSLIDER_DIR.'includes/slide-function.php' );
        include_once( SOLCAROUSELSLIDER_DIR.'includes/frontview.php' );
        
        add_action('admin_init', array('scSlider','wp_sol_slider_reg_function'), 5);
        add_action('admin_init', array('scSlider','wp_sol_slider_save_settings'), 6);
        add_action('init',array('scSlider','sc_admin_enques'));
        add_action( 'init', array('scSlider','custom_post_sol_carousel_slider' ));
        add_action( 'admin_menu', array('scSlider','add_submenu_settingpage' ));
        add_action('admin_head', array('scSlider','remove_media_settings'));
        add_action('manage_edit-sol_carousel_slider_columns', array('scSlider','add_new_header_text_column'));
        add_action('manage_sol_carousel_slider_posts_custom_column',array('scSlider','show_order_column'));
        add_filter( 'post_row_actions', array('scSlider','sc_duplicate_post_link'), 10, 2 );
        add_filter( 'post_row_actions', array('scSlider','sc_preview_row_action'), 9, 3 );
        add_action('init',array('scSlider','preview_admin_enque'));
        add_action('wp_ajax_sc_preview_slide', array('scSlider','sc_preview_slide'));
        add_action( 'wp_ajax_nopriv_sc_preview_slide', array('scSlider','sc_preview_slide' ));
        add_action('wp_ajax_sc_slider', array('scSlider','sc_slider'));
        add_action( 'wp_ajax_nopriv_sc_slider', array('scSlider','sc_slider' ));
        add_action( 'admin_action_sc_export_slide', array('scSlider','sc_export_slide' ));
        add_filter( 'post_row_actions', array('scSlider','sc_export_row_actions'),11, 4 );
        add_action('admin_head-edit.php',array('scSlider','addCustomImportButton'));
        add_filter( 'bulk_actions-edit-sol_carousel_slider', array('scSlider','register_export_slide_bulk_actions' ));
        add_filter( 'handle_bulk_actions-edit-sol_carousel_slider', array('scSlider','multiple_export_bulk_action_handler'), 10, 3 );
        add_filter( 'views_edit-sol_carousel_slider',array('scSlider','remove_post_count_row'));
        add_filter('post_row_actions',array('scSlider','delete_action_row'), 8,3);
        add_filter( 'screen_options_show_screen', array('scSlider','sc_remove_screen_options'), 10, 2 );
        
}
    
    /*
    * Admin enque scripts and stysle
    */
    public function sc_admin_enques(){
        wp_enqueue_script('jquery'); 
        wp_enqueue_script('jqueryui','https://code.jquery.com/ui/1.12.1/jquery-ui.js');
        wp_enqueue_script('adminscirpt', SOLCAROUSELSLIDER_URL . '/js/admin_script.js');
        wp_localize_script( 'adminscirpt', 'preview_ajax', array( 'ajax_url' => admin_url('admin-ajax.php')) );
        wp_register_style('jquery-uicss', 'http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('jquery-uicss');
        wp_enqueue_style( 'admin-css', SOLCAROUSELSLIDER_URL . '/css/admin.css');

    }
    
    /*
    *creating custom post type sol_carousel_slider
    */
    public function custom_post_sol_carousel_slider() {
        $labels = array(
            'name'               => _x( 'Sol Carousel Slider', 'post type general name' ),
            'singular_name'      => _x( 'Carousel', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'Add new slide' ),
            'add_new_item'       => __( 'Add New slide' ),
            'edit_item'          => __( 'Edit carousel' ),
            'new_item'           => __( 'New carousel slide' ),
            'all_items'          => __( 'All slides' ),
            'view_item'          => __( 'View nook' ),
            'search_items'       => __( 'Search carousel slide' ),
            'not_found'          => __( 'No carousel slide found' ),
            'not_found_in_trash' => __( 'No carousel slide found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Sol Carousel Slider'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'Usefull for gretae and simple slider',
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'editor', 'thumbnail'),
            'has_archive'   => true,
            'query_var' => true,
            
        );
        register_post_type( 'sol_carousel_slider', $args ); 
    }
    
     /*
     * Function to display settings layout
     */
    public function add_submenu_settingpage(){
        add_submenu_page('edit.php?post_type=sol_carousel_slider',
            __( 'Sol Carousel Settings', 'sol-carousel-slider' ),
            __( 'Settings', 'sol-carousel-slider' ),
            'edit_themes',
            'sol_carousel_settings',
            'scSlider::manage_options'
        );
    }
    
    /*
     * Function to generate settings page in Sol_Carousel Slider
     */
    public function manage_options() { 
         $commoninter = new Common_Function;
         $settings = unserialize(get_option("wp_sol_slider_settings"));

        ?>
        <div class="wrap">
                <h2><?php _e('Settings', 'sol-carousel-slider') ?></h2>
                <div class="updated notice notice-success" id="message">
                    <p> Simply select settings from here and it will show you on your front-side with your choice.</p>
                </div>
                <div class="wrap-inner" id="settings-tabs-wrapper">
                    <form method="post" action="edit.php?post_type=sol_carousel_slider&page=sol_carousel_settings" class="bd-form-class">
                        <div class="sc-header">
                            <h2 class="pull-left"><?php _e('Slider Settings', 'sol-carousel-slider') ?></h2>
                            <div class="pull-right" ><input type="submit" name="submit_sol_settings" style="" class="button button-primary" value="<?php _e('Save Changes', ''); ?>" /></div>
                            <div class="clear"></div>
                        </div>
                        <div class="sc-body">
                            <div id="settings">
                                <ul>
                                  <li><a href="#tabs-skin"><?php _e('Skin', 'sol-carousel-slider') ?></a></li>
                                  <li><a href="#tabs-general"><?php _e('General', 'sol-carousel-slider') ?></a></li>
                                  <li><a href="#tabs-layout"><?php _e('Layout', 'sol-carousel-slider') ?></a></li>
                                  <li><a href="#tabs-navigation"><?php _e('Navigation', 'sol-carousel-slider') ?></a></li>
                                  <li><a href="#tabs-advance"><?php _e('Advance', 'sol-carousel-slider') ?></a></li>
                                </ul>
                                <div id="tabs-skin">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label for="linksocial"><?php _e('Select skin', 'sol-carousel-slider'); ?></label></th>
                                                <td><input type="hidden" name="linkskin" id="linkskin" value="<?php echo $settings['linkskin'];?>">
                                                    <div class="skin-wrap" id="skinwrap">
                                                        <img <?php if($settings['linkskin']== 'skin1') echo "class='selectedimage'";?> id="skin1" src="<?php echo plugins_url('../images/skin1.png', __FILE__);?>" />
                                                        <img <?php if($settings['linkskin']== 'skin2') echo "class='selectedimage'";?> id="skin2" src="<?php echo plugins_url('../images/skin2.png', __FILE__);?>" />
                                                        <img <?php if($settings['linkskin']== 'skin3') echo "class='selectedimage'";?> id="skin3" src="<?php echo plugins_url('../images/skin3.png', __FILE__);?>" />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="tabs-general">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label for="responsive"><?php _e('Responsive', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('responsive', 'responsive', 'small-text', $settings['responsive'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="autoplay"><?php _e('Auto play', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('autoplay', 'autoplay', 'small-text',$settings['autoplay'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="stop_hover"><?php _e('Stop on hover', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('stop_hover', 'stop_hover', 'small-text',$settings['stop_hover'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="autoheight"><?php _e('Auto height', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('autoheight', 'autoheight', 'small-text', $settings['autoheight'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="mousedrag"><?php _e('Move slide on drag ', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('mousedrag', 'mousedrag', 'small-text', $settings['mousedrag'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="mousetouch"><?php _e('Move slide on drag ', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('mousetouch', 'mousetouch', 'small-text', $settings['mousetouch'])?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="tabs-layout">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label for="slide_speed"><?php _e('Slider speed', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_numberbox('slide_speed', 'slide_speed', 'small-text', 1000,200, $settings['slide_speed'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="pagination_speed"><?php _e('Pagination speed', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_numberbox('pagination_speed', 'pagination_speed', 'small-text', 1000,200,$settings['pagination_speed'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="desktopwidth"><?php _e('SmallDesktop view options', 'sol-carousel-slider'); ?></label></th>
                                                <td>
                                                    <?php $commoninter->generate_numberbox('desktopwidth', 'desktopwidth', 'small-text', 1500,900,$settings['desktopwidth'])?>
                                                    <?php $commoninter->generate_numberbox('desktopitems', 'desktopitems', 'small-text', 10,1,$settings['desktopitems'])?><br>
                                                    <?php _e('Width   X    items ','sol-carousel-slider' );?> 
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="tabletwidth"><?php _e('Tablet view options', 'sol-carousel-slider'); ?></label></th>
                                                <td>
                                                    <?php $commoninter->generate_numberbox('tabletwidth', 'tabletwidth', 'small-text', 900,600,$settings['tabletwidth'])?>
                                                    <?php $commoninter->generate_numberbox('tabletitems', 'tabletitems', 'small-text', 5,1,$settings['tabletitems'])?><br>
                                                    <?php _e('Width   X    items ','sol-carousel-slider' );?> 
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="mobilewidth"><?php _e('Mobile view options', 'sol-carousel-slider'); ?></label></th>
                                                <td>
                                                    <?php $commoninter->generate_numberbox('mobilewidth', 'mobilewidth', 'small-text', 600,400,$settings['mobilewidth'])?>
                                                    <?php $commoninter->generate_numberbox('mobileitems', 'mobileitems', 'small-text', 3,1,$settings['mobileitems'])?><br>
                                                    <?php _e('Width   X    items ','sol-carousel-slider' );?> 
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                 <div id="tabs-navigation">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label for="items"><?php _e('Slider Items', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_numberbox('items','items', 'small-text', 3,0, $settings['items'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="navigation"><?php _e('Navigation', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('navigation','navigation', 'small-text', $settings['navigation'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="navigationtext"><?php _e('Navigation options', 'sol-carousel-slider'); ?></label></th>
                                                <td><input type="hidden" name="navigationtext" id="navigationtext" value="<?php echo $settings['navigationtext'];?>">
                                                    <div id='navigationwrap'>
                                                        <a class="navigation-style" data="fa-arrow-left"><i class="fa fa-arrow-left"></i><span style="margin:0px 10px"></span><i class="fa fa-arrow-right"></i></a>
                                                        <a class="navigation-style" data="fa-long-arrow"><i class="fa fa-long-arrow-left"></i><span style="margin:0px 10px"></span><i class="fa fa-long-arrow-right"></i></a>
                                                        <a class="navigation-style" data="fa-arrow-circle-o"><i class="fa fa-arrow-circle-o-left"></i><span style="margin:0px 10px"></span><i class="fa fa-arrow-circle-o-right"></i></a>
                                                        <a class="navigation-style" data="fa-chevron"><i class="fa fa-chevron-left"></i><span style="margin:0px 10px"></span><i class="fa fa-chevron-right"></i></a>
                                                        <a class="navigation-style" data="fa-angle-double"><i class="fa fa-angle-double-left"></i><span style="margin:0px 10px"></span><i class="fa fa-angle-double-right"></i></a>
                                                        <a id="navigation-text " data="['next','prev']" class="navigation-text navigation-style"> <?php _e("next <span style='margin:0px 10px'></span> prev",'sol-carousel-slider');?></a>
                                                        <div class="nav-text" style="display:none" >
                                                            <input value="<?php echo $settings['nav-next'] ?>" onblur="checkLength(this)" onchange="checkLength(this)" type="text" name="nav-next" minlength="1">
                                                            <input value="<?php echo $settings['nav-prev'] ?>" onblur="checkLength(this)" type="text" name="nav-prev" minlength="1">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="tabs-advance">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label for="linktitle"><?php _e('Display link on title', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('linktitle', 'linktitle', 'small-text', $settings['linktitle'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="linkimage"><?php _e('Display link on image', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('linkimage', 'linkimage', 'small-text', $settings['linkimage'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="linkreadmore"><?php _e('Display Readmore Button', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('linkreadmore', 'linkreadmore', 'small-text', $settings['linkreadmore'])?>
                                                    <span class="readmoretext" <?php if($settings['linkreadmore'] == 0 ) echo "style='display:none;'"; ?>><input type="text" name="readmoretext" value="<?php echo $settings['readmoretext'] ?>"/></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="linksocial"><?php _e('Display social support', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('linksocial', 'linksocial', 'small-text', $settings['linksocial'])?></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label for="lazyload"><?php _e('Display loader', 'sol-carousel-slider'); ?></label></th>
                                                <td><?php $commoninter->generate_radiobox('lazyload', 'lazyload', 'small-text', $settings['lazyload'])?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                        
                        <div class="inner">
                        
                        <p class="wl-saving-warning"></p>
                        <div class="clear"></div>
                    </div>
                    </form>
                </div>

        </div>

        <?php 
    }

    /**
     * @return Set default value for slider option
     */
    public function wp_sol_slider_reg_function() {
        $settings = get_option("wp_sol_slider_settings");
        if (empty($settings)) {
            $settings = array(
                'items' => '3',
                'slide_speed' => '300',
                'pagination_speed' => '200',
                'autoplay' => '1',
                'stop_hover' => '1',
                'autoheight' => '0',
                'linktitle' => '0',
                'linkimage' => '0',
                'linkreadmore' => '1',
                'readmore'  =>'Read more',
                'linksocial' => '0',
                'linkskin'  => 'skin1',
                'mousedrag'  => '1',
                'lazyload'  => '0',
                'transitionstyle'  => '0',
                'responsive'  => '1',
                'nav-next'  => 'next',
                'nav-prev'  => 'prev',
                'desktopwidth'=>'1000',
                'tabletwidth'=>'768',
                'mobilewidth'=>'420',
                'desktopitems'=>'2',
                'tabletitems'=>'2',
                'mobileitems'=>'1',
                
            );
            $settings = is_array($settings) ? serialize($settings) : $settings;
            $updated = update_option("wp_sol_slider_settings", $settings);
        }
    }

    /*
    * Save slider settings
    */
    public function wp_sol_slider_save_settings() {

        if(isset($_POST['submit_sol_settings']) && $_POST['submit_sol_settings']){
            $settings = $_POST;
            $settings = is_array($settings) ? serialize($settings) : $settings;
            $updated = update_option("wp_sol_slider_settings", $settings);
        }
    }
    
    /**
     * Remove media button from custom post type
     *
     */
    public function remove_media_settings(){
         global $post_type;
        if ( $post_type == 'sol_carousel_slider' ) {
            remove_action( 'media_buttons', 'media_buttons' );
        }
    }
    
    /**
    * add order column to admin listing screen for header text
    */
    public function add_new_header_text_column($header_text_columns) {
      $header_text_columns['menu_order'] = "Order";
      $header_text_columns['visible'] = "Is visible";
      return $header_text_columns;
    }
    
    /*
    *show custom order column values
    */
    public function show_order_column($name){
      global $post;

      switch ($name) {
        case 'menu_order':
          $order = get_post_meta( $post->ID, 'sc-order', true );
          echo $order;
          break;
      case 'visible':
          $visible = (get_post_meta( $post->ID, 'sc-visisble', true ) == 1 ? 'Yes' : 'No');
          echo $visible;
       default:
          break;
       }
    }
    
    /*
    * Add the duplicate link to action list for post_row_actions
    */
    public function sc_duplicate_post_link( $actions, $post ) {
            if (current_user_can('edit_posts') && $post->post_type == 'sol_carousel_slider') {
                    $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=sc_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
            }
            return $actions;
    }
    
    
    /*
     * Add the preview link to action list for post_row_actions
     */
    public function sc_preview_row_action( $actions, $post ) {
            if (current_user_can('edit_posts') && $post->post_type == 'sol_carousel_slider') {
                    $actions['sc_preview'] = "<a href='javascript:(void(0));' data='". $post->ID ."' title='Preview this item' rel='permalink' id='preview_slide'>Preview</a>";
            }
            return $actions;
    }
    
    /*
     * Admin enqueue for preview slider and slide
     */
    public function preview_admin_enque(){
        //if(isset($_POST[post_type]) && $POST['post_type']== 'sol_carousel_slider'){
            wp_enqueue_script('jquery');
            wp_enqueue_script('sliderjs', SOLCAROUSELSLIDER_URL . '/js/sol.carousel.js');
            wp_enqueue_script('boostrapjs2', SOLCAROUSELSLIDER_URL . '/js/bootstrap.min.js');

            wp_enqueue_style('awesome', SOLCAROUSELSLIDER_URL . '/css/font-awesome.min.css');
            wp_enqueue_style('slidercsss', SOLCAROUSELSLIDER_URL . '/css/sol.carousel.css');
            wp_enqueue_style('owlslidertheme', SOLCAROUSELSLIDER_URL . '/css/sol.theme.css');
            wp_enqueue_style('slidercustomize', SOLCAROUSELSLIDER_URL . '/css/customize.css');
        //}

    }
    
    
    /*
     * preview single slide
     */
    public function sc_preview_slide(){

        if(isset($_REQUEST['post_id'])){
            global $wpdb;
            $settings = unserialize(get_option("wp_sol_slider_settings"));
            $args = array('post_type' => 'sol_carousel_slider', 'posts_per_page' => 1, 'p' => $_REQUEST['post_id'] );
            $loop = new WP_Query($args);
            if ($loop->have_posts()) {
            ?>
                <h3 class="text-center"><?php _e( 'Solwin Carousel Slider', 'sol-carousel-slider' );?> </h3>
                <div id="<?php echo $settings['linkskin'];?>" class="myCarousel" data-ride="carousel">
           <?php $post = $loop->the_post();
                frontView::get_slide($post);
                frontView::get_slide($post);
             } ?>
                </div>
                <script>
                jQuery("#<?php echo $settings['linkskin'];?>").solCarousel({
                    singleItem  : <?php echo ($settings['linkskin'] == 'skin3' ?  'true' : 'false');?>,
                    navigation  :   false,
                    pagination  :   false,
                    autoPlay    :   true,
                    stopOnHover :   true,
                    autoHeight  :   false,
                    responsive  :   true,
                    slideSpeed  :   <?php echo (isset($settings['slide_speed']) ? $settings['slide_speed'] : 300)?>,
                    paginationSpeed:<?php echo (isset($settings['pagination_speed']) ? $settings['pagination_speed'] : 800)?>,
                    <?php echo ($settings['linkskin'] != 'skin3' ? "items:".$settings['items']."," : "" )?>
                    <?php echo ( ($settings['linkskin'] !== 'skin3' && isset($settings['desktopitems'])  ) ? "itemsDesktop:[" .$settings['desktopwidth'] . ',' .  $settings['desktopitems'] . '],' : '' )?>
                    <?php echo ( ($settings['linkskin'] !== 'skin3' && isset($settings['tabletitems'])  ) ? "itemsTablet:[" .$settings['tabletwidth'] . ',' .  $settings['tabletitems'] . '],' : '' )?>
                    <?php echo ( ($settings['linkskin'] !== 'skin3' && isset($settings['mobileitems'])  ) ? "itemsMobile:[" .$settings['mobilewidth'] . ',' .  $settings['mobileitems'] . '],' : '' )?>    
                    
                });
                
                    
                
                </script>
                <?php
        }else{
            return;
        }
    }


    /*
     *  Admin-side preview slider
     */
    public function sc_slider(){
        $settings = unserialize(get_option("wp_sol_slider_settings"));    
        if(isset($_REQUEST['action'])){
            frontView::get_carousel_slider();
               ?>

                <?php
        }else{
            return;
        }
    }
    
    /*
     * export the single slide 
     */
    public function sc_export_slide() {
       if(isset($_REQUEST['post'])){
           $post = $_REQUEST['post'];
           slideFunction::export_data($post);
       }
       else{
           return;
       }
    }
    
    /*
     * Add the export link to  list for post_row_actions
     */
    public function sc_export_row_actions( $actions, $post ) {
            if (current_user_can('edit_posts') && $post->post_type == 'sol_carousel_slider') {
                    unset( $actions['inline hide-if-no-js'] );
                    unset($actions['view']);
                    $actions['export'] = '<a href="' . wp_nonce_url('admin.php?action=sc_export_slide&post=' . $post->ID ,basename(__FILE__), 'export_nonce' ) . '" title="Export this item" rel="permalink">Export</a>';
            }
            return $actions;
    }
    
    
    /**
     * Adds "Import" button on module list page
     */
    public function addCustomImportButton()
    {
        global $current_screen;

        // Not our post type, exit earlier
        // You can remove this if condition if you don't have any specific post type to restrict to. 
        if ('sol_carousel_slider' != $_REQUEST['post_type']) {
            return;
        }
        else{
        ?>


        <div style="display:none" id='dialog' title='Select file to import'>
            <form class="form" name="myimportform" method="post" action="<?php echo admin_url('edit.php'). "?" . $_SERVER['QUERY_STRING']; ?>" enctype="multipart/form-data"> 
                <input type="file" name="importfile" id="importfile"><br>
                <input type="submit" value="import" class="" name="importsubmit">
            </form>
        </div>
            <script type="text/javascript">
                jQuery(document).ready( function($)
                {
                    jQuery(jQuery("#posts-filter .search-box")).append("<a  id='doc_popup' class='add-new-h2'>Import</a><a  id='slider_preview' class='add-new-h2'>Preview Slider</a>");
                    //jQuery("#posts-filter").after();

                    jQuery("#doc_popup").click(function () {
                        jQuery( "#dialog" ).dialog({ "modal" : true , "bgiframe" : true, classes: { "ui-dialog": "sc-dialog-slider" }} );
                    });


                    //preview slider whole slider
                    jQuery("#slider_preview").click(function () { 
                    jQuery.ajax({
                           url : preview_ajax.ajax_url,
                           type : 'post',
                           dataType:"html",
                           data : {
                               action : 'sc_slider',
                           },
                           success : function( response ) {
                               jQuery("#posts-filter").after("<div id='sc_slider' class='hidden'>" + response + "</div>");
                               jQuery( "#sc_slider" ).dialog(  {"width": 700 , "maxWidth" : 600 , "height" : 400 , "modal" : true, classes: { "ui-dialog": "sc-dialog-slider" }});
                           }

                       });
                   });

                     jQuery(".ui-widget-overlay").bind('click',function(e){
                        //alert("hi");
                        jQuery("#sc_slider").dialog("close");
                    });
                });
            </script>
        <?php
        }
    }

    /*
     * Add multiple export link to bulk action
     */
    public function register_export_slide_bulk_actions($bulk_actions) {
        $bulk_actions['trash'] = __( 'Delete Slides', 'sol-carousel-slider');
        $bulk_actions['export_slides'] = __( 'Export Slides', 'sol-carousel-slider');
        return $bulk_actions;
    }
    
    /*
     *  multiple export handler 
     */
    public function multiple_export_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
        if ( $doaction !== 'export_slides') {
            return $redirect_to;
        }else {
            slideFunction::export_data($post_ids);
        }
    }
    
    /*
     * Remove all,publish,trash and post other list page row
     */
    public function remove_post_count_row( $views ){
        if( !current_user_can( 'manage_options' ) )
            return $views;

        $remove_views = [ 'all','publish','future','sticky','draft','pending','trash' ];

        foreach( (array) $remove_views as $view )
        {
            if( isset( $views[$view] ) )
                unset( $views[$view] );
        }
        return $views;
    }
    
    /*
     * Add delete option in row post action
     */
    public function delete_action_row($actions, $post){
       if ($post->post_type =="sol_carousel_slider"){
          //remove what you don't need
           unset( $actions['trash'] );
           //check capabilites
           $post_type_object = get_post_type_object( $post->post_type );
           if ( !$post_type_object ) return;
           if ( !current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) return;

           
            //the get the meta and check
             $state = get_post_meta( $post->ID, 'v_state', true );
             if ($state == 'in'){
               $actions['trash'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this item permanently')) . "' href='" . get_delete_post_link($post->ID, '', true) . "'>" . __('Delete') . "</a>";
             }else{
               $actions['trash'] = "<a class='submitdelete' title='" . esc_attr(__('Delete this item permanently')) . "' href='" . get_delete_post_link($post->ID, '', true) . "'>" . __('Delete') . "</a>";
             }
           }
       
       return $actions;
    }

    /*
    * Hide screen option
    */
    public function sc_remove_screen_options( $display_boolean, $wp_screen_object ){
        global $post;
        $blacklist = array('post-new.php', 'post.php' ,'edit.php');
        if ( in_array( $GLOBALS['pagenow'], $blacklist ) ) {
            
            if ($post->post_type =="sol_carousel_slider"){
                $wp_screen_object->render_screen_layout();
                $wp_screen_object->render_per_page_options();
                return false;
            }
        }
        return true;
    }
}//end of class




new scSlider();

?>