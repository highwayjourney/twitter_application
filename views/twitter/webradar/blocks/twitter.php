
<?php if ($mention->actions) :?>

    <a class="reply" data-id="<?php echo $mention->original_id; ?>" title="Reply"
        href="<?php echo site_url('twitter/activity/tweet'); ?>"><i class="fa fa-reply"></i>
    </a>
    <?php $retwittedClass = $mention->other_field('retweeted') ? 'retweet_yet' : ''; ?>
    <?php $retwittedLink = $retwittedClass
        ? site_url('twitter/activity/unretweet/'. $mention->original_id) 
        : site_url('twitter/activity/retweet/'. $mention->original_id);
    ?>
    <a class="retweet <?php echo $retwittedClass;?>" title="<?php echo ($retwittedClass) ? 'Unretweet' : 'Retweet';?>"
        href="<?php echo $retwittedLink; ?>"><i class="fa fa-share"></i>
    </a>
    <?php $favoritedClass = $mention->other_field('favorited') ? 'favorite_yet' : ''; ?>
    <?php $favoritedLink = $favoritedClass
        ? site_url('twitter/activity/unfavorite/'. $mention->original_id) 
        : site_url('twitter/activity/favorite/'. $mention->original_id);
    ?>
    <a class="favorite <?php echo $favoritedClass; ?>" title="<?php echo ($favoritedClass) ? 'Unfavorite' : 'Favorite';?>"
        href="<?php echo $favoritedLink; ?>"><i class="fa fa-star"></i>
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

<?php endif;?>



<i class="fa fa-twitter-square i-twitter"></i>
<?php if($picture = $mention->other_field('picture')): ?>
    <img src="<?php echo $picture;?>" class="web_radar_image" alt="">
<?php endif;?>
<p class="web_radar_text">
    <?php echo $mention->parse_message_links(); ?>
</p>

