<!-- Latest compiled and minified CSS --><link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.5.4/bootstrap-select.min.css"><script>// Tooltips Initialization$(function () {  $('[data-toggle="tooltip"]').tooltip()})</script><style>.sp-replacer{	border: solid 0px #91765d !important;	background : #fff !important;}.sp-preview {    position: relative;    width: 25px;    height: 25px;    border: solid 2px #222;    margin-right: 5px;    float: left;    z-index: 0;    border-radius: 45px;}.sp-preview-inner, .sp-alpha-inner, .sp-thumb-inner{	border-radius: 24px;}</style><!-- File input type --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.css"><script src="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.js"></script><!-- New look --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.3.0/css/mdb.min.css"><script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.3.0/js/mdb.min.js"></script><script src="https://storage.googleapis.com/code.getmdl.io/1.0.6/material.min.js"></script>           <script langauage="javascript">      function showMessage(value){         document.getElementById("canvasWidth").innerHTML = value;		 $('#canvasWidth').val(value);		 activeCanvas.setWidth(value);      }			function showHeight(value){         document.getElementById("canvasHeight").innerHTML = value;		 $('#canvasHeight').val(value);		 activeCanvas.setHeight(value);      }	  	   </script>   <div class="row" id="tool">
    <div class="container">
        <div class="row">
            <div class="col-sm-5" id="frame" style="margin-top:15px;">

                <ul class="nav nav-pills">                    <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-picture-o" style="font-size:20px;"></i></a></li>                    <li class=""><a data-toggle="tab" href="#youtube"><i class="fa fa-youtube" style="font-size:20px;"></i></a></li>                </ul>

                <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">
                        <p>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="badge grey">Text & Objects Tools</h4>									<p class="text-justify"></p>
                                    <button data-toggle="tooltip" title="Draw" id="freedraw" class="btn btn-primary"  style="width:px; margin-right:10px;"><i class="fa fa-paint-brush"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Add Text" class="btn btn-primary" onclick="dropText();" style="width:px; margin-right:10px;"><i class="fa fa-font"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Add Circle" class="btn btn-primary" onclick="circle();" style="margin-right:10px;"><i class="fa  fa-circle"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Add Square" class="btn btn-primary" onclick="rect();" style="margin-right:10px;"><i class="fa fa-square"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Clone Object" class="btn btn-primary" onclick="cloneobject();" style="width:px; margin-right:10px;"><i class="fa fa-files-o"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Delete Object" class="btn btn-primary" onclick="deleteObject();" style="width:px; margin-right:10px;"><i class="fa fa-trash-o"></i></button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="badge grey">Frame Tools</h4>									<p class="text-justify"></p>
                                    <button type="button" data-toggle="tooltip" title="New Frame" class="btn btn-primary" onclick="createFrame();" style="margin-right:10px;"><i class="fa fa-plus"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Delete Current Frame" id="deleteFrame" class="btn btn-primary"  style="margin-right:10px;"><i class="fa fa-minus"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Clone Current Frame" class="btn btn-primary" onclick="cloneFrame();" style="margin-right:10px;"><i class="fa fa-files-o"></i></button>                                
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="badge grey">Common Tools</h4>									<p class="text-justify"></p>
                                    <button type="button" data-toggle="tooltip" title="Bring Front" class="btn btn-primary" onclick="bringfront();" style="margin-right:10px;"><img src="https://maxcdn.icons8.com/iOS7/PNG/25/User_Interface/bring_to_front-25.png" title="Bring To Front" width="25"></i></button>
                                    <button type="button" data-toggle="tooltip" title="Send Back" class="btn btn-primary" onclick="bringforward();" style="margin-right:10px;"><img src="https://maxcdn.icons8.com/iOS7/PNG/25/Editing/send_to_back_filled-25.png" title="Send To Back Filled" width="25"></button>
                                    <button type="button" data-toggle="tooltip" title="Move Tool" class="btn btn-primary" onclick="selection();" style="margin-right:10px;"><i class="fa fa-arrows"></i></button>
                                    <input type='text' title="Object Color" class='basic' id="custom" value='#428BCA'  />
                                </div>
                            </div>														<div class="row">                                <div class="col-sm-12">									<div class="fileinput fileinput-new" data-provides="fileinput">											<span class="btn btn-default btn-file"><span>Choose file</span><input type="file" id="imgLoader" /></span>											<span class="fileinput-filename"></span><span class="fileinput-new badge grey" >No file chosen</span>									</div>								</div>							</div>							
                            <div class="row">								                                 <div class="col-sm-12">									<h4 class="badge grey">All Fonts</h4>									<p class="text-justify"></p>                                    <select class="form-control" data-style="btn-default" id="fontfamilychange" data-width="50%" style="height: 43px;width: 259px;">                                       <option value="Arial">Arial</option>                                       <option value="Verdana">Verdana</option>                                       <option value="Tahoma">Tahoma</option>                                       <option value="Courier New">Courier New</option>                                       <option value="Lucida Console">Lucida Console</option>                                       <option value="Charcoal"> Charcoal</option>                                       <option value="Comic Sans MS">Comic Sans MS</option>                                       <option value="Georgia">Georgia</option>                                       <option value="Times New Roman">Times New Roman</option>                                    </select>                                                                  </div>                            </div>    



                        </p>
                    </div>
                    <div id="youtube" class="tab-pane fade in">
                        <div id="main-view"> </div>
                    </div>

                </div>

                <div>
                </div>
                <div>
                </div>


            </div>

            <div class="col-sm-7 text-center">

                <div class="row">
                    <div class="col-sm-1" style="margin-left:20px;"></div>
                    <div class="col-sm-4">						<div style="width:200px">							<input id="slider2" class="mdl-slider mdl-js-slider" type="range"  								 min="400" max="1000" value="400" tabindex="0" 								 oninput="showMessage(this.value)" onchange="showMessage(this.value)">						 </div>						<div style="margin-left: 28px;"> 
							<label>Width:</label>							<input id="canvasWidth" type="text" value="400" name="canvasWidth" >						</div>                    </div>
                    <div class="col-sm-4">						<div style="width:200px">							<input id="slider2" class="mdl-slider mdl-js-slider" type="range"  								 min="360" max="1000" value="5" tabindex="0" 								 oninput="showHeight(this.value)" onchange="showHeight(this.value)">						 </div>						<div style="margin-left: 28px;">
							<label>Height:</label>							<input id="canvasHeight" type="text" value="360" name="canvasHeight">						</div>                    </div>                    <div class="col-sm-2" style="margin-top:41px;">                    <input  type='text' class='basic' id="custom-background" value='black' />
                  </div>
                </div>				<div class="row">						<div class="col-sm-1" style="margin-left:20px;"></div>						<div class="col-sm-4">												 <div style="margin-left: 28px;">							<label>Speed:</label>							<input id="canvasHeight" type="text" value="0.1" name="canvasSpeed">						</div>						</div>				</div>

                <div style="margin-top:15px; margin-bottom:20px;">
                    <div id="canvas-id" style="margin-top:10px;"></div>
                    <button type="button" class="btn btn-primary btn-md" style="margin-top:10px; width:200px;" onclick="saveGif();" data-toggle="modal" data-target="#myModale">Save & Preview</button>

                </div>
            </div>


            <div class="row">
                <div class="col-sm-12"  id="frameimage" style="overflow-x:auto;  overflow-y: hidden; max-width:90%; white-space:nowrap;">
                 </div>
            </div>
<!-- MODAL MODAL MODAL -->
            <!-- Trigger the modal with a button -->

            <!-- Modal -->
            <div id="myModale" class="modal fade" role="dialog">
              <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Save & Preview</h4>
                  </div>
                  <div class="modal-body" id="modalgif">
                  </div>
                  <div class="modal-footer">
                    <button type="button" id="save_modal" class="btn btn-default" data-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>

              </div>
            </div>

<!-- MODAL MODAL MODAL -->

        </div>
    </div>    
</div>
<script type="text/template" id="main-layout"/>
    <div id="main-region"></div>
</script>
<?php echo $this->template->block('youtube_search', 'engage/gif/blocks/_youtube_template'); ?>
