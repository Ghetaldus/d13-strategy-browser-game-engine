<div class="swiper-slide">
	
	<div class="card no-border large-card card-shadow">
		<div class="card-header no-border">
			<div class="d13-heading">{{tvar_itemName}}</div>
			<a class="close-popup" href="#"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>
  
	<div class="card-content">
    <div class="card-content-inner">
	
			<div class="row">
    	
			<div class="col-25">
				<img src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/{{tvar_itemImageDirectory}}/{{tvar_itemImage}}" width="80">
			</div>
			
			<div class="col-75">
				<p class="d13-italic">
					{{tvar_itemDescription}}
				</p>
			</div>
				
				
			</div>
							
			<div class="row">
			
			<div class="col-100">
				
				<div class="list-block no-hairlines-between">
					<ul>
						
						<li class="item-content">
							<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/{{tvar_itemImageDirectory}}/{{tvar_itemResource}}" title="{{tvar_itemResourceName}}"></div>
							<div class="item-inner">
								<div class="item-title">
									{{tvar_ui_stored}}:
								</div>
								<div class="item-after">
									<span class="badge">{{tvar_itemValue}} / {{tvar_itemMaxValue}}</span>
								</div>
							</div>
						</li>
				
						<li class="item-content">
							<div class="item-media">{{tvar_costIcon}}</div>
							<div class="item-inner">
							<div class="item-title">{{tvar_ui_cost}}:</div>
							<div class="item-after">{{tvar_costData}}</div>
							</div>
						</li>
						
						<li class="item-content">
							<div class="item-inner">
							<div class="item-input">{{tvar_linkData}}</div>
							</div>
						</li>

					</ul>
				</div>
			
			</div>
			
	</div>
	
	
	</div>
	</div>

	<div class="card-footer no-border">
	</div>

	</div>
	
</div>