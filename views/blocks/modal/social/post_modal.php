<div id="post_media" class="modal fade" aria-hidden="true" data-width="760" style="display: none;">
   
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row" id="create-social">
                    <div class="col-xs-12">
                        <div class="tab-content settings_content">
                            <div class="tab-pane active">
                                <form action="<?php echo site_url('social/create/post_create');?>" method="POST" id="post-update-form" autocomplete="off">
                                    <?php echo $this->template->block('post_to', 'social/create/blocks/_post_to'); ?>
                                    <div class="row">
                                        <div id="focus_on" class="col-sm-5 m-t10">
                                            <p class="text_color strong-size"><?= lang('type_a_message') ?></p>
                                            <div class="form-group">
                                                <textarea id="description" name="description" rows="5" class="form-control"><?php echo isset($social_post) ? $social_post->description : '';?></textarea>
                                                <span class="help-block char-counter pull-right is-block p-t10"></span>
                                            </div>
                                            <p class="text_color strong-size"><?= lang('add_link') ?></p>
                                            <div class="form-group">
                                                <input type="text" name="url" class="form-control" value="<?php echo isset($social_post) ? $social_post->url : '';?>"/>
                                            </div>                                          
                                            <?php echo $this->template->block('post_on', 'social/create/blocks/_post_on', isset($social_post) ? array('social_post' => $social_post) : array()); ?>
                                            <?php echo $this->template->block('schedule', 'social/create/blocks/_schedule_block', isset($social_post) ? array('social_post' => $social_post) : array());?>                                                                            
                                        </div>
                                        <div class="col-sm-7 m-t10">
                                            <?php if(!isset($social_post)) : ?>
                                            <?php echo $this->template->block(
                                                'attachment',
                                                'social/autopixar/blocks/_post_attachment'
                                            ); ?>                  
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if(isset($social_post)): ?>
                                        <input type="hidden" name="post_id" value="<?php echo $social_post->id; ?>">
                                    <?php endif;?>
                                    <?php //echo $this->template->block('cron', 'social/create/blocks/_post_cron', isset($social_post) ? array('social_post' => $social_post) : array());?>
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
            </div>
        </div>
    
</div>