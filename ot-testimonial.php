<?php
/*
Plugin Name: Testimonial Widget
Plugin URI: https://www.omegatheme.com/
Description:  Testimonial Widget is to display testimonials, reviews or quotes in multiple ways!.Multiple options and adaptive to any Wordpress website.
Author: Omegatheme
Version: 1.2.1
Company: XIPAT Flexible Solutions 
Author URI: http://www.omegatheme.com
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

define('OTTESTI_PLUGIN_NAME', 'Testimonial Widget');
define('OTTESTI_PLUGIN_URL',plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));

function ottesti_e($text, $params=null) {
    if (!is_array($params)) {
        $params = func_get_args();
        $params = array_slice($params, 1);
    }
    return vsprintf(__($text, 'ottestimonial'), $params);
}

include_once("ot-testimonial-widget.php");
include_once("functions.php");

add_action( 'admin_init', 'ottesti_admin_scripts' );
function ottesti_admin_scripts() {
    wp_enqueue_script( 'ottesti-script-admin', OTTESTI_PLUGIN_URL . '/js/ot-testimonial.js', array(), false, true );
    wp_enqueue_media();
}
class ottesti_list_table extends WP_List_Table {
    var $strQuery;
    public function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'ottestimonial',     //singular name of the listed records
            'plural'    => 'ottestimonials',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

        register_activation_hook( __FILE__, array( $this, 'ottesti_install_testimonial' ) );
        register_uninstall_hook( __FILE__, array( $this, 'ottesti_uninstall_testimonial' ) );
    }

    function ottesti_install_testimonial() {
        global $wpdb;
        global $wp_version;
        If ( version_compare( $wp_version, "4.0", "<" ) ) {
            deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
            wp_die( "This plugin requires WordPress version 4.0 or higher." );
        }
        $strTbl = $wpdb->prefix."ot_testimonial";
        $createTbl =  "CREATE TABLE $strTbl  (
                        `id` int(10) NOT NULL AUTO_INCREMENT,
                        `description` text NOT NULL,
                        `company` varchar(255) NOT NULL,
                        `website` text NOT NULL,
                        `client_name` varchar(255) NOT NULL,
                        `client_email` varchar(255) NOT NULL,
                        `category` varchar(255) NOT NULL,
                        `client_avtar` varchar(255) NOT NULL,
                        `star` int(1) NOT NULL,
                        PRIMARY KEY (`id`)
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $strCatTbl = $wpdb->prefix."ot_category";
        $createCatTbl = "CREATE TABLE $strCatTbl (
                         `id` int(10) NOT NULL AUTO_INCREMENT,
                         `category_name` varchar(255) NOT NULL,
                         PRIMARY KEY (`id`)
                        ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($createTbl);
        dbDelta($createCatTbl);
    }

    function ottesti_uninstall_testimonial(){
        global $wpdb;
        $strTbl = $wpdb->prefix."ot_testimonial";
        $strCatTbl = $wpdb->prefix."ot_category";
        $wpdb->query("DROP TABLE IF EXISTS $strTbl,$strCatTbl");
    }


    function column_default($item, $column_name){
        switch($column_name){
            case 'Rank':
            case 'description':
            case 'company':
            case 'website':
                return stripslashes($item[$column_name]);
            case 'client_name':
            case 'client_email':
            case 'category':
            case 'client_avtar':
            case 'star':
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_client_avtar($item){
        if(empty($item['client_avtar'])){
            return ottesti_e('Not Available');
        }else{    
            return "<img height='50px' width='50px' src='".$item['client_avtar']."'>";
        }
    }

    function column_description($item){
        return ottesti_e('%s',substr(stripslashes($item['description']),0,50));
    }

    function column_client_name($item){
         $actions = array(
            'edit'      => sprintf('<a href="?page=%s&mode=%s&testimonial=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf("<a href=\"?page=%s&action=%s&testimonial_id=%s\" onclick=\"if ( confirm( '" . esc_js( sprintf( __( "You are about to delete this List '%s'\n  'Cancel' to stop, 'OK' to delete." ),  $item['client_name'] ) ) . "' ) ) { return true;}return false;\">Delete</a>",$_REQUEST['page'],'delete',$item['id']),
        );
        return sprintf('%s %s',$item['client_name'],$this->row_actions($actions));
    }

    function column_client_email($item){
        return sprintf('%s',$item['client_email']);
    }

    function column_category($item){
        global $wpdb;
        $strTblName = $wpdb->prefix."ot_category";
        $Cat_Name = $wpdb->get_var( 'SELECT category_name FROM '.$strTblName.' WHERE id = '.$item['category'] );
        return sprintf('%s',$Cat_Name);
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("testimonial")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
 
    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'client_name'   => ottesti_e('Author'),
            'company'       => ottesti_e('Company'),
            'description'   => ottesti_e('Description'),
            'client_avtar'  => ottesti_e('Avatar'),
            'category'      => ottesti_e('Category'),
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'client_name'   => array("client_name",false),
            'client_email'  => array("client_email",false),            
            'company'       => array('company',false)            
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete'    => ottesti_e('Delete')
        );
        return $actions;
    }
    
    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die(ottesti_e('Items deleted (or they would be if we had items to delete)!'));
        }
        
    }
    function prepare_items($searchvar= NULL) {
        global $wpdb; //This is used only if making any database queries

        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
      
        $strTbl = $wpdb->prefix."ot_testimonial";
        $this->_column_headers = array($columns, $hidden, $sortable);
        $wpdb->query("SET @a=0");
        if(!empty($searchvar)){
            $this->strQuery = "SELECT id, description, company, website,client_name,client_email,client_avtar,category,star  FROM $strTbl WHERE client_name LIKE '%".$searchvar."%' ORDER BY id DESC";
        }else{
        $this->strQuery = "SELECT (@a:=@a+1) AS Rank, id, description, company, website,client_name,client_email,client_avtar,category,star FROM ".$strTbl ." ORDER BY id DESC";
        }
        $data = $wpdb->get_results($this->strQuery,ARRAY_A );

        if(isset($_GET['filter_cat']) && !empty($_GET['cat_name'])):
            $strReviewStatus = $_GET['cat_name'];
            if($strReviewStatus != "All"):              
                $data = $wpdb->get_results( "SELECT id, description, company, website,client_name,client_email,client_avtar,category,star FROM $strTbl WHERE category = '".$strReviewStatus."' ORDER BY id DESC", ARRAY_A );
            else:
                $data = $wpdb->get_results( "SELECT id, description, company, website,client_name,client_email,client_avtar,category,star FROM $strTbl ORDER BY id DESC", ARRAY_A );
            endif;
        endif;
                               
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to rank
                       $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to desc
            if(is_numeric($a[$orderby]))
            {
                 $result = ($a[$orderby] > $b[$orderby]?-1:1); //Determine sort order
            }
            else
            {
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            }
            
            return ($order==='desc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
              
        $this->items = $data;
      
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}
add_action('admin_menu', 'ottesti_AddMenuItems');
function ottesti_AddMenuItems(){
    add_menu_page('Testimonials', 'Testimonials', 'activate_plugins', 'ottestimonial', 'ottesti_DemoRenderListPage', OTTESTI_PLUGIN_URL.'/images/icon.png',6);
    add_submenu_page( 'ottestimonial', 'Categories', 'Categories', 'manage_options', 'ottestimonial_category', 'ottesti_add_category_menu' );
}

function ottesti_DemoRenderListPage(){
                
    //Create an instance of our package class...
    $testListTable = new ottesti_list_table();

    //for message display
    $messages = array();
    if ( isset($_GET['update']) ) :
        switch($_GET['update']) {
            case 'del':
            case 'del_many':
                $delete_count = isset($_GET['delete_count']) ? (int) $_GET['delete_count'] : 0;
                $messages[] = '<div id="message" class="updated"><p>' . sprintf( _n( 'testimonial deleted.', '%s testimonials deleted.', $delete_count ), number_format_i18n( $delete_count ) ) . '</p></div>';
                break;
            case 'add':
                $strmsg = isset($_GET['id']) ? "updated" : "Added";
                $messages[] = '<div id="message" class="updated"><p>' . __( 'New record $strmsg.' ) . '</p></div>';
                break;
        }
    endif; 

    $this_file = "?page=".$_REQUEST['page'];

    switch($testListTable->current_action())
    {
        case "add":
        case "edit":
        case "delete":
            global $wpdb;
                       
            if(isset($_GET['action2']) && ($_GET['action2']=="-1"))
            {
                $del_id = $_GET['testimonial'];
                if(is_array($del_id)){
                    foreach ($del_id as $value) {
                        $del_data = ot_delete_data($value);
                    }
                }else{
                    $del_data = ot_delete_data($del_id);    
                }
                
            }
            
            if(isset($_GET['testimonial_id']) && $_GET['testimonial_id'])
            {
                $del_id = $_GET['testimonial_id'];
                $del_data = ot_delete_data($del_id);
            }
            if(isset($del_data)){ ?>
                <div class='<?php if(!empty($del_data['msg'])): echo $del_data['msgClass']; endif; ?>'>
                    <p><?php if(!empty($del_data['msg'])): echo $del_data['msg']; endif; ?></p>
                </div>
            <?php } 
                     
            $this_file = $this_file."&update=delete";
        default:
            wp_enqueue_script('ottesti_validate_js', OTTESTI_PLUGIN_URL.'/js/jquery.validate.js');
        ?>
            <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery("#add_testi").validate();
            });
            </script>

            <?php
            global $wpdb;
                
            $strTbl = $wpdb->prefix."ot_testimonial";
            $strPageListingParam ="testimonial";
            $arrWhere = array();
            $description = sanitize_text_field($_POST['description']);
            if(!empty($description))
            {
                substr($description,0,500);
            }

            //check blank data & add record
            if (!empty($_POST['addTesti']))
            {
                //call function add_update_testi to add / edit record
                if($_POST['id'] != "")
                {
                    $arrWhere = array("id" => $_POST['id'] );
                    unset($_POST['id']);
                }
                //remove submit button & remove blank field
                unset($_POST['addTesti']);                
                $arrData = array();
                foreach ($_POST as $key => $value) {
                    $arrData[$key] = stripslashes($value);
                }
                $arrMsg = array();
                
                if(count($arrData ) > 0)
                {
                    $aAllowedTypes = array('image/jpeg','image/pjpeg','image/png','image/gif');
                    if( $_FILES['client_avtar']['name'] != "" ) {
                        $aSavedFiles = ot_upload_file_on_server('client_avtar', testi_FILE_DIR , $_FILES, $aAllowedTypes);
                    }
                    if( isset($aSavedFiles) ) {
                        $arrData['client_avtar'] = $aSavedFiles[0];
                    }

                    $boolAdded = ot_add_update_testi($strTbl,$arrData,$arrWhere); 
                    if(!empty($arrWhere) && $boolAdded )
                    {
                        $arrMsg = array('msg' => ottesti_e('Testimonial Updated.'),'msgClass' =>'updated');
                        
                    }
                    elseif (empty($arrWhere) && $boolAdded) {
                        $arrMsg = array('msg' => ottesti_e('Testimonial Added.'),'msgClass' =>'updated');
                        
                    }
                    else
                    {
                        $arrMsg = array('msg' => ottesti_e('Error occured while saving your testimonial.'),'msgClass' =>'error');
                    }
                }
            }
            
            if( isset($_GET['mode']) && ($_GET['mode'] == 'edit') ){
                if(isset($_GET['testimonial']))
                {
                    $intEditId = $_GET['testimonial'];
                    if($intEditId > 0)
                    {
                        $arrWhere = array("id=$intEditId");   
                        $arrTestiData = ot_edit_data($strTbl,$arrWhere);
                        
                    }
                }
            }
            
            //Fetch, prepare, sort, and filter our data...
            if(isset($_GET['s'])):
                $testListTable->prepare_items($_GET['s']);
            else:
                $testListTable->prepare_items();
            endif;
            if ( ! empty($messages) ) {
                foreach ( $messages as $msg )
                echo $msg;
            }
            ?>
            
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit">
                    <br>
                </div>
                <h2><?php echo ottesti_e('Testimonials') ?></h2>
                <?php if(isset($arrMsg) && !empty($arrMsg)){ ?>
                    <div class="<?php echo $arrMsg['msgClass']; ?>">
                    <p><?php echo $arrMsg['msg']; ?></p>
                </div>
                <?php } 
                ?>
                <div id="col-container">
                    <div id="col-right">
                        <div class="col-wrap">
                            <div class="form-wrap">
                                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                <form id="testimonials-filter" method="get">
                                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                                    <select class="postform" id="cat_name" name="cat_name">
                                        <option>All</option>
                                        <?php
                                        $allCat = otGetCategory();
                                        if( (isset($_GET['cat_name'])) && (!empty($_GET['cat_name']) ) ){
                                            $drop_cat_name = $_GET['cat_name'];
                                            foreach ($allCat as $value) {
                                            ?>
                                            <option <?php if($drop_cat_name == $value['id']): ?> selected="" <?php endif; ?> value="<?php echo $value['id']; ?>"><?php echo $value['category_name']; ?></option>
                                            <?php
                                        }
                                        }else{
                                        foreach ($allCat as $value) {
                                        ?>

                                        <option value="<?php echo $value['id']; ?>"><?php echo $value['category_name']; ?></option>
                                        <?php
                                        }}
                                        ?>
                                    </select>
                                    <input type="submit" value="Filter" class="button" name="filter_cat">
                                    <p class="search-box">
                                        <label class="screen-reader-text" for="post-search-input"><?php echo ottesti_e('Search Testimonails') ?>:</label>
                                        <input id="post-search-input" type="search" value="<?php if(isset($_GET['s'])): echo $_GET['s']; endif; ?>" name="s">
                                        <input id="search-submit" class="button" type="submit" value="Search Testimonails" name="">
                                        <?php 
                                        if(isset($_GET['s']) && !empty($_GET['s']))
                                            { 
                                                ?><a href="?page=ottestimonial">Reset</a><?php
                                            } 
                                        ?>                                        
                                    </p>
                                    <!-- Now we can render the completed list table -->
                                    <?php $testListTable->display() ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="col-left">
                        <div class="col-wrap">
                            <div class="form-wrap">
                                <?php
                                    if(isset($intEditId)) 
                                    {
                                        $strLabel = "Edit";
                                    }
                                    else
                                    {
                                        $strLabel = "Add";
                                    }
                                ?>
                                <h3>
                                    <?php echo $strLabel; ?> <?php echo ottesti_e('Testimonial') ?>
                                    <?php if(isset($intEditId)) { ?>
                                    <a href="?page=ottestimonial" class="add-new-h2"><?php echo ottesti_e('Add New') ?></a>
                                    <?php } ?>
                                </h3>
                                <form id="add_testi" name="add_testi" enctype="multipart/form-data" method="post" action="" class="frm_testi">
                                    <div class="form-field">
                                        <label for="Company"><?php echo ottesti_e('Company Name') ?><span class="chkRequired">*</span></label>
                                        <input type="text" size="40" class="required" value="<?php if(isset($arrTestiData->company)) {  echo stripslashes($arrTestiData->company);} ?>" id="company" name="company">
                                        <p><?php echo ottesti_e('Name of company who gave you the feedback.') ?></p>
                                    </div>
                                    <div class="form-field">
                                        <label for="Company"><?php echo ottesti_e('Author Name') ?><span class="chkRequired">*</span></label>
                                        <input type="text" size="40" class="required" value="<?php if(isset($arrTestiData->client_name)) {  echo stripslashes($arrTestiData->client_name);} ?>" id="client_name" name="client_name">
                                        <p><?php echo ottesti_e('Name of author who gave you the feedback.') ?></p>
                                    </div>
                                    <div class="form-field">
                                        <label for="Company"><?php echo ottesti_e('Email') ?></label>
                                        <input type="text" size="40" class="" value="<?php if(isset($arrTestiData->client_email)) {  echo stripslashes($arrTestiData->client_email);} ?>" id="client_email" name="client_email">
                                        <p><?php echo ottesti_e('Email of author who gave you the feedback.') ?></p>
                                    </div>
                                    <div class="form-field">
                                        <label for="client_avtar"><?php echo ottesti_e('Author Avatar') ?></label>
                                        <input type="text" name="client_avtar" id="client_avtar" value="<?php if(isset($arrTestiData->client_avtar)) {  echo stripslashes($arrTestiData->client_avtar);}  ?>">
                                        <input id="client_avtar_button" type="button" value="<?php echo ottesti_e('Upload Image') ?>" />
                                        <p class="recommended_text"><?php echo ottesti_e('Click on the text box to add image, recommend: ') ?> 100 X 100</p>
                                    </div>
                                    <div class="form-field">
                                        <label for="Company"><?php echo ottesti_e('Testimonial Category') ?></label>
                                        <?php $arrCategory = otGetCategory(); ?>
                                        <select name="category" id="category">
                                            <?php foreach ($arrCategory as $value) {
                                            ?>
                                            <option <?php if( (isset($arrTestiData->category)) && ($arrTestiData->category == $value['category_name']) ): ?> selected="" <?php endif; ?> value="<?php echo $value['id']; ?>"><?php echo $value['category_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-field">
                                        <label for="Website"><?php echo ottesti_e('Website') ?></label>              
                                        <input type="text" size="40" value="<?php if(isset($arrTestiData->website)) {echo $arrTestiData->website;} ?>" id="website" name="website">
                                    </div>
                                    <div class="form-field">
                                        <label for="Description"><?php echo ottesti_e('Testimonial') ?><span class="chkRequired">*</span></label>
                                        <textarea name ='description' class="required test_description" cols="51" rows="7" ><?php if(isset($arrTestiData->description)) {echo stripslashes($arrTestiData->description);} ?></textarea>                          
                                        <p><?php echo ottesti_e('Few words said by the person.') ?></p>            
                                    </div>
                                    <div class="form-field">
                                        <label for="Company"><?php echo ottesti_e('Star rank') ?></label>
                                        <select name="star" id="star">
                                            <?php for($i=1;$i<=5;$i++) { ?>
                                                <option <?php if( (isset($arrTestiData->star)) && ($arrTestiData->star == $i) ): ?> selected="" <?php endif; ?> value="<?php echo $i ?>"><?php echo $i; ?> <?php echo ottesti_e('star') ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <p class="submit">
                                        <?php 
                                            $strBtn = 'Add';
                                            if(isset($_GET['testimonial']))
                                            {
                                                $strBtn = 'Update';
                                            }
                                        ?>
                                        <input type="hidden" value="<?php if(isset($_GET['testimonial'])){ echo $arrTestiData->id;} ?>" name="id">
                                        <input type="submit" value="<?php echo $strBtn; ?>" class="button button-primary" id="addTestis" name="addTesti">
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div><!-- /col-left -->
                </div><!-- /col-container -->
            </div>
            <?php
            break;
    }
}

function ottesti_add_category_menu(){
    include 'ot-testimonial-category.php';
    fncategory();
}
