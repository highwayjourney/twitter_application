<?php
/**
 * @var array $available_languages
 * @var string $default_language
 * @var array $dropdownManagerUsers
 * @var integer $currentId
 * @var User $c_user
 */
$jvzoo_products = array(
    1 => "#",  //AutoPixar PRO 
    2 => "http://localhost/max", //AutoPixar LITE
    3 => "http://localhost/ecommerce/", //AutoPixar MAX
    4 => "http://localhost/elite/",  //AutoPixar MAX LITE
    5 => "http://localhost/special/"
    );
    unset($planes[5]);
    //ddd($plans);
?>
<div class="navbar navbar-fixed-top header">
    <div style="float:left">
            <img src="<?php echo site_url('public/images/logo-f.png'); ?>" style="max-width:210px">
            <a href="https://localhost/claim" target="_blank page"><button class="btn" style="background-color:#FD3A3E">ATTENTION</button></a>
    </div>
    <button class="btn btn-menu">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>   
    <div class="head_nav">
        <?php if ($managerCode = $this->ion_auth->getManagerCode() && isset($dropdownManagerUsers)) :?>
            <?php echo $this->template->block('manager_menu', 'manager/blocks/headermenu', array('users'=> $dropdownManagerUsers, 'currentId' => $currentId)); ?>
        <?php endif;?>
        <ul class="user-nav">
            <li class="user-nav_item">
                <a class="user-nav_link" target="_blank" href="http://localhost/training/">
                    User Guide
                </a>
            </li>
            <li class="user-nav_item">
                <a class="user-nav_link" href="http://thinkbigsupport.com/">
                    Support
                </a>
            </li>
            <li class="user-nav_item">
              <a href="#" class="user-nav_link nav_link">
                <i class="fa fa-level-up"></i> Upgrade
              </a>
                  <ul class="sub_menu clearfix">
                        <?php foreach ($planes as $key => $value): ?>
                        <?php if($key >= $current_plan): ?>
                            <li class="user-nav_item">
                                <a class="user-nav_link" href="<?php echo $jvzoo_products[$key]; ?>">
                                    <i class="ion ion-ios-people info"></i> <?php echo $value; ?> <?php echo $key == $current_plan?'<span class="label label-danger"> Current</span>':''; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php endforeach; ?>                    
                   </ul>
            </li>
            <li class="user-nav_item">
                <a class="user-nav_link nav_link">
                    <?= !empty(trim($c_user->user_name)) ? $c_user->user_name : lang('user');?>
                </a>
                <ul class="sub_menu clearfix">
                    <li class="user-nav_item">
                        <a class="user-nav_link" href="<?php echo site_url('auth/logout'); ?>"><?= lang('logout') ?></a>
                    </li>
                </ul>
            </li>
            <?php if(isset($active_profile) && isset($profiles) && $profiles->count() > 1) : ?>
                <li class="user-nav_item">
                    <select id="user_active_profile" class="chosen-select m-t7">
                    <?php foreach($profiles as $profile) : ?>
                        <option
                            value="<?= $profile->id ?>"
                            <?= ($profile->id == $active_profile->id) ? 'selected="selected"' : '' ?>
                        >
                            <?= $profile->name ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </li>
            <?php endif; ?>
<!--             <li class="user-nav_item">
                <select id="user_active_language" class="chosen-select m-t7">
                    <?php foreach($available_languages as $key => $available_language) : ?>
                        <option
                            value="<?= $key ?>"
                            <?= ($available_language == $default_language) ? 'selected="selected"' : '' ?>
                            >
                            <?= ucfirst($available_language) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li> -->
        </ul>
    </div>    
    <i class="fa fa-qrcode collapse-button"></i>
</div>