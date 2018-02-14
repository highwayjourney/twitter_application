<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= lang('api_keys') ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-2 m-t10">
            <form action="<?php echo site_url('admin/template_update/update'); ?>" method="POST">
                <select name="plan" class="form-control">                
                    <?php foreach($plans as $plan): ?>
                        <option value="<?php echo $plan; ?>"><?php echo $plan; ?></option>                       
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="btn btn-save" value="update" />
            </form>
        </div>
    </div>
</div>