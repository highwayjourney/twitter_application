<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <form class="control user-dropdown" id="add-user" method="POST" action="<?php echo site_url('admin/manage_accounts/adduser');?>">
                <?php echo form_dropdown('user', $freeusers, [], 'class="chosen-select"'); ?>
                <input type="hidden" name="manager" value="<?php echo($managerAccount);?>">
                <input type="submit" class="bind-user-account btn" value="<?= lang('add') ?>">
            </form>
        </div>
    </div>
</div>
<div class="main_block">
    <?php echo $this->template->block('users', 'admin/manage_accounts/blocks/users.php', array(
        'users' => $users,
        'c_user' => $c_user,
        'group' => $group,
        'managerAccount' => $managerAccount
    ));
    ?>
</div>