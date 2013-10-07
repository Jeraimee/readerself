	<nav>
		<ul class="actions">
			<li><a href="<?php echo base_url(); ?>members"><i class="icon icon-step-backward"></i><?php echo $this->lang->line('back'); ?></a></li>
		</ul>
	</nav>
</header>
<main>
	<section>
		<section>
		<article class="title<?php if($mbr->mbr_id == $this->member->mbr_id) { ?> item-selected<?php } ?>">
			<ul class="actions">
				<?php if($mbr->mbr_nickname) { ?><li><a href="<?php echo base_url(); ?>member/<?php echo $mbr->mbr_nickname; ?>"><i class="icon icon-unlock"></i><?php echo $this->lang->line('public_profile'); ?></a></li><?php } ?>
			</ul>
			<h2><i class="icon icon-user"></i><?php if($mbr->mbr_nickname) { ?><?php echo $mbr->mbr_nickname; ?> / <?php } ?><?php echo $mbr->mbr_email; ?></h2>
			<?php if($this->config->item('gravatar') && $mbr->mbr_gravatar) { ?>
				<p><img alt="" src="http://www.gravatar.com/avatar/<?php echo md5(strtolower($mbr->mbr_gravatar)); ?>?rating=<?php echo $this->config->item('gravatar_rating'); ?>&amp;size=<?php echo $this->config->item('gravatar_size'); ?>&amp;default=<?php echo $this->config->item('gravatar_default'); ?>">
			<?php } ?>
			<?php if($mbr->mbr_description) { ?>
				<p><?php echo strip_tags($mbr->mbr_description); ?></p>
			<?php } ?>
		</article>

		<h2><i class="icon icon-trash"></i><?php echo $this->lang->line('delete'); ?></h2>

		<?php echo validation_errors(); ?>

		<?php echo form_open(current_url()); ?>

		<p>
		<?php echo form_label($this->lang->line('confirm').' *', 'confirm'); ?>
		<?php echo form_checkbox('confirm', '1', FALSE, 'id="confirm" class="inputcheckbox"'); ?>
		</p>

		<p><i class="icon icon-signin"></i><?php echo $connections_total; ?> connection(s)</p>
		<p><i class="icon icon-bookmark"></i><?php echo $subscriptions_total; ?> subscription(s)</p>
		<p><i class="icon icon-folder-close"></i><?php echo $folders_total; ?> folder(s)</p>
		<p><i class="icon icon-eye-open"></i><?php echo $read_items_total; ?> read item(s)</p>
		<p><i class="icon icon-star"></i><?php echo $starred_items_total; ?> starred item(s)</p>
		<p><i class="icon icon-heart"></i><?php echo $shared_items_total; ?> shared item(s)</p>

		<p>
		<button type="submit"><?php echo $this->lang->line('send'); ?></button>
		</p>

		<?php echo form_close(); ?>

		</section>
	</section>
</main>