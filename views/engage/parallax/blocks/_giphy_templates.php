    <script type="text/template" id="giphy-modes">
        <label class="cb-radio regRoboto m-r10">
            <span class="cb-inner">
                <i><input class="mode" name="mode" value="search" type="radio"></i>
            </span>
            Search
        </label>
        <label class="cb-radio regRoboto m-r10">
            <span class="cb-inner">
                <i><input class="mode" name="mode" value="trending" type="radio"></i>
            </span>
            Search
        </label>
        <label class="cb-radio regRoboto m-r10">
            <span class="cb-inner">
                <i><input class="mode" name="mode" value="translate" type="radio"></i>
            </span>
            Search
        </label>                
    </script>   

    <script type="text/template" id="giphy-search">
        <div class='m-b10 m-t10'>
            <div class="round-radio">
                <input id="radio1" class="mode" name="mode" value="search" type="radio">
                <label for="radio1"></label>
                <div class="cb-label">Search</div>
            </div>    
            <div class="round-radio">
                <input id="radio2" class="mode" name="mode" value="trending" type="radio">
                <label for="radio2"></label>
                <div class="cb-label">Tending</div>
            </div>   
            <div class="round-radio">
                <input id="radio3" class="mode" name="mode" value="translate" type="radio">
                <label for="radio3"></label>
                <div class="cb-label">Translate</div>
            </div>                                    
        </div>
        <div class="col-xs-12"></div>
        <div class='m-b10 m-t10'>    
            <label>
            <p class="m-t10">Keyword</p>
            <input type='text' id='giphy_keyword'/>
            </label>
            <button id="giphy_button">Search</button>
        </div>
        <div id="ajax-indicator" >
        </div>
        <div id="giphy-results-region">
            <ul class="block-grid-lg-2 block-grid-md-1 block-grid-sm-1" id="giphy-list"></ul>
            <ul class="block-grid-lg-2 block-grid-md-1 block-grid-sm-1" id="giphy-posts-list"></ul>
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
    <script type="text/template" id="giphy-embed"> 
        <li class="giphy-post-item block-grid-item" data-giphy-post-index="<%= index %>">
            <iframe src='<%= embed_url %>' width="480" height="178" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>
            <p width="480"><a href="javascript:void(0)" class="btn button-gloss gloss-red giphy-item">Pick</a><a style="margin-left: 349px" target="_blank" href="<%= bitly_url %>">via GIPHY</a></p>
        </li>
    </script>   
    <script type="text/template" id="giphy-embed-post">
        <li class="giphy-post-item block-grid-item" data-giphy-post-index="<%= attributes.source_id %>">
            <div class="fb-post" data-href="<%= attributes.url %>" data-width="500"></div>
            <button class="small save add-post" data-cid="<%= cid %>">Pick</button>
        </li>
    </script>   
    <script type="text/template" id="giphy-external-post">
        <li class="giphy-post-item block-grid-item">
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