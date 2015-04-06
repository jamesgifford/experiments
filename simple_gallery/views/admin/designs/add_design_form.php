<div id="add_design">
	<h2><?= lang('add_design_heading') ?></h2>
	
	<?= form_open_multipart('admin/designs/add_design') ?>
		<?php if (validation_errors()) : ?><p class="form_errors"><?= validation_errors() ?></p><?php endif; ?>
		
		<p><?= lang('add_design_label_url', 'url') ?>
		<?= form_input(array('name' => 'url', 'value' => set_value('url'))) ?></p>
		
		<p><?= lang('add_design_label_title', 'title') ?>
		<?= form_input(array('name' => 'title', 'value' => set_value('title'))) ?></p>
		
		<p><?= lang('add_design_label_description', 'description') ?>
		<?= form_input(array('name' => 'description', 'value' => set_value('description'))) ?></p>
		
		<p><?= lang('add_design_label_screenshot', 'screenshot') ?>
		<?= form_upload(array('name' => 'screenshot')) ?></p>
		
		<?php foreach ($categories as $category) : ?>
		
		<p><?= lang('add_design_label_category_'.$category['name']) ? lang('add_design_label_category_'.$category['name'], 'category_'.$category['name']) : form_label(ucwords($category['name']), 'category_'.$category['name']) ?>
		<?= form_textarea(array('name' => $category['name'], 'rows' => 5, 'cols' => 50)) ?></p>
		
		<?php endforeach; ?>
		
		<p class="controls">
			<?= form_submit(array('name' => 'add_design_submit', 'value' => lang('add_design_submit'))) ?>
		</p>
	</form>
</div>
