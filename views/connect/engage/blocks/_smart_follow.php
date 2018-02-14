<div class="panel panel-green m-t10 p-10">
	<div class="panel-heading">
		<h4>Upcoming Follows</h4>
	</div>
	<div class="panel-body">
		<?php if(!empty($will_follow)): ?>
		<div class="table-responsive">
			<table class="table table-hover no-margin">
				<thead>
					<tr>
						<th style="width:5%">Image</th>
						<th style="width:15%">Username</th>
						<th style="width:60%">Description</th>
						<th style="width:10%">Followers</th>
						<th style="width:10%">Tweets</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($will_follow as $key => $follower): ?>
					<tr class="follower-row" data-id="<?php echo $key; ?>" data-follower="<?php echo $follower['follower_id']; ?>" data-toggle="tooltip" title="click" style="cursor: pointer;">
						<td class="lists_item sm_follow follower-image"></td>
						<td class="lists_item sm_follow follower-username"></td>
						<td class="lists_item sm_follow follower-description"></td>
						<td class="lists_item sm_follow follower-followers"></td>
						<td class="lists_item sm_follow follower-statuses"></td>					
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
<div id="follower_modal_area">
</div>
<script id="follower_modal_template" type="text/x-handlebars-template">
				<div style="padding-right: 17px; top: 24%; right: 8%;" class="modal fade" id="follower_modal" tabindex="-1" role="dialog" aria-labelledby="follower_modal"><div class="modal-dialog modal-sm" role="document"><div class="modal-content"><div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h5 class="modal-title" style="color:white">Select Action</h5>
				</div>
				<div class="modal-body">
					<button type="button" data-id="{{ this }}" id="follow_now" class="btn btn-success btn-rounded btn-transparent">Follow Now</button>
					<a target="_blank" href="https://twitter.com/intent/user?user_id={{ this }}"><button type="button" class="btn btn-warning btn-rounded btn-transparent">View</button></a>
					<button type="button" data-id="{{ this }}" id="no_follow" class="btn btn-danger btn-rounded btn-transparent">Remove</button>
				</div>
			</div>
		</div>
	</div>
</script>