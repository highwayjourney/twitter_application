<div class="panel panel-green">
	<div class="panel-heading">
		<h4>Suggested Lists</h4>
	</div>
	<div class="panel-body">
		<?php if(!empty($lists)): ?>
		<div class="table-responsive">
			<table class="table table-hover no-margin">
				<thead>
					<tr>
						<th style="width:80%">List Name</th>
						<th style="width:10%">Subscribers</th>
						<th style="width:10%">Members</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($lists as $key => $list): ?>
					<tr data-toggle="tooltip" title="click" style="cursor: pointer;">
						<?php if(is_object($list)): ?>
						<td class="lists_item suggestion" data-id="<?php echo $key; ?>"><?php echo $list->name; ?></td>
						<td class="lists_item suggestion" data-id="<?php echo $key; ?>"><?php echo $list->subscriber_count; ?></td>
						<td class="lists_item suggestion" data-id="<?php echo $key; ?>"><?php echo $list->member_count; ?></td>
					<?php else: ?>
						<td class="lists_item suggestion" data-id="<?php echo $key; ?>"><?php echo $list['name']; ?></td>
						<td class="lists_item suggestion" data-id="<?php echo $key; ?>"><?php echo $list['subscriber_count']; ?></td>
						<td class="lists_item suggestion" data-id="<?php echo $key; ?>"><?php echo $list['member_count'];  ?></td>
					<?php endif; ?>					
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else: ?>
		<div class="alert alert-danger alert-transparent">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
			<strong>Oh snap!</strong> There are not suggestions yet
		</div>
		<?php endif;?>
	</div>
</div>
<div id="action_modal_area">
</div>
<script id="action_modal" type="text/x-handlebars-template">
				<div style="padding-right: 17px; top: 24%; right: 8%;" class="modal fade" id="buttonModal" tabindex="-1" role="dialog" aria-labelledby="buttonModal"><div class="modal-dialog modal-sm" role="document"><div class="modal-content"><div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h5 class="modal-title" style="color:white">Select Action</h5>
				</div>
				<div class="modal-body">
					<button type="button" data-id="{{ this }}" id="add_suggested_list" class="modal-action-add btn btn-success btn-rounded btn-transparent">Add</button>
					<button type="button" data-id="{{ this }}" id="remove_suggested_list" class="modal-action-delete btn btn-danger btn-rounded btn-transparent">Dont show again</button>
				</div>
			</div>
		</div>
	</div>
</script>