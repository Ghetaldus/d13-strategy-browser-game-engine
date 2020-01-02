 <div class="login-screen modal-in">
    <div class="view">
      <div class="page">
        <div class="page-content login-screen-content">
        <div class=" d13-window">
          <div class="login-screen-title">{{tvar_ui_resetPassword}}</div>
          <form method="post" action="?p=reset" id="login">
            <div class="list-block no-hairlines-between">
             
              <ul>
				
				{{tpl_pvar_message}}
				
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title label">{{tvar_ui_username}}</div>
                    <div class="item-input">
                      <input type="text" name="name" placeholder="{{tvar_user_name}}">
                    </div>
                  </div>
                </li>
                
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title label">{{tvar_ui_email}}</div>
                    <div class="item-input">
                      <input type="text" name="email" placeholder="{{tvar_user_email}}">
                    </div>
                  </div>
                </li>
                
              </ul>
             
              
            </div>
            <div class="list-block no-hairlines-between">
              <ul>
                <li>
                	
                	<input class="button button-big button-round active" type="submit" value="{{tvar_ui_resetPassword}}">
                
                </li>
              </ul>
              <div class="list-block-label">
                <p><a href="#" class="close-login-screen">Close Reset Screen</a></p>
              </div>
            </div>
          </form>
        </div>
      </div>
      </div>
    </div>
  </div>
