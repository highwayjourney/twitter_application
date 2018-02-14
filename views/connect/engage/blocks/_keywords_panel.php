<div class="panel panel-blue m-t10 p-10">
    <div class="panel-heading">                            
        <h4 class="text_color strong-size">Engagement Keywords</h4>
    </div>
    <form id="user-search-keywords-form" action="<?php echo site_url('connect/engage'); ?>" method="POST">
        <input type="hidden" name="submit"/>
        <div class="panel-body">    
          <div class="row" id="user-search-keywords">
                <?php $i = 1; ?>
                <?php foreach ($keywords as $keyword): ?>
                    <?php $id = $keyword->id ? $keyword->id : 'new_' . $i; ?>
                    <div class="m-t20 user_search_keywords_block">
                        <div class="b-Bottom m-b15">
                            <div class="form-group">
                                <input class="form-control m-b10"
                                       name="keyword[<?php echo $id; ?>]"
                                       value="<?php echo HTML::chars($keyword->keyword); ?>"
                                       placeholder="<?= lang('keywords') ?>">
                                <i class="cb-remove user_search_keywords_delete"></i>
                                <div class="clearfix row">
                                    <div class="col-xs-4">
                                        <label class="cb-checkbox text-size pull-sm-left">
                                            <input type="checkbox"
                                                   id="keyword_exact_<?php echo $id ?>"
                                                   name="exact[<?php echo $id; ?>]"
                                                   <?php if ($keyword->exact): ?>checked="checked"<?php endif; ?>>
                                            <?= lang('exact') ?>
                                        </label>
                                    </div>
                                    <div class="col-xs-4">
                                        <a href="" class="link show_include_exclude"><?= lang('include_exclude') ?></a>
                                    </div>
                                    <div class="col-xs-4">
                                        <select class="form-control" name="lang[<?php echo $id; ?>]" id="user_search_keywords_lang_<?php echo $id ?>">
                                            <?php foreach ($available_lang as  $lang): ?>
                                                <option value="<?php echo $lang['code']; ?>" <?php echo $lang['code'] == HTML::chars($keyword->get_other_fields('lang', TRUE))?' selected':''; ?>> <?php echo $lang['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>                                         
                                </div>

                                <div class="toggle_include_exclude row"  style="display: none;">
                                    <div class="col-sm-6">
                                        <p class="text_color"><?= lang('include') ?></p>
                                        <div class="form-group">
                                            <textarea class="form-control"
                                                      id="user_search_keywords_include_<?php echo $id ?>"
                                                      name="include[<?php echo $id; ?>]"
                                                      placeholder="<?= lang('comma_separated_words') ?>"><?php echo HTML::chars($keyword->get_other_fields('include', TRUE)); ?></textarea>
                                        </div>
                                    </div>                                                      
                                    <div class="col-sm-6">
                                        <p class="text_color"><?= lang('exclude') ?></p>
                                        <div class="form-group">
                                            <textarea class="form-control"
                                                      id="user_search_keywords_exclude_<?php echo $id ?>"
                                                      name="exclude[<?php echo $id; ?>]"
                                                      placeholder="<?= lang('comma_separated_words') ?>"><?php echo HTML::chars($keyword->get_other_fields('exclude', TRUE)); ?>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>                             
                            </div>
                        </div>
                    </div>
                    <?php $i += 1; ?>
                <?php endforeach; ?>
            </div>  
            <div class="row">
                <div class="col-xs-12">
                    <div class="pull-sm-right">
                        <a class="btn btn-add user_search_keywords_add_btn m-tb20 m-r20"><?=lang('add_keyword') ?></a>
                        <input class="btn btn-save m-tb20 pull-right" type="submit" value="<?= lang('save') ?>"/>
                    </div>
                </div>
            </div>                                                                              
        </div>
</form>
</div>