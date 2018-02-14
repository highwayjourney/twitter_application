<?php
/**
 * @var bool $isSupportScheduledPosts
 * @var array $groups
 * @var array $socials
 * @var array $imageDesignerImages
 */
?>
<div class="row" id="create-social">
    <div class="col-xs-12">
        <div class="tab-content settings_content">
            <div class="tab-pane active">
                <form action="<?php echo site_url('monetize/post/post_create');?>" method="POST" id="post-update-form" autocomplete="off">
                    <?php echo $this->template->block('post_to', 'monetize/post/blocks/_post_to'); ?>                    
                    <?php if(!isset($social_post)) : ?>
                        <?php echo $this->template->block(
                            'sources',
                            'monetize/post/blocks/_social_sources',
                            array(
                                'socials' => $socials
                            )
                        ); ?>    
                    <?php endif; ?>
                    <div class="is-hidden" id="custom">
                        <div class="row">
                            <div id="focus_on" class="col-xs-12 m-t10 col-lg-6 col-md-8">
                                <p class="text_color strong-size"><?= lang('type_a_message') ?></p>
                                <div class="form-group">
                                    <textarea id="description" name="description" rows="5" class="form-control"><?php echo isset($social_post) ? $social_post->description : '';?></textarea>
                                    <span class="help-block char-counter pull-right is-block p-t10"></span>
                                </div>
                            </div>
                        </div>

                    <div style="margin-top: 35px;" class="row">
                        <div class="col-sm-6">
                            <p class="text_color strong-size"><?= lang('add_link') ?></p>
                            <div class="form-group">
                                <input type="text" name="url" class="form-control" value="<?php echo isset($social_post) ? $social_post->url : '';?>"/>
                            </div>
                        </div>
                    </div>   
                
                        <?php if(!isset($social_post)) : ?>
                        <?php echo $this->template->block(
                            'attachment',
                            'monetize/post/blocks/_post_attachment',
                            array(
                                'groups' => $groups,
                                'imageDesignerImages' => $imageDesignerImages
                            )
                        ); ?>                
                        <?php endif; ?>


                        <?php if(isset($social_post)): ?>
                            <input type="hidden" name="post_id" value="<?php echo $social_post->id; ?>">
                        <?php endif;?>
                        <?php echo $this->template->block('post_on', 'monetize/post/blocks/_post_on', isset($social_post) ? array('social_post' => $social_post) : array()); ?>
                        <?php if($isSupportScheduledPosts): ?>
                            <?php echo $this->template->block('schedule', 'monetize/post/blocks/_schedule_block', isset($social_post) ? array('social_post' => $social_post) : array());?>
                        <?php endif;?>
                        <?php echo $this->template->block('cron', 'monetize/post/blocks/_post_cron', isset($social_post) ? array('social_post' => $social_post) : array());?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right m-t20 m-b40">
            <button id="post-button" type="button" class="btn btn-save"><?= lang('post') ?></button>
        </div>
    </div>
</div>