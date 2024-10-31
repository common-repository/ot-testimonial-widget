<?php

add_action('widgets_init', create_function('', 'return register_widget("OT_Testimonial_Widget");'));

function ot_add_update_testi($strTbl,$arrData,$arrWhere= array())
{
    global $wpdb;

    if(count($arrWhere)==0)
    {
        $wpdb->insert($strTbl,$arrData);
        return $wpdb->insert_id;
    }
    else
    {
        $wpdb->update($strTbl,$arrData,$arrWhere);

        return true;
    }
    return false;
}

function ot_edit_data($strTbl,$arrWhere="",$boolLimit=true)
{
    global $wpdb;
    $strWhere = "";

    if(count($arrWhere) > 0 )
    {

        $strSep =  (count($arrWhere) > 1?" AND ":"");
        
        $strWhere = " WHERE ".implode($strSep, $arrWhere);

    }
    if($boolLimit)
    {
        $strLimit = "LIMIT 1";
    }
    $strSql = "Select id,description,company, website,client_name,client_email,client_avtar,star from $strTbl $strWhere $strLimit";
    
    if($boolLimit)
    {
        $arrResult =  $wpdb->get_row($strSql);  
    }
    else
    {
        $arrResult =  $wpdb->get_results($strSql);      
    }
    return $arrResult;
}

function ot_delete_data($intId)
{
    global $wpdb;
    $strTbl = $wpdb->prefix."ot_testimonial";

    $chkArray = is_array($intId);
    if($chkArray)
    {
        foreach($intId as $del_id)
        {
            $old_file_name = $wpdb->get_var( $wpdb->prepare( 'SELECT client_avtar FROM '.$strTbl.' WHERE id = %d', $del_id ) );
            ot_delete_file(OTTESTI_UPLOAD_DIR, $old_file_name);
            $deleteTesti = $wpdb->query("DELETE FROM ".$strTbl." WHERE id = ".$del_id);
        }
    }
    else
    {
        $old_file_name = $wpdb->get_var( $wpdb->prepare( 'SELECT client_avtar FROM '.$strTbl.' WHERE id = %d', $intId ) );
        ot_delete_file(OTTESTI_UPLOAD_DIR, $old_file_name);

        $deleteTesti = $wpdb->query("DELETE FROM $strTbl WHERE id =".$intId);        
    }

    if($deleteTesti)
    {
        $arrMsg = array('msg' => 'Testimonial(s) Deleted.','msgClass' =>'updated');
    }
    if(!empty($arrMsg)){
        return $arrMsg;    
    }
    else{
        return "";
    }
}

function otGetCategory(){
    global $wpdb;
    $strTblName = $wpdb->prefix."ot_category";
    $arrCat = $wpdb->get_results( 'SELECT id,category_name FROM '.$strTblName, ARRAY_A );
    return $arrCat;
}
function ot_testimonials_randomkey($length) {
    $pattern = "0123456789";
    $key = '';
    for($i = 0; $i < $length; $i++)    {
        $key .= $pattern{rand(0,strlen($pattern)-1)};
    }
    return $key;
}

function ottesti_slide1column_output($arrTestimonial,$indicators,$time,$id){

    $html = '<div id="ot_testimonial_'.$id.'" class="ot_testimonial">
                <div id="testimonial_mod_'.$id.'" class="carousel slide noconflict" data-interval="'.$time.'" data-ride="carousel">';
    if($indicators==1) {
        $html .= '<ol class="carousel-indicators">';
        $number = count($arrTestimonial);
        for($i=0;$i<$number;$i++) {
            if ($number > 1){
                if ($i == 0) {
                    $html .= '<li data-target="#testimonial_mod_'.$id.'" data-slide-to="'.$i.'" class="active"></li>';
                } else {
                    $html .= '<li data-target="#testimonial_mod_'.$id.'" data-slide-to="'.$i.'"></li>';
                }
            }
        }
        $html .= '</ol>';
    }
    $html .= '<div class="carousel-inner">';
    foreach($arrTestimonial as $key=>$testi) {
        if($key==0) $html .= '<div class="active item"><div class="ot_left">';
        else $html .= '<div class="item"><div class="ot_left">';
        $html .= '<div class="ot_tcontent1">
                    <div class="ot_title">'.$testi['description'].'</div>                     
                </div>
                <div class="arrow-down1"></div>
                <div class="ot_info1">';
        if(array_key_exists('client_avtar',$testi) && !empty($testi['client_avtar'])) {
            $html .= '<div class="ot_image1">
                        <img src="'.$testi['client_avtar'].'" alt="Author Image">
                    </div>';
        }
        $html .= '<div class="ot_aditional1">';
        if(array_key_exists('client_name',$testi) && !empty($testi['client_name'])) {
            $html .= '<div class="ot_name">'.$testi['client_name'].'</div>';
        }
        if(array_key_exists('company',$testi) && !empty($testi['company'])) {
            $html .= '<div class="ot_work">'.$testi['company'].'</div>';
        }
        if(array_key_exists('client_email',$testi) && !empty($testi['client_email'])) {
            $html .= '<div class="ot_work">'.$testi['client_email'].'</div>';
        }
        if(array_key_exists('website',$testi) && !empty($testi['website'])) {
            $html .= '<div class="ot_website">'.$testi['website'].'</div>';
        }
        if(array_key_exists('star',$testi) && !empty($testi['star'])) {
            $html .= '<div class="ot_ratting">';
            for ($j = 0; $j < $testi['star']; $j++) {
                $html .= '<i class="glyphicon glyphicon-star"></i> ';
            }
            $html .= '</div>';
        }

        $html .= '</div></div></div></div>';
    }
    $html .= '</div></div></div>';
    
    return $html;
}

function ottesti_slide2column_output($arrTestimonial,$indicators,$time,$id){

    $html = '<div id="ot_testimonial_'.$id.'" class="ot_testimonial">
                <div id="testimonial_mod_'.$id.'" class="carousel slide noconflict" data-interval="'.$time.'" data-ride="carousel">';
    if($indicators==1) {
        $html .= '<ol class="carousel-indicators">';
        $number = round(count($arrTestimonial)/2);
        for($i=0;$i<$number;$i++) {
            if ($number > 1){
                if ($i == 0) {
                    $html .= '<li data-target="#testimonial_mod_'.$id.'" data-slide-to="'.$i.'" class="active"></li>';
                } else {
                    $html .= '<li data-target="#testimonial_mod_'.$id.'" data-slide-to="'.$i.'"></li>';
                }
            }
        }
        $html .= '</ol>';
    }
    $html .= '<div class="carousel-inner"><div class="active item">';
    foreach($arrTestimonial as $key=>$testi) {
        $html .= '<div class="ot_left">';
        $html .= '<div class="ot_tcontent1">
                    <div class="ot_title">'.$testi['description'].'</div>                     
                </div>
                <div class="arrow-down1"></div>
                <div class="ot_info1">';
        if(array_key_exists('client_avtar',$testi) && !empty($testi['client_avtar'])) {
            $html .= '<div class="ot_image1">
                        <img src="'.$testi['client_avtar'].'" alt="Author Image">
                    </div>';
        }
        $html .= '<div class="ot_aditional1">';
        if(array_key_exists('client_name',$testi) && !empty($testi['client_name'])) {
            $html .= '<div class="ot_name">'.$testi['client_name'].'</div>';
        }
        if(array_key_exists('company',$testi) && !empty($testi['company'])) {
            $html .= '<div class="ot_work">'.$testi['company'].'</div>';
        }
        if(array_key_exists('client_email',$testi) && !empty($testi['client_email'])) {
            $html .= '<div class="ot_work">'.$testi['client_email'].'</div>';
        }
        if(array_key_exists('website',$testi) && !empty($testi['website'])) {
            $html .= '<div class="ot_website">'.$testi['website'].'</div>';
        }
        if(array_key_exists('star',$testi) && !empty($testi['star'])) {
            $html .= '<div class="ot_ratting">';
            for ($j = 0; $j < $testi['star']; $j++) {
                $html .= '<i class="glyphicon glyphicon-star"></i> ';
            }
            $html .= '</div>';
        }

        $html .= '</div></div></div>';
        if(($key+1)%2==0 && ($key+1)!=count($arrTestimonial)) $html .= '</div><div class="item">';
    }
    $html .= '<div></div></div></div>';
    
    return $html;
}
function ottesti_slidethumb_output($arrTestimonial,$id){

    $html = '<div id="ot_testimonial_'.$id.'" class="ot_testimonial">
                <div id="myCarousel" class="carousel slide noconflict" data-ride="carousel">
                    <div id="carousel-wrapper">
                        <div id="carousel">';
    foreach($arrTestimonial as $key=>$testi) {
        $html .=            '<span id="'.$id.'">
                                <div class="ot_tcontent">
                                    <div class="ot_title">'.$testi["description"].'</div>                     
                                </div>
                                <div class="ot_aditional">';
        if(array_key_exists('client_name',$testi) && !empty($testi['client_name'])) {
            $html .= '<div class="ot_name">'.$testi['client_name'].'</div>';
        }
        if(array_key_exists('company',$testi) && !empty($testi['company'])) {
            $html .= '<div class="ot_work">'.$testi['company'].'</div>';
        }
        if(array_key_exists('client_email',$testi) && !empty($testi['client_email'])) {
            $html .= '<div class="ot_work">'.$testi['client_email'].'</div>';
        }
        if(array_key_exists('website',$testi) && !empty($testi['website'])) {
            $html .= '<div class="ot_website">'.$testi['website'].'</div>';
        }
        if(array_key_exists('star',$testi) && !empty($testi['star'])) {
            $html .= '<div class="ot_ratting">';
            for ($j = 0; $j < $testi['star']; $j++) {
                $html .= '<i class="glyphicon glyphicon-star"></i> ';
            }
            $html .= '</div>';
        }
        $html .=                '</div>
                            </span>';
    }
    $html .=            '</div>
                    </div>
                    <div id="thumbs-wrapper">
                        <div id="thumbs">';
    foreach($arrTestimonial as $key=>$testi) {
        $html .=            '<a href="#'.$key.'" ';
        if($key==0) $html .= 'class="selected "';
        $html .=            '><img src="'.$testi["client_avtar"].'"></a>';
    }
    $html .=            '</div>
                        <a id="prev" href="#"></a>
                        <a id="next" href="#"></a>
                    </div>';
    $html .= '</div></div>';
    return $html;
}

function ottesti_grid_output($arrTestimonial,$id){

    $html = '<div id="ot_testimonial_'.$id.'" class="ot_testimonial">';
    
    foreach($arrTestimonial as $key=>$testi) {
        $html .= '<div class="ot_left">
                    <div class="ot_tcontent1">
                        <div class="ot_title">'.$testi['description'].'</div>
                    </div>
                    <div class="arrow-down1"></div>
                    <div class="ot_info1">';

        if(array_key_exists('client_avtar',$testi) && !empty($testi['client_avtar'])) {
            $html .=    '<div class="ot_image1">
                            <img src="'.$testi['client_avtar'].'" alt="Author Image">
                        </div>';
        }
        $html .=        '<div class="ot_aditional1">';
        if(array_key_exists('client_name',$testi) && !empty($testi['client_name'])) {
            $html .=        '<div class="ot_name">'.$testi['client_name'].'</div>';
        }
        if(array_key_exists('company',$testi) && !empty($testi['company'])) {
            $html .=        '<div class="ot_work">'.$testi['company'].'</div>';
        }
        if(array_key_exists('client_email',$testi) && !empty($testi['client_email'])) {
            $html .=        '<div class="ot_work">'.$testi['client_email'].'</div>';
        }
        if(array_key_exists('website',$testi) && !empty($testi['website'])) {
            $html .=        '<div class="ot_website">'.$testi['website'].'</div>';
        }
        if(array_key_exists('star',$testi) && !empty($testi['star'])) {
            $html .=        '<div class="ot_ratting">';
            for ($j = 0; $j < $testi['star']; $j++) {
                $html .=        '<i class="glyphicon glyphicon-star"></i> ';
            }
            $html .=        '</div>';
        }

        $html .=        '</div>
                    </div>
                </div>';
    }
    $html .= '</div>';
    
    return $html;
}

function ottesti_list_output($arrTestimonial,$id){

    $html = '<div id="ot_testimonial_'.$id.'" class="ot_testimonial">';
    
    foreach($arrTestimonial as $key=>$testi) {
        $html .= '<div class="ot_list">
                    <div class="ot_info">';

         if(array_key_exists('client_avtar',$testi) && !empty($testi['client_avtar'])) {
            $html .=    '<div class="ot_image">
                            <img src="'.$testi['client_avtar'].'" alt="Author Image">
                        </div>';
        }

        $html .=        '<div class="ot_aditional">';

        if(array_key_exists('client_name',$testi) && !empty($testi['client_name'])) {
            $html .=        '<div class="ot_name">'.$testi['client_name'].'</div>';
        }
        if(array_key_exists('company',$testi) && !empty($testi['company'])) {
            $html .=        '<div class="ot_work">'.$testi['company'].'</div>';
        }
        if(array_key_exists('client_email',$testi) && !empty($testi['client_email'])) {
            $html .=        '<div class="ot_work">'.$testi['client_email'].'</div>';
        }
        if(array_key_exists('website',$testi) && !empty($testi['website'])) {
            $html .=        '<div class="ot_website">'.$testi['website'].'</div>';
        }
        if(array_key_exists('star',$testi) && !empty($testi['star'])) {
            $html .=        '<div class="ot_ratting">';
            for ($j = 0; $j < $testi['star']; $j++) {
                $html .=        '<i class="glyphicon glyphicon-star"></i> ';
            }
            $html .=        '</div>';
        }

        $html .=        '</div>
                    </div>
                    <div class="arrow-down"></div>
                    <div class="ot_tcontent">
                        <div class="ot_title">'.$testi['description'].'</div>        
                    </div>
                </div>';
    }   
    $html .= '</div>';
    
    return $html;
}