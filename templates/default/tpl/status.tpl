<script type="text/javascript">
   var timerIds=new Array();
</script>

<div class="d13-node" >
<div class="card no-border large-card card-shadow">

	<div class="card-header no-border">
  		<div class="d13-heading">{{tvar_ui_user}} {{tvar_ui_status}}</div>
  		<a class="external" href="?p=node&action=list"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
	</div>
  
	<div class="card-content">
		<div class="card-content-inner">

		<div class="row">
	
		<div class="col-25">
			<img src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/avatars/{{tvar_userImage}}" width="80">
			{{tvar_avatarLink}}
		</div>
		
		<div class="col-75">
			
			<div class="list-block no-hairlines-between">
				<ul>

					<li class="item-content">
						<div class="item-inner">
						<div class="item-after">{{tvar_userName}}</div>
						</div>
						<div class="item-inner">
						
							<div class="progressbar color-{{tvar_userColor}}" data-progress="{{tvar_userPercentageExperience}}" style="height:8px;">
    							<span></span>
							</div>
						
						</div>
					</li>
					
							<li class="item-content">
								<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_userImageLevel}}"></div>
								<div class="item-inner">
									<div class="item-title">
										{{tvar_ui_level}}:
									</div>
									<div class="item-after">
										<span class="badge">{{tvar_userLevel}}</span>
									</div>
								</div>
								<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_userImageExperience}}"></div>
								<div class="item-inner">
									<div class="item-title">
										{{tvar_ui_experience}}:
									</div>
									<div class="item-after">
										<span class="badge">{{tvar_userExperience}}</span>
									</div>
									
								</div>
							</li>
							
							<li class="item-content">
								<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_userImageTrophies}}"></div>
								<div class="item-inner">
									<div class="item-title">
										{{tvar_ui_trophies}}:
									</div>
									<div class="item-after">
										<span class="badge">{{tvar_userTrophies}}</span>
									</div>
								</div>
								<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/leagues/{{tvar_userImageLeague}}"></div>
								<div class="item-inner">
									<div class="item-title">
										{{tvar_ui_league}}:
									</div>
									<div class="item-after">
										<span class="badge">{{tvar_userLeague}}</span>
									</div>
								</div>
							</li>
							
							<li class="item-content">
								<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/alliances/{{tvar_userAllianceImage}}"></div>
								<div class="item-inner">
									<div class="item-title">
										{{tvar_ui_alliance}}:
									</div>
									<div class="item-after">
										<span class="badge">{{tvar_userAllianceName}} {{tvar_userAllianceTag}}</span>
									</div>
								</div>
								<div class="item-media"></div>
								<div class="item-inner">
									<div class="item-title">
										
									</div>
									<div class="item-after">
										
									</div>
								</div>
							</li>

				</ul>
				
			</div>
		</div>
</div>

	</div>
	</div>

	<div class="card-footer no-border">
		<div class="left"></div>
		<div class="right"></div>
	</div>
	
</div>
</div>