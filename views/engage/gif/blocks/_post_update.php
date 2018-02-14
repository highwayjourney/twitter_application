<?php
/**
 * @var bool $isSupportScheduledPosts
 * @var array $groups
 * @var array $socials
 * @var array $imageDesignerImages
 */
?>
<?php echo $this->template->block('_post_update', 'engage/gif/blocks/_tool'); ?>
<div class="row is-hidden" id="tweet">
    <div class="col-xs-12">
        <form action="<?php echo site_url('engage/gif/post_create');?>" method="POST" id="post-update-form" autocomplete="off">
            <?php echo $this->template->block('post_to', 'monetize/post/blocks/_post_to'); ?>
            <div class="row">
                <?php $classInput = (isset($dashboard)) ? 'col-md-10' : 'col-lg-4 col-md-5';?>
                <div id="focus_on" class="col-xs-12 m-t10 <?php echo $classInput;?>">
                    <p class="text_color strong-size"><?= lang('type_a_message') ?></p>
                    <div class="form-group">
                        <textarea id="description" name="description" rows="5" class="form-control"><?php echo isset($social_post) ? $social_post->description : '';?></textarea>
                        <span class="help-block char-counter pull-right is-block p-t10"></span>
                    </div>
                      
                        <p class="text_color strong-size"><?= lang('add_link') ?></p>
                        <div class="form-group">
                            <input type="text" name="url" class="form-control" value="<?php echo isset($social_post) ? $social_post->url : '';?>"/>
                        </div>
                       
                    
                </div> 
                <div class="col-xs-12 col-lg-8 col-md-7">
                    <div class="form-group">
                        <div id="gif-wrap">
                        </div>                        
                    </div>
                </div>                            
            </div>
             <input name="image_name" type="hidden" />    
            <?php if(isset($social_post)): ?>
                <input type="hidden" name="post_id" value="<?php echo $social_post->id; ?>">
            <?php endif;?>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right m-t20 m-b40">
            <button id="edit" type="button" class="btn btn-edit is-hidden"><?= 'Edit' ?></button>
            <button id="post-button" type="button" class="btn btn-save is-hidden"><?= 'Post' ?></button>
        </div>
    </div>
</div>