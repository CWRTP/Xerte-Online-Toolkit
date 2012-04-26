<?php

// This file could be used instead of index.php to perform authentication. 
// The list of usernames/passwords are hard coded below.  (search for 'sarah')

require("config.php");

/**
 * 
 * Login page, self posts to become management page
 *
 * @author Patrick Lockley
 * @version 1.0
 * @copyright Copyright (c) 2008,2009 University of Nottingham
 * @package
 */

include $xerte_toolkits_site->php_library_path . "login_library.php";

include $xerte_toolkits_site->php_library_path . "display_library.php";

include $xerte_toolkits_site->php_library_path . "database_library.php";

/**
 *  Check to see if anything has been posted to distinguish between log in attempts
 */

//ADDED on 02/16
ini_set('display_errors', "1");
error_reporting(E_ALL ^ E_NOTICE);

if((!isset($_POST["login"]))&&(!isset($_POST["password"]))){

    $buffer = login_page_format_top(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_top"));

    $buffer .= $form_string;

    $buffer .= login_page_format_bottom(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_bottom"));

    echo $buffer;

}

/*
 * Some data has bee posted, interpret as attempt to login
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /**
     * Username and password left empty
     */

    if(($_POST["login"]=="")&&($_POST["password"]=="")){

        $buffer = login_page_format_top(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_top"));

        $buffer .= "<p>Please enter your username and password</p>";

        $buffer .= login_page_format_bottom(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_bottom"));	

        echo $buffer;

        /*
         * Username left empty
         */

    }else if($_POST["login"]==""){

        $buffer = login_page_format_top(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_top"));

        $buffer .= "<p>Please enter your username</p>";

        $buffer .= login_page_format_bottom(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_bottom"));	

        echo $buffer;

        /*
         * Password left empty
         */

    }else if($_POST["password"]==""){

        $buffer = login_page_format_top(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_top"));

        $buffer .= "<p>Please enter your password</p>";

        $buffer .= login_page_format_bottom(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_bottom"));

        echo $buffer;

        /*
         * Password and username provided, so try to authenticate
         */

    }else if(($_POST["login"]!="")&&($_POST["password"]!="")){

        /*
         * See if the submitted values are valid logins
         */

        $authenticated = false;
        $needs_update = false;

        function set_user_details($firstname, $surname){

            $_SESSION['toolkits_firstname'] = $firstname;
            $_SESSION['toolkits_surname'] = $surname;

        }

        /*switch($_POST["login"]){

            case "pat": if($_POST["password"]=="patpassword"){ $authenticated = true; set_user_details("Pat","Blair");}; break;
            case "john": if($_POST["password"]=="johnpassword"){ $authenticated = true; set_user_details("John","Obama"); }; break;
            case "bob": if($_POST["password"]=="bobpassword"){ $authenticated = true; set_user_details("Bob","Putin"); }; break;
            case "sarah": if($_POST["password"]=="sarahpassword"){ $authenticated = true; set_user_details("Sarah","Sarkozy"); }; break;
            default: $authenticated = false; break;

        }*/
        
        $user_record = db_query_one("SELECT password, firstname, surname, CAST(needs_update AS unsigned integer) AS needs_update FROM allowed_users WHERE username = ?", array($_POST["login"]));
        
        if( $user_record["needs_update"] != 0 )
        {
            $needs_update = true;
        }
        else if( sha1($_POST["password"]) == $user_record["password"] )
        {
            $authenticated = true;
            set_user_details($user_record["firstname"],$user_record["surname"]);
        }

        if($authenticated){

            /*
             * Give the session its own session id
             */		

            $_SESSION['toolkits_sessionid'] = $session_id; 


            //include $xerte_toolkits_site->php_library_path . "database_library.php";

            include $xerte_toolkits_site->php_library_path . "user_library.php";

            $mysql_id=database_connect("index.php database connect success","index.php database connect fail");			

            $_SESSION['toolkits_logon_username'] = $_POST["login"];

            /*
             * Check to see if this is a users' first time on the site
             */

            if(check_if_first_time($_SESSION['toolkits_logon_username'])){

                /*
                 *	create the user a new id			
                 */

                $_SESSION['toolkits_logon_id'] = create_user_id($_SESSION['toolkits_logon_username'], $_SESSION['toolkits_firstname'], $_SESSION['toolkits_surname']);

                /*
                 *   create a virtual root folder for this user
                 */

                create_a_virtual_root_folder();			

            }else{

                /*
                 * User exists so update the user settings
                 */

                $_SESSION['toolkits_logon_id'] = get_user_id();

                update_user_logon_time();

            }

            recycle_bin();		

            /*
             * Output the main page, including the user's and blank templates
             */

            echo file_get_contents($xerte_toolkits_site->website_code_path . "management_headers");

            echo "<script type=\"text/javascript\"> // JAVASCRIPT library for fixed variables\n // management of javascript is set up here\n // SITE SETTINGS\n";

            echo "var site_url = \"" . $xerte_toolkits_site->site_url .  "\";\n";

            echo "var site_apache = \"" . $xerte_toolkits_site->apache .  "\";\n";

            echo "var properties_ajax_php_path = \"website_code/php/properties/\";\n var management_ajax_php_path = \"website_code/php/management/\";\n var ajax_php_path = \"website_code/php/\";\n";

            echo file_get_contents($xerte_toolkits_site->website_code_path . "management_top");

            list_users_projects("data_down");

            echo logged_in_page_format_middle(file_get_contents($xerte_toolkits_site->website_code_path . "management_middle"));

            list_blank_templates();

            echo file_get_contents($xerte_toolkits_site->website_code_path . "management_bottom");

        }else{

            /*
             * login has failed
             */

            $buffer = login_page_format_top(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_top"));

            if( $needs_update )
            {
                $buffer .= "<p>Sorry but your password needs to be updated. Use the link below to change your password. </p>";
            }
            else
            {
                $buffer .= "<p>Sorry that password combination was not correct</p>";
            }

            $buffer .= login_page_format_bottom(file_get_contents($xerte_toolkits_site->root_file_path . $xerte_toolkits_site->website_code_path . "login_bottom"));	

            echo $buffer;	

        }

    }

}

?>	
</body>
</html>
