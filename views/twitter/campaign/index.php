<div class="p-rl30 p-tb20">
    <div class="row">
        <div class="col-xs-12">
            <h1 class="page-title"><?= 'Campaigns' ?></h1>
        </div>
    </div>
</div>
<!-- <div class="main_block">
    <div class="row">
        <div class="col-sm-12">
            <div class="block_content m-b20">
                <div class="row">
                    <div class="col-xs-12 clearfix "> -->

<div class="main_block content" id="ajax-container">
    <?php if($is_user_set_timezone): ?>
        <div id="main-view" class="row" ></div>

        <script type="text/template" id="main-layout"/>
            <div id="main-region" class="col-xs-12"></div>
        </script>
    <?php else:?>
        <div class="alert alert-warning alert-transparent no-margin m-t10">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> 
            <strong>Warning!</strong> 
            It seems like you haven't set your timezone, click <a href="<?php echo site_url('settings/personal/'); ?>">here</a>.
        </div>    
    <?php endif;?>
    <script type="text/template" id="campaing-layout">
        <button id="add" class="btn small">Add New</button>
        <div class="row">
            <div id="new-campaign-region" class="col-xs-12 m-t10">
            </div>
        </div>

              
        <div class="row">
            <div id="campaign-region" class="col-xs-12 m-t10">
            </div>
        </div>
    </script>

    <script id="table-template" type="text/template">
      <div class="box-body table-responsive no-padding">
          <table id="campaign-table" class="table table-hover">
            <thead class="table_head">
              <tr>
                <th>Name</th>
                <th>Sources</th>
                <th>Keywords</th>
                <th>Urls</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Delete</th> 
                <th>Save</th>       
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
    </script>

    <script id="add-campaign-template" type="text/template">
      <div class="box-body table-responsive no-padding">
      <table class="table table-hover">
        <thead class="table_head">
          <tr>
            <th>Name</th>
            <th>Sources</th>
            <th>Keywords</th>
            <th>Urls</th>
            <th>Priority</th>    
          </tr>
        </thead>
        <tbody>
            <td><input type="text" id="name" class="form-control"/>
            <td>
                <select class="form-control" multiple name="sources" id="sources">
                    <% $(postSources).each(function(index, value){ %>
                        <option value="<%= value %>"> <%= value %></option>
                    <% }) %>
                </select>
            </td>       
            <td><ul id="keywords"></ul></td>
            <td><ul id="urls"></ul></td>
            <td>
                <select class="form-control" name="priority" id="priority">
                    <% $(prioSources).each(function(index, value){ %>
                        <option value="<%= value %>"> <%= value %></option>
                    <% }) %>
                </select>
            </td>
            <td><a class="save-new link"><i class="fa fa-save"></i></a></td>              
        </tbody>
      </table>
        </div>
    </script>


    <script type="text/template" id="campaing-row-view">
        <td><input type="text" id="campaign_name<%= id %>" value="<%= name %>" class="form-control" placeholder="Campaign Name"/></td>
        <td>
            <select class="form-control" multiple name="sources<%= id %>" id="sources<%= id %>">
                <% $(postSources).each(function(index, value){ %>
                    <option value="<%= value %>" <% if($.inArray( value, JSON.parse(sources) ) > -1){%> selected <%}else{}%> ><%= value %> </option>
                <% }) %>
            </select>
        </td>   
        <td>
            <ul class="keywords" id="keywords<%= id %>">
                <% if (typeof keywords !== "undefined") { %>
                    <% $(JSON.parse(keywords)).each(function(index, value){ %>
                        <li><%= value %></li>
                    <% }) %>
                <% } %>
            </ul>
        </td>

        <td>
            <ul class="urls" id="urls<%= id %>">
                <% if (typeof url !== "undefined") { %>
                    <% $(JSON.parse(url)).each(function(index, value){ %>
                        <li><%= value %></li>
                    <% }) %>
                <% } %>
            </ul>
        </td>        

        <td>
           <select style="width: 60px;" id="priority<%= id %>" class="form-control">
                <% $(prioSources).each(function(index, value){ %>
                    <option value="<%= value %>" <% if( value == priority){%> selected <%}else{}%> > <%= value %> </option>
                <% }) %>
            </select>
        <td>
            <input type="checkbox" id="status<%= id %>"<% if(status == 'enabled'){%> checked <%}else{}%> class="enable-toogle" data-onstyle="success" data-offstyle="warning" data-toggle="toggle" data-on="On" data-off="Off">            
        </td>
        <td>
            <button class="btn btn-default btn-rounded btn-transparent btn-sm remove-campaign" data-id="<%= id %>">
                <i class="fa fa-remove m-r5"></i>
            </button>
        </td>        
        <td>
            <button class="btn btn-default btn-rounded btn-transparent btn-sm save-campaign" data-id="<%= id %>">
                <i class="fa fa-save m-r5"></i>
            </button>
        </td>        
    </script>

    <script type="text/template" id="no-camps-added-template">
        <p>No Campaigns added yet</p>
    </script>

    <script type="text/javascript">

    </script>    

   