<!DOCTYPE html>
<html>
	<head>
    	{{tpl_page_meta_header}}
	</head>
	
	<body class="theme-{{tvar_global_color}}">
	
		<div class="panel-overlay"></div>
 
    	<div class="panel panel-left panel-cover" onClick="myApp.closePanel()">
       		{{tpl_page_leftPanel}}
    	</div>
 
    	<div class="panel panel-right panel-cover" onClick="myApp.closePanel()">
       		{{tpl_page_rightPanel}}
    	</div>
	
	  <div class="views"  >
		<div class="view view-main" >
		  <div class="pages toolbar-through">
			
			<div data-page="{{tpl_pvar_name}}" class="page {{tvar_global_notoolbar}} theme-{{tvar_global_color}} with-subnavbar" style="background-image: url({{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/nodeBackground.png);">

			  {{tpl_page_subbar}}
			  
			  <div class="toolbar theme-{{tvar_global_color}}">
				<div class="toolbar-inner">
				  {{tpl_page_navbar}}
				  
				</div>
			  </div>
 
			  <div class="page-content messages-content">
			  	{{tpl_pvar_message}}
				{{tpl_page_content}}
			  </div>
							       
			</div>
		  </div>
		</div>
		
	  </div>
		{{tpl_page_meta_footer}}
		{{tpl_page_cache}}
	</body>
</html>