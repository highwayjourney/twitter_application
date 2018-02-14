<script type="text/template" id="youtube-layout-template">
		<div id="myModal" class="modal fade">
		    <div class="modal-dialog" id="video-preview">
		    </div>
		</div>		
		<label>
			<p class="m-t10">Search for videos:</p>
			<input type="text" name="search" id="search-box"/>
				<button id="search-button">Get videos</button>				
				<div class="m-t20">
					<div id="ajax-indicator" >
					</div>
					<ul class="block-grid-lg-4 block-grid-md-3 block-grid-sm-2"id="video-list"></ul>
					<div style="clear:both"></div>
					<div id="page-buttons" >
						<button id="prev-page-button" class="page-button" data-page-token=""
						style="display: none;">
						<i class="fa fa-arrow-circle-left" ></i> Previous
						</button>
						<button id="next-page-button" class="page-button" data-page-token=""
						style="display: none;">
						Next <i class="fa fa-arrow-circle-right" ></i>
						</button>
					</div>
				</div>
		</label>
</script>

<script type="text/template" id="video-preview-template">
		        <div class="modal-content">
		            <div class="modal-header">
						<h3 id="modalTitle"><%= title %></h3>
		            </div>
		            <div class="modal-body">
		                <iframe width="560" height="315" src="https://www.youtube.com/embed/<%= source_id %>?rel=0" frameborder="0" allowfullscreen></iframe>
		            </div>
		            <div class="modal-footer">
						<div class="button-collection">
							<button id="select-video-button" class="select-video-button" data-video-index="<%= index %>" >
								Choose this video...
							</button>
					  	<div>		                
		            </div>
		            <input type="hidden" id="video-url" value="https://www.youtube.com/watch?v=<%= source_id %>"/>
		        </div>
	</script>

	<script type="text/template" id='video-template'>
		<li class="video-item block-grid-item" data-video-index="<%= index %>">
			<a data-video-url="<%= url %>" >
				<img class="img-responsive" src="<%= media_url %>" style="padding: 3px; margin: 5px; border: 1px #ccc	solid;" />
			</a>
		</li>
</script>

	<script type="text/template" id="youtube-url-template" >
		<label>

			<p>Video name: </p>
			<input class="large-8 columns" type="text" id="video-name" name="video-name" />


			<p>Type in your video URL:</p>

			<input class="large-8 columns" type="text" id="video-url" name="video-url" />

		</label>

		<button class="select-video-button url-button" >Save and continue...</button>

	</script>




