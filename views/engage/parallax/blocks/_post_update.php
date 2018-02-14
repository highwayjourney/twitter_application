<?php
/**
 * @var bool $isSupportScheduledPosts
 * @var array $groups
 * @var array $imageDesignerImages
 */
?>
<div class="row" id="parallax-social">
    <div class="col-xs-12">
        <div class="tab-content settings_content">
            <div class="tab-pane active">
                <form action="<?php echo site_url('engage/parallax/post_create');?>" method="POST" id="post-update-form" autocomplete="off">
                    <?php echo $this->template->block('post_to', 'engage/parallax/blocks/_post_to'); ?>
                    <?php //echo $this->template->block('bulk_upload', 'engage/parallax/blocks/_bulk_upload'); ?>
                    <div class="row">
                        <?php $classInput = (isset($dashboard)) ? 'col-md-10' : 'col-lg-6 col-md-8';?>
                        <div class="col-xs-12 m-t10 <?php echo $classInput;?>">
                            <div class="panel panel-blue">
                               <div class="panel-heading">                            
                                    <p class="text_color strong-size"><?= lang('type_a_message') ?></p>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <textarea name="description" rows="5" class="form-control"><?php echo isset($social_post) ? $social_post->description : '';?></textarea>
                                        <span class="help-block char-counter pull-right is-block p-t10"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(!isset($social_post)) : ?>
                    <?php echo $this->template->block(
                        'attachment',
                        'engage/parallax/blocks/_post_attachment',
                        array(
                            'groups' => $groups,
                            'imageDesignerImages' => $imageDesignerImages
                        )
                    ); ?>
                    <?php endif; ?>
                 
                    <div class="row">
                        <div class="col-sm-6 m-t20">
                            <div class="panel panel-blue">
                               <div class="panel-heading">                            
                                    <p class="text_color strong-size"><?= lang('add_link') ?></p>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <input type="text" name="url" class="form-control" value="<?php echo isset($social_post) ? $social_post->url : '';?>"/>
                                    </div>
                                </div>
                            </div>                                                        
                        </div>
                    </div>

                    <?php if(isset($social_post)): ?>
                        <input type="hidden" name="post_id" value="<?php echo $social_post->id; ?>">
                    <?php endif;?>
                    <?php echo $this->template->block('post_on', 'engage/parallax/blocks/_post_on', isset($social_post) ? array('social_post' => $social_post) : array()); ?>
                    <?php if($isSupportScheduledPosts): ?>
                        <?php echo $this->template->block('schedule', 'engage/parallax/blocks/_schedule_block', isset($social_post) ? array('social_post' => $social_post) : array());?>
                    <?php endif;?>
                    <?php echo $this->template->block('cron', 'engage/parallax/blocks/_post_cron', isset($social_post) ? array('social_post' => $social_post) : array());?>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right m-t20 m-b40">
            <button id="post-button" type="button" class="btn btn-save"><?= 'Post' ?></button>
        </div>
    </div>
</div>