<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>PRD系统登录</title> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="images/login.js"></script>
<link href="css/login2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>诸葛找房PRD系统登录<sup>V1.0.0</sup></h1>

<div class="login" style="margin-top:50px;">
    <div class="web_qr_login" id="web_qr_login" style="display: block; height: 235px;">    
        <!--登录-->
        <div class="web_login" id="web_login">
            <div class="login-box">
			    <div class="login_form">
				    <form action="controller/loginp.php" name="loginform" accept-charset="utf-8" id="login_form" class="loginForm" method="get">
                        <?php
                        if(!empty($_GET)){
                            if($_GET["err"]==1){
                                echo '<div style="text-align: center;color: #ff0000;">用户名或密码错误!!!</div>';
                            }
                        }
                        
                        
                        ?>
                        <div class="uinArea" id="uinArea">
                            <label class="input-tips" for="u">帐号：</label>
                            <div class="inputOuter" id="uArea">
                                <input type="text" id="u" name="username" class="inputstyle"/>
                            </div>
                        </div>
                        <div class="pwdArea" id="pwdArea">
                            <label class="input-tips" for="p">密码：</label> 
                            <div class="inputOuter" id="pArea">
                                <input type="password" id="p" name="p" class="inputstyle"/>
                            </div>
                        </div>
               
                        <div style="padding-left:50px;margin-top:20px;">
                            <input type="submit" value="登 录" style="width:150px;" class="button_blue"/>
                        </div>
                    </form>
                </div>
            </div>    
        </div>
        <!--登录end-->
  </div>
</div>
</body></html>