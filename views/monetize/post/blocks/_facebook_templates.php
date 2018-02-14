    <script type="text/template" id="facebook-search">
        <label>
        <p>Keyword</p>
        <input type='text' id='facebook_keyword'/>
        </label>
        <button id="facebook_button">Search</button>
        <div id="ajax-indicator" >
        </div>
        <div id="facebook-results-region">
            <ul class="block-grid-lg-4 block-grid-md-3 block-grid-sm-2" id="facebook-list"></ul>
            <ul class="block-grid-lg-3 block-grid-md-3 block-grid-sm-1" id="facebook-posts-list"></ul>
            <div style="clear:both"></div>
            <div id="page-buttons" >
                <button id="prev-page-button" class="page-button" data-page-token=""
                style="display: none;">
                <i class="fa fa-arrow-circle-left" ></i> Previous
                </button>
                <button id="next-page-button" class="page-button" data-page-token=""
                style="display: none;">
                Next <i class="fa fa-arrow-circle-right" ></i>

                <button id="prev-post-button" class="arrow" data-page-token=""
                style="display: none;">
                <i class="fa fa-arrow-circle-left" ></i> Previous
                </button>
                <button id="next-post-button" class="arrow" data-page-token=""
                style="display: none;">
                Next <i class="fa fa-arrow-circle-right" ></i>
                </button>                
                </button>
            </div>          
        </div>
    </script>
    <script type="text/template" id="facebook-embed"> 
        <li class="facebook-post-item block-grid-item" data-facebook-post-index="<%= id %>">
            <img src="<%= picture.data.url %>" class="thumb"/>
            <a href="#" class="button content-button fb-posts">Posts</a>
            <h5><%= name %></h5>
            <p><%= about %></p>
            </br>
            <span class="facebook_details"><strong> <%= talking_about_count %></strong> <p>people are talking about this page</p> 
            
        </li>
    </script>   
    <script type="text/template" id="facebook-embed-post">
        <li class="facebook-post-item block-grid-item" data-facebook-post-index="<%= attributes.source_id %>">
            <div class="fb-post" data-href="<%= attributes.url %>" data-width="500"></div>
            <button class="small save add-post" data-cid="<%= cid %>">Pick</button>
        </li>
    </script>   
    <script type="text/template" id="facebook-external-post">
        <li class="facebook-post-item block-grid-item">
            <p class="small-text">
            <a href="<%= attributes.url %>" target="_blank" >
            <% if (typeof(attributes.title) !== "null" && attributes.title !== null ) { %>
                <%= attributes.title.substring(0,75) %>
            <% } %>                                                
            </a></p>
            <img class="img-responsive" src="<%= attributes.media_url %>" />
            <button class="small save add-post" data-cid="<%= cid %>">Pick</button>
        </li>   
    </script>