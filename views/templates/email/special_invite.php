<div>
    You have been invited to special subscription plan of <?php echo $sitename;?>:</div><br/>
    <h2><?php echo $plan->name; ?></h2>
    <?php $features = $plan->getAttachedFeatures();?>
    <ul>
    <?php foreach ($features as $feature) :?>
        <li><?php echo $feature->getFeature()->name;?></li>
    <?php endforeach; ?>
    </ul><br/>
    Please Follow this link <a href="<?php echo $register_link;?>"> to complete registration</a>.
</div>