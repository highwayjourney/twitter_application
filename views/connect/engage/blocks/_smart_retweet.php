<div class="col-xs-8">
	<div class="panel panel-green m-t10 p-10">
		<div class="panel-heading">
			<h4>Upcoming Retweets</h4>
		</div>
		<div class="panel-body">
			<div class="col-xs-12 m-t20">
				<div class="row">
					<?php if(empty($retweet_data)): ?>
					<div class="twitter-wrapper retweet-wrapper">
					</div>
					<?php else: ?>
					<div class="alert alert-danger alert-transparent">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
						<strong>Oh snap!</strong> There are not suggestions yet
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-xs-4">
	<div class="panel m-t10 p-10">
		<div class="panel-heading">
			<h4>Quote Text</h4>
		</div>
		<div class="panel-body">
			<div class="col-xs-12 m-t20">
				<div class="row">	
					<div class="form-group">
					<?php if(!empty($quotes)): ?>
						<?php foreach ($quotes as  $quote): ?>
							<textarea  data-toggle="tooltip" placeholder="quote"  cols=5 rows=5 class="form-control quote"><?php echo trim($quote); ?></textarea>
							<a href="#" data-name="custom_quote" class="remove_field">Remove</a>           
						<?php endforeach; ?>
					<?php else: ?>
						<textarea placeholder="quote"  cols=5 rows=5 class="form-control quote"></textarea>
					<?php endif; ?>
					</div>
					<button class="add_input btn btn-save boton">Add Another Quote</button>
					<button class="btn btn-save boton" id="quote_save">Save</button> 
				</div>
			</div>
		</div>
	</div>
</div>
<div id="follower_modal_area">
</div>
<script id="timeline-template" type="text/x-handlebars-template">
	<ul class="timeline">
		{{#each this}}
		<li class="tweet" data-retweet="{{ this.id_str }}">
			<div class="avatar">
				<img src="{{ this.user.profile_image_url_https }}" />
				<div class="hover" title="Retweet Now" data-toggle="tooltip" data-id="{{ this.id_str }}" id="retweet">
					<div class="icon-twitter"></div>
				</div>
			</div>
			<div class="bubble-container">
				<div class="bubble">
				<div class="retweet">
					<div class="fa fa-trash delete" title="Delete" data-id="{{ this.id_str }}" data-toggle="tooltip"></div>
				</div>
					<h3 class="username">{{ this.user.screen_name }}	</h3> <br/>
					<p class="description">	
						{{ this.text }}								
					</p>
				</div>
				
				<div class="arrow"></div>
			</div>
		</li>
		{{/each}}
	</ul>
</script>

