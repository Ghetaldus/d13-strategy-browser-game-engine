<div class="popup popup-build-{{tvar_id}}">
<div class="card no-border large-card">
	
	<div class="card-header no-border">
			<div class="d13-heading">{{tvar_title}}</div>
	</div>
	
	<div class="card-content">
		<div class="card-content-inner">

		<div class="list-block no-hairlines-between">
			<ul>
					
				<li class="item-content">
					<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/clock.png"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_duration}}</div>
					<div class="item-after"><span class="badge">{{tvar_moduleDuration}} {{tvar_ui_minutes}}</span></div>
					</div>
				</li>
				
				<li class="item-content">
					<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInputImage}}" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_ui_max}} {{tvar_moduleInputName}}</div>
					<div class="item-after"><span class="badge">{{tvar_moduleLimit}}</span></div>
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
					<div class="item-media"><img class="d13-icon" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_moduleInputImage}}" title="{{tvar_moduleInputName}}"></div>
					<div class="item-inner">
					<div class="item-title">{{tvar_moduleInputName}}</div>
					<div class="item-after">{{tvar_inputSlider}}</div>
					</div>
				</li>
				
			</ul>
		</div>

		</div>
	</div>
	
</div>
</div>