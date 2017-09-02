<?php 
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}
Class scSlide{
    
    
    public function __construct() {
        add_action('admin_head', array('scSlide','remove_media_settings'));
        add_filter( 'wp_editor_settings',array('scSlide', 'sc_post_type_editor_settings' ));
        add_action( 'add_meta_boxes',array('scSlide', 'sc_register_meta_boxes' ));
        add_action( 'save_post',array('scSlide', 'sc_save_meta_box' ));
        add_filter( 'get_sample_permalink_html', array('scSlide','hide_permalink' ));
        add_action('admin_head-post.php', array('scSlide','hide_publishing_actions'));
        add_action('admin_head-post-new.php', array('scSlide','hide_publishing_actions'));

    }
    
    /**
     * Remove media button from custom post type
     */
    function remove_media_settings(){
         global $post_type;
        if ( $post_type == 'sol_carousel_slider' ) {
            remove_action( 'media_buttons', 'media_buttons' );
        }
    }

    /**
     * Remove editor options from content from custom post type
     *
     */
    function sc_post_type_editor_settings( $settings ) {
        global $post_type;
        if ( $post_type == 'sol_carousel_slider' ) {
            $settings[ 'tinymce' ] = false;
            $settings['quicktags'] = false;
        }
        return $settings;
    }

    /**
     * Register meta box(visible).
     */
    function sc_register_meta_boxes() {
        add_meta_box( 'meta-box-id', __( 'Settings', 'sol-carousel-slider' ), 'scSlide::cs_visible_callback', 'sol_carousel_slider' );
    }

    /**
     * Meta box settings callback.
     @param WP_Post $post Current post object.
     */
    function cs_visible_callback( $post ) { 
        $commoninter = new Common_Function;
        $sc_settings['order'] = get_post_meta( $post->ID, 'sc-order', true );
        $sc_settings['visible'] = get_post_meta( $post->ID, 'sc-visisble', true );
        $sc_settings['transitionstyle'] = get_post_meta( $post->ID, 'sc-transitionstyle', true );
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label class="post-attributes-label" for="order"><?php _e('Slider order', 'sol-carousel-slider'); ?></label></th>
                    <td><?php $commoninter->generate_numberbox('order', 'order', 'meta-box', 10000, 1, $sc_settings['order']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label class="post-attributes-label" for="order"><?php _e('Visible on front', 'sol-carousel-slider'); ?></label></th>
                    <td><?php $commoninter->generate_radiobox('visible', 'visible', 'meta-box',$sc_settings['visible']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label class="post-attributes-label" for="order"><?php _e('Is transition Style on', 'sol-carousel-slider'); ?></label></th>
                    <td><?php $commoninter->generate_selectbox('transitionstyle', array('fade','backSlide','goDown','scaleUp') , 'small-text', $settings['transitionstyle'])?></td>
                </tr>
            </tbody>
        </table>
        
<?php }

    /**
     * Save meta box(visible)content.
     *
     * @param int $post_id Post ID
     */
    function sc_save_meta_box( $post_id){
        if (isset($_POST['visible'])  && isset($_POST['order'])) {   
            $settings['visible'] = $_POST['visible'] ;
            $settings['order'] = $_POST['order'] ;
            $settings['transitionstyle'] = $_POST['transitionstyle'] ;
            $sc_settings = serialize($settings);
            update_post_meta($post_id, 'sc-visisble',$settings['visible'] );      
            update_post_meta($post_id, 'sc-order',$settings['order'] );  
            update_post_meta($post_id, 'sc-transitionstyle',$settings['transitionstyle'] );  
        }
    }
    
    
    /*
     *  REMOVE permalink edit option
     */
    function hide_permalink($html) {
         global $post;
         if ($post->post_type =="sol_carousel_slider"){
            return '';
         }
         else{
             return $html;
         }
    }

    /*
     * Hide extra from publish metabox
     */
    function hide_publishing_actions(){
            $my_post_type = 'sol_carousel_slider';
            global $post;
            if($post->post_type == $my_post_type){
                echo '
                    <style type="text/css">
                        #misc-publishing-actions,
                        #minor-publishing-actions,
                        #delete-action{
                            display:none;
                        }
                    </style>
                ';
            }
    }
}//end of class 

new scSlide();
?>