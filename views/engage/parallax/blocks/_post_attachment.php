<?php
/**
 * @var array $groups
 * @var array $imageDesignerImages
 */
?>
<style>
    .preview .img-close {
        position: absolute;
        right: 27px;
        top: 13px;
        cursor: pointer;
    }

    .preview .img-preview {
        width: 100%;
        margin-bottom: 10px;
    }
    .preview .img-close.logo-close {
        position: absolute;
        right: 8px;
        top: -5px;
        cursor: pointer;
    }

    .preview .img-preview.logo-preview {
        width: 100%;
        margin-bottom: 10px;
    }
</style>
<div class="row" id="attachment">
    <div class="col-xs-12 custom-form">
        <label class="cb-radio regRoboto m-r10 checked">
            <input name="attachment_type" value="image-designer" checked="checked" type="radio">
            <?= lang('image_designer') ?>
        </label>
    </div>
    <div class="col-xs-12 attachment-block" id="image-designer-block"> 
        <div class="well well-standart well-upload-image">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <p class="head_tab"><?= lang('head_text') ?></p>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group ">
                                        <select id="headline_font_select" class="chosen-select">
                                            <option selected="selected" value="Fredoka One">Fredoka One</option>
                                            <option value="Hammersmith One">Hammersmith One</option>
                                            <option value="Josefin Slab">Josefin Slab</option>
                                            <option value="Lato">Lato</option>
                                            <option value="Merriweather">Merriweather</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Open Sans">Open Sans</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Satisfy">Satisfy</option>
                                            <option value="Ubuntu">Ubuntu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group pull-md-right">
                                        <div class="input-group pick-a-color-markup" id="headline_color">
                                            <input value="ffffff" name="headline_color" class="pick-a-color form-control" type="hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="headline_text" id="image-designer-headline-text" rows="10" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    
                    <div class="row">
                        <h4 class="col-sm-3">
                            Animation Type
                        </h4>
                        <div class="col-sm-3">
                            <select id="animation_type" class="form-control">
                                <option value="None">None</option>
                                <option value="FadeIn">Fade In</option>
                                <option value="FadeOut">Fade Out</option>
                                <option value="ZoomIn">Zoom In</option>
                                <option value="ZoomOut">Zoom Out</option>
                                <option value="Move">Move</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <a class="btn btn-success move_points form-control" data-startX="10" data-startY="10" data-endX="50" data-endY="40" style="display:none;font-size:12px;padding-top:8px;"> Click here after moving the header to the start state. </a>
                        </div>
                        
                    </div>
                    <br/>


                    <div class="row">
                        <div class="col-xs-12" id="secondary-text-block" style="display: none;">
                            <p class="head_tab"><?= lang('secondary_text') ?></p>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group ">
                                        <select id="secondary_font_select" class="chosen-select">
                                            <option selected="selected" value="Fredoka One">Fredoka One</option>
                                            <option value="Hammersmith One">Hammersmith One</option>
                                            <option value="Josefin Slab">Josefin Slab</option>
                                            <option value="Lato">Lato</option>
                                            <option value="Merriweather">Merriweather</option>
                                            <option value="Montserrat">Montserrat</option>
                                            <option value="Open Sans">Open Sans</option>
                                            <option value="Roboto">Roboto</option>
                                            <option value="Satisfy">Satisfy</option>
                                            <option value="Ubuntu">Ubuntu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group pull-md-right">
                                        <div class="input-group pick-a-color-markup" id="headline_color">
                                            <input value="ffffff" name="secondary_color" class="pick-a-color form-control" type="hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="secondary_text" id="image-designer-secondary-text" rows="10" class="form-control"></textarea>
                            </div>

                            
                            <div class="row">
                                <h4 class="col-sm-3">
                                    Animation Type
                                </h4>
                                <div class="col-sm-3">
                                    <select id="animation_type1" class="form-control">
                                        <option value="None">None</option>
                                        <option value="FadeIn">Fade In</option>
                                        <option value="FadeOut">Fade Out</option>
                                        <option value="ZoomIn">Zoom In</option>
                                        <option value="ZoomOut">Zoom Out</option>
                                        <option value="Move">Move</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <a class="btn btn-success move_points1 form-control" data-startX="10" data-startY="100" data-endX="10" data-endY="100" style="display:none;font-size:12px;padding-top:8px;"> Click here after moving the header to the start state. </a>
                                </div>

                            </div>
                            <br />

                        </div>
                    </div>
                    <div id="image-designer-logo" style="display: none;">
                        <div class="row">
                            <div class="col-sm-5 preview" id="image-designer-logo">

                            </div>
                            <br/>
                            <div class="row">
                                <h4 class="col-sm-3">
                                    Animation Type
                                </h4>
                                <div class="col-sm-3">
                                    <select id="animation_type3" class="form-control">
                                        <option value="None">None</option>
                                        <option value="FadeIn">Fade In</option>
                                        <option value="FadeOut">Fade Out</option>
                                        <option value="ZoomIn">Zoom In</option>
                                        <option value="ZoomOut">Zoom Out</option>
                                        <option value="Move">Move</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <a class="btn btn-success move_points3 form-control" data-startX="10" data-startY="100" data-endX="10" data-endY="100" style="display:none;font-size:12px;padding-top:8px;"> Click here after moving the header to the start state. </a>
                                </div>

                            </div>
                            <br />
                        </div>
                    </div>
                    <div class="row well-standart">
                        <div class="col-xs-12 text-left">
                            <div class="progressBar" style="display: none;">
                                <div class="progressLine" data-value="0"></div>
                            </div>
                            <button class="btn btn-add m-b20" id="image-designer-add-secondary-text" data-added="false">
                                <?= lang('add_secondary_text') ?>
                            </button>
                            <button class="btn btn-save fileSelect m-b20">
                                <?= lang('add_logo') ?>
                            </button>
                            <input class="uploadbtn inputFile" id="image-designer" multiple="" type="file">
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="well_photo">
                            <div class="progressBar" style="display: none;">
                                <div class="progressLine" data-value="0"></div>
                            </div>
                            <button class="btn btn-upload-photo fileSelect">
                                <?= lang('upload_background_image') ?>
                            </button>
                            <input class="uploadbtn inputFile" id="image-designer-bg" multiple="" type="file">
                            <?php foreach($imageDesignerImages as $imageDesignerImage) : ?>
                                <img
                                    class="image-designer-bg-image"
                                    src="<?= base_url().$imageDesignerImage['thumbnail'] ?>"
                                    alt=""
                                    data-src="<?= base_url().$imageDesignerImage['image'] ?>"
                                    />
                            <?php endforeach; ?>
                        </div>

                        <div class="row">
                            <h4 class="col-sm-3">
                                Animation Type
                            </h4>
                            <div class="col-sm-3">
                                <select id="animation_type2" class="form-control">
                                    <option value="None">None</option>
                                    <option value="FadeIn">Fade In</option>
                                    <option value="FadeOut">Fade Out</option>
                                    <option value="ZoomIn">Zoom In</option>
                                    <option value="ZoomOut">Zoom Out</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6 well-standart">
                    <div class="canvas-block">
                        <div class="canvas-container">
                            <canvas id="image-designer-canvas" width="512px" height="256px"></canvas>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 custom-form m-tb20">
                            <label class="cb-radio regRoboto m-r10 checked">
                                <input name="bg_image_type" value="normal" checked="checked" type="radio">
                                <?= lang('normal') ?>
                            </label>
                            <label class="cb-radio regRoboto m-r10">
                                <input name="bg_image_type" value="blurred" type="radio">
                                <?= lang('blurred') ?>
                            </label>
                            <label class="cb-radio regRoboto m-r10">
                                <input name="bg_image_type" value="grayscale" type="radio">
                                <?= lang('black_and_white') ?>
                            </label>
                            <label class="cb-checkbox regRoboto">
                                <input name="bg_image_type_contrast" id="image-designer-bg-type-contrast" type="checkbox">
                                <?= lang('increased_contrast') ?>
                            </label>
                        </div>
                    </div>

                    <div>
                        <br />
                        <div class="row">
                            <h4 class="col-sm-1">
                            </h4>
                            <h4 class="col-sm-2">
                                Duration
                            </h4>
                            <div class="col-sm-3">
                                <input id="animation_duration" type="number" class="form-control" value="1000" placeholder="in milisecond..." />
                            </div>

                            <div class="col-sm-1">
                                <label><input type="checkbox" checked="checked" class="loop_animation" /> Loop </label>
                            </div>

                            <div class="col-sm-4">
                                <a class="btn btn-primary form-control generate_gif"> Generate GIF</a>
                            </div>
                        </div>
                        
                        <br /><br />
                        <div id="gif_status">Click Generate GIF to generate the GIF from the canvas on top.</div>
                        <img style="display:none;" id="gen_gif" />
                        <br />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-8 col-lg-6 attachment-block" id="photo-block" style="display: none;">
        <div class="well well-standart">
            <?php if(isset($isMedia) && $isMedia && $isMedia->type == 'image'): ?>
                <div class="preview">
                    <img class="img-close"
                         src="/public/images/im_prev_close.png">
                    <img class="img-preview"
                         src="<?= preg_split('|\.\./\.\.|', $isMedia->path)[1]; ?>">
                </div>
            <?php else: ?>
                <i class="fa fa-image"></i>
            <?php endif; ?>
            <button class="btn-save fileSelect">
                <?= lang('upload_photo') ?>
            </button>
            <div class="progressBar" style="display: none;">
                <div class="progressLine" data-value="0"></div>
            </div>
            <input class="form-control uploadbtn inputFile" type="file" multiple="">
        </div>
    </div>
    <div class="col-sm-12 col-md-8 col-lg-6 attachment-block" id="video-block" style="display: none;">
        <div class="well well-standart">
            <i class="fa fa-play"></i>
            <button class="btn-save fileSelect">
                <?= lang('upload_video') ?>
            </button>
            <div class="progressBar" style="display: none;">
                <div class="progressLine" data-value="0"></div>
            </div>
            <input class="form-control uploadbtn inputFile" id="videos" type="file" multiple="">
        </div>
    </div>
    <input type="hidden" name="image_name" value="">
    <input type="hidden" name="image_designer_data" value="">
</div>