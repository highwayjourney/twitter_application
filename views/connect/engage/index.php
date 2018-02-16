<?php
/**
 * @var Access_token $token
 * @var array $pages
 * @var integer $selected_fanpage_id
 * @var array $available_configs
 * @var array $not_display_configs
 * @var array $not_display_configs_values
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= 'Engage' ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs" role="tablist">
                <?php foreach ($engage_settings as $key => $value) : ?>
                    <?php if(!empty($value)): ?>
                        <li class="tab tab-active">
                             <li role="presentation" <?php echo ($key=='smart_engage') ? 'class="active"' : '' ?>>
                                <a href="#<?= $key; ?>" aria-controls="<?= $key; ?>" role="tab" data-toggle="tab">
                                    <?= lang($key); ?>
                                </a>
                             </li>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <?php foreach ($engage_settings as $key => $value) : ?>
                    <div role="tabpanel" class="tab-pane <?php echo ($key=='smart_engage') ? 'active' : '' ?>" id="<?= $key; ?>">
                        <?php if($key == 'smart_engage'): ?>
                            <div class="row">
                                <div class="col-xs-6">
                                <?php echo $this->template->block('_keywords_panel', 'connect/engage/blocks/_keywords_panel'); ?>                            
                                </div>
                                <div class="col-xs-6">
                                    <?php echo $this->template->block('_suggested_keywords_panel', 'connect/engage/blocks/_suggested_keywords_panel'); ?>                                    
                                    <?php echo $this->template->block('_suggested_lists_panel', 'connect/engage/blocks/_suggested_lists_panel'); ?>
                                    <?php echo $this->template->block('_current_lists_panel', 'connect/engage/blocks/_current_lists_panel'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if($key == 'auto_follow'): ?>
                            <div class="row">
                                <div class="col-xs-12">
                                <?php echo $this->template->block('_smart_follow', 'connect/engage/blocks/_smart_follow'); ?>                            
                                </div>
                            </div>
                        <?php endif; ?>   
                        <?php if($key == 'auto_retweet'): ?>
                            <div class="row">
                                <div class="col-xs-12">
                                <?php echo $this->template->block('_smart_retweet', 'connect/engage/blocks/_smart_retweet'); ?>                            
                                </div>
                            </div>
                        <?php endif; ?> 
                        <?php if($key == 'auto_favourite'): ?>
                            <div class="row">
                                <div class="col-xs-12">
                                <?php echo $this->template->block('_smart_favourite', 'connect/engage/blocks/_smart_favourite'); ?>                            
                                </div>
                            </div>
                        <?php endif; ?> 
                        <?php if($key == 'smart_mention'): ?>
                            <div class="row">
                                <div class="col-xs-12">
                                <?php echo $this->template->block('_smart_mention', 'connect/engage/blocks/_smart_mention'); ?>                            
                                </div>
                            </div>
                        <?php endif; ?>                                                                                              
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script id="keyword-template" type="text/x-handlebars-template">
    <div class="col-xs-12 m-t20 user_search_keywords_block">
        <div class="row">
            <div class="col-xs-12">
                <div class="b-Bottom m-b15">
                    <div class="form-group">
                        <input class="form-control m-b10"
                               name="keyword[{{ id }}]"
                               placeholder="<?= lang('keywords') ?>">
                        <i class="cb-remove user_search_keywords_delete"></i>
                        <div class="clearfix">
                            <div class="col-xs-4">
                                <label class="cb-checkbox text-size pull-sm-left">
                                    <input type="checkbox"
                                           id="keyword_exact_{{ id }}"
                                           name="exact[{{ id }}]">
                                    <?= lang('exact') ?>
                                </label>
                            </div>                                
                            <div class="col-xs-4">
                                <a href="" class="link show_include_exclude"><?= lang('include_exclude') ?></a>
                            </div>
                            <div class="col-xs-4">
                                <select class="form-control" name="lang[{{ id }}]" id="user_search_keywords_lang_{{ id }}">
                                    <?php foreach ($available_lang as  $lang): ?>
                                        <option value="<?php echo $lang['code']; ?>"> <?php echo $lang['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>                             
                        </div>

                        <div class="toggle_include_exclude row" style="display: none;">
                            <div class="col-sm-6">
                                <p class="text_color"><?= lang('include') ?></p>
                                <div class="form-group">
                                <textarea class="form-control"
                                          id="user_search_keywords_include_{{ id }}"
                                          name="include[{{ id }}]"
                                          placeholder="<?= lang('comma_separated_words') ?>"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <p class="text_color"><?= lang('exclude') ?></p>
                                <div class="form-group">
                                    <textarea class="form-control"
                                              id="user_search_keywords_exclude_{{ id }}"
                                              name="exclude[{{ id }}]"
                                              placeholder="<?= lang('comma_separated_words') ?>"></textarea>
                                </div>
                            </div>
                        </div>                                           
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>