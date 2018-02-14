
<i class="fa fa-instagram i-instagram"></i>
<img src="<?php echo $mention->other_field('low_resolution');?>" class="web_radar_image" alt="">
<p class="web_radar_text">
    <?php if($mention->message): ?>
        <?php echo make_links_clicable($mention->message); ?>
    <?php endif; ?>
</p>
<?php if ($mention->actions) :?>
    <div class="clearfix social_like">
        <?php $isLiked = $mention->other_field('i_like', 0) > 0;?>
        <?php   $likeUrl = $isLiked
                    ? site_url('twitter/activity/instagramdislike')
                    : site_url('twitter/activity/instagramlike');
            $likeClass = $isLiked ? 'dislike-button' : 'like-button';
        ?>
        <a href="javascript: void(0)" class="<?php echo $likeClass ?>"
           data-url="<?php echo $likeUrl; ?>"
           data-id="<?php echo $mention->original_id; ?>"><?php echo $isLiked ? '<i class="ti-thumb-down"></i>' : '<i class="ti-thumb-up"></i>'; ?></a>
        <?php $commentsUrl = site_url('twitter/activity/instagram_get_comments/'
            . $mention->original_id);
        ?>
        <a class="m-l10 show_comments" href="javascript: void(0)" data-type="not_loaded"
           data-url="<?php echo $commentsUrl ?>">
            <i class="ti-comment-alt"></i>
        </a>
        <?php if (!$mention->influencer) :?>
            <a href="javascript: void(0)" class="add-influencer"
               data-creator_id="<?php echo $mention->creator_id; ?>"
               data-social="<?php echo $mention->social; ?>"
               style="display:none"
                >
                <?= lang('add_to_influencers') ?>
            </a>
        <?php endif;?>
    </div>
<?php endif;?>
<?php echo $this->template->block(
    '_comments',
    'twitter/activity/blocks/_instagram_comments',
    array(
        '_post' => array('id' => $mention->original_id),
        'picture' => $mention->user_image,
    )
);
?>