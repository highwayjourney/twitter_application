<!--<div class="sidebar span3 pos-rlv clearfix">

	<div class="sidebar-layer"></div>
	<div class="sidebar-layer x"></div>
	
    <div class="pull-left pos-rlv zx-10">
        <?php /*echo menu_render('customer.main');*/?>
    </div>
</div>-->
<div class="vertical-nav sidebar active">
<!--     <div class="logo" style="text-align: center;">
    	<a href="<?php echo site_url('dashboard'); ?>">
        	<img src="<?php echo site_url('public/images/logo-f.png'); ?>" style="max-width:190px"/>
        </a>
    </div>	 -->
    <div class="user-info">
    	<div class="user-img">
    		<img src="<?php echo $user_image?$user_image:site_url('public/images/t4.png'); ?>" alt="User Info"> 
    		<!-- <span class="likes-info">26</span> -->
    	</div>
    	<h5 class="user-name-o"><?php echo !empty($c_user->first_name)?$c_user->first_name:$c_user->username; ?></h5>
    	<!-- <p class="profile-complete">Profile Complete - 78%</p> -->
    </div>    
    <?php echo menu_render('customer.main');?>
</div>