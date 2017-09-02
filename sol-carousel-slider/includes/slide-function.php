<?php 
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}
/*
 * single slide settings in admin side 
 */
class slideFunction{
    
    
    public function __construct() {
        add_action( 'admin_action_sc_duplicate_post_as_draft', array('slideFunction','sc_duplicate_post_as_draft' ));
        add_action('admin_init', array('slideFunction','sc_import_slide'), 7);
    }
            
    /**
     * Get text file from zip file
     *
     * @since 1.7
     * @param directory $source
     * @param file $destination
     * @param bool $include_dir
     * @return boolean
     */
    public function Zip($source, $destination, $include_dir = false) {
       
        if ( !function_exists( 'export_wp' ) ) { 
            require_once ABSPATH . '/wp-admin/includes/export.php'; 
        } 

        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }
        if (file_exists($destination)) {
            unlink($destination);
        }
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }
        $source = str_replace('\\', '/', realpath($source));
        if (is_dir($source) === true) {
            
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                // Ignore "." and ".." folders
                if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            
            $zip->addFromString(basename($source), file_get_contents($source));
        }
       
        return $zip->close();
    }
    
    
    /*
     * Remove export folder from upload dir
     */
    public function remove_export_dir($dir){
    
        $dirs = array($dir);
        $files = array();
        for ($i = 0;; $i++) {
            if (isset($dirs[$i]))
                $dir = $dirs[$i];
            else
                break;

            if ($openDir = opendir($dir)) {
                while ($readDir = @readdir($openDir)) {
                    if ($readDir != "." && $readDir != "..") {

                        if (is_dir($dir . "/" . $readDir)) {
                            $dirs[] = $dir . "/" . $readDir;
                        } else {

                            $files[] = $dir . "/" . $readDir;
                        }
                    }
                }
            }
        }
        foreach ($files as $file) {
                   unlink($file);
        }
        $dirs = array_reverse($dirs);
        foreach ($dirs as $dir) {
            rmdir($dir);
        }
    }
    /*
    * Make zip folder and unlink from upload dir 
    * 
    */
   public function saveSliderAsZip($file_path, $folderName) {
        $targetdir = $file_path . '/' . $folderName;
        //echo $targetdir;exit();
        $zip_name = $folderName . ".zip";

        $zip_targetdir = $file_path . '/' . $zip_name;

        // make zip of new created folder
        slideFunction::Zip($targetdir . '/', $zip_targetdir);
        // if zip exists on dir then ask for download
        if (file_exists($zip_targetdir)) {
        header("Expires: 0");
        ob_clean();
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: " . filesize($zip_targetdir));
        header("Content-Disposition: attachment; filename=\"" . $zip_name . "\"");
        readfile($zip_targetdir);
        slideFunction::remove_export_dir($targetdir);
        unlink($zip_targetdir);
        exit;
        }
    }
    /*
    * Export slide by post
    */
    public function export_data($posts_data){
         global $wpdb;
        $table_name = $wpdb->prefix.'posts';
        $tableslides_meta = $wpdb->prefix.'postmeta';

        if(!is_array($posts_data)){
            $posts[] = $posts_data;
        }else{
            $posts = $posts_data;

        }
                $upload_dir = wp_upload_dir();
                $file_path = str_replace('\\', '/', $upload_dir['path']);
                $slide_name = preg_replace('/[^a-zA-Z0-9]/', '_', trim(strtolower($slide_detail['post_name'])));
                $folderName = "export-slide";
                $export_file_name = $folderName.".txt";
                $targetdir = $file_path.'/'.$folderName;

                if(!is_dir($targetdir)){
                    mkdir($targetdir,0777,true);
                    chmod($targetdir,0777);
                }
                $start = 0;
            foreach($posts as $post){

                $slides_detail = $wpdb->get_results('select post_content,post_author,post_date,post_date_gmt,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count from ' . $table_name . ' where ID = '. $post, 'ARRAY_A');
                $slide_detail = $slides_detail[0];

                //copy image to export folder
                $original_image = get_the_post_thumbnail_url($post);
                $image_name = basename($original_image);
                $copy_file = $targetdir.'/'.$image_name;
                if (!file_exists($copy_file))
                {
                    copy($original_image,$copy_file);
                }
                //echo "<br>select post_content,post_author,post_date,post_date_gmt,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count from " . $table_name . " where post_parent = '". $post . "' AND post_type = 'attachment'";
                $slides_meta = $wpdb->get_results("select meta_key,meta_value from " . $tableslides_meta . " where post_id = '". $post . "'", 'ARRAY_A');
                $attchemnt_id = get_post_meta($post, '_thumbnail_id', true);
                $attchemnt = $wpdb->get_results("select post_content,post_author,post_date,post_date_gmt,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count from " . $table_name . " where ID = '". $attchemnt_id . "'", 'ARRAY_A');


                $data[$start]['posts'] = $slide_detail;
                $data[$start]['attchment'] = $attchemnt[0];
                $data[$start]['postmeta'] = $slides_meta;
                $start++;
            }

        /*echo "<pre>";
        print_r($data);
        exit();*/
        //Generate txt file
        $output = base64_encode(serialize($data));

        $fp = fopen($targetdir . "/".$export_file_name,"wb");
        fwrite($fp,$output);
        fclose($fp);
        //Call to generate zip file
        slideFunction::saveSliderAsZip($file_path, $folderName);

       wp_redirect( admin_url( 'edit.php?post_type=sol_carousel_slider') );
       exit;
    }
    
    /*
     * import slide
     */
    public function sc_import_slide() {
     
        if (isset($_POST['importsubmit'])) {
            global $error;
            
            $aios_content = '';
            if (isset($_FILES['importfile']) && $_FILES['importfile']['error'] == '4') {
                $error[] = __('No file is selected for import.', 'sol-carousel-slider');
                add_action( 'admin_notices',$error);
            } else {
                
                global $wpdb;
                $upload_dir = wp_upload_dir();
                $path = str_replace('\\', '/', $upload_dir['path']);

                $filename = $_FILES["importfile"]["name"];
                $filenoext = basename($filename, '.zip'); // absolute path to the directory where zipper.php is in (lowercase)
                $filenoext = basename($filenoext, '.ZIP');
                $targetdir = $path . "/" . $filenoext; // target directory
                $targetzip = $path . "/" . $filename; // target zip file
               
                //Upload zip file in uploades folder
                if (move_uploaded_file($_FILES["importfile"]["tmp_name"], $targetzip)) {

                    //Extract zip file in folder on same location
                    $zip = new ZipArchive();
                    $x = $zip->open($targetzip); // open the zip file to extract
                    if ($x === true) {
                        $zip->extractTo($targetdir); // place in the directory with same name
                       
                        chmod($targetdir ,0777);
                        $zip->close();
                        unlink($targetzip);
                    }
                }

                //$slider_id = $_POST['as-select-slider'];
                
                $export_slide_file = $targetdir . '/' . $filenoext.".txt";
                //echo $export_slide_file;
                
                $output = true;
                
                if (ini_get('allow_url_fopen')) {
                    $aios_file_method = 'fopen';
                } else {
                    $aios_file_method = 'file_get_contents';
                }
                if ($aios_file_method == 'fopen') {
                    $aios_handle = fopen($export_slide_file, 'rb');

                    if ($aios_handle !== false) {
                        while (!feof($aios_handle)) {
                            $aios_content .= fread($aios_handle, 8192);
                        }
                        fclose($aios_handle);
                    }
                    $file_content = $aios_content;
                } else {
                    $file_content = file_get_contents($export_slide_file);
                }
                if ($file_content) {
                    //unlink($export_slide_file);
                    $unserialized_content = maybe_unserialize(base64_decode($file_content));
                    if ($unserialized_content) {
                        $content_import = $unserialized_content;
                    }
                } else {
                    $error = __('File is empty.', 'sol-carousel-slider');
                    add_action( 'admin_notices', 'scValidationMessage');
                }
                if (!empty($content_import)) {
                    
                    $slides_table_name = $wpdb->prefix . 'posts';
                    $slides_metatable_name = $wpdb->prefix . 'postmeta';
                    //echo "<pre>";
                    //print_r($content_import);                    exit();
                    if(!empty($content_import)){  
                    foreach($content_import as $slidedata){
                        foreach($slidedata as $key => $data){
                            $slides_options = array();
                            $meta_options = array();
                            $place_holders = array();
                            if($key == 'posts'){
                                $query = "INSERT INTO " . $slides_table_name . " (post_content,post_author,post_date,post_date_gmt,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count) VALUES ";
                                foreach($data as $key => $value )
                                {
                                        array_push($slides_options, $value );
                                }
                                $place_holders[] = "('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d')";
                                
                                $query .= implode(', ', $place_holders);
                                $final_query = $wpdb->prepare($query, $slides_options);
                                //echo $final_query;
                                $wpdb->query($final_query);
                                $post_id = $wpdb->insert_id;
                            }
                            if($key == 'attchment'){
                                
                                $baseimage = basename($data['guid']);
                                $upload_dir = wp_upload_dir();
                                //print_r($upload_dir);exit();
                                $file_path = str_replace('\\', '/', $upload_dir['path']);
                                $copy_file = $file_path . "/" .$baseimage ;
                                $original_image = $file_path . "/$filenoext/".$baseimage;
                               
                                
                                $url = $upload_dir['url'] . '/' . $filenoext . '/' . $baseimage; // file name with full url path
                               // echo $url;
                                $attchemnt_id = slideFunction::media_image_upload($url, 0);
                                                 
                                
                                
                            }
                            if($key == 'postmeta'){
                                $query = "INSERT INTO " . $slides_metatable_name . " (post_id, meta_key, meta_value) VALUES ";
                                $place_holdersmeta = array();
                                //print_r($content_import['postmeta']);
                                foreach ($data as $single_meta) {
                                        array_push($meta_options, $post_id );
                                        foreach($single_meta as $key => $value )
                                        {
                                           array_push($meta_options, $value );
                                        }
                                        $place_holdersmeta[] = "('%d', '%s', '%s')";
                                    }
                                    $query .= implode(', ', $place_holdersmeta);
                                    $final_query = $wpdb->prepare($query, $meta_options);
                                    $wpdb->query($final_query);
                                    if($attchemnt_id > 0){
                                        update_post_meta($post_id, '_thumbnail_id', $attchemnt_id ); 
                                    }
                                }
                                
                            }
                        }
                        
                    }
                    }else{
                        $error[] = __('Please upload valid zip.', 'sol-carousel-slider');
                        add_action( 'admin_notices', 'scValidationMessage');
                    }
            }
        }
    }
    /*
     * upload image by path
     * @return attchment post_id
     */
    public function media_image_upload($file, $post_id, $desc = null) {
            global $wp_rewrite;
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-includes" . '/pluggable.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');

            $src = '';
            if (!empty($file)) {
                // Download file to temp location
                $tmp = download_url($file);

                // Set variables for storage
                // fix file filename for query strings
                preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches);
                $file_array['name'] = basename($matches[0]);
                $file_array['tmp_name'] = $tmp;

                // If error storing temporarily, unlink
                if (is_wp_error($tmp)) {
                    @unlink($file_array['tmp_name']);
                    $file_array['tmp_name'] = '';
                }
                $wp_rewrite1 = (array) $wp_rewrite;
                if (empty($wp_rewrite1)) {
                    $wp_rewrite = new stdClass();
                    $wp_rewrite->feeds = array();
                }

                // do the validation and storage stuff
                $id = media_handle_sideload($file_array, $post_id, $desc);
                // If error storing permanently, unlink
                if (is_wp_error($id)) {
                    @unlink($file_array['tmp_name']);
                    return $id;
                }

                $src = wp_get_attachment_url($id);
            }
            return $id;
        }  
        
        /*
        * Duplicate slide
        * Function creates post duplicate as a draft and redirects then to the edit post screen
        */
        public function sc_duplicate_post_as_draft(){
            //echo "infuntion";
            global $wpdb;
            if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'sc_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
                    wp_die('No post to duplicate has been supplied!');
            }

            /*
             * Nonce verification
             */
            if ( !isset( $_GET['duplicate_nonce'] ) || wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ){
                    //return;
            }

            /*
             * get the original post id
             */
            $post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
            /*
             * and all the original post data then
             */
            $post = get_post( $post_id );

            /*
             * if you don't want current user to be the new post author,
             * then change next couple of lines to this: $new_post_author = $post->post_author;
             */
            $current_user = wp_get_current_user();
            $new_post_author = $current_user->ID;

            /*
             * if post data exists, create the post duplicate
             */
            if (isset( $post ) && $post != null && $post->post_type == 'sol_carousel_slider') {

                    /*
                     * new post data array
                     */
                    $args = array(
                            'comment_status' => $post->comment_status,
                            'ping_status'    => $post->ping_status,
                            'post_author'    => $new_post_author,
                            'post_content'   => $post->post_content,
                            'post_excerpt'   => $post->post_excerpt,
                            'post_name'      => $post->post_name,
                            'post_parent'    => $post->post_parent,
                            'post_password'  => $post->post_password,
                            'post_status'    => 'draft',
                            'post_title'     => $post->post_title,
                            'post_type'      => $post->post_type,
                            'to_ping'        => $post->to_ping,
                            'menu_order'     => $post->menu_order
                    );

                    /*
                     * insert the post by wp_insert_post() function
                     */
                    $new_post_id = wp_insert_post( $args );

                    /*
                     * get all current post terms ad set them to the new post draft
                     */
                    $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
                    foreach ($taxonomies as $taxonomy) {
                            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
                    }

                    /*
                     * duplicate all post meta just in two SQL queries
                     */
                    $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                    if (count($post_meta_infos)!=0) {
                            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                            foreach ($post_meta_infos as $meta_info) {
                                    $meta_key = $meta_info->meta_key;
                                    if( $meta_key == '_wp_old_slug' ) continue;
                                    $meta_value = addslashes($meta_info->meta_value);
                                    $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
                            }
                            $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                            $wpdb->query($sql_query);
                    }


                    /*
                     * finally, redirect to the edit post screen for the new draft
                     */
                    wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
                    exit;
            } else {
                    wp_die('Post creation failed, could not find original post: ' . $post_id);
            }
    }

}//end of class

new slideFunction();

    
?>