<div class="col-xs-6">
	<div class="panel panel-green m-t10 p-10">
		<div class="panel-heading">
			<h4>Upcoming Mentions</h4>
		</div>
		<div class="panel-body">
			<div class="col-xs-12 m-t20">
				<div class="row">
					<?php if(!empty($mentions)): ?>
					<div class="twitter-wrapper">
						<ul class="timeline">
							<?php foreach ($mentions as  $mention): ?>
							<li class="tweet" data-mention="<?php echo $mention['id']; ?>">
								<div class="avatar">
									<img src="<?php echo $mention['user_image']; ?>" />
									<div class="hover" title="Retweet Now" data-toggle="tooltip" data-id="<?php echo $mention['id']; ?>" id="mention">
										<div class="icon-twitter"></div>
									</div>
								</div>
								<div class="bubble-container">
									<div class="bubble">
									<div class="retweet">
										<div class="fa fa-trash" title="Delete" data-id="<?php echo $mention['id']; ?>" id="mention-delete" data-toggle="tooltip"></div>
									</div>
		<!-- 								<h3 class="username">{{ this.user.screen_name }}	</h3> <br/> -->
										<p class="description">	
											<?php echo $mention['message']; ?>							
										</p>
									</div>
									
									<div class="arrow"></div>
								</div>
							</li>						
							<?php endforeach; ?>
						</ul>
					</div>
					<?php else: ?>
					<div class="alert alert-danger alert-transparent">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
						<strong>Oh snap!</strong> There are not mentions yet
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="col-xs-6">
	<div class="panel m-t10 p-10">
		<div class="panel-heading">
			<h4>Mention Text</h4>
		</div>
		<div class="panel-body">
			<div class="col-xs-12 m-t20">
				<div class="row">	
					<div class="form-group">
						<input type="text" placeholder="website" id="website" value="<?php echo empty($website)?'':$website; ?>">
					</div>
				</div>				
				<div class="row">	
					<div class="form-group">
					<?php if(!empty($mention_quotes)): ?>
						<?php foreach ($mention_quotes as  $quote): ?>
							<textarea  data-toggle="tooltip" placeholder="Hey {user}! Check this cool website: {website}"  cols=5 rows=5 class="form-control mention-quote"><?php echo trim($quote); ?></textarea>
							<a href="#" data-name="custom_quote" class="remove_field">Remove</a>           
						<?php endforeach; ?>
					<?php else: ?>
						<textarea placeholder="Hey {user}! Check this cool website: {website}" cols=5 rows=5 class="form-control quote"></textarea>
						<a href="#" data-name="custom_quote" class="remove_field">Remove</a>
					<?php endif; ?>
					</div>
					<button class="mention-add_input btn btn-save boton">Add Another Mention</button>
					<button class="btn btn-save boton" id="mention-quote_save">Save</button> 
				</div>
			</div>
		</div>
	</div>
</div>


