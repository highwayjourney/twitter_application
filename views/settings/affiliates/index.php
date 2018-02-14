<?php
/**
 * @var array $timezones
 * @var string $current_timezone
 */
?>
<div class="p-rl30 p-tb20 top-bar">
    <h1 class="page-title"><?= lang('settings_affiliate') ?></h1>
    <?php //echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
</div>

<div class="main_block content">
    <form action="<?php echo site_url('settings/affiliates'); ?>" method="POST">
        <h4><?= lang('amazon_credentials') ?> </h4>
        <div class="row">
            <?php //echo $this->template->block('_info_block', 'blocks/settings/info_block'); ?>
            <div class="col-md-10 col-lg-8">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('amazon_associate_tag') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" name="amazon[associate_tag]" value="<?php echo isset($amazon_associate_tag)?$amazon_associate_tag:''; ?>" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('amazon_public_key') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" name="amazon[public_key]" value="<?php echo isset($amazon_public_key)?$amazon_public_key:''; ?>" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('amazon_private_key') ?> *</p>
                        <div class="form-group">
                            <input class="form-control" value="<?php echo isset($amazon_private_key)?$amazon_private_key:''; ?>" name="amazon[private_key]"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <p class="text_color strong-size"><?= lang('amazon_country') ?> *</p>
                        <div class="form-group">
                            <select name="amazon[country]" class="chosen-select" id="amazon_country">
                                <?php foreach($amazon_countries as $key => $value): ?>
                                    <option 
                                        value="<?php echo $key;?>"  
                                            <?php echo $key==$amazon_country?'selected':'';?>>
                                            <?php echo $value;?>
                                    </option> 
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10 col-lg-8">
            <div class="row">
                <div class="col-sm-6">
                    <p class="text_color strong-size"><?= lang('ebay_campaign_id') ?> *</p>
                    <div class="form-group">
                        <input class="form-control" value="<?php echo isset($ebay_campaign_id)?$ebay_campaign_id:''; ?>" name="ebay[campaign_id]"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <p class="text_color strong-size"><?= lang('ebay_credentials') ?> *</p>
                    <div class="form-group">
                        <select name="ebay[country]" class="chosen-select" id="">
                            <?php foreach($ebay_countries as $key => $value): ?>
                                <option 
                                    value="<?php echo $key;?>"  
                                    <?php echo $key==$ebay_country?'selected':'';?>>
                                    <?php echo $value;?>
                                </option> 
                            <?php endforeach; ?>
                        </select>
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