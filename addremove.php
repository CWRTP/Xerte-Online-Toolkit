<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add of remove users</title>
<link href="website_code/styles/passwordpages.css" media="screen" type="text/css" rel="stylesheet" />
</head>
<body>
    <div>
        <form method="post" action="addremove.php">
            <div class="form-element">
                <p>Your username <input type="text" size="20" maxlength="100" name="login" class="inp"/></p>
                <p>Password <input type="password" size="20" maxlength="100" name="login_password" class="inp"/></p>

                <p>Username to add/remove <input type="text" size="20" maxlength="100" name="username" class="inp"/>
            </div>
            <div class="form-element">
                <span>Only fill for adding a new user:</span>
                <p>First name <input type="text" size="20" maxlength="100" name="firstname" class="inp"/>
                <p>Last name <input type="text" size="20" maxlength="100" name="lastname" class="inp"/>
                <p>User Password <input type="password" size="20" maxlength="100" name="new_password" class="inp"/></p>
                <p>Repeat Password <input type="password" size="20" maxlength="100" name="repeat_password" class="inp"/></p>
            </div>
            
            <div class="form-element">
                <select name="addremove">
                    <option>Add</option>
                    <option>Remove</option>
                </select>
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
            else if($_POST["login_password"]=="")
            {
                echo "<p>Error: Please enter your password</p>";
            }
            else if($_POST["username"]=="")
            {
                echo "<p>Error: Please enter username</p>";
            }
            else if (($_POST["login"]!="")&&($_POST["login_password"]!="")&&($_POST["username"]!=""))
            {
                $user_record = db_query_one("SELECT password, CAST(is_admin AS unsigned integer) AS is_admin FROM allowed_users WHERE username = ?", array($_POST["login"]));
                if( sha1($_POST["login_password"]) == $user_record["password"] && $user_record["is_admin"] != 0 )
                {
                    if($_POST["addremove"] == "Add")
                    {
                        if($_POST["firstname"]=="")
                        {
                            echo "<p>Error: Please enter first name</p>";
                        }
                        else if($_POST["lastname"]=="")
                        {
                            echo "<p>Error: Please enter last name</p>";
                        }
                        else if($_POST["new_password"]=="")
                        {
                            echo "<p>Error: Please enter new password</p>";
                        }
                        else if($_POST["repeat_password"]=="")
                        {
                            echo "<p>Error: Please enter copy of the new password</p>";
                        }
                        else if($_POST["new_password"] != $_POST["repeat_password"])
                        {
                            echo "<p>Please enter the same new password and its copy</p>";
                        }
                        else
                        {
                            db_query("INSERT INTO allowed_users VALUES(0, ?, ?, ?, ?, 0, 0)",
                                    array($_POST["firstname"],$_POST["lastname"], $_POST["username"], sha1($_POST["new_password"])));
                        }
                    }
                    else
                    {
                        //Removing user
                        db_query("DELETE FROM allowed_users WHERE username = ?",
                                    array($_POST["username"]));
                    }
                }
                else
                {
                    echo "<p>Wrong password and/or no administrator privileges</p>";
                }
            }
        }
        
        echo "<p><a href=\"" . $xerte_toolkits_site->site_url . "\">Return to the login page</a></p>";
    ?>
</body>
