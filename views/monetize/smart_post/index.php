<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= 'Smart Posts' ?></h1>
            <div class="row">
                <div class="col-xs-12">
                    <?php //echo $this->template->block('app_breadcrumbs', 'layouts/block/application/breadcrumbs', array('menu' => 'customer.main')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main-block">
  <div class="row m-l10" <?php echo empty($campana)?'style="height:560px"':''; ?>>
    <div class="col-lg-3"> 
      <h4>Please Select a Campaign</h4> 
      <form method="post" id="form">
      <select class="form-control quantity" id="campaign" name="campaign">
        <option value="">------------None-------------</option>
        <?php foreach ($campaigns as $campaign): ?>
          <option <?php echo $campana == $campaign->id?'selected':''; ?> value="<?php echo $campaign->id; ?>"><?php echo $campaign->name; ?> </option>
        <?php endforeach; ?>
      </select>
      </form>     

  <?php if(empty($campaigns->to_array()['id'])): ?>

    <div class="alert alert-warning alert-transparent no-margin m-t10"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> <strong>Warning!</strong> It seems like you haven't created any campaign yet, click <a href="<?php echo site_url('social/automatic'); ?>">here</a>.</div>    
  
  <?php endif; ?>
  </div>                 
  </div>        
  <?php if(!empty($posts)): ?>
  <div class="row">
  <?php foreach ($posts as $key => $post): ?>
    <?php $data = unserialize($post->campaign_data);
      $colsm= $data['source'] == 'giphy'?6:4;
     ?>
    <div class="col-xs-12 col-md-<?php echo $colsm;  ?>">
        <div class="box">
            <div class="box-icon tool-tiper" data-id="<?php echo $key; ?>" data-type="postNow" title="Click to post now">
                <span class="fa fa-4x fa-<?php echo $data['source']; ?>"></span>  
            </div>
            <span class="badge red-bg tool-tiper" data-type="delete" data-id="<?php echo $key; ?>"><a href="#" title="Click to delete from Queue">Delete</a></span>
              <?php if ($data['source'] == 'youtube'): ?>
                  <iframe id="player" type="text/html" src="<?php echo str_replace('?v=', '/', str_replace('watch', 'embed', $post->description)); ?>" frameborder="0"></iframe>
              <?php endif; ?>    
              <?php if ($data['source'] == 'giphy'): ?>                            
                <iframe src="<?php echo $data['embed_url'] ?>" style="display:block" width="480" height="178" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>              
              <?php endif; ?>            
              <?php if ($data['source'] == 'facebook' || $data['source'] == 'amazon' || $data['source'] == 'ebay'): ?>                            
                <img class="img-responsive center-block" src="<?php echo $data['media_url'] ?> " />
              <?php endif; ?>
            <div class="info">
              <!-- <textarea style="width:100%" rows="4"><?php //echo $data['description']." ".$data['url']; ?></textarea> -->
              <p><?php echo $post->description." ".$post->url; ?></p>
              <span class="badge blue-bg">Post on: <?php echo date("D M j G:i:s Y T", $post->schedule_date) ?></span>
            </div>
        </div>
    </div>
  <?php endforeach; ?>
  </div>
  <?php elseif(!empty($campana) && empty($posts)): ?>
  <div class="row">
    <div class="col-xs-5">
      <div class="alert alert-warning alert-transparent no-margin m-t10 m-l20">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
        This campaign haven't returned any post yet, please try again later
      </div>    
    </div>    
  </div>
  <?php endif; ?> 
</div>