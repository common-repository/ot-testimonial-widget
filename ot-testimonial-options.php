<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Widget title'); ?>:</p>
        <input type="text" id="<?php echo $this->get_field_id('widgettitle'); ?>" name="<?php echo $this->get_field_name('widgettitle'); ?>" value="<?php echo $widgettitle; ?>" class="form-control widefat" />
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Number of testimonials to show'); ?>:</p>
        <input type="number" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo $count; ?>" class="form-control widefat" />
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Category'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('category_name') ?>">
        	<option value="all"><?php echo ottesti_e('All'); ?></option>
        	<?php $arrCats = otGetCategory();
			foreach ($arrCats as $key => $value): ?>
				<option value="<?php echo $value['id']; ?>"<?php selected($value['id'], $category_name); ?>><?php echo ottesti_e($value['category_name']); ?></option>
			<?php endforeach ?>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Order by'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('orderby') ?>">
            <option value="id"<?php selected('id', $orderby); ?>><?php echo ottesti_e('By ID'); ?></option>
            <option value="company"<?php selected('company', $orderby); ?>><?php echo ottesti_e('By Company'); ?></option>
            <option value="description"<?php selected('description', $orderby); ?>><?php echo ottesti_e('By Description'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Order'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('order') ?>">
            <option value="asc"<?php selected('asc', $order); ?>><?php echo ottesti_e('Ascending'); ?></option>
            <option value="desc"<?php selected('desc', $order); ?>><?php echo ottesti_e('Descending'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Layout'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('layout') ?>">
            <option value="1"<?php selected('1', $layout); ?>><?php echo ottesti_e('Slider with 1 column'); ?></option>
            <option value="2"<?php selected('2', $layout); ?>><?php echo ottesti_e('Slider with 2 column'); ?></option>
            <option value="3"<?php selected('3', $layout); ?>><?php echo ottesti_e('Slider with thumbnails'); ?></option>
            <option value="4"<?php selected('4', $layout); ?>><?php echo ottesti_e('Grid Layout'); ?></option>
            <option value="5"<?php selected('5', $layout); ?>><?php echo ottesti_e('List Layout'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Speed slider (ms)'); ?>:</p>
        <input type="number" id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name('time'); ?>" value="<?php echo $time; ?>" class="form-control widefat" />
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show company Name'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('show_company') ?>">
            <option value="1"<?php selected('1', $show_company); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $show_company); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show Author Name'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('show_name') ?>">
            <option value="1"<?php selected('1', $show_name); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $show_name); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show Email'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('show_email') ?>">
            <option value="1"<?php selected('1', $show_email); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $show_email); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show Website'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('show_website') ?>">
            <option value="1"<?php selected('1', $show_website); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $show_website); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show Avatar'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('show_avtar') ?>">
            <option value="1"<?php selected('1', $show_avtar); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $show_avtar); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show Rank star'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('show_star') ?>">
            <option value="1"<?php selected('1', $show_star); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $show_star); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Show/hide slide indicators'); ?>:</p>
        <select class="form-control widefat" name="<?php echo $this->get_field_name('indicators') ?>">
            <option value="1"<?php selected('1', $indicators); ?>><?php echo ottesti_e('Yes'); ?></option>
            <option value="0"<?php selected('0', $indicators); ?>><?php echo ottesti_e('No'); ?></option>
        </select>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-12">
        <p><?php echo ottesti_e('Widget class'); ?>:</p>
        <input type="text" id="<?php echo $this->get_field_id('widgetclass'); ?>" name="<?php echo $this->get_field_name('widgetclass'); ?>" value="<?php echo $widgetclass; ?>" class="form-control widefat" />
    </div>
</div>