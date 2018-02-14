<div class="main sign_in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="sign_block ">
                    <div class="login-block reset-password clearfix">
                        <form action="<?php echo site_url(); ?>auth/invite" method="POST">
                            <h2 class="sign_title text-center"><?= lang('complete_registration') ?></h2>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" type="password" name="password" placeholder="<?= lang('sign_up_password') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" type="password" name="confirm" placeholder="<?= lang('sign_up_confirm_password') ?>">
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="code" value="<?php echo $code;?>">
                            <div class="row custom-form">
                                <div class="col-xs-12">
                                    <div class="pull-right">
                                        <input type="submit" class="btn-save" value="<?= lang('forgot_password_submit_btn') ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
