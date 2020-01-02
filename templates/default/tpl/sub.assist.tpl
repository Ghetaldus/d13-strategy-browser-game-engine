 <div class="login-screen modal-in">
    <div class="view">
      <div class="page">
        <div class="page-content login-screen-content">
          <div class="login-screen-title">{{tvar_ui_sit}}</div>
          <form method="post" action="?p=login&action=sit" id="sit">
            <div class="list-block no-hairlines-between">
             
              
              <ul>
				
				{{tpl_pvar_message}}
				
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title label">{{tvar_ui_username}}</div>
                    <div class="item-input">
                      <input type="text" name="name" placeholder="{{tvar_ui_username}}">
                    </div>
                  </div>
                </li>
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title label">{{tvar_ui_password}}</div>
                    <div class="item-input">
                      <input type="password" name="password" placeholder="{{tvar_ui_password}}">
                    </div>
                  </div>
                </li>
                
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title label">{{tvar_ui_sitter}}</div>
                    <div class="item-input">
                      <input type="text" name="sitter" placeholder="{{tvar_ui_sitter}}">
                    </div>
                  </div>
                </li>
                
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title label">{{tvar_ui_rememberMe}}</div>
                    <div class="item-input">
                      <input type="checkbox" name="remember" value="1">
                    </div>
                  </div>
                </li>
                
                
                
              </ul>
             
              
            </div>
            <div class="list-block no-hairlines-between">
              <ul>
                <li>
                	
                	<input class="button" type="submit" value="{{tvar_ui_login}}"></div>
                
                </li>
              </ul>
              <div class="list-block-label">
                <p><a class="external" href="reset.php">{{tvar_ui_resetPassword}}</a></p>
                <p><a href="#" class="close-login-screen">Close Login Screen</a></p>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>