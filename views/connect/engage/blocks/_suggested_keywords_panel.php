<div class="panel panel-pink m-t10 p-10">
    <div class="panel-heading">                            
        <h4 class="text_color strong-size">Suggested Keywords</h4>
    </div>
    <div class="panel-body">    
      <div class="row">
		<div class="demo-xs-btn-group clearfix">
			<?php foreach ($trends as $key=> $trend): ?>
				<a href="#" style="margin:2px" data-toggle="tooltip" title="pick keyword" class="<?php echo $classes[$key] ?> trend"><?php echo $trend->name ?></a> 
			<?php endforeach; ?>			     
      </div>
  </div>
</div>
</div>