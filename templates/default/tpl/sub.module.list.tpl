<div class="swiper-slide">

	
	<div class="card no-border large-card card-shadow">

	  <div class="card-header no-border">
		<div class="d13-heading">{{tvar_name}}</div>
		<a class="external" href="?p=node&action=get&nodeId={{tvar_nodeID}}"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	  </div>
  
	  <div class="card-content">
		<div class="card-content-inner">
	
			<div class="row">
			
				<div class="col-40">
					<div class="d13-module  {{tvar_moduleDescription}}" style="background-image: url('{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/moduleBackground.png'); background-repeat: repeat;">
					<img class="d13-module-image" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/modules/{{tvar_nodeFaction}}/{{tvar_trueimage}}">
					</div>
					{{tvar_linkData}}
				</div>

				<div class="col-60">
				
					<div class="list-block no-hairlines-between">
					<ul>
					
					<li class="item-content">
						<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_maxInstances}}</div>
						<div class="item-after"><span class="badge">{{tvar_maxinstances}}</span></div>
						</div>
					</li>

					
					<li class="item-content">
						<div class="item-media"><a href="#" class="tooltip" data-tooltip="{{tvar_moduleInputName}}"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInput}}.png" title="{{tvar_moduleInputName}}"></a></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_maxInput}}</div>
						<div class="item-after"><span class="badge">{{tvar_maxinput}}</span></div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_ratio}}</div>
						<div class="item-after"><span class="badge">{{tvar_ratio}}</span></div>
						</div>
					</li>
				
				
				
					<li class="item-content">
						<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_add}} {{tvar_ui_duration}}</div>
						<div class="item-after"><span class="badge">{{tvar_duration}} {{tvar_ui_minutes}}</span></div>
						</div>
					</li>
							
					<li class="item-content">
						<div class="item-media">{{tvar_costIcon}}</div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_cost}}</div>
						<div class="item-after">{{tvar_costData}}</div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-media">{{tvar_requirementsIcon}}</div>
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_requirements}}:</div>
						<div class="item-after">{{tvar_requirementsData}}</div>
						</div>
					</li>
				
					<li class="item-content">
						<div class="item-inner">
						<div class="item-title">{{tvar_ui_output}}:</div>
						<div class="item-after">{{tvar_outputData}}</div>
						</div>
					</li>

					</ul>
					</div>
				</div>
			</div>
	
		</div>
	  </div>
  
	</div>

</div>