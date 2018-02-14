<div class="col-xs-8">
	<div class="panel panel-green m-t10 p-10">
		<div class="panel-heading">
			<h4>Upcoming Favourites</h4>
		</div>
		<div class="panel-body">
			<div class="col-xs-12 m-t20">
				<div class="row">
					<?php if(empty($favourite_data)): ?>
					<div class="twitter-wrapper favourite-wrapper">
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
<div id="follower_modal_area">
</div>
<script id="favourite-template" type="text/x-handlebars-template">
	<ul class="timeline">
		{{#each this}}
		<li class="tweet" data-retweet="{{ this.id_str }}">
			<div class="avatar">
				<img src="{{ this.user.profile_image_url_https }}" />
				<div class="hover" title="Favourite Now" data-toggle="tooltip" data-id="{{ this.id_str }}" id="retweet">
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

