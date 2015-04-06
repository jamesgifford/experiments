<div id="login_form">
	<h2><?= lang('login_heading') ?></h2>
	
	<?= form_open('admin/login') ?>
		<?php if (validation_errors()) : ?><p class="form_errors"><?= validation_errors() ?></p><?php endif; ?>
		
		<p><?= lang('login_label_username', 'username') ?>
		<?= form_input(array('name' => 'username', 'value' => set_value('username'))) ?></p>
		
		<p><?= lang('login_label_password', 'password') ?>
		<?= form_password(array('name' => 'password')) ?></p>
		
		<p class="controls">
			<?= form_submit(array('name' => 'login_submit', 'value' => lang('login_submit'))) ?>
		</p>
	</form>
</div>
