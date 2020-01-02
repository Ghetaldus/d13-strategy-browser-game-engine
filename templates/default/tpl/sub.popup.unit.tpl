<div class="popup popup-unit-{{tvar_id}}">
	<div class="card no-border large-card">
		<div class="card-header no-border">
			<div class="d13-heading">
				{{tvar_Name}}
			</div><a class="close-popup" href="#"><img class="d13-icon hvr-pulse" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/cross.png"></a>
		</div>
		<div class="card-content">
			<div class="card-content-inner">
			
				<div class="row">
					<div class="col-25">
						<img class="d13-unit" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/units/{{tvar_nodeFaction}}/{{tvar_Image}}" width="80">
					</div>
					
					<div class="col-75">
						<p class="d13-italic">{{tvar_Description}}</p>
					</div>
				</div>
					
				<div class="row">
					<div class="col-100">
						<div class="list-block no-hairlines-between">
							<ul>
								
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/resources/{{tvar_upkeepResource}}.png" title="{{tvar_upkeepResource}}"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_stationed}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitValue}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_class}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_Class}}</span>
										</div>
									</div>
								</li>

								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_hp.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_hp}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitHP}} {{tvar_unitHPPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_damage.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_damage}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitDamage}} {{tvar_unitDamagePlus}}</span>
										</div>
									</div>
								</li>
								
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_armor.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_armor}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitArmor}} {{tvar_unitArmorPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_speed.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_speed}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitSpeed}} {{tvar_unitSpeedPlus}}</span>
										</div>
									</div>
								</li>
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_critical.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_critical}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitCritical}} {{tvar_unitCriticalPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_capacity.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_capacity}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitCapacity}} {{tvar_unitCapacityPlus}}</span>
										</div>
									</div>
								</li>
								<li class="item-content">
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_vision.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_vision}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitVision}} {{tvar_unitVisionPlus}}</span>
										</div>
									</div>
									<div class="item-media"><img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/stat_stealth.png"></div>
									<div class="item-inner">
										<div class="item-title">
											{{tvar_ui_stealth}}:
										</div>
										<div class="item-after">
											<span class="badge">{{tvar_unitStealth}} {{tvar_unitStealthPlus}}</span>
										</div>
									</div>
								</li>
								<li class="item-content">
							<div class="item-media">
								<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/mod_atk.png">
							</div>
							<div class="item-inner">
								<div class="item-title">
									{{tvar_ui_attackModifier}}: 
								</div>
								<div class="item-after">
									{{tvar_attackModifier}}
								</div>
							</div>
							<div class="item-media">
								<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/mod_def.png">
							</div>
							<div class="item-inner">
								<div class="item-title">
									{{tvar_ui_defenseModifier}}: 
								</div>
								<div class="item-after">
									{{tvar_defenseModifier}}
								</div>
							</div>
						</li>
					
						<li class="item-content">
							<div class="item-media">
								<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/mod_armyatk.png">
							</div>
							<div class="item-inner">
								<div class="item-title">
									{{tvar_ui_armyAttackModifier}}: 
								</div>
								<div class="item-after">
									{{tvar_armyAttackModifier}}
								</div>
							</div>
							<div class="item-media">
								<img class="d13-resource" src="{{tvar_global_directory}}templates/{{tvar_global_template}}/images/icon/mod_armydef.png">
							</div>
							<div class="item-inner">
								<div class="item-title">
									{{tvar_ui_armyDefenseModifier}}: 
								</div>
								<div class="item-after">
									{{tvar_armyDefenseModifier}}
								</div>
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