<?php
/**
 * @var array $socials
 */
//For development only
$sociales = array(
        0 => 'twitter'
    );
if(isset($social_post)) {
    $activeSocials = unserialize($social_post->post_to_socials);
} else {
    $activeSocials = $socials;
}
$inactiveSocials = Social_post::$socials;
foreach($inactiveSocials as $key => $inactiveSocial) {
    if(in_array($inactiveSocial, $activeSocials)) {
        unset($inactiveSocials[$key]);
    }
}
//ddd($sociales, $inactiveSocials);
?>
<p class="text_color strong-size hidden"><?= lang('post_to') ?>
    <span class="custom-form is-relative top-5 p-l10">
        <?php foreach($sociales as $social) : ?>
            <?php if($social == "twitter") : ?>
                <label class="cb-checkbox regRoboto m-r10">
                    <input
                        type="checkbox"
                        name="post_to_socials[]"
                        value="<?= $social ?>"
                        <?= (in_array($social, $activeSocials)) ? 'checked="checked"' : ''; ?>
                        >
                    <cite class="ti-<?= $social ?>"></cite>
                    <?= ucfirst($social) ?>
                </label>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php foreach($inactiveSocials as $inactiveSocial) : ?>
            <?php if($inactiveSocial == "twitter") : ?>
                <p>
                    Please add <a href="<?= site_url('settings/socialmedia/'); ?>"><?= ucfirst($inactiveSocial) ?> account.</a>
                </p>
            <?php endif; ?>
        <?php endforeach; ?>
    </span>
</p>