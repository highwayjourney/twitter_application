<?php
/**
 * @var array $timezones
 * @var string $current_timezone
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= 'Twitter API keys' ?></h1>
        </div>
    </div>
</div>   

<div class="main_block content">
    <div class="row">
        <div class="col-xs-5">
            <div class="alert alert-warning alert-transparent no-margin">
                        <h4>Twitter Callback URL</h4>

                        <p>http://app.autosoci.com/settings/socialmedia/twitter_callback</p>
            </div>
        </div>
    </div>
    <form action="<?php echo site_url('settings/api_settings'); ?>" method="POST">
        <h4><?= lang('twitter_key') ?> </h4>
        <div class="row">
            <?php //echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
            <div class="col-md-10 col-lg-8">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= 'Consumer Key' ?> *</p>
                        <div class="form-group">
                            <input class="form-control" name="twitter[consumer_key]" value="<?php echo isset($twitter['consumer_key'])?$twitter['consumer_key']:''; ?>" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= 'Consumer Secret' ?> *</p>
                        <div class="form-group">
                            <input class="form-control" name="twitter[consumer_secret]" value="<?php echo isset($twitter['consumer_secret'])?$twitter['consumer_secret']:''; ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-lg-8">
                <div class="b-Top p-tb20 m-t20">
                    <button class="btn btn-save pull-right"><?= lang('save') ?></button>
                </div>
            </div>
        </div>
    </form>
</div>