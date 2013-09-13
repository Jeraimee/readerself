	<nav>
		<ul class="actions">
			<li><a href="#" class="refresh-items" id="refresh-items"><i class="icon icon-refresh"></i><?php echo $this->lang->line('refresh'); ?></a></li>
			<?php if($this->input->cookie('mode-items') == 'read_and_unread') { ?>
				<li class="hide-phone"><a href="#" id="mode-items" class="mode-items"><span class="unread_only" style="display:inline-block;"><i class="icon icon-eye-close"></i><?php echo $this->lang->line('unread_only'); ?></span><span class="read_and_unread" style="display:none;"><i class="icon icon-eye-open"></i><?php echo $this->lang->line('read_and_unread'); ?></span></a></li>
			<?php } else { ?>
				<li class="hide-phone"><a href="#" id="mode-items" class="mode-items"><span class="unread_only"><i class="icon icon-eye-close"></i><?php echo $this->lang->line('unread_only'); ?></span><span class="read_and_unread"><i class="icon icon-eye-open"></i><?php echo $this->lang->line('read_and_unread'); ?></span></a></li>
			<?php } ?>
			<?php if($this->input->cookie('display-items') == 'collapse') { ?>
				<li><a href="#" class="display-items" id="display-items"><span class="expand" style="display:inline-block;"><i class="icon icon-collapse"></i><?php echo $this->lang->line('expand'); ?></span><span class="collapse" style="display:none;"><i class="icon icon-collapse-top"></i><?php echo $this->lang->line('collapse'); ?></span></a></li>
			<?php } else { ?>
			<li><a href="#" class="display-items" id="display-items"><span class="expand"><i class="icon icon-collapse"></i><?php echo $this->lang->line('expand'); ?></span><span class="collapse"><i class="icon icon-collapse-top"></i><?php echo $this->lang->line('collapse'); ?></span></a></li>
			<?php } ?>
			<li class="hide-phone"><a href="<?php echo base_url(); ?>home/history/dialog" id="read_all" class="read_all modal_show"><i class="icon icon-ok"></i><?php echo $this->lang->line('mark_all_as_read'); ?>...</a></li>
			<li><a href="#" class="item-up" id="item-up"><i class="icon icon-chevron-up"></i><?php echo $this->lang->line('up'); ?></a></li>
			<li><a href="#" class="item-down" id="item-down"><i class="icon icon-chevron-down"></i><?php echo $this->lang->line('down'); ?></a></li>
		</ul>
	</nav>
</header>
<aside>
	<ul>
		<li class="active"><a id="load-all-items" href="<?php echo base_url(); ?>home/items/all"><i class="icon icon-asterisk"></i><?php echo $this->lang->line('all_items'); ?> (<span>0</span>)</a></li>
		<?php if($this->config->item('star')) { ?>
		<li><a id="load-starred-items" href="<?php echo base_url(); ?>home/items/starred"><i class="icon icon-star"></i><?php echo $this->lang->line('starred_items'); ?> {<span>0</span>}</a></li>
		<?php } ?>
		<?php if($this->config->item('share')) { ?>
		<li><a id="load-shared-items" href="<?php echo base_url(); ?>home/items/shared"><i class="icon icon-heart"></i><?php echo $this->lang->line('shared_items'); ?> {<span>0</span>}</a></li>
		<?php } ?>
		<?php if($this->config->item('tags')) { ?>
		<li><a id="load-cloud-tags-items" href="<?php echo base_url(); ?>home/items/cloud/tags"><i class="icon icon-tags"></i><?php echo $this->lang->line('tags'); ?></a></li>
		<?php } ?>
		<li><a id="load-cloud-authors-items" href="<?php echo base_url(); ?>home/items/cloud/authors"><i class="icon icon-group"></i><?php echo $this->lang->line('authors'); ?></a></li>
		<?php if($folders && $this->config->item('folders')) { ?>
		<?php foreach($folders as $folder) { ?>
		<li><a id="load-folder-<?php echo $folder->flr_id; ?>-items" href="<?php echo base_url(); ?>home/items/folder/<?php echo $folder->flr_id; ?>"><i class="icon icon-folder-close"></i><?php echo $folder->flr_title; ?> (<span>0</span>)</a></li>
		<?php } ?>
		<li><a id="load-nofolder-items" href="<?php echo base_url(); ?>home/items/nofolder"><i class="icon icon-folder-close"></i><em><?php echo $this->lang->line('no_folder'); ?></em> (<span>0</span>)</a></li>
		<?php } ?>
		<li><label for="search_items"><i class="icon icon-file-text-alt"></i><?php echo $this->lang->line('items'); ?></label></li>
		<li>
			<?php echo form_open(base_url().'home/items/search', array('id'=>'search_items_form')); ?>
			<?php echo form_input('search_items', '', 'id="search_items"'); ?>
			<?php echo form_close(); ?>
		</li>
		<li><label for="fed_title"><i class="icon icon-rss"></i><?php echo $this->lang->line('subscriptions'); ?></label></li>
		<li>
			<?php echo form_open(base_url().'home/subscriptions', array('id'=>'search_subscriptions_form')); ?>
			<?php echo form_input('fed_title', '', 'id="fed_title"'); ?>
			<?php echo form_close(); ?>
		</li>
	</ul>
</aside>
<main>
	<section>
		<section>
		</section>
	</section>
</main>
