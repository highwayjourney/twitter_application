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
<!-- <div class="row">
    <div class="col-xs-12 m-t5 custom-form">
        <label class="cb-checkbox regRoboto m-r10" data-toggle="#attachment">
            <input type="checkbox" id="need_attach">
            <?= lang('attach_image_or_video') ?>
        </label>
    </div>
</div> -->
<div class="row" id="attachment">
    <div class="col-sm-12 custom-form pull-right hidden">
        <label class="cb-radio regRoboto m-r10">
            <input name="attachment_type" value="photo" checked="checked" type="radio">
            <?= lang('photo') ?>
        </label>
        <label class="cb-radio regRoboto">
            <input name="attachment_type" value="video" type="radio">
            <?= lang('video') ?>
        </label>
    </div>
    <div class="col-sm-12 attachment-block" id="photo-block">
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