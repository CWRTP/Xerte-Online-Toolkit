<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Change xerte toolkits password</title>
<link href="website_code/styles/passwordpages.css" media="screen" type="text/css" rel="stylesheet" />
</head>
<body>
    <div>
        <form method="post" action="changepassword.php">
            <div class="form-element">
                <p>Username <input type="text" size="20" maxlength="100" name="login"  class="inp"/></p>
            </div>
            <div class="form-element">
                <p>Old Password <input type="password" size="20" maxlength="100" name="old_password" class="inp"/></p>
                <p>New Password <input type="password" size="20" maxlength="100" name="new_password"  class="inp"/><span class="error"></span></p>
                <p>Repeat Password <input type="password" size="20" maxlength="100" name="repeat_password"  class="inp"/></p>
            </div>
            <p><input type="submit" class="btn"></input></p>
        </form>
    </div>
    <?php
        require("config.php");
        include_once($xerte_toolkits_site->php_library_path . "database_library.php");
                
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if($_POST["login"]=="")
            {
                echo "<p>Error: Please enter login name</p>";
            }
            else if($_POST["old_password"]=="")
            {
                echo "<p>Error: Please enter the old password</p>";
            }
            else if($_POST["new_password"]=="")
            {
                echo "<p>Error: Please enter new password</p>";
            }
            else if($_POST["repeat_password"]=="")
            {
                echo "<p>Error: Please enter copy of the new password</p>";
            }
            else if(strlen($_POST["new_password"]) < 8)
            {
                echo "<p>Error: new password is shorter than 8 symbols</p>";
            }
            else if (($_POST["login"]!="")&&($_POST["old_password"]!="")&&($_POST["new_password"]!="")&&($_POST["repeat_password"]!=""))
            {
                $user_record = db_query_one("SELECT password FROM allowed_users WHERE username = ?", array($_POST["login"]));
                if( sha1($_POST["old_password"]) == $user_record["password"] )
                {
                    if($_POST["new_password"] == $_POST["old_password"])
                    {
                        echo "<p>Please enter a new password different from the old one</p>";
                    }
                    else if($_POST["new_password"] != $_POST["repeat_password"])
                    {
                        echo "<p>Please enter the same new password and its copy</p>";
                    }
                    else
                    {
                        db_query("UPDATE allowed_users SET password = ?, needs_update = 0 WHERE username = ?", array(sha1($_POST["new_password"]), $_POST["login"]));
                    }
                }
                else
                {
                    echo "<p>Wrong password</p>";
                }
            }
        }
        
        echo "<p><a href=\"" . $xerte_toolkits_site->site_url . "\">Return to the login page</a></p>";
    ?>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript">
        $(function(){
            $('input').filter('[name="new_password"],[name="repeat_password"]').focusout(function(){
                if( $('input[name="new_password"]').val() != $('input[name="repeat_password"]').val())
                {
                    $('.error').text('*New passwords do not match!');
                }
                else
                {
                    $('.error').empty();
                }
            });
        });
    </script>
</body>
