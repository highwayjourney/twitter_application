<div id="pixabay_modal" class="modal fade" aria-hidden="true" data-width="760" style="display: none;">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <form class="form-inline" id="input-form">
                            <label class="sr-only" for="q">Keyword</label>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Keyword" name="q" id="q">
                            </div>
                            <label class="" for="image_type">Type</label>
                            <div class="form-group">
                                <select class="form-control" name="image_type" id="image_type">
                                    <option value="all">All</option>
                                    <option value="photo">Photo</option>
                                    <option value="vector">Vector</option>
                                    <option value="illustration">Illustration</option>
                                </select>
                            </div>  
                            <label class="" for="orientation">Orientation</label>
                            <div class="form-group">
                                <select class="form-control" name="orientation" id="orientation">
                                    <option value="all">All</option>
                                    <option value="horizontal">Horizontal</option>
                                    <option value="vertical">Vertical</option>
                                </select>
                            </div>                        
                            <label class="" for="category">Category</label>
                            <div class="form-group">
                                <select class="form-control" name="category" id="category">
                                    <option value="">All</option>
                                    <option value="fashion">Fashion</option>
                                    <option value="nature">Nature</option>
                                    <option value="backgrounds">Backgrounds</option>
                                    <option value="science">Science</option>
                                    <option value="Education">Education</option>
                                    <option value="people">People</option>
                                    <option value="feelings">Feelings</option>
                                    <option value="religion">Religion</option>
                                    <option value="health">Helath</option>
                                    <option value="places">Places</option>
                                    <option value="animals">Animals</option>
                                    <option value="industry">Industry</option>
                                    <option value="food">Food</option>
                                    <option value="computer">Computer</option>
                                    <option value="sports">Sports</option>
                                    <option value="transportation">Transportation</option>
                                    <option value="travel">Travel</option>
                                    <option value="buildings">Buildings</option>
                                    <option value="business">Business</option>
                                    <option value="music">Music</option>                                                                                                                                                                                        
                                </select>
                            </div>
                        </form>                                                                    
                        <button class="btn btn-save pull-right" id="search">Search</button>
                    </div>
                </div>    
                <div class="row top-buffer"> 
                    <div class="col-xs-12">
                        <div id="resultsRegion">
                        </div>
                    </div>
                </div>        
            </div>
        </div>

</div>
<script id="pixalbay-template" type="text/x-handlebars-template">
    <ul class="block-grid-lg-4 block-grid-md-3 block-grid-sm-2">
        {{#each this}}
            <li class="block-grid-item" data-id="{{@index}}">
                <div class="hovereffect">
                    <img src="{{ previewURL }}" />
                    <div class="overlay">
                        <p class="icon-links">
                            <a href="#" data-size="340" class="pixal">
                                <span class="fa">sm</span>
                            </a>
                            <a href="#" data-size="640" class="pixal">
                                <span class="fa">M</span>
                            </a>
                            <a href="#" data-size="960" class="pixal">
                                <span class="fa">L</span>
                            </a>                            
                        </p>
                    </div>   
                </div>                  
            </li>
        {{/each}}
    </ul>
    <div style="clear:both"></div>
    <div id="page-buttons" >
        <button id="prev-page" data-page="1" class="page-button" >
            <i class="fa fa-arrow-circle-left" ></i> Previous
        </button>
        <button id="next-page" data-page="2" class="page-button" >
            Next <i class="fa fa-arrow-circle-right" ></i>
        </button> 
    </div>   
</script>