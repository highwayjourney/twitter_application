<!-- <div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?php echo $trivia['category']; ?></h1>
        </div>
    </div>
</div> -->
<div class="main_block">
    <div class="row" style="margin-top:10px">
        <div class="col-xs-6">
        	<img src="<?php echo $image; ?>" class="img-responsive"/>
                      <?php if(isset($company)): ?>
                        <div class="carousel-caption">
                          <p><?php echo $company; ?> </p>  
                        </div>                         
                      <?php endif; ?>           
        </div>
        <div class="col-xs-6">
        	<h3 class="page-title result" style="font-weight: 600;margin-bottom: 10px;">Click on your answer to see if you're right...</h3>
            <div class="list-group">
        		<?php foreach ($options as  $option): ?>
        			<a href="#" class="list-group-item list-group-item-action" data-option="<?php echo $option; ?>"><?php echo $option; ?></a>
        		<?php endforeach; ?>
        	</div>
            <button class="btn btn-save" style="display:none" id="click_continue">Continue</button>            
        </div>        
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        var mode = "<?php echo $click_continue; ?>";
		$(".list-group-item").click(function(){
			if($(this).data('option') == g_settings.correct){
                $(this).addClass('active');
                if(mode == "true"){
                    //showFlashSuccess('Correct!! Click on button to continue');
                    $('.result').css('color', 'green');
                    $('.result').fadeOut();
                    $('.result').html('Congratulations!  You got it right!');                    
                    $('.result').fadeIn();
                    $("#click_continue").css('background-color', 'green');
                    $("#click_continue").fadeIn();
                } else {
                    $('.result').css('color', 'green');
                    $('.result').fadeOut();
                    $('.result').html('Congratulations!  You got it right!');                    
                    $('.result').fadeIn();
                    showFlashSuccess('Redirecting...please wait');  
                    setTimeout(function(){ window.location.replace(g_settings.redirect) }, 5000);               
                }
			} else {
				$('.list-group').children().each(function(){
					if($(this).data('option') == g_settings.correct){
						$(this).addClass('active');
					}
				});
                if(mode == "true"){
                    $('.result').css('color', 'red');
                    $('.result').fadeOut();
                    $('.result').html('Sorry...wrong answer but we highlighted the correct answer for you');                    
                    $('.result').fadeIn();
                    $("#click_continue").css('background-color', 'green');
                    $("#click_continue").fadeIn();
                    //$("#click_continue").removeClass('hidden');
                } else { 
                    $('.result').css('color', 'red');
                    $('.result').fadeOut();
                    $('.result').html('Sorry...wrong answer but we highlighted the correct answer for you');                    
                    $('.result').fadeIn();
                    showFlashSuccess('Redirecting...please wait');  
                    setTimeout(function(){ window.location.replace(g_settings.redirect) }, 5000);                
                }
			}
		});
        $("#click_continue").click(function(){
             window.location.replace(g_settings.redirect);
        });
	});
</script>