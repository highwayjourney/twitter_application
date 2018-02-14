<?php
/**
 * @var array $feeds
 */
?>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title">Create Social Media</h1>
        </div>
    </div>
</div>
<div class="main_block">
    <?php echo $this->template->block('_nav', 'twitter/create/blocks/_navigation');?>
    <div id="create-social">
        <?php echo $this->template->block('_post_update', 'twitter/create/blocks/_post_rss', array('feeds' => $feeds, 'content' => $content)); ?>

    </div>
</div>