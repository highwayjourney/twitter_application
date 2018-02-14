
  <?php if(!empty($posts)): ?>
  <div class="page-header text-center">
      <h1 id="timeline">Timeline 2.1</h1>
  </div>
    <div class="row">
<?php foreach ($posts as $key => $post): ?>
  <?php $data = unserialize($post->campaign_data); ?>
  <div class="col-xs-12 col-md-4 col-sm-6">
      <div class="box">
          <div class="box-icon" data-id="<?php echo $key; ?>">
              <span class="fa fa-4x fa-<?php echo $data['source']; ?>"></span>  
          </div>
            <?php if ($data['source'] == 'youtube'): ?>
                <iframe id="player" type="text/html" src="<?php echo str_replace('?v=', '/', str_replace('watch', 'embed', $post->description)); ?>" frameborder="0"></iframe>
            <?php endif; ?>    
            <?php if ($data['source'] == 'giphy'): ?>                            
              <iframe src="<?php echo $data['embed_url'] ?>" width="480" height="178" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>              
            <?php endif; ?>            
            <?php if ($data['source'] == 'facebook'): ?>                            
              <img class="img-responsive center-block" src="<?php echo $data['media_url'] ?> " />
            <?php endif; ?>
          <div class="info">
            <!-- <textarea style="width:100%" rows="4"><?php //echo $data['description']." ".$data['url']; ?></textarea> -->
            <p><?php echo $post->description." ".$post->url; ?></p>
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
