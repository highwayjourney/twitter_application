<?php
/**
 * @var array $groups
 * @var array $imageDesignerImages
 */
// if(isset($social_post)) {
//     $activeSocials = unserialize($social_post->post_to_socials);
// } else {
//     $activeSocials = $socials;
// }
// $inactiveSocials = Social_post::$socials;
// foreach($inactiveSocials as $key => $inactiveSocial) {
//     if(in_array($inactiveSocial, $activeSocials)) {
//         unset($inactiveSocials[$key]);
//     }
// }
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




[class*="block-grid-"] {
  display: block;
  margin: -15px;
  padding: 0;
}
.block-grid-item {
  display: inline;
  margin: 0;
  padding: 15px;
  height: auto;
  float: left;
  width: 100%;
  list-style: none;
}
.block-grid-xs-12 > .block-grid-item {
  width: 8.333333333333334%;
}
.block-grid-xs-12 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-12 > .block-grid-item:nth-of-type(12n+1) {
  clear: both;
}
.block-grid-xs-11 > .block-grid-item {
  width: 9.090909090909092%;
}
.block-grid-xs-11 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-11 > .block-grid-item:nth-of-type(11n+1) {
  clear: both;
}
.block-grid-xs-10 > .block-grid-item {
  width: 10%;
}
.block-grid-xs-10 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-10 > .block-grid-item:nth-of-type(10n+1) {
  clear: both;
}
.block-grid-xs-9 > .block-grid-item {
  width: 11.11111111111111%;
}
.block-grid-xs-9 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-9 > .block-grid-item:nth-of-type(9n+1) {
  clear: both;
}
.block-grid-xs-8 > .block-grid-item {
  width: 12.5%;
}
.block-grid-xs-8 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-8 > .block-grid-item:nth-of-type(8n+1) {
  clear: both;
}
.block-grid-xs-7 > .block-grid-item {
  width: 14.285714285714286%;
}
.block-grid-xs-7 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-7 > .block-grid-item:nth-of-type(7n+1) {
  clear: both;
}
.block-grid-xs-6 > .block-grid-item {
  width: 16.666666666666668%;
}
.block-grid-xs-6 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-6 > .block-grid-item:nth-of-type(6n+1) {
  clear: both;
}
.block-grid-xs-5 > .block-grid-item {
  width: 20%;
}
.block-grid-xs-5 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-5 > .block-grid-item:nth-of-type(5n+1) {
  clear: both;
}
.block-grid-xs-4 > .block-grid-item {
  width: 25%;
}
.block-grid-xs-4 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-4 > .block-grid-item:nth-of-type(4n+1) {
  clear: both;
}
.block-grid-xs-3 > .block-grid-item {
  width: 33.333333333333336%;
}
.block-grid-xs-3 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-3 > .block-grid-item:nth-of-type(3n+1) {
  clear: both;
}
.block-grid-xs-2 > .block-grid-item {
  width: 50%;
}
.block-grid-xs-2 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-2 > .block-grid-item:nth-of-type(2n+1) {
  clear: both;
}
.block-grid-xs-1 > .block-grid-item {
  width: 100%;
}
.block-grid-xs-1 > .block-grid-item:nth-of-type(n) {
  clear: none;
}
.block-grid-xs-1 > .block-grid-item:nth-of-type(1n+1) {
  clear: both;
}
@media (min-width: 768px) {
  .block-grid-sm-12 > .block-grid-item {
    width: 8.333333333333334%;
  }
  .block-grid-sm-12 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-12 > .block-grid-item:nth-of-type(12n+1) {
    clear: both;
  }
  .block-grid-sm-11 > .block-grid-item {
    width: 9.090909090909092%;
  }
  .block-grid-sm-11 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-11 > .block-grid-item:nth-of-type(11n+1) {
    clear: both;
  }
  .block-grid-sm-10 > .block-grid-item {
    width: 10%;
  }
  .block-grid-sm-10 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-10 > .block-grid-item:nth-of-type(10n+1) {
    clear: both;
  }
  .block-grid-sm-9 > .block-grid-item {
    width: 11.11111111111111%;
  }
  .block-grid-sm-9 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-9 > .block-grid-item:nth-of-type(9n+1) {
    clear: both;
  }
  .block-grid-sm-8 > .block-grid-item {
    width: 12.5%;
  }
  .block-grid-sm-8 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-8 > .block-grid-item:nth-of-type(8n+1) {
    clear: both;
  }
  .block-grid-sm-7 > .block-grid-item {
    width: 14.285714285714286%;
  }
  .block-grid-sm-7 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-7 > .block-grid-item:nth-of-type(7n+1) {
    clear: both;
  }
  .block-grid-sm-6 > .block-grid-item {
    width: 16.666666666666668%;
  }
  .block-grid-sm-6 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-6 > .block-grid-item:nth-of-type(6n+1) {
    clear: both;
  }
  .block-grid-sm-5 > .block-grid-item {
    width: 20%;
  }
  .block-grid-sm-5 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-5 > .block-grid-item:nth-of-type(5n+1) {
    clear: both;
  }
  .block-grid-sm-4 > .block-grid-item {
    width: 25%;
  }
  .block-grid-sm-4 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-4 > .block-grid-item:nth-of-type(4n+1) {
    clear: both;
  }
  .block-grid-sm-3 > .block-grid-item {
    width: 33.333333333333336%;
  }
  .block-grid-sm-3 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-3 > .block-grid-item:nth-of-type(3n+1) {
    clear: both;
  }
  .block-grid-sm-2 > .block-grid-item {
    width: 50%;
  }
  .block-grid-sm-2 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-2 > .block-grid-item:nth-of-type(2n+1) {
    clear: both;
  }
  .block-grid-sm-1 > .block-grid-item {
    width: 100%;
  }
  .block-grid-sm-1 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-sm-1 > .block-grid-item:nth-of-type(1n+1) {
    clear: both;
  }
}
@media (min-width: 992px) {
  .block-grid-md-12 > .block-grid-item {
    width: 8.333333333333334%;
  }
  .block-grid-md-12 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-12 > .block-grid-item:nth-of-type(12n+1) {
    clear: both;
  }
  .block-grid-md-11 > .block-grid-item {
    width: 9.090909090909092%;
  }
  .block-grid-md-11 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-11 > .block-grid-item:nth-of-type(11n+1) {
    clear: both;
  }
  .block-grid-md-10 > .block-grid-item {
    width: 10%;
  }
  .block-grid-md-10 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-10 > .block-grid-item:nth-of-type(10n+1) {
    clear: both;
  }
  .block-grid-md-9 > .block-grid-item {
    width: 11.11111111111111%;
  }
  .block-grid-md-9 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-9 > .block-grid-item:nth-of-type(9n+1) {
    clear: both;
  }
  .block-grid-md-8 > .block-grid-item {
    width: 12.5%;
  }
  .block-grid-md-8 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-8 > .block-grid-item:nth-of-type(8n+1) {
    clear: both;
  }
  .block-grid-md-7 > .block-grid-item {
    width: 14.285714285714286%;
  }
  .block-grid-md-7 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-7 > .block-grid-item:nth-of-type(7n+1) {
    clear: both;
  }
  .block-grid-md-6 > .block-grid-item {
    width: 16.666666666666668%;
  }
  .block-grid-md-6 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-6 > .block-grid-item:nth-of-type(6n+1) {
    clear: both;
  }
  .block-grid-md-5 > .block-grid-item {
    width: 20%;
  }
  .block-grid-md-5 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-5 > .block-grid-item:nth-of-type(5n+1) {
    clear: both;
  }
  .block-grid-md-4 > .block-grid-item {
    width: 25%;
  }
  .block-grid-md-4 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-4 > .block-grid-item:nth-of-type(4n+1) {
    clear: both;
  }
  .block-grid-md-3 > .block-grid-item {
    width: 33.333333333333336%;
  }
  .block-grid-md-3 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-3 > .block-grid-item:nth-of-type(3n+1) {
    clear: both;
  }
  .block-grid-md-2 > .block-grid-item {
    width: 50%;
  }
  .block-grid-md-2 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-2 > .block-grid-item:nth-of-type(2n+1) {
    clear: both;
  }
  .block-grid-md-1 > .block-grid-item {
    width: 100%;
  }
  .block-grid-md-1 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-md-1 > .block-grid-item:nth-of-type(1n+1) {
    clear: both;
  }
}
@media (min-width: 1200px) {
  .block-grid-lg-12 > .block-grid-item {
    width: 8.333333333333334%;
  }
  .block-grid-lg-12 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-12 > .block-grid-item:nth-of-type(12n+1) {
    clear: both;
  }
  .block-grid-lg-11 > .block-grid-item {
    width: 9.090909090909092%;
  }
  .block-grid-lg-11 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-11 > .block-grid-item:nth-of-type(11n+1) {
    clear: both;
  }
  .block-grid-lg-10 > .block-grid-item {
    width: 10%;
  }
  .block-grid-lg-10 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-10 > .block-grid-item:nth-of-type(10n+1) {
    clear: both;
  }
  .block-grid-lg-9 > .block-grid-item {
    width: 11.11111111111111%;
  }
  .block-grid-lg-9 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-9 > .block-grid-item:nth-of-type(9n+1) {
    clear: both;
  }
  .block-grid-lg-8 > .block-grid-item {
    width: 12.5%;
  }
  .block-grid-lg-8 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-8 > .block-grid-item:nth-of-type(8n+1) {
    clear: both;
  }
  .block-grid-lg-7 > .block-grid-item {
    width: 14.285714285714286%;
  }
  .block-grid-lg-7 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-7 > .block-grid-item:nth-of-type(7n+1) {
    clear: both;
  }
  .block-grid-lg-6 > .block-grid-item {
    width: 16.666666666666668%;
  }
  .block-grid-lg-6 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-6 > .block-grid-item:nth-of-type(6n+1) {
    clear: both;
  }
  .block-grid-lg-5 > .block-grid-item {
    width: 20%;
  }
  .block-grid-lg-5 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-5 > .block-grid-item:nth-of-type(5n+1) {
    clear: both;
  }
  .block-grid-lg-4 > .block-grid-item {
    width: 25%;
  }
  .block-grid-lg-4 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-4 > .block-grid-item:nth-of-type(4n+1) {
    clear: both;
  }
  .block-grid-lg-3 > .block-grid-item {
    width: 33.333333333333336%;
  }
  .block-grid-lg-3 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-3 > .block-grid-item:nth-of-type(3n+1) {
    clear: both;
  }
  .block-grid-lg-2 > .block-grid-item {
    width: 50%;
  }
  .block-grid-lg-2 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-2 > .block-grid-item:nth-of-type(2n+1) {
    clear: both;
  }
  .block-grid-lg-1 > .block-grid-item {
    width: 100%;
  }
  .block-grid-lg-1 > .block-grid-item:nth-of-type(n) {
    clear: none;
  }
  .block-grid-lg-1 > .block-grid-item:nth-of-type(1n+1) {
    clear: both;
  }
}

.product-wrap{
  cursor: pointer;
}


   
</style>

<?php // ********************** MAIN TEMPLATE**************************************************** ?>
    <script type="text/template" id="main-layout"/>
        <div id="main-region">Please Select a Social Source</div>
    </script>



<?php echo $this->template->block('facebook_search', 'monetize/post/blocks/_facebook_templates'); ?>
<?php echo $this->template->block('youtube_search', 'monetize/post/blocks/_youtube_templates'); ?>
<?php echo $this->template->block('youtube_search', 'monetize/post/blocks/_product_templates'); ?>

<div class="row" id="social_grab">
    <div class="col-xs-12 custom-form"> 
                <?php if (sizeof($postSources) < 1) : ?>
                <div class="callout callout-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-ban"></i> <?= lang('alert') ?></h4>
                                 <?= lang('no_social_sources') ?> <a href="<?php echo site_url('settings/socialmedia');?>"><?= lang('do_it') ?></a>
                </div>
                <?php else : ?> 
                    <?php foreach($postSources as $social => $value) : ?>
                        <label class="cb-radio regRoboto m-r10" data-hide="#custom">
                            <input name="social_source" value="<?= $social ?>" type="radio">
                            <cite class="ti-<?= $social ?>"></cite>
                            <?= ucfirst($social) ?>
                        </label>
                    <?php endforeach; ?>                   
                <?php endif; ?>
        </p>        
    </div>
    <div class="col-xs-12 attachment-block" id="image-designer-block">
        <div id="main-view"> </div>
    </div>    
</div>
