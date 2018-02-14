
<form action="<?php echo site_url('auth/login'); ?>" method="POST">

    <div id="box" class="animated bounceIn">
        <div id="top_header">
            <h3><img src="<?php echo site_url('public/images/autosoci-logo-dark.png'); ?>" alt="autosoci-logo"></h3>
            <h5>Sign in to continue to your<br>AutoSoci Account.</h5>
        </div>
        <div id="inputs">
            <div class="form-control">
                <input type="text" name="identity" placeholder="Email">
                <i class="icon-email"></i>
            </div>
            <div class="form-control">
                <input type="password" name="password" placeholder="Password"> 
                <i class="icon-lock2"></i>
            </div><input type="submit" value="Sign In">
        </div>
        <div id="bottom">
            <div class="squared-check">
                <input type="checkbox" value="None" id="remember" name="remember" checked="">
                <label for="remember"></label>
                <div class="cb-label">Remember</div>
            </div>
            <a class="right_a" href="<?php echo site_url(); ?>auth/forgot_password">Forgot password?</a>
        </div>
</form>