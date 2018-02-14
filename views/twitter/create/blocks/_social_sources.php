<?php // ********************** MAIN TEMPLATE**************************************************** ?>
    <script type="text/template" id="main-layout"/>

        <div id="main-region"></div>
    </script>
<?php //************************ Facebook Templates******************************?> 
<?php echo $this->template->block('facebook_search', 'twitter/create/blocks/_facebook_templates'); ?>

<?php //************************ Youtube Templates******************************?> 
<?php echo $this->template->block('youtube_search', 'twitter/create/blocks/_youtube_templates'); ?>

<?php //************************ Youtube Templates******************************?> 
<?php echo $this->template->block('giphy_search', 'twitter/create/blocks/_giphy_templates'); ?>

<div class="row">
    <div class="col-xs-12 m-t5 custom-form">
        <label class="cb-checkbox regRoboto m-r10" class="social_attach" data-toggle="#social_grab" id="source_label">
            <input type="checkbox" id="social_attach" class="social_attach">
            <?= 'Grab Content' ?>
        </label>
    </div>
</div>
<div class="row is-hidden" id="social_grab">
    <div class="col-xs-12 custom-form">
        <div class="m-b10 m-t10"/>
            <?php if (sizeof($postSources) < 1) : ?>
                <span class="badge yellow-bg">Warning</span>
                <?= 'Please Add a Social Account.  ' ?> <a href="<?php echo site_url('settings/socialmedia');?>"><?= 'Do it Now' ?></a>                
            <?php else : ?> 
                <p class="text_color strong-size"><?= 'Select Source' ?></p>
                <?php foreach($postSources as $social) : ?>
                    <div class="round-radio">
                        <input id="radio-<?= $social ?>" name="social_source" value="<?= $social ?>" type="radio">
                        <label for="radio-<?= $social ?>"></label>
                        <div class="cb-label">
                            <cite class="ti-<?= $social ?>"></cite>
                            <?= ucfirst($social) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xs-12 attachment-block" id="image-designer-block">
        <div id="main-view"> </div>
    </div>    
</div>
<script>window.fbAsyncInit = function() {
  FB.init({
    xfbml      : true,
    version    : 'v2.4'
  });
  }; (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>