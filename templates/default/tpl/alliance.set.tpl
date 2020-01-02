<div class="d13-node" >
<div class="card no-border large-card card-shadow">

  <div class="card-header no-border">
  	{{tvar_ui_alliance}} {{tvar_ui_edit}}
  	<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
  </div>
  
  <div class="card-content">
    <div class="card-content-inner">
    	
    	<form method="post" class="pure-form" action="?p=alliance&action=set">
    	
    	<div class="row">
	
			<div class="col-40">
				<img src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/alliances/{{tvar_allianceAvatar}}" width="80">
				{{tvar_avatarLink}}
				<input type="hidden" name="avatar" value="{{tvar_avatarid}}">
			</div>
		
			<div class="col-60">

    			<div class="list-block no-hairlines-between">
					<ul>
			
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/flag.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_alliance}} {{tvar_ui_node}}</div>
						<div class="item-after"><select class="dropdown" name="nodeId">{{tvar_nodeList}}</select></div>
						</div>
					</li>
    			
    				<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/flag.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_alliance}} {{tvar_ui_name}}</div>
						<div class="item-after"><input class="textbox" type="text" name="name" minlength="3" maxlength="32" value="{{tvar_allianceName}}"></div>
						</div>
					</li>
					
					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/flag.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_alliance}} {{tvar_ui_tag}}</div>
						<div class="item-after"><input class="textbox" type="text" name="tag" minlength="3" maxlength="3" value="{{tvar_allianceTag}}"></div>
						</div>
					</li>

					<li class="item-content">
						<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/flag.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_edit}} {{tvar_ui_cost}}:</div>
						<div class="item-after"><span class="badge">{{tvar_costData}}</span></div>
						</div>
					</li>
					
					<li class="item-content">
						<div class="item-inner">
							<input class="button active" type="submit" value="{{tvar_ui_set}}">
						</div>
					</li>
					
    				</ul>
    			</div>

    		</div>
    		
    	</div>
    	
		</form>
		
    </div>
  </div>
  
  <div class="card-footer">
  	{{tvar_tpl_allianceMenu}}
  </div>
  
</div>
</div>



