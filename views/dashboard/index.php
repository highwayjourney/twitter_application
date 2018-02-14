<?php
/**
 * @var array $summary
 * @var bool $need_welcome_notification
 */
?>
<!-- <div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= lang('dashboard');?></h1>
        </div>
    </div>
</div> -->
<div class="main_block">
    <div class="row">
        <div class="col-md-12 box_dashboard">
            <div class="block_content">
                <!-- <h2 class="block_content_title"><?= lang('trends');?></h2> -->
                <div class="block_content_body">
                    <div class="row">
                        <?php if(!empty($opportunities['twitter'])):?>
                        
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-grey">
                                <div class="panel-body" style="background-color: #e77338;">
                                    <div class="social-widget">
                                        <div class="social-body">
                                            <h1><?php echo empty($total_tweets)?'0':$total_tweets ?><span>Tweets</span></h1>
                                        </div>
                                        <p class="social-widget-info">More Info about this <strong class="text-info">Graphics Charts</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-grey">
                                <div class="panel-body" style="background-color: #91c46b;">
                                    <div class="social-widget">
                                        <div class="social-body">
                                            <h1><?php echo empty($total_followers)?'0':$total_followers ?><span>Followers</span></h1>
                                        </div>
                                        <p class="social-widget-info">More Info about this <strong class="text-info">Graphics Charts</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-grey">
                                <div class="panel-body" style="background-color: #6e91cb;">
                                    <div class="social-widget">
                                        <div class="social-body">
                                            <h1><?php echo empty($total_following)?'0':$total_following ?><span>Following</span></h1>
                                        </div>
                                        <p class="social-widget-info">More Info about this <strong class="text-info">Graphics Charts</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                            <div class="panel panel-grey">
                                <div class="panel-body" style="background-color: #FFD06B;">
                                    <div class="social-widget">
                                        <div class="social-body">
                                            <h1><?php echo empty($total_favorites)?'0':$total_favorites ?><span>Favourites</span></h1>
                                        </div>
<!--                                         <div class="social-footer clearfix">
                                            <div class="growth"><i class="icon-circle-down text-danger">
                                                </i><h2>10<sup>%</sup></h2>
                                            </div>
                                        </div> -->
                                        <p class="social-widget-info">More Info about this <strong class="text-info">Graphics Charts</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <?php endif;?>                                                                      
                    </div>
                    <div class="row m-t30">
   
                        <div class="col-xs-12">
                            <div class="tab-content settings_content">
                                <div class="row" style="display: none;">
                                    <div class="col-xs-12">
                                        <ul class="nav nav-tabs settings_tab">
                                            <?php foreach($access_tokens as $access_token) : ?>
                                                <li class="setting_item <?= ($token['id'] == $access_token['id']) ? 'active' : '' ?> auto token_item"
                                                    data-token-id="<?= $access_token['id'] ?>">
                                                    <a class="setting_link" href="<?php echo site_url('twitter/activity/twitter?token_id='.$access_token['id']); ?>">
                                                        <i class="ti-folder"></i>
                                                        <?= $access_token['name'] ?>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <ul class="nav nav-tabs settings_tab">
                                            <li class="setting_item active auto">
                                                <a class="setting_link" href="<?php echo site_url('twitter/activity/load_tweets'); ?>" id="twitter-feed" data-toggle="tab">
                                                    <i class="ti-folder"></i>
                                                    <?= lang('main_feed') ?>
                                                </a>
                                            </li>
                                            <li class="setting_item auto">
                                                <a class="setting_link" href="<?php echo site_url('twitter/activity/load_tweets'); ?>" id="mentions" data-toggle="tab">
                                                    <i class="ti-book"></i>
                                                    <?= lang('mentions') ?>
                                                </a>
                                            </li>
                                            <li class="setting_item auto">
                                                <a class="setting_link" href="<?php echo site_url('twitter/activity/load_tweets'); ?>" id="my-feed" data-toggle="tab">
                                                    <i class="ti-new-window"></i>
                                                    <?= lang('sent_tweets') ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="tab-content settings_content">
                                    <div class="tab-pane active">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="web_radar m-t20 pull_border" id="ajax-area">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <ul class="pagination pull-right">
                                            <li class="pagination_item">
                                                <a href="<?php echo site_url('twitter/activity/load_tweets'); ?>" class="prev pagination_link" data-url="<?php echo isset($paging['previous']) ? $paging['previous'] : ''; ?>">
                                                    <?= lang('previous') ?>
                                                </a>
                                            </li>
                                            <li class="pagination_item active">
                                                <a class="pagination_link" id="pages-counter">1</a>
                                            </li>
                                            <li class="pagination_item">
                                                <a href="<?php echo site_url('twitter/activity/load_tweets'); ?>" class="next pagination_link"  data-url="<?php echo isset($paging['next']) ? $paging['next'] : ''; ?>">
                                                    <?= lang('next') ?>
                                                </a>
                                            </li>
                                            <input type="hidden" id="page_number" value="1">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div id="reply-window" class="modal fade">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <h4 class="head_tab"><?= lang('enter_reply_text') ?></h4>
                                        <textarea class="form-control" rows="5" cols="10" class="twitter_reply_textarea"></textarea>
                                    </div>
                                    <div class="modal-footer clearfix">
                                        <div class="pull-right">
                                            <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel') ?></a>
                                            <button type="button" id="reply" class="btn btn-save"><?= lang('send') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal-->
        <div id="reply-window" class="modal fade" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4 id="myModalLabel" class="head_tab"><?= lang('enter_reply_text');?></h4>
                        <textarea rows="5" cols="70" class="twitter_reply_textarea"></textarea>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="pull-right">
                            <a class="link m-r10" data-dismiss="modal" aria-hidden="true" href=""><?= lang('cancel');?></a>
                            <button type="button" id="reply" class="btn btn-save"><?= lang('send');?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php if(isset($need_welcome_notification)): ?>
    <script language="JavaScript">
        $(window).ready(function() {
            $('#welcome').modal();
            $.ajax({
                url: g_settings.base_url + 'social/create/update_notification',
                data: {
                    notification: 'welcome',
                    show: false
                },
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    if(!data.success) {
                        showFlashErrors(data.message);
                    } else {
                        $('#welcome').modal('close');
                    }
                },
                complete: function () {
                    //stopWait();
                }
            });
        });
    </script>
<div id="welcome" class="modal welcome-modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <h4 class="modal-body__title text-center"><?= lang('thanks_for_register') ?></h4>
                <p class="modal-body__text text-center">
                    <?= lang('welcome_modal_text') ?>
                </p>
                <div class="text-center">
                    <a href="<?php echo site_url('settings/socialmedia/facebook') ?>" class="btn btn-modal btn-modal-facebook"><i class="fa fa-facebook"></i> <?= lang('connect_facebook') ?>
                    </a>
                    <a href="<?php echo site_url('settings/socialmedia/twitter') ?>" class="btn btn-modal btn-modal-twitter"><i class="fa fa-twitter"></i> <?= lang('connect_twitter') ?>
                    </a>
                    <a href="<?php echo site_url('settings/socialmedia/linkedin') ?>" class="btn btn-modal btn-modal-linkedin"><i class="fa fa-linkedin"></i> <?= lang('connect_linkedin') ?>
                    </a>
                </div>
                <p class="black text-center m-tb20">
                    <?= lang('or') ?> <a href="<?php echo site_url('settings/socialmedia') ?>" class="link"><?= lang('go_to_settings') ?></a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>