<div id="storage_modal"  class="modal fade" tabindex="-1" data-focus-on="input:first" data-width="760" style="display: none;">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12" id="storage_modal_body">
                        
                    </div>                  
                </div>
                <div class="row top-buffer">
                    <div class="col-xs-12" id="storage_modal_result">
                    </div>   
                </div>
            </div>
        </div>
  
</div>
<div id="stack2" class="modal fade" tabindex="-1" data-focus-on="input:first" data-width="600"  style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div id="video_modal_content">
        </div>       
    </div>
</div>

<script id="video-player-template" type="text/x-handlebars-template">
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ this }}?rel=0" frameborder="0" allowfullscreen></iframe>
                </div>                  
            </div>
        </div>
        <div class="modal-footer">
          <div class="form-group">
          <input type="text" placeholder="Start Time (seconds)" id="since"/>
          <input type="text" placeholder="End Time (seconds)" id="until"/>
          <input type="text" placeholder="Frame Rate" id="frame_rate"/>
            <label class="radio-inline">
              <input type="radio" name="optradio" value="advanced">Advanced
            </label>
            <label class="radio-inline">
              <input type="radio" name="optradio" checked value="normal">Normal
            </label>
            <div class="button-collection">
                <button id="select-video-button" data-id="{{ this }}" >
                    Choose this video...
                </button>
            <div>     
          </div>                  
        </div> 
</script>

<script id="save-design-template" type="text/x-handlebars-template">
    <form class="form-inline" id="input-form">
        <p>Tag your design</p>
        <div class="form-group">
            <ul id="myTags">
                <li>
                    {{ this.tag }}
                </li>
            </ul> 
        </div>
    </form>   

    <button class="btn btn-save" data-type="{{ this.type }}" id="btn-cloud">Save</button>
         
</script>

<script id="design-search-template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-xs-12">
            <form class="form-inline" id="search-design-form">
                <label class="" for="category">Tags</label>
                <div class="form-group">
                    <select id="tag-select" name="tags" multiple>
                        <option value="all" selected>any</option>
                        {{#each user_tags}}
                            <option value="{{ this }}">{{this}} </option>
                        {{/each}}
                    </select>
                </div>                        
                <label class="" for="type">Type</label>
                <div class="form-group">
                    <select class="form-control" name="type" id="type">
                        <option value="all">All</option>
                        <option value="design">Design</option>
                        <option value="gif">Gif</option>
                    </select>
                </div>                        
            </form>                                                                    
            <button class="btn btn-save pull-right" id="filter">Search</button>
        </div>
    </div>    
      
           
</script>


<script id="current-designs-template" type="text/x-handlebars-template">
    {{#if data}}
        <ul class="block-grid-lg-3 block-grid-md-3 block-grid-sm-2">
            {{#each data}}
                <li class="block-grid-item" data-id="{{id}}" data-name="{{created}}" data-type="{{type}}">
                    <div class="hovereffect">
                        <img class="img-responsive" src="{{ ../url }}public/assets/design-tool/data/user-designs/thumbs/{{ user_id }}/{{ created }}.png" />
                        <div class="overlay">
                            <p class="icon-links">
                                <a href="#">
                                    <span class="fa fa-pencil" id="editDesign"></span>
                                </a>
                                <a href="#">
                                    <span class="fa fa-trash-o" id="deleteDesign"></span>
                                </a>
                            </p>
                        </div>   
                    </div>             
                </li>
            {{/each}}
        </ul>
        <div style="clear:both"></div>
        <div id="page-buttons" >
            <button id="prev-page" data-page="{{pagination.previous}}" class="page-button" >
                <i class="fa fa-arrow-circle-left" ></i> Previous
            </button>
            <button id="next-page" data-page="{{pagination.next}}" class="page-button" >
                Next <i class="fa fa-arrow-circle-right" ></i>
            </button> 
        </div> 
    {{else}}
        <h3>There is not data available</h3>
    {{/if}}  
</script>


<script id="youtube_search_template" type="text/x-handlebars-template">
    <div class="row">
        <div class="col-xs-12">
            <form class="form-inline" id="search-design-form">
                <label class="" for="category">Keyword</label>
                <div class="form-group">
                    <input type="text" id="keyword" name="keyword" />
                </div>                                               
            </form>                                                                    
            <button class="btn btn-save pull-right" id="youtube_search">Search</button>
        </div>
    </div>    
      
           
</script>


<script id="youtube_results_template" type="text/x-handlebars-template">
    {{#if this}}
        <ul class="block-grid-lg-3 block-grid-md-3 block-grid-sm-2">
            {{#each this}}
                <li class="block-grid-item" data-id="{{source_id}}" >
                    <img class="img-responsive" src="{{ media_url }}" />   
                </li>
            {{/each}}
        </ul>
        <div style="clear:both"></div>
        <div id="page-buttons" >
            <button id="prev-page" data-page="{{prevPageToken}}" class="page-button-y" >
                <i class="fa fa-arrow-circle-left" ></i> Previous
            </button>
            <button id="next-page" data-page="{{nextPageToken}}" class="page-button-y" >
                Next <i class="fa fa-arrow-circle-right" ></i>
            </button> 
        </div> 
    {{else}}
        <h3>There is not data available</h3>
    {{/if}}  
</script>