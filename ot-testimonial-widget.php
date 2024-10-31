<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class OT_Testimonial_Widget extends WP_Widget {
	public $options;

    public $widget_fields = array(
        'widgettitle'      => '',
        'count'            => 1,
        'category_name'    => '',
        'orderby'          => '',
        'order'            => '',
        'layout'           => '1',
        'time'             => '',
        'show_company'     => '',
        'show_name'        => '',
        'show_email'       => '',
        'show_website'     => '',
        'show_avtar'       => '',
        'show_star'        => '',
        'indicators'       => '',
        'widgetclass'      => '',
    );

    public function __construct() {
        parent::__construct(
            'ottesti_widget', // Base ID
            'OT Testimonial Widget', // Name
            array(
                'classname'   => 'ottesti-widget',
                'description' => ottesti_e('Display Testimonials Widget')
            )
        );
        add_action( 'wp_enqueue_scripts', array($this,'ottesti_widget_style_scripts') );
    }

    function ottesti_widget_style_scripts() {
        wp_enqueue_script( 'ottesti_widget_style_scripts', OTTESTI_PLUGIN_URL.'/js/jquery.carouFredSel-6.2.1.js', array( 'jquery' ) );
        wp_enqueue_script( 'ottesti_widget_style_scripts', OTTESTI_PLUGIN_URL.'/js/ot-testimonial.js', array( 'jquery' ) );
        wp_enqueue_style('ottesti_widget_style_scripts', OTTESTI_PLUGIN_URL.'/css/ottestimonial.css');
    }

    function widget($args, $instance) {
    	global $post;
		global $wp_query;
		global $wpdb;
    	extract($args);
        foreach ($this->widget_fields as $variable => $value) {
            ${$variable} = !isset($instance[$variable]) ? $this->widget_fields[$variable] : esc_attr($instance[$variable]);
        }
        
		if(!empty($category_name) && ($category_name != 'all') ):
	        $categoryquery = " WHERE category = ".$category_name;
	    else:
	        $categoryquery = " ";
	    endif;

	    $fieldcheck = 'id,description';
	    if($show_company == '1') $fieldcheck .= ',company';
	    if($show_name == '1') $fieldcheck .= ',client_name';
	    if($show_email == '1') $fieldcheck .= ',client_email';
	    if($show_website == '1') $fieldcheck .= ',website';
	    if($show_avtar == '1') $fieldcheck .= ',client_avtar';
	    if($show_star == '1') $fieldcheck .= ',star';

	    $strTbl = $wpdb->prefix."ot_testimonial";
	    $strTestimonial = "SELECT ".$fieldcheck." FROM $strTbl ".$categoryquery." ORDER BY ". $orderby ." ". $order." LIMIT ".$count;

	    $arrTestimonial =  $wpdb->get_results($strTestimonial,ARRAY_A);

	    $id = ot_testimonials_randomkey(4);
	    ?>
	    <div class="widget ottesti-widget <?php echo $widgetclass ?>">
	    	<?php if($widgettitle) echo '<h2 class="ottesti-widget-title widget-title">'.$widgettitle.'</h2>'; ?>
	    	<?php if($arrTestimonial) : ?>
	    		<?php switch ($layout) {
	    			default:
	    				echo ottesti_slide1column_output($arrTestimonial,$indicators,$time,$id);
	    				break;
	    			case '2':
	    				echo ottesti_slide2column_output($arrTestimonial,$indicators,$time,$id);
	    				break;
	    			case '3':
	    				echo ottesti_slidethumb_output($arrTestimonial,$id); ?>
	    				<script type="text/javascript">
         					jQuery.noConflict();
							jQuery(function ($) {
								$('#carousel').carouFredSel({
									responsive: true,
									circular: false,
									auto: false,
									items: {
										visible: 1,
									},
									scroll: {
										fx: 'directscroll'
									}
								});
								$('#thumbs').carouFredSel({
									responsive: true,
									circular: false,
									infinite: false,
									auto: false,
									prev: '#prev',
									next: '#next',
									items: {
										visible: {
											min: 1,
											max: 5
										},
										width: 150,
										height: '90%'
									}
								});

								$('#thumbs a').click(function() {
									$('#carousel').trigger('slideTo', '#' + this.href.split('#').pop() );
									$('#thumbs a').removeClass('selected');
									$(this).addClass('selected');
									return false;
								});
							});
						</script> 
	    				<?php
    					break;
    				case '4':
    					echo ottesti_grid_output($arrTestimonial,$id);
    					break;
					case '5':
    					echo ottesti_list_output($arrTestimonial,$id);
    					break;
	    		} ?>
	    	<?php endif ?>
	    </div>
	    <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        foreach ($this->widget_fields as $field => $value) {
            $instance[$field] = strip_tags(stripslashes($new_instance[$field]));
        }
        return $instance;
    }

    function form($instance) {
        global $wp_version;
        foreach ($this->widget_fields as $field => $value) {
            if (array_key_exists($field, $this->widget_fields)) {
                ${$field} = !isset($instance[$field]) ? $value : esc_attr($instance[$field]);
            }
        } ?>

        <div id="<?php echo $this->id; ?>">
            <?php include(dirname(__FILE__) . '/ot-testimonial-options.php'); ?>
        </div>

        <?php
    }
}