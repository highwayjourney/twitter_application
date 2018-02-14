<div class="panel panel-red m-t10 p-10">
    <div class="panel-heading">                            
        <h4 class="text_color strong-size">Engagement Lists</h4>
    </div>
    <div class="panel-body">    
      <div class="row">
		<div class="demo-xs-btn-group clearfix">
			<?php foreach ($current_lists as $key=> $current_list): ?>
        <ul class="todo-list">
          <li class="list" data-id="<?php echo $key; ?>">
            <span></span><?php echo $current_list['name']?>
          </li>
        </ul>
			<?php endforeach; ?>			     
    </div>
  </div>
</div>
</div>