<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">×</button>
	<h3><?php echo $tag->tag_title; ?></h3>
</div>
<div class="modal-body">
	<p>
	<a href="<?php echo base_url(); ?>tags/delete_confirm/<?php echo $tag->tag_id;?>" class="btn btn-danger modal_call"><?php echo $this->lang->line('delete'); ?></a>
	</p>
</div>
<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo $this->lang->line('cancel'); ?></button>
</div>
