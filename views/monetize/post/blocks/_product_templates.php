    <script type="text/template" id="product-layout-template">
        <label>
        <p>Keyword</p>
        <input type='text' id='search-box'/>
        </label>
        <button id="search-button">Search</button>
        <div id="ajax-indicator">
        </div>

        <div id="product-results-region">
   
        </div>
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
    </script>

    <script type="text/template" id="product-single-template">
        <div class="product-wrap" data-productid="<%= source_id %>">
            <textarea cols="32" rows="3" disabled="disabled"><%= description %></textarea>
            <a target="_blank" href="<%= url %>"><img src="<%= media_url %>" class="img-thumbnail"/></a>
        </div>

    </script>

    <script type="text/template" id="no-data-template">
        <p>No Results Found</p>

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