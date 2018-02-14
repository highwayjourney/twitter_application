
    <div id="mainContainer">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#topMenu" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="topMenu">
 
                    <ul class="nav navbar-nav">
<!--                         <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-arrows"></span> Sizes <span class="caret"></span></a>
                            <ul class="dropdown-menu" id="menuSizes">
                            </ul>
                        </li> -->                          
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="fa fa-pie-chart"></span> Shapes <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" id="menuShapeSquare"><span class="fa fa-square"></span> &nbsp; Square</a></li>
                                <li><a href="#" id="menuShapeCircle"><span class="fa fa-circle"></span> &nbsp; Circle</a></li>
                                <li><a href="#" id="menuShapeTriangle"><span class="fa fa-play fa-rotate-270"></span> &nbsp; Triangle</a></li>
                            </ul>
                        </li>  
                        <li>
                            <a id="menuText" href="#">
                                <span class="fa fa-file-text"></span>
                                Text
                            </a>
                        </li>                                                                    
                        <li class="disabled">
                            <a id="menuUndo" href="#">
                                <span class="fa fa-rotate-left"></span>
                                Undo
                            </a>
                        </li>
                        <li class="disabled">
                            <a id="menuRedo" href="#">
                                <span class="fa fa-rotate-right"></span>
                                Redo
                            </a>
                        </li>
                        <li>
                            <a id="menuNew" href="#">
                                <span class="fa fa-file-o"></span>
                                New
                            </a>
                        </li>       
                        <?php if($has_cloud): ?>
                        <li>
                            <a id="menuGetcloud" href="#">
                                <span class="fa fa-cloud-download"></span>
                                Cloud
                            </a>
                        </li> 
                        <?php endif; ?>      
                        <li>
                            <a id="menuLoad" href="#" data-toggle="modal" data-target="#openDialog">
                                <span class="fa fa-folder-open-o"></span>
                                Load
                            </a>
                        </li>
                       
<!--                         <li>
                            <a>
                            <i class="fa fa-plus-circle fa-lg" id="btnZoomIn" style="cursor:pointer;"></i><span id="zoomperc">100%</span><i class="fa fa-minus-circle fa-lg" id="btnZoomOut" style="cursor:pointer;"></i>
                            </a>
                        </li> -->
                                    
                    </ul>                  
                </div>
            </div>
        </nav>
        <div class="tool-box">
            <div class="row">
                <div class="col-sm-3">
                    <ul class="nav nav-tabs ver">                      
<!--                         <li>
                            <a id="menuTemplates" href="#">
                                <span class="fa fa-files-o"></span>
                                <br>
                                Templates
                            </a>
                        </li> -->
                        <!--<li>
                                <a id="menuBackgrounds" href="#">
                                    <span class="fa fa-picture-o"></span>
                                    Backgrounds
                                </a>
                            </li>-->
                        <li>
                            <a id="menuGraphics" href="#">
                                <span class="fa fa-file-image-o"></span>
                                <br>
                                Background
                            </a>
                        </li>
                        <li>
                            <a id="menuEffect" href="#">
                                <span class="fa fa-star"></span>
                                <br>
                                Effects
                            </a>
                        </li>                        
                        <li>
                            <a id="menuMore" href="#">
                                <span class="fa fa-bars"></span>
                                <br>
                                PixaBay
                            </a>
                        </li>                                                                        
                        <li>
                            <a id="menuUpload" href="#">
                                <span class="fa fa-upload"></span>
                                <br>
                                Upload
                            </a>
                        </li>
                        <li>
                            <a id="menuPost" href="#">
                                <span class="fa fa-send"></span>
                                <br>
                                Post
                            </a>
                        </li>
                        <li>
                            <a id="menuCampaign" href="#">
                                <span class="fa fa-send"></span>
                                <br>
                                Export to Campaign
                            </a>
                        </li>  
                        <li>
                            <a id="menuSpinner" href="#" data-toggle="modal">
                                <span class="fa fa-refresh"></span><br>
                                Spin Design
                            </a>
                        </li>                                              
                        <?php if($has_cloud): ?>
                            <li class="save_existing disabled">
                                <a id="menuExistingCloud" href="#">
                                    <span class="fa fa-cloud-upload"></span>
                                    <br>
                                    Save
                                </a>   
                            </liv>                   
                            <li>
                                <a id="menuCloud" href="#">
                                    <span class="fa fa-cloud"></span>
                                    <br>
                                    Save as New
                                </a>
                            </li> 
                        <?php endif; ?>                                                                                                                                            
                        <li>
                            <a id="menuSave" href="#" download="design.json">
                                <span class="fa fa-save"></span>
                                <br>
                                Download
                            </a>
                        </li>
                        <li>
                            <a id="menuExport" href="#" download="design.png">
                                <span class="fa fa-download"></span>
                                <br>
                                Export
                            </a>
                        </li>    
<!--                         <ul class="list-unstyled hidden-xs" id="sidebar-footer">
                            <li>
                                <i class="fa fa-plus-circle fa-lg" id="btnZoomIn" style="cursor:pointer;"></i><br><span id="zoomperc">83%</span><br><i class="fa fa-minus-circle fa-lg" id="btnZoomOut" style="cursor:pointer;"></i>
                            </li>
                        </ul>    -->                                            
                    </ul>
                </ul>
                </div>
                <div class="col-sm-9">
                    <div id="panelControl"></div>
                </div>   
            </div>             
        </div>
        <!-- <div id="panelControl"></div> -->  <!-- MOD -->
        <div id="panelEdit">
            <div id="singleActions" style="display: none;">
            <div>
                <a id="btnMoveTop" class="btn btn-primary" title="Move to Top">
                    <span class="glyphicon glyphicon-arrow-up"></span>
                </a>
                <a id="btnMoveUp" class="btn btn-primary" title="Move Up">
                    <span class="glyphicon glyphicon-chevron-up"></span>
                </a>
                <a id="btnMoveDown" class="btn btn-primary" title="Move Down">
                    <span class="glyphicon glyphicon-chevron-down"></span>
                </a>
                <a id="btnMoveBottom" class="btn btn-primary" title="Move to Bottom">
                    <span class="glyphicon glyphicon-arrow-down"></span>
                </a>
                <a id="aleft" class="btn btn-primary" title="Align Left">
                    <span class="glyphicon glyphicon-align-left"></span>
                </a>
                <a id="acenter" class="btn btn-primary" title="Align Center">
                    <span class="glyphicon glyphicon-align-center"></span>
                </a>
                <a id="aright" class="btn btn-primary" title="Align Right">
                    <span class="glyphicon glyphicon-align-right"></span>
                </a>                
                <a id="btnCopy" class="btn btn-warning" title="Copy">
                    <span class="fa fa-copy"></span>
                </a>
                <a id="btnDelete" class="btn btn-danger" title="Remove">
                    <span class="fa fa-trash"></span>
                </a>
            </div>
            <br />

            <div class="slider-container">
                <span>Rotation</span>
                <div>
                    <input id="sliderRotation" type="text" data-slider-min="0" data-slider-max="360" data-slider-step="1" data-slider-value="0" />
                </div>
            </div>
            <div class="slider-container">
                <span>Scale</span>
                <div>
                    <input id="sliderScale" type="text" data-slider-min="0.1" data-slider-max="10" data-slider-step="0.1" data-slider-value="1" />
                </div>
            </div>
            <div class="slider-container">
                <span>Opacity</span>
                <div>
                    <input id="sliderOpacity" type="text" data-slider-min="0" data-slider-max="1" data-slider-step="0.1" data-slider-value="1" />
                </div>
            </div>
            </div>
            <div id="textActions" style="display: none;">
                <div class="slider-container">
                    <span>Text</span>
                    <textarea id="txtText" class="form-control ver"></textarea>
                </div>
                <div class="slider-container">
                    <span>Color</span>
                    <div id="colorPickerDiv" class="input-group" style="padding: 0;">
                        <input type="text" value="" class="form-control ver" />
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <div class="slider-container">
                    <span>Font</span>
                    <select id="ddlFont" class="form-control ver">
                    </select>
                </div>
                <div class="slider-container">
                    <span>Size</span>
                    <div>
                        <input id="sliderTextSize" type="text" data-slider-min="10" data-slider-max="200" data-slider-step="1" data-slider-value="40" />
                    </div>
                </div>                
                <div>
                    <a id="btnTextBold" class="btn btn-default"><span class="fa fa-bold"></span></a>
                    <a id="btnTextItalic" class="btn btn-default"><span class="fa fa-italic"></span></a>
                    <a id="btnTextUnderline" class="btn btn-default"><span class="fa fa-underline"></span></a>
                </div>
                <br/>
            </div>
        
            <div id="shapeActions" style="display: none;">
                <div class="slider-container">
                    <span>Color</span>
                    <div id="colorPickerShapeDiv" class="input-group" style="padding: 0;">
                        <input type="text" value="" class="form-control ver" />
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
            </div>
            <hr/>
            <div id="shadowActions" style="display: none">
                <div class="slider-container">
                    <span>Shadow / Glow</span>
                    <select id="ddlShadow" class="form-control">
                        <option value="" selected="selected">None</option>
                        <option value="shadow">Shadow</option>
                        <option value="glow">Outer Glow</option>
                    </select>
                </div>
                <div class="slider-container">
                    <span>Color</span>
                    <div id="colorPickerShadowDiv" class="input-group" style="padding: 0;">
                        <input type="text" value="" class="form-control" />
                        <span class="input-group-addon"><i></i></span>
                    </div>
                </div>
                <div class="slider-container">
                    <span>Opacity</span>
                    <div>
                        <input id="sliderShadowOpacity" type="text" data-slider-min="0" data-slider-max="1" data-slider-step="0.1" data-slider-value="1" style="width: 100%;" />
                    </div>
                </div>
                <div class="slider-container">
                    <span>Blur size</span>
                    <div>
                        <input id="sliderShadowSize" type="text" data-slider-min="10" data-slider-max="50" data-slider-step="5" data-slider-value="10" style="width: 100%;" />
                    </div>
                </div>
            </div>
            <?php if($has_parallax): ?>
            <hr/>
            <div id="videoActions">
                <div class="slider-container">
                    <span>Parrallax Effect</span>
                    <select id="ddlAnimations" class="form-control ver">
                        <option value="" selected="selected">None</option>
                        <option value="zoom">Zoom in/out</option>
                        <option value="move">Move</option>
                        <option value="rotate">Rotate</option>
                        <option value="opacity">Opacity</option>
                        <option value="color">Color</option>
                    </select>
                </div>
                <div class="slider-container" data-group="zoom" style="display: none;">
                    <span>Zoom level</span>
                    <select id="ddlAnimationZoom" class="form-control ver">
                        <option value="0">Zero (hidden)</option>
                        <option value="0.2">5x (smaller)</option>
                        <option value="0.5">2x (smaller)</option>
                        <option value="2" selected="selected">2x (bigger)</option>
                        <option value="5">5x (bigger)</option>
                        <option value="10">10x (bigger)</option>
                    </select>
                </div>
                <div data-group="move" style="display: none;">
                    <div class="slider-container">
                        <span>X-delta (horizontal)</span>
                        <input id="txtAnimationX" type="text" class="form-control ver" />
                    </div>
                    <div class="slider-container">
                        <span>Y-delta (vertical)</span>
                        <input id="txtAnimationY" type="text" class="form-control ver" />
                    </div>
                </div>
                <div class="slider-container" data-group="rotate" style="display: none">
                    <span>Angle</span>
                    <div>
                        <input id="sliderAnimationAngle" type="text" data-slider-min="15" data-slider-max="360" data-slider-step="15" data-slider-value="360" />
                    </div>
                </div>
                <div class="slider-container" data-group="opacity" style="display: none">
                    <span>Opacity transform</span>
                    <select id="ddlAnimationOpacity" class="form-control ver">
                        <option value="-1" selected="selected">Visible to Hidden</option>
                        <option value="1">Hidden to Visible</option>
                    </select>
                </div>
                <div data-group="color" style="display: none;">
                    <div class="slider-container">
                        <span>Color 1</span>
                        <div id="colorPickerAnimation1" class="input-group" style="padding: 0;">
                            <input type="text" value="#ff0000" class="form-control ver" />
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                    <div class="slider-container">
                        <span>Color 2</span>
                        <div id="colorPickerAnimation2" class="input-group" style="padding: 0;">
                            <input type="text" value="#00ff00" class="form-control ver" />
                            <span class="input-group-addon"><i></i></span>
                        </div>
                    </div>
                </div>
                <div class="slider-container">
                    <span>Number of frames</span>
                    <select id="ddlAnimationFrames" class="form-control ver ver">
                        <option value="2">2</option>
                        <option value="5" selected="selected">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
                
                <br/>
                <a id="btnAnimation" class="btn btn-warning" title="Apply Effect">
                    <span class="glyphicon glyphicon-time"></span> Apply Effect
                </a>
            </div>          
            <?php endif; ?>  
        </div>
        <div id="panelUpload">
            <div class="dropzone">
                Drag & drop image here
            </div>
            <div class="images"></div>
        </div>
        <div id="panelEffect">
            <div id="effectActions" class="slider-container">
                <span>Effect</span>
                <select id="ddlEffect" class="form-control" style="margin-top: 10px;">
                    <option value="" selected="selected">None</option>
                    <option value="grayscale">Grayscale</option>
                    <option value="emboss">Emboss</option>
                    <option value="sharpen">Sharpen</option>
                    <option value="blur">Blur</option>
                </select>
            </div>
            <a id="btnApplyMosaic" class="btn btn-warning" style="margin-top: 10px; margin-bottom: 10px;">
                Apply Mosaic
            </a>
            <hr/>
            <div id="lightActions" class="slider-container">
                <span>Lighting</span>
                <div class="images">
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/1.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/1.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/2a.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/2a.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/2b.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/2b.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/2c.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/2c.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/2d.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/2d.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/2e.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/2e.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/3.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/3.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/4.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/4.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/5.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/5.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/6.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/6.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/7.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/7.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/8.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/8.png" />
                    <img src="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/thumb/9.png" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/lights/9.png" />
                </div>
            </div>
            <div id="gradientOverlayActions" class="slider-container">
                <span>Gradient overlay</span>
                <div id="colorPickerOverlay1" class="input-group" style="padding: 0; margin-top: 10px;">
                    <input type="text" value="#ff0000" class="form-control" />
                    <span class="input-group-addon"><i></i></span>
                </div>
                <div id="colorPickerOverlay2" class="input-group" style="padding: 0; margin-top: 10px;">
                    <input type="text" value="#00ff00" class="form-control" />
                    <span class="input-group-addon"><i></i></span>
                </div>
                <a id="btnGradientOverlay" class="btn btn-warning" title="Add overlay" style="margin-top: 10px; margin-bottom: 10px;">
                    Add overlay
                </a>
            </div>
            <div id="patternActions" class="slider-container">
                <span>Patterns</span>
                <div class="items">
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg2.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg2.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg3.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg3.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg4.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg4.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg5.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg5.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg6.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg6.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg7.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg7.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg8.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg8.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg9.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg9.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg10.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg10.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg11.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg11.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg12.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg12.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg13.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg13.png"></div>
                    <div style="background-image: url(https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg14.png)" data-path="https://app.socimattic.com/public/assets/design-tool/data/effects/patterns/body-bg14.png"></div>
                </div>
            </div>
        </div>
        <div id="panelDraw">
            <canvas id="canvas"></canvas>
            <div class="info"></div>
        </div>
    </div>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="openDialog">
    
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Load design</h4>
                </div>
                <div class="modal-body">
                    <div class="dropzone">
                        Drag & drop data file here
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
      
    </div>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="sizeDialog">
   
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Input your size</h4>
                </div>
                <div class="modal-body">
                    <div class="form-inline">
                        <span>width: </span>
                        <input type="text" class="form-control ver" />
                        <span> - height: </span>
                        <input type="text" class="form-control ver" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">OK</button>
                </div>
            </div>
    
    </div>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="gifDialog">
        
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Animated output</h4>
                </div>
                <div class="modal-body">
                    <img id="gifImage" style="max-width: 100%" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a id="gifDownload" download="design.gif" class="btn btn-primary">Download</a>
                    <a id="postGif"  class="btn btn-primary">Post</a>
                </div>
            </div>
       
    </div>
    
    <div class="modal fade" tabindex="-1" role="dialog" id="openSpinner">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Spinner</h4>
                </div>
                <div class="modal-body">
                    <div class="spinner_config">
                        <div class="row">
                            <div class="col-xs-6">
                                <h4 class="head_tab"><?= 'Background' ?></h4>
                                <label class="">
                                    <span class="cb-inner">
                                        <i><input type="radio" name="background" value="random" checked="checked"></i>
                                    </span>
                                    Random        
                                </label> 
                                <label class="">
                                    <span class="cb-inner">
                                        <i><input type="radio" name="background" value="unique"></i>
                                    </span>
                                    Custom Design Only        
                                </label>   

                                <h4 style="margin-top:5px" class="head_tab" id="type_head"><?= 'Number of Slides' ?></h4>
                                <select name="slide_count" class="form-control slide_count">   
                                    <option>4</option>
                                    <option>6</option> 
                                    <option>8</option> 
                                    <option>10</option> 
                                </select>                                            
                            </div>
                            <div class="col-xs-6">
                                <h4 class="head_tab"><?= 'Fonts' ?></h4>
                                <select name="camp_font" class="form-control camp_font">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12" id="result_area">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="spin" class="btn btn-save">Spin</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
      
    </div>    


    <div id="overlay">
        <span class="fa fa-spin fa-spinner"></span>
    </div>     
    <?php if($has_gif): ?>
    <!-- Bof mods BlueMagica -->
    <div class="modal fade" tabindex="-1" role="dialog" id="gifSettingsDialog">
        
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="fa fa-gear"></span> Settings</h4>
                </div>
                <div class="modal-body">
                    <div class="form-inline">
                        <span>Presets: </span>
                        <select class="form-control ver gif_presets">
                            <option>320 x 240</option>
                        </select>
                    </div>
                    <div class="form-inline">
                        <span>Width: </span>
                        <input type="text" name="gif_width" class="form-control ver gif_width" />
                        <span> Height: </span>
                        <input type="text" name="gif_height" class="form-control ver gif_height" />
                    </div>
                    <div class="form-inline">
                        <span>Quality: </span>
                        <select name="gif_quality" class="form-control ver gif_quality">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            <option>6</option>
                            <option>7</option>
                            <option>8</option>
                            <option>9</option>
                            <option default>10</option>
                        </select>
                    </div>
                    <div class="bm_approxFileSize"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        
    </div>

    <div id="bm_animLineUpCont" class="inactive">
        <div class="bm_animLineUpCont_header">
            <div class="title"><span class="fa fa-plus-square"></span><span class="fa fa-minus-square"></span> Mini-Video GIF</div>
            <div class="settings_btn"><span class="fa fa-gear"></span> Settings</div>
            <div class="export_cloud_btn disabled" id="save_gif"><span class="fa fa-film disabled"></span> Save</div>
            <div class="export_cloud_btn" id="save_new_gif"><span class="fa fa-film"></span> Save as</div>            
            <div class="export_btn"><span class="fa fa-film"></span> Export GIF</div> 
            <div class="export_cloud_btn" id="post_gif"><span class="fa fa-send"></span> Post</div>
            <div class="export_cloud_btn" id="menu_youtube_search"><span class="fa fa-youtube"></span> Youtube</div>             
            <div class="preview_btn"><span class="play_animation"><span class="fa fa-eye"></span> Preview</span><span class="stop_animation"><span class="fa fa-stop"></span> Stop</span></div>
        </div>
        <div class="bm_animLineUpCont_body">
            <div class="bm_animLineUpCont_body_slides"></div>
            <div class="bm_animLineUpCont_addSlide"></div>
        </div>
    </div>
    <!-- Eof mods BlueMagica -->
    <?php endif;?>
    <div id="outMosaic" style="display: none"></div>
    <?php echo $this->template->block('_post_modal', 'blocks/modal/twitter/post_modal'); ?>
    <?php echo $this->template->block('_pixabay_modal', 'blocks/modal/twitter/pixabay_modal'); ?>
    <?php echo $this->template->block('_pixabay_modal', 'blocks/modal/twitter/storage_modal'); ?>


<script id="carousel" type="text/x-handlebars-template"> 

        <h4 style="margin-top:20px" class="head_tab"><?= 'Results' ?></h4>
        <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="10000000000">

          <ol class="carousel-indicators">
            {{#each this}}
                <li data-target="#myCarousel" data-slide-to="{{@index}}" class="{{class @index}}"></li>
            {{/each}}
          </ol>

          <div class="carousel-inner" role="listbox">
            {{#each this}}

                <div class="item {{class @index}}">
                  <img src="{{ url }}" alt="">
                  <div class="carousel-caption">
                    <div class="box" data-id="{{ @index }}">
                        <div class="box-icon">
                          <span class="fa fa-2x icon  fa-heart-o"></span>  
                        </div>                        
                    </div>
                  </div>
                  <span class="download" data-id="{{ @index }}">
                    <a id="download" download href="{{ url }}" data-toggle="tooltip" title="Download Image"><i class="fa fa-download close_block"></i></a>
                  </span>                  
                </div>
            {{/each}}

          </div>

          <!-- Left and right controls -->
          <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>        

       
</script>