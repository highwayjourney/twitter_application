<div class="panel panel-red m-t10 p-10">
    <div class="panel-heading">                            
        <h4 class="text_color strong-size">Engagement Lists</h4>
    </div>
    <div class="panel-body">    
      <div class="row">
		<div class="demo-xs-btn-group clearfix">
      <ul class="todo-list">
			<?php foreach ($current_lists as  $current_list): ?>
        <?php if($current_list['show']): ?>
          <li class="list" data-toggle="tooltip" title="click to disable" data-id="<?php echo $current_list['id']; ?>">
            <span></span><?php echo $current_list['name']?>
          </li>
        <?php endif; ?>
			<?php endforeach; ?>		
      </ul>	     
    </div>
  </div>
</div>
</div>