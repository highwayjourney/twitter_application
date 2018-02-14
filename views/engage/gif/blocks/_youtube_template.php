<script type="text/template" id="youtube-layout-template">
		<div id="myModal" class="modal fade">
		    <div class="modal-dialog" id="video-preview">
		    </div>
		</div>		
		<label>
			<p>Search for videos:</p>
			<input type="text" name="search" id="search-box"/>
				<button id="search-button" class="btn btn-primary">Get videos</button>				
				<div >
					<div id="ajax-indicator" >
					</div>
					<ul class="block-grid-lg-4 block-grid-md-3 block-grid-sm-2"id="video-list"></ul>
					<div style="clear:both"></div>
					<div id="page-buttons" >
						<button id="prev-page-button" class="btn btn-primary page-button btn-sm" data-page-token=""
						style="display: none;">
						<i class="fa fa-arrow-circle-left" ></i> Previous
						</button>
						<button id="next-page-button" class="btn btn-primary page-button btn-sm" data-page-token=""
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
						<h3 id="modalTitle" style="color:white"><%= title %></h3>
		            </div>
		            <div class="modal-body">
		                <iframe width="560" height="315" src="https://www.youtube.com/embed/<%= source_id %>?rel=0" frameborder="0" allowfullscreen></iframe>
		            </div>
		            <div class="modal-footer">
		               <p>Max Duration 15 Seconds</p>
		              <input type="text" placeholder="Start Time (seconds)" id="since"/>
  					  <input type="text" placeholder="End Time (seconds)" id="until"/>
						<div class="button-collection">
							<button id="select-video-button" class="select-video-button btn btn-primary btn-sm" data-video-index="<%= index %>" >
								Choose this video...
							</button>
					  	<div>		                
		            </div>
		            <input type="hidden" id="video-url" value="<%= source_id %>"/>
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