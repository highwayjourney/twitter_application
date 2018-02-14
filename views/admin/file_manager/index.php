<?php //ddd($tags); ?>
<style>
/*body {font-family: "lucida grande","Segoe UI",Arial, sans-serif; font-size: 14px;width:1024;padding:1em;margin:0;}*/
th {font-weight: normal; color: #1F75CC; background-color: #F0F9FF; padding:.5em 1em .5em .2em; 
	text-align: left;cursor:pointer;user-select: none;}
th .indicator {margin-left: 6px }
thead {border-top: 1px solid #82CFFA; border-bottom: 1px solid #96C4EA;border-left: 1px solid #E7F2FB;
	border-right: 1px solid #E7F2FB; }
#top {height:52px;}
#mkdir {display:inline-block;float:right;padding-top:16px;}
label { display:block; font-size:11px; color:#555;}
#file_drop_target {width:500px; padding:12px 0; border: 4px dashed #ccc;font-size:12px;color:#ccc;
	text-align: center;float:right;margin-right:20px;}
#file_drop_target.drag_over {border: 4px dashed #96C4EA; color: #96C4EA;}
#upload_progress {padding: 4px 0;}
#upload_progress .error {color:#a00;}
#upload_progress > div { padding:3px 0;}
.no_write #mkdir, .no_write #file_drop_target {display: none}
.progress_track {display:inline-block;width:200px;height:10px;border:1px solid #333;margin: 0 4px 0 10px;}
.progress {background-color: #82CFFA;height:10px; }
footer {font-size:11px; color:#bbbbc5; padding:4em 0 0;text-align: left;}
footer a, footer a:visited {color:#bbbbc5;}
#breadcrumb { padding-top:34px; font-size:15px; color:#aaa;display:inline-block;float:left;}
#folder_actions {width: 50%;float:right;}
a, a:visited { color:#00c; text-decoration: none}
a:hover {text-decoration: underline}
.sort_hide{ display:none;}
table {border-collapse: collapse;width:100%;}
thead {max-width: 1024px}
td { padding:.2em 1em .2em .2em; border-bottom:1px solid #def;height:30px; font-size:12px;white-space: nowrap;}
td.first {font-size:14px;white-space: normal;}
td.empty { color:#777; font-style: italic; text-align: center;padding:3em 0;}
.is_dir .size {color:transparent;font-size:0;}
.is_dir .size:before {content: "--"; font-size:14px;color:#333;}
.is_dir .download{visibility: hidden}
a.delete {display:inline-block;
	background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADtSURBVHjajFC7DkFREJy9iXg0t+EHRKJDJSqRuIVaJT7AF+jR+xuNRiJyS8WlRaHWeOU+kBy7eyKhs8lkJrOzZ3OWzMAD15gxYhB+yzAm0ndez+eYMYLngdkIf2vpSYbCfsNkOx07n8kgWa1UpptNII5VR/M56Nyt6Qq33bbhQsHy6aR0WSyEyEmiCG6vR2ffB65X4HCwYC2e9CTjJGGok4/7Hcjl+ImLBWv1uCRDu3peV5eGQ2C5/P1zq4X9dGpXP+LYhmYz4HbDMQgUosWTnmQoKKf0htVKBZvtFsx6S9bm48ktaV3EXwd/CzAAVjt+gHT5me0AAAAASUVORK5CYII=) no-repeat scroll 0 2px;
	color:#d00;	margin-left: 15px;font-size:11px;padding:0 0 0 13px;
}
.download {
	background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAB2klEQVR4nJ2ST2sTQRiHn5mdmj92t9XmUJIWJGq9NHrRgxQiCtqbl97FqxgaL34CP0FD8Qv07EHEU0Ew6EXEk6ci8Q9JtcXEkHR3k+zujIdUqMkmiANzmJdnHn7vzCuIWbe291tSkvhz1pr+q1L2bBwrRgvFrcZKKinfP9zI2EoKmm7Azstf3V7fXK2Wc3ujvIqzAhglwRJoS2ImQZMEBjgyoDS4hv8QGHA1WICvp9yelsA7ITBTIkwWhGBZ0Iv+MUF+c/cB8PTHt08snb+AGAACZDj8qIN6bSe/uWsBb2qV24/GBLn8yl0plY9AJ9NKeL5ICyEIQkkiZenF5XwBDAZzWItLIIR6LGfk26VVxzltJ2gFw2a0FmQLZ+bcbo/DPbcd+PrDyRb+GqRipbGlZtX92UvzjmUpEGC0JgpC3M9dL+qGz16XsvcmCgCK2/vPtTNzJ1x2kkZIRBSivh8Z2Q4+VkvZy6O8HHvWyGyITvA1qndNpxfguQNkc2CIzM0xNk5QLedCEZm1VKsf2XrAXMNrA2vVcq4ZJ4DhvCSAeSALXASuLBTW129U6oPrT969AK4Bq0AeWARs4BRgieMUEkgDmeO9ANipzDnH//nFB0KgAxwATaAFeID5DQNatLGdaXOWAAAAAElFTkSuQmCC) no-repeat scroll 0px 5px;
	padding:4px 0 4px 20px;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script
  src="https://code.jquery.com/ui/1.8.24/jquery-ui.min.js"
  integrity="sha256-UOoxwEUqhp5BSFFwqzyo2Qp4JLmYYPTHB8l+1yhZij8="
  crossorigin="anonymous"></script>
<script>
(function($){
	$.fn.tablesorter = function() {
		var $table = this;
		this.find('th').click(function() {
			var idx = $(this).index();
			var direction = $(this).hasClass('sort_asc');
			$table.tablesortby(idx,direction);
		});
		return this;
	};
	$.fn.tablesortby = function(idx,direction) {
		var $rows = this.find('tbody tr');
		function elementToVal(a) {
			var $a_elem = $(a).find('td:nth-child('+(idx+1)+')');
			var a_val = $a_elem.attr('data-sort') || $a_elem.text();
			return (a_val == parseInt(a_val) ? parseInt(a_val) : a_val);
		}
		$rows.sort(function(a,b){
			var a_val = elementToVal(a), b_val = elementToVal(b);
			return (a_val > b_val ? 1 : (a_val == b_val ? 0 : -1)) * (direction ? 1 : -1);
		})
		this.find('th').removeClass('sort_asc sort_desc');
		$(this).find('thead th:nth-child('+(idx+1)+')').addClass(direction ? 'sort_desc' : 'sort_asc');
		for(var i =0;i<$rows.length;i++)
			this.append($rows[i]);
		this.settablesortmarkers();
		return this;
	}
	$.fn.retablesort = function() {
		var $e = this.find('thead th.sort_asc, thead th.sort_desc');
		if($e.length)
			this.tablesortby($e.index(), $e.hasClass('sort_desc') );
		
		return this;
	}
	$.fn.settablesortmarkers = function() {
		this.find('thead th span.indicator').remove();
		this.find('thead th.sort_asc').append('<span class="indicator">&darr;<span>');
		this.find('thead th.sort_desc').append('<span class="indicator">&uarr;<span>');
		return this;
	}
})(jQuery);
$(function(){
	$(document).ready(function(){
		list();
		$('.main_block').on('click',  '.name' , function(){
			console.log($(this).data('id'));
			list($(this).data('id'));
		});
	});
	var current_folder;
	var XSRF = (document.cookie.match('(^|; )_sfm_xsrf=([^;]*)')||0)[2];
	var MAX_UPLOAD_SIZE = <?php echo $MAX_UPLOAD_SIZE ?>;
	var availableTags= JSON.parse('<?php echo $tags; ?>');
	//console.log(availableTags);
	var $tbody = $('#list');
	//$(window).bind('hashchange',list).trigger('hashchange');
	$('#table').tablesorter();

	$("#type").change(function(){
		list();
	});
	
	$('.delete').live('click',function(data) {
		$.post(g_settings.base_url + g_settings.current_url + "/req",{'do':'delete',file:$(this).attr('data-file'),xsrf:XSRF},function(response){
			list();
			//location.reload();
		},'json');
		return false;
	});
	$('#mkdir').submit(function(e) {
		var hashval = window.location.hash.substr(1),
			$dir = $(this).find('[name=name]');
		e.preventDefault();
		$dir.val().length && $.post(g_settings.base_url + g_settings.current_url + '/req',{'do':'mkdir',name:$dir.val(),xsrf:XSRF,file:hashval, type: $("#type").val()},function(data){
			list();
		},'json');
		$dir.val('');
		//location.reload();
		return false;
	});
	// file upload stuff
	$('#file_drop_target').bind('dragover',function(){
		$(this).addClass('drag_over');
		return false;
	}).bind('dragend',function(){
		$(this).removeClass('drag_over');
		return false;
	}).bind('drop',function(e){
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		$.each(files,function(k,file) {
			uploadFile(file);
		});
		$(this).removeClass('drag_over');
	});
	$('input[type=file]').change(function(e) {
		e.preventDefault();
		$.each(this.files,function(k,file) {
			uploadFile(file);
		});
	});
	function uploadFile(file) {
		var folder = window.location.hash.substr(1);
		if(file.size > MAX_UPLOAD_SIZE) {
			var $error_row = renderFileSizeErrorRow(file,$("#type").val());
			$('#upload_progress').append($error_row);
			window.setTimeout(function(){$error_row.fadeOut();},5000);
			return false;
		}
		console.log(current_folder);
		var $row = renderFileUploadRow(file,folder);
		$('#upload_progress').append($row);
		var fd = new FormData();
		fd.append('file_data',file);
		fd.append('file',$("#type").val() + "/" + current_folder);
		fd.append('xsrf',XSRF);
		fd.append('do','upload');
		var xhr = new XMLHttpRequest();
		xhr.open('POST', g_settings.base_url + g_settings.current_url + '/req');
		xhr.onload = function() {
			$row.remove();
    		list();
  		};
		xhr.upload.onprogress = function(e){
			if(e.lengthComputable) {
				$row.find('.progress').css('width',(e.loaded/e.total*100 | 0)+'%' );
			}
		};
	    xhr.send(fd);
	}
	function renderFileUploadRow(file,folder) {
		return $row = $('<div/>')
			.append( $('<span class="fileuploadname" />').text( (folder ? folder+'/':'')+file.name))
			.append( $('<div class="progress_track"><div class="progress"></div></div>')  )
			.append( $('<span class="size" />').text(formatFileSize(file.size)) )
	};
	function renderFileSizeErrorRow(file,folder) {
		return $row = $('<div class="error" />')
			.append( $('<span class="fileuploadname" />').text( 'Error: ' + (folder ? folder+'/':'')+file.name))
			.append( $('<span/>').html(' file size - <b>' + formatFileSize(file.size) + '</b>'
				+' exceeds max upload size of <b>' + formatFileSize(MAX_UPLOAD_SIZE) + '</b>')  );
	}
	function list(hashval) {
		//var hashval = window.location.hash.substr(1);    
		//var hashval = "";
		var type = $("#type").val();
		$.get(g_settings.base_url + g_settings.current_url + '/req',{'do':'list','file':hashval, 'type': type},function(data) {
			$tbody.empty();
			//console.log(hashval.length);
			if(hashval){
				// hashval1 = hashval.split("/");
				// console.log(hashval1);
				// var bread = [];
				// bread.push(hashval1[hashval1.length-1]);
				// bread.push(hashval1[hashval1.length-2]);
				// bread = bread.join("/")				
				$('#breadcrumb').empty().html(renderBreadcrumbs(hashval));
			}
			if(data.success) {
				$.each(data.results,function(k,v){
					$tbody.append(renderFileRow(v));
				});
				!data.results.length && $tbody.append('<tr><td class="empty" colspan=5>This folder is empty</td></tr>')
				data.is_writable ? $('body').removeClass('no_write') : $('body').addClass('no_write');
			} else {
				console.warn(data.error.msg);
			}
			$('#table').retablesort();
		},'json');
	}
	function getMaches(keywords, container){
        $.ajax({
            url : g_settings.base_url + g_settings.current_url + '/getMaches',
            type: 'POST',
            dataType: 'json',
            data : { keywords : keywords  }
        }).done(function(response){
        	var content;
        	content = container.html();
        	var test = /\([^)]+\)/;
        	var nada = content.replace(test, "");
        	container.html(nada);
        	container.append("("+response.matches+")");
        });
	}
	function renderFileRow(data) {
		var $link = $('<a class="name" />')
			.attr('href','#')
			.attr('data-id', data.is_dir ?  data.path : './'+data.path)
			.text(data.name);
		var $dl_link = $('<a/>').attr('href',g_settings.base_url + g_settings.current_url + '/req?do=download&file='+encodeURIComponent(data.path))
			.addClass('download').text('download');
		var $delete_link = $('<a href="#" />').attr('data-file',data.path).addClass('delete').text('delete');
		var tags = $('<ul id="tag">');
		if(data.keywords){
			$.each(data.keywords, function(i,s){
				$(tags).append('<li>' + s + '</li>');
			});	
			getMaches(data.keywords, $link);
		}	
		var description = $('<p> ' + data.description + '</p>');
		//$(description).val(data.description);
		var perms = [];
		//console.log(data);
		if(data.is_readable) perms.push('read');
		if(data.is_writable) perms.push('write');
		if(data.is_executable) perms.push('exec');
		var $html = $('<tr />')
			.addClass(data.is_dir ? 'is_dir' : '')
			.append( $('<td class="first" />').append($link) ) 
			.append( $('<td/>').attr('data-sort',data.mtime).text(formatTimestamp(data.mtime)) )
			//.append( $('<td/>').text(perms.join('+')) )
			.append( $('<td/>').append($dl_link).append( data.is_deleteable ? $delete_link : ''))
			.append($('<td/>').append( data.is_base && data.is_dir ? tags : ''))
			.append($('<td/>').append( data.is_base && data.is_dir ? description : ''))
		if(data.is_base && data.is_dir){
		     $(description).editable(g_settings.base_url+g_settings.current_url+'/add_description', {
		         indicator : 'Saving...',
		         tooltip   : 'Click to edit...',
		         type      : 'textarea',
		         rows: 4,
		         cols: 24,
		         submit   : 'OK',
		         name : 'description',
				 submitdata : {folder: data.name, type: $("#type").val()},
		     });			
			$(tags).tagit(
         		{ 
                    availableTags: availableTags,
                    autocomplete: {delay: 0, minLength: 2},
				    afterTagAdded: function(event, ui) {
				    	if(!ui.duringInitialization){
				    		$.ajax({
				    			url: g_settings.base_url + g_settings.current_url + '/add_tags',
				    			type: 'POST',
				    			data: {folder: data.name, tags: $(this).tagit("assignedTags"), type: $("#type").val()}
				    		});
				    		//console.log(event, $link);
				    		getMaches($(this).tagit("assignedTags"), $link);
				    	}
				    },
				    afterTagRemoved: function(event, ui) {
			    		$.ajax({
			    			url: g_settings.base_url + g_settings.current_url + '/add_tags',
			    			type: 'POST',
			    			data: {folder: data.name, tags: $(this).tagit("assignedTags"), type: $("#type").val()}
			    		});
			    		getMaches($(this).tagit("assignedTags"), $link);
				    }				                        
                }
			);
		}
		return $html;
	}
	function renderBreadcrumbs(path) {
		var base = "",
			$html = $('<div/>').append( $('<a href=#>Home</a></div>') );
		$.each(path.split('/'),function(k,v){
			if(v) {
				//console.log(k,v);
				$html.append( $('<span/>').text(' ▸ ') )
					.append( $('<a href="#" class="name"/>').attr('data-id','/'+base+v).text(v) );
				base += v + '/';
				current_folder = v;
			}
		});
		return $html;
	}
	function formatTimestamp(unix_timestamp) {
		var m = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		var d = new Date(unix_timestamp*1000);
		return [m[d.getMonth()],' ',d.getDate(),', ',d.getFullYear()," ",
			(d.getHours() % 12 || 12),":",(d.getMinutes() < 10 ? '0' : '')+d.getMinutes(),
			" ",d.getHours() >= 12 ? 'PM' : 'AM'].join('');
	}
	function formatFileSize(bytes) {
		var s = ['bytes', 'KB','MB','GB','TB','PB','EB'];
		for(var pos = 0;bytes >= 1000; pos++,bytes /= 1024);
		var d = Math.round(bytes*10);
		return pos ? [parseInt(d/10),".",d%10," ",s[pos]].join('') : bytes + ' bytes';
	}
})
</script>
</head>
<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= 'Categories' ?></h1>
        </div>
    </div>
</div>
<div class="main_block">
    <div class="row">
        <div class="col-xs-12 m-t10">
			<div id="top">
	    		<select id="type">
	    			<option value="quote">Quote</option>
	    			<option value="trivia">Trivia</option>
	    			<option value="fact">Fact</option>
	    		</select>			
				<form action="?" method="post" id="mkdir" />
					<label for=dirname>Create New Category</label><input id=dirname type=text name=name value="" />
					<input type="submit" value="create" />
				</form>
				<div id="file_drop_target">
					Drag Files Here To Upload
					<b>or</b>
					<input type="file" multiple />
				</div>
				<div id="breadcrumb">&nbsp;</div>
			</div>

			<div id="upload_progress"></div>
			<table id="table"><thead><tr>
				<th>Name</th>
				<th>Modified</th>
				<th>Actions</th>
				<th>Tags</th>
				<th>Description</th>
			</tr></thead><tbody id="list">

			</tbody></table>
		</div>
	</div>
</div>