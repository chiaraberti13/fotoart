<?php
	require_once('../assets/includes/session.php');							# INCLUDE THE SESSION START FILE		
	require_once('mgr.security.php');								# INCLUDE SECURITY CHECK FILE		
	require_once('mgr.config.php');									# INCLUDE MANAGER CONFIG FILE
	require_once('../assets/includes/tweak.php');							# INCLUDE TWEAK FILE
	if(file_exists("../assets/includes/db.config.php"))
	{			
		require_once('../assets/includes/db.config.php');					# INCLUDE DATABASE CONFIG FILE
	}
	else
	{ 											
		@$script_error[] = "The db.config.php file is missing.";	# DATABASE CONFIG FILE MISSING
	}
	require_once('../assets/includes/shared.functions.php');					# INCLUDE SHARED FUNCTIONS FILE
	require_once('mgr.functions.php');								# INCLUDE MANAGER FUNCTIONS FILE		
	error_reporting(0);												# TURN ERROR REPORTING OFF TEMPORARILY TO USE SCRIPT ERROR REPORTING
	require_once('../assets/includes/db.conn.php');							# INCLUDE DATABASE CONNECTION FILE
	require_once('mgr.select.settings.php');						# SELECT THE SETTINGS DATABASE
	include_lang();													# INCLUDE THE LANGUAGE FILE
	require_once('../assets/includes/addons.php');									# INCLUDE MANAGER ADDONS FILE			
	require_once('mgr.error.check.php');							# INCLUDE THE ERROR CHECKING FILE
?>
// OPEN MESSAGE WINDOW
function message_window(mem_id)
{
    //workboxobj.id = mem_id;
    //workbox(workboxobj);
    workbox2({page: 'mgr.workbox.php',pars: 'box=newticket&id='+mem_id});
}
function support_ticket_window(mem_id)
{
    //workboxobj.id = mem_id;
    //workbox(workboxobj);
    workbox2({page: 'mgr.workbox.php',pars: 'box=newticket&tickettab=1&id='+mem_id});
}

// SUBMIT NEW SUPPORT TICKET MESSAGE
function submit_new_message()
{
	<?php
        if($_SESSION['admin_user']['admin_id'] == "DEMO")
        {
            //echo "$('workbox').hide();";
            //echo "$('overlay').hide();";
            echo "demo_message2();";
        }
        else
        {
    ?>
        $('submit_button').disable();
        $('support_ticket_form').request({
            evalScripts: true,
            onFailure: function() {}, 
            onSuccess: function(transport) {
                //eval(transport.responseText);
                //alert('testing');
                //alert(transport.responseText);
                //$('testresult').update(t.responseText);
                //close_workbox();
                //$('workbox').hide();
                //$('overlay').hide();
                if($('message_type_email').checked)
                {
                    simple_message_box2('Your email has been sent.','');
                }
                else
                {
                    simple_message_box2('Your ticket has been submitted.','');
                }
                //load_tickets();
            }
        });
    <?php
        }
    ?>
}

function ticket_summary()
{
    var ticket_id = $('ticket_id').options[$('ticket_id').selectedIndex].value;
    if(ticket_id == 0)
    {
        $('summary_p').show();
    }
    else
    {
        $('summary_p').hide();
    }
}

// SET EMAIL BUTTON IN WORKBOX
function emailbutton()
{
    show_div('message_email_div');
    hide_div('message_ticket_div');
    $('email_b').className = 'subsubon';
    $('ticket_b').className = 'subsuboff';
}
// SET TICKET BUTTON IN WORKBOX		
function ticketbutton()
{
	show_div('message_ticket_div');
    hide_div('message_email_div');
    $('email_b').className = 'subsuboff';
    $('ticket_b').className = 'subsubon';
    $('message_type_ticket').checked = true;
}