<div class="content-header p-rl30 p-tb20">
  <h1>
    <?= lang('settings_collaboration_team') ?>
  </h1>
  <ol class="breadcrumb">
    <li><a href="<?php echo site_url('settings');?>"><i class="fa fa-wrench"></i> <?= lang('settings') ?></a></li>
    <li class="active"><?= lang('settings_collaboration_team') ?></li>
  </ol>

<div class="main_block content">
    <div class="row">
        <div class="col-xs-12">
            <a href="" class="link invite_user"><?= lang('invite') ?></a>
        </div>
    </div>
    <div class="row invite_block m-t20 invite-block">
        <div class="col-xs-12">
            <div class="input-group form-group">
                <input type="text" class="form-control invite-email" placeholder="<?= lang('enter_email') ?>" name="email" data-role="tagsinput">
                    <span class="input-group-btn">
                        <button class="btn btn-save invite-btn" type="submit"><?= lang('save') ?></button>
                    </span>
            </div>
        </div>
    </div>
    <?php if(empty($users)): ?>
        <div class="row">
            <div class="col-xs-12 m-t10">
                <p class="large-size text_color">
                    <?= lang('no_users') ?>
                </p>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-xs-12 m-t10">
                <table class="responsive-table">
                    <thead class="table_head">
                    <tr>
                        <th><?= lang('username') ?></th>
                        <th><?= lang('email') ?></th>
                        <th><?= lang('actions') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td data-th="<?= lang('username') ?>"><?php echo $user->username; ?></td>
                                <td data-th="<?= lang('email') ?>"><?php echo $user->email; ?></td>
                                <td data-th="<?= lang('actions') ?>">
                                    <ul class="nav admin-users-actions">
                                        <?php if ( !($user->active == $user::STATUS_INVITE)): ?>
                                            <li>
                                                <?php if ($user->active): ?>
                                                    <a class="block-user" href="<?php echo site_url('settings/collaboration/block/' . $user->id); ?>">
                                                        <?= lang('block') ?>
                                                    </a>
                                                <?php else: ?>
                                                    <a class="unblock-user" href="<?php echo site_url('settings/collaboration/unblock/' . $user->id); ?>">
                                                        <?= lang('unblock') ?>
                                                    </a>
                                                <?php endif;?>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <a class="delete-user" href="<?php echo site_url('settings/collaboration/delete/' . $user->id); ?>">
                                                <button type="button" class="btn"><?= lang('remove') ?></button>
                                            </a>
                                        </li>

                                        <?php if ($user->user_active == $user::STATUS_INVITE) :?>
                                            <li>
                                                <a class="reinvite-user" href="<?php echo site_url('settings/collaboration/inviteuser/' . $user->user_id); ?>">
                                                    <button type="button" class="btn"><?= lang('reinvite') ?></button>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif;?>
</div>