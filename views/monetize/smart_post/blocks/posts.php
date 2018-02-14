<div class="container">
    <?php if(!empty($posts)): ?>
    <div class="page-header text-center">
        <h1 id="timeline">Timeline 2.1</h1>
    </div>
      <div class="row">
      <?php foreach ($posts as $key => $post): ?>
        <?php $data = unserialize($post->post_data); ?>

        <div class="col-xs-12 col-md-4 col-sm-6">
            <div class="box">
                <div class="box-icon" data-id="<?php echo $key; ?>">
                    <span class="fa fa-4x fa-<?php echo $post->source; ?>"></span>  
                </div>
                  <?php if ($post->source != 'youtube'): ?>
                      <img class="img-responsive center-block" src="<?php echo site_url('public').'/uploads/'.$post->user_id.'/'.$data['image_name']; ?> " />
                  <?php else: ?>
                    <iframe id="player" type="text/html" src="<?php echo str_replace('?v=', '/', str_replace('watch', 'embed', $data['description'])); ?>" frameborder="0"></iframe>
                  <?php endif; ?>                                
                <div class="info">
                  <!-- <textarea style="width:100%" rows="4"><?php //echo $data['description']." ".$data['url']; ?></textarea> -->
                  <p><?php echo $data['description']." ".$data['url']; ?></p>
                </div>
            </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-4">
          <h3>There are not Suggestions</h3>
        </div>
    </div>
  <?php endif; ?>
</div>