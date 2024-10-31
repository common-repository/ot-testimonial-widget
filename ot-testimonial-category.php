<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//step1 Load basic Class
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class ottesti_category_list_table extends WP_List_Table {
    var $strQuery;
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'category',     //singular name of the listed records
            'plural'    => 'categories',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }
    
    /**
     * Function column_default() is called when the parent class can't find a method specifically build for a given column.
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
    */
    function column_default($item, $column_name){
       
    }    

    function column_id($item){
        
        return sprintf('%s', $item['id']);
    }

    function column_category_name($item){
         $actions = array(
            'edit'      => sprintf('<a href="?page=%s&mode=%s&category=%s">'.ottesti_e('Edit').'</a>',$_REQUEST['page'],'edit',$item['id']),
            'delete'    => sprintf("<a href=\"?page=%s&action=%s&id=%s\" onclick=\"if ( confirm( '" . esc_js( sprintf( __( "You are about to delete 'this category' \n 'Cancel' to stop, 'OK' to delete." ),  $item['category_name'] ) ) . "' ) ) { return true;}return false;\">Delete</a>",$_REQUEST['page'],'delete',$item['id']),
        );
        return sprintf('%s %s', $item['category_name'],$this->row_actions($actions));
    }
    function column_count($item){
        global $wpdb;
        $strNTbl = $wpdb->prefix."ot_testimonial";
        $count = $wpdb->get_var("SELECT COUNT(category) AS Total FROM $strNTbl WHERE category = ".$item['id']);        
        return sprintf('<a href="'.admin_url().'admin.php?page=testimonial&cat_name=%s&filter_cat=Filter">%s</a>', $item['id'], $count);
    }

    /**
     * Function column_cb() is to display checkboxes.
     * @param array $item a singular item.
    */
    function column_cb($item){        
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],
            /*$2%s*/ $item['id']
        );
    }


    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            //Below are Column name
            'id'            => ottesti_e('ID'),
            'category_name' => ottesti_e('Category Name'),
            'count'         => ottesti_e('Count'),
         );

        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'category_name' => array('category_name', false),
        );
        return $sortable_columns;
    }

    /**
     * Function get_bulk_actions() is to set bulk actions for table.
    */ 
    function get_bulk_actions() {
        $actions = array(
            'delete'    => ottesti_e('Delete')
        );
        return $actions;
    }
    
    /**
     * Function process_bulk_action() is to set bulk actions for table.
    */
    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die(ottesti_e('Items deleted (or they would be if we had items to delete)!'));
        }
    }

 
    /**
     * Function prepare_items() is to list testimonial and set order.
    */
    function prepare_items($searchvar= NULL) {       
        
        global $wpdb; //This is used only if making any database queries

        $per_page = 10;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
      
        $strTbl = $wpdb->prefix."ot_category";              
        $this->_column_headers = array($columns, $hidden, $sortable);
        if(!empty($this->strQuery)){
        $wpdb->query($this->strQuery);
        }
        if(!empty($searchvar)){
            $this->strQuery = "SELECT id,category_name  FROM $strTbl WHERE category_name LIKE '%".$searchvar."%' ORDER BY id DESC"; 
        }
        else
        {
            $this->strQuery = "SELECT id,category_name FROM $strTbl ORDER BY id DESC"; 
        }

        $data = $wpdb->get_results( $this->strQuery, ARRAY_A );
        //pr($data);

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

/**
* Function _otDemoRenderListPage is called to display list of table.
*/
function fncategory(){
                
    //Create an instance of our package class...
    $testListTable = new ottesti_category_list_table();

    //for message display
    $messages = array();
    if ( isset($_GET['update']) ) :
        switch($_GET['update']) {
            case 'del':
            case 'del_many':
                $delete_count = isset($_GET['delete_count']) ? (int) $_GET['delete_count'] : 0;
                $messages[] = '<div id="message" class="updated"><p>' . sprintf( _n( 'category deleted.', '%s category deleted.', $delete_count ), number_format_i18n( $delete_count ) ) . '</p></div>';
                break;
            case 'add':
                $strmsg = isset($_GET['ID']) ? "updated" : "Added";
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

              
            if((isset($_GET['action2']) && ($_GET['action2']=="-1")) || isset($_GET['action']) && ($_GET['action']=="-1"))
            {

                $del_id = $_GET['category'];
                $chkArray = is_array($del_id);
                if($chkArray)
                {
                    foreach($del_id as $delId)
                    {
                        $del_data = ottesti_delete_cat_data( $delId );
                    }
                }               

            }
            
            if(isset($_GET['id']) && $_GET['id'])
            {
                $del_id = $_GET['id'];
                $del_data = ottesti_delete_cat_data( $del_id );
            }
            if(isset($del_data)){ ?>
                <div class='<?php if(!empty($del_data['msg'])): echo $del_data['msgClass']; endif; ?>'>
                    <p><?php if(!empty($del_data['msg'])): echo $del_data['msg']; endif; ?></p>
                </div>
            <?php } 
                     
            $this_file = $this_file."&update=delete";
        default:
        ?>

            <screen-reader-textript type='text/javascript' src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.validate.js"></script>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                

                $('.category_type').on('change' , function(){
                    var src = '<?php echo CHILD_IMG; ?>category';
                    var category = $('.category_key').val();
                    $('.category_img').attr("src", src+category+'.png');
                });
            });
            </script>

            <?php
            global $wpdb;            

            $isValid = true;
            //check blank data & add record
            if (!empty($_POST['submit']))
            {   
                
                foreach ($_POST as $strKey => $strValue) {
                    if(($strKey == 'category_name') && $strValue == ''):
                        $e_msg[] = 'Please enter '.ucwords(str_replace("_", " ", $strKey));
                        $isValid = false;
                    endif;
                }
                
                $strTbl = $wpdb->prefix."ot_category";                
                $arrData = array(                        
                    'category_name' =>  $_POST['category_name'],                 
                );
                //pr($arrData);die();
                if(isset($_GET['category']))
                {
                    $arrData['id'] = $_GET['category'];                    
                    $where = array(
                            'id' => $_GET['category']
                        );
                    $bool = $wpdb->update( $strTbl, $arrData, $where );
                    $bool = true;                
                }
                elseif($isValid)
                {                    
                    $bool = $wpdb->insert( $strTbl, $arrData);                              
                }
                if(!empty($_GET['category']) && $bool)
                {
                    $arrMsg = array('msg' => 'Category Updated.','msgClass' =>'updated');
                    
                }
                elseif (empty($_GET['category']) && $bool) {
                    $arrMsg = array('msg' => 'Category Added.','msgClass' =>'updated');
                }
                else
                {
                    if($isValid && !$bool):
                        $arrMsg = array('msg' => 'Error occured while saving category.','msgClass' =>'error');
                    endif;
                } 

                if(!empty($e_msg)):

                    $arrMsgs = implode("<br>", $e_msg);
                    $arrMsg = array('msg' => $arrMsgs,'msgClass' =>'error');
                endif;
            }

            $strTbl = $wpdb->prefix."ot_category";
    
            if(isset($_GET['category']))
            {   
                $intEditId= $_GET['category'];
                
                if(is_array($intEditId)){
                    foreach ($intEditId as  $value) {
                        $arrDataAll = $wpdb->get_row("SELECT id,category_name  FROM $strTbl WHERE id = '$value'", ARRAY_A);            
                    }
                }
                else
                {
                $arrDataAll = $wpdb->get_row("SELECT id,category_name  FROM $strTbl WHERE id = '$intEditId'", ARRAY_A);    
                }
                
            }
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
            <style type="text/css">
                label.error, .chkRequired {
                  color: #ff0000;
                }
            </style>
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit">
                    <br>
                </div>
                <h2>Manage Categories</h2>
                <?php if(isset($arrMsg) && !empty($arrMsg)){ ?>
                    <div class="<?php echo $arrMsg['msgClass']; ?>">
                    <p><?php echo $arrMsg['msg']; ?></p>
                </div>
                <?php } 
                ?>
                <div id="col-container" class="categoryContainer">
                    <div id="col-right">
                        <div class="col-wrap">
                            <div class="form-wrap">
                                <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
                                <form id="categories-filter" method="get">
                                    <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                                    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                                    <p class="search-box">
                                        <label class="screen-reader-text" for="post-search-input">Search category:</label>
                                        <input id="post-search-input" type="search" value="<?php if(isset($_GET['s'])): echo $_GET['s']; endif; ?>" name="s">
                                        <input id="search-submit" class="button" type="submit" value="Search category" name="">
                                        <?php 
                                        if(isset($_GET['s']) && !empty($_GET['s']))
                                            { 
                                                ?><a href="?page=testimonail_category">Reset</a><?php
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
                                    <?php echo $strLabel; ?> Category
                                    <?php if(isset($intEditId)) { ?>
                                    <a href="?page=testimonail_category" class="add-new-h2">Add New</a>
                                    <?php } ?>
                                </h3>
                                <form id="add_category" method="post" action="" name="add_category">

                                    <div class="form-field">
                                       <label id="title-prompt-text" class="" for="category_name"><strong>Category Name</strong><span class="chkRequired">*</span> </label>                      
                                        <input class="category_name required cls_input_width" placeholder="" type="text" required id="category_name" name="category_name" value="<?php if(isset($arrDataAll)) echo $arrDataAll['category_name']; ?>" maxlength="100" />
                                        <p>Enter category name here</p>
                                    </div>                                                                      

                                    <p class="submit">
                                        <?php $strBtn = (empty($_GET['category'])) ? 'Submit' : 'Update'; ?>
                                        <input type="hidden" value="<?php if(isset($_GET['category'])){ echo $_GET['category'];} ?>" name="id">
                                        <input type="submit" value="<?php echo $strBtn; ?>" class="button button-primary" id="addcategory" name="submit">
                                        <?php if(isset($_GET['category'])): ?>
                                            <a href="?page=testimonail_category" class="cancel button-primary">Cancel</a>
                                        <?php endif; ?>
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
function ottesti_delete_cat_data($intId)
{
    global $wpdb;
    $arrMsg = "";
    $strTbl = $wpdb->prefix."ot_category";
    $chkArray = is_array($intId);
    if($chkArray)
    {
        foreach($intId as $del_id)
        {
            $deleteTesti = $wpdb->query("DELETE FROM ".$strTbl." WHERE id = ".$del_id);
        }
    }
    else
    {
        $deleteTesti = $wpdb->query("DELETE FROM ".$strTbl." WHERE id = ".$intId);
    }

    if($deleteTesti)
    {
        $arrMsg = array('msg' => 'Category(s) Deleted.','msgClass' =>'updated');
    }
    return $arrMsg;
}
?>