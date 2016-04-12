<?php if (!defined('SERVER_ROOT')) {
    exit('No direct script access allowed');
} 
    //redirect
    $redirct_url = get_request_var('u','');
    $check_permit = get_request_var('c','');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <title>Đăng nhập hệ thống</title>
        <script language="javascript" type="text/javascript">

            function setFocus() {
                document.loginForm.txt_login_name.select();
                document.loginForm.txt_login_name.focus();
            }

            function btn_login_onclick(){
                var f=document.loginForm;
                if (f.txt_login_name.value == ''){
                    alert("Ban phai nhap [Ten dang nhap]!");
                    f.txt_login_name.focus();
                    return false;
                }
                
                if (document.loginForm.txt_password.value == ''){
                    alert("Ban phai nhap [Mat khau]!");
                    f.txt_password.focus();
                    return false;
                }
                
                f.submit();
            }

            function login(evt){
                if(navigator.appName=="Netscape"){theKey=evt.which}
                if(navigator.appName.indexOf("Microsoft")!=-1){theKey=window.event.keyCode}
                if(theKey==13){
                    btn_login_onclick();
                }
            }
        </script>
        <style type="text/css">
            body{ margin:0px; padding:0px; color:#333; background-color:#FFF; font-size:11px; font-family:Arial,Helvetica,sans-serif} #break{ height:50px} form{ margin:0px} .button{ border:solid 1px #ccc; background:#E9ECEF; color:#666; font-weight:bold; font-size:11px; padding:4px} .login{ margin-left:auto; margin-right:auto; margin-top:6em; padding:15px; border:1px solid #ccc; width:429px; background:#F1F3F5} .form-block{ border:1px solid #ccc; background:#E9ECEF; padding-top:15px; padding-left:10px; padding-bottom:10px; padding-right:10px} .login-form{ text-align:left; float:right; width:60%} .login-text{ text-align:left; width:40%; float:left} .inputlabel{ font-weight:bold; text-align:left} .inputbox{ width:150px; margin:0 0 1em 0; border:1px solid #ccc} .clr{ clear:both} .ctr{ text-align:center}
        </style>
        
        <style>
            table.TableBound
                {
                	width:100%;
                	padding-top:64px;
                	width:349px;
                }
                
                table.TableLogin
                {
                	font-family:Arial;
                	font-size:12px;
                	font-weight:normal;
                	color:#000000;
                	width:100%;
                }
                table.TableLogin td.LoginTitle
                {
                	background-image:url(<?php echo SITE_ROOT;?>public/images/login/LoginTitle.gif);
                	background-position:left bottom;
                	background-repeat:no-repeat;
                	height:74px;
                	font-family:Tahoma;
                	font-size:15px;
                	font-weight:bold;
                	color:#0c3ca8;
                	padding-left:100px;
                }
                table.TableLogin td.Content
                {
                	background-image:url(<?php echo SITE_ROOT;?>public/images/login/bgLogin.gif);
                	background-position:left top;
                	background-repeat:repeat-y;
                	
                }
                table.TableLogin td.LoginFooter
                {
                	background-image:url(<?php echo SITE_ROOT;?>public/images/login/LoginFooter.gif);
                	background-position:left top;
                	background-repeat:no-repeat;
                	height:65px;
                	font-family:Arial;
                	font-size:12px;
                	font-weight:normal;
                	color:#000000;
                	padding-top:20px;
                	text-align:center;
                }
                
                table.TableContent
                {
                	width:285px;
                }
                table.TableContent td.LoginName
                {
                	padding-top:7px;
                	padding-bottom:3px;
                }
                table.TableContent td.LoginPassWord
                {
                	padding-top:10px;
                	padding-bottom:3px;
                }
                table.TableContent td.SaveInfo
                {
                	padding-top:10px;
                	padding-bottom:10px;
                }
                
                .ErrorMessage
                {
                	font-family:Arial;
                	font-size:12px;
                	font-weight:normal;
                	color:#FF0000;
                	padding-left:5px;
                }
                
                .LoginButton
                {
                	font-family:Arial;
                	font-size:12px;
                	font-weight:normal;
                	color:#0c3aaa;
                	border:0px;
                	cursor:pointer;
                	background-image:url(<?php echo SITE_ROOT;?>public/images/login/bg_login_button.gif);
                	background-position:left center;
                	background-repeat:no-repeat;
                	height:24px;
                	width:92px;
                	padding-left:30px;
                	text-decoration:underline;
                	background-color:#FFFFFF;
                }
                
                .LoginTextBox
                {
                	font-family:Arial;
                	font-size:12px;
                	font-weight:normal;
                	color:#000000;
                	width:280px;
                	height:17px;
                	border:#8d8d8d 1px solid;
                }
                .LoginSelectBox
                {
                	font-family:Arial;
                	font-size:12px;
                	font-weight:normal;
                	color:#000000;
                	width:100%;
                }
        </style>
    </head>
    <body>
        <form action="<?php echo $this->get_controller_url();?>do_login/" method="post" name="loginForm" id="loginForm">
            <input type="hidden" name="hdn_redirect_url" id="hdn_redirect_url" value="<?php echo $redirct_url?>">
            <input type="hidden" name="hdn_check_permit" id="hdn_check_permit" value="<?php echo $check_permit?>">
            <table class="TableBound" cellpadding="0" cellspacing="0" align="center">
                    <tr>
                        <td>
                            <table class="TableLogin" cellpadding="0" cellspacing="0" >
                                        <tr>
                                    <td class="LoginTitle"><span id="lblLoginTitle">Đăng nhập hệ thống <br/></span>
                                    </td></tr>
                                <tr>
                                    <td class="Content" valign="top">
                                        <table class="TableContent" cellpadding="0" cellspacing="0" align="center">
                                            <tr>
                                                <td class="ErrorMessage">
                                                    <span id="lblErrorMessage"></span>
                                                    <div id="vsLogin" style="color:Red;display:none;">

            </div>
                                                </td>
                                            </tr>
                                            <tr><td class="LoginName">
                                                    <span id="lblUserName">Tên đăng nhập:</span>
                                                    <span id="rfvUserName" style="color:Red;display:none;">Tên đăng nhập không được bỏ trống</span>
                                            </td></tr>
                                            <tr><td>
                                                    <input name="txt_login_name" type="text" id="txt_login_name" class="LoginTextBox" onkeypress="login(event);" autofocus="autofocus"/>&nbsp;
                                            </td>
                                            </tr>
                                            <tr><td class="LoginPassWord">
                                                    <span id="lblPassword">Mật khẩu:</span>
                                                    <span id="rfvPassword" style="color:Red;display:none;">Mật khẩu không được bỏ trống</span>
                                            </td></tr>
                                            <tr><td>
                                                    <input name="txt_password" type="password" id="txt_password" class="LoginTextBox" value="123456" onkeypress="login(event);" />&nbsp;
                                            </td></tr>

                                            <tr><td style="height:10px"></td></tr>

                                            <tr>
                                                <td>
                                                    <input type="button" name="cmdLogin" value="Đăng nhập" onclick="btn_login_onclick();" id="cmdLogin" class="LoginButton" />
                                                </td>
                                            </tr>
                                            <tr><td style="height:10px"></td></tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="LoginFooter" colspan="2"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
<script>
    setFocus();
</script>
<noscript>
    <h2 align="center">Thông báo: Javascript đang bị cấp!</h2>
</noscript>
    </body>
</html>