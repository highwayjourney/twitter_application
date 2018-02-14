<?php //ddd($campaign, $items); ?>
<!-- <div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title">Preview</h1>
        </div>
    </div>
</div> -->
<div class="main_block">
    <div class="row" style="margin-top:10px">
        <div class="col-xs-2"></div>
        <div class="col-xs-8">
            <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="10000000000">

              <ol class="carousel-indicators">
                <?php foreach ($items as $key => $item): ?>
                    <li data-target="#myCarousel" data-slide-to="<?php echo $key; ?>" class="<?php echo $key==0?'active':''; ?>"></li>
                <?php endforeach; ?>
              </ol>

              <div class="carousel-inner" role="listbox">
                <?php foreach ($items as $key => $item): ?>
                    <div class="item <?php echo $key==0?'active':''; ?>">
                    <?php //$item = $item->to_array(); ?>
                      <img src="<?php echo site_url('public/uploads/'.$campaign['user_id'].'/'.$item['final_image_thumb']); ?>" alt="">
                      <?php if(isset($company)): ?>
                        <div class="carousel-caption">
                          <p style="font-size: 17px;"><?php echo $company; ?> </p>  
                        </div>                         
                      <?php endif; ?>
                      <span class="download">
                        <a id="download" download href="<?php echo site_url('public/uploads/'.$campaign['user_id'].'/'.$item['final_image_thumb']); ?>" data-toggle="tooltip" title="Download Image"><i class="fa fa-download close_block"></i></a>
                      </span>                  
                    </div>
                <?php endforeach; ?>
              </div>

              <!-- Left and right controls -->
              <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div> 
        </div>
        <div class="col-xs-2"></div>
    </div>
</div>
<style>
.download {
    position: absolute;
    top: 10px;
    right: 85px;
    z-index: 100000000;
}
#download i {
    font-size: 30px;
    background-color: rgba(0, 0, 0, 0.69);
    color: white;
    padding: 3px;
    border-radius: 8px;
}
.carousel-inner img {
  margin: auto;
}
</style>