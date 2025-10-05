<?php
/*
//================================================================================
* phphq.Net Custom PHP Scripts *
//================================================================================
:- Script Name: phMailer
:- Version: 1.5.1
:- Release Date: Jan 27th 2004
:- Last Update: Jan 25 2010
:- Author: Scott Lucht <scott@phphq.net> http://www.phphq.net
:- Copyright(c) 2010 All Rights Reserved
:-
:- This script is free software; you can redistribute it and/or modify
:- it under the terms of the GNU General Public License as published by
:- the Free Software Foundation; either version 2 of the License, or
:- (at your option) any later version.
:-
:- This script is distributed in the hope that it will be useful,
:- but WITHOUT ANY WARRANTY; without even the implied warranty of
:- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
:- GNU General Public License for more details.
:-http://www.gnu.org/licenses/gpl.txt
:-
//================================================================================
* Description
//================================================================================
:- phMailer is a very simple PHP mail script that supports attachments. This is very helpful if you want your
:- visitors to be able to contact you without them knowing your real email address. On great feature of this
:- script is the ability to allow users to attach multiple files when sending an email directly from your site. Of
:- course, you can disable this feature if you wish. Any file type is accepted as long as they are included in your
:- file extension list. Another popular use for a PHP email form is protection against spam bots. Spam, is a major
:- downside of email, and placing your email publicly on your website is asking for spam. Spam bots can take your
:- email address right off your site and add it to thousands of spam databases, resulting in a never ending supply
:- of spam for you. I coded this script, because I couldn't find a simple mail script that would allow my visitors to 
:- send me attachments while keeping my email hidden from spam bots.
//================================================================================
* Setup
//================================================================================
:- To setup this script, simply upload this file to your website. Then edit the variables found herein to adjust
:- how the form works.
//================================================================================
* Change log
//================================================================================
:- Version 1.0
:-		1) Initial Release
:- Version 1.1
:-		1) Minor bug fixes / html improvement	
:- Version 1.2
:-		1) Added CSS styling
:-		2) Cleaned html and improved form style
:-		3) Removed html embedded directly in php tags
:-		4) Improved security checks to prevent forging email headers
:- Version 1.3
:-		1) Cleaned up html and CSS styles
:-		2) Added support to attach multiple files
:-		3) Minor bug fixes
:- Version 1.4
:-		1) Re-write of many core functions to improve attachment handling
:-		2) Added feature that allows users to select an email address from a drop down
:-		3) Minor bug fixes
:- Version 1.5
:-		1) Added multiple new security checks to prevent email header forging. 
:-		2) Cleaned up script and reduced PHP needed to complete tasks.
:-		3) Minor bug fixes
:- Version 1.5.1
:-		1) Cleaned up script and reduced PHP needed to complete tasks.
:-		2) Added text/html as email type to allow users to use line breaks when sending a message. Messages
:-			now display correctly in newer email clients such as Gmail.
:-		3) Removed unnecessary email headers and improved standardization
:-		4) Made sure script is completely compatible with PHP 5.3.x
//================================================================================
* Frequently Asked Questions
//================================================================================
:- Q1: I never receive any mail, but people say they have emailed me through the form.
:-		1) Try the mailtest.php file that came with this script. If that fails, then mail() is probably not setup right.
:-		2) Double check to make sure your email address is correct.
:-		3) Try using the form with $allowattach set to 0. It could be your mail server rejecting the mail
:-				because of attachments.
:-		4) If you are on windows, make sure your SMTP is set to your mail server. If you are on Linux, make sure
:-				your sendmail path if correct. Again, ask your host if you are unsure about this.

:- Q2: I never receive any attachments.
:-		1) Maybe your server has some security against uploading files or sending attachments through mail,
:-				check with your host on this issue. This script does send attachments, it's been tested many
:-				times on many different platforms and versions of PHP with safe mode on and off.
:-		2) Maybe the files people are submitting are too big. Check php.ini for the post_max_size,
:-				upload_max_filesize, file_uploads, max_execution_time you may have to check with your host on this.
:-
:- Q3: The page takes long to load and then gives me a page cannot be displayed or a blank page.
:-		1) This is usually due to a low value in php.ini for "max_execution_time". 
:-		2) A newer ini setting "max_file_uploads" in php 5.2.12 was added which may be limiting the number
			of simultaneous uploads.
:-		3) Your "upload_max_filesize" and "post_max_size" in php.ini might be set to low.
:-
:- Q4: How do I edit the colors of the form?
:-		1) You will need to edit the CSS near the bottom of the script to change the looks and colors of the form.
:-			Check http://www.w3schools.com/css/default.asp for more information on CSS.
:-
:- Q5: Can I add more fields for the users to enter information in?
:-		1) That's the beauty of PHP! It's open source, you can edit it all you want, change whatever you don't like.
:-				Just please leave in my copyright. So many times I see my script without it and it makes me sad. 
:-
:- Q6: Dude! Can you add more fields for me? I don't know PHP!
:-		1) Maybe, but I do usually charge a fee depending on what you want done. Don't freak out! It's usually
:-				a very small one. I can't do everything for free..
:-
:- Q7: Can I remove your copyright link?
:-		1) I can't physically stop you. However, I really appreciate it when people leave it intact.
:-			Some people donate $5, $10, $20 to take it off.
:-
:- Q8: You never respond to my emails or to my questions in your forums!
:-		1) I'm a very busy guy. I'm out of town a lot, and at any given time I have several projects going on.
:-			I get a lot of emails about this script, not to mention my other ones.
:-		2) I only understand English. If your English is very bad please write in your native language and then
:-			translate it to English using <http://babelfish.altavista.com/babelfish/tr>.
:-		3) If you are going to contact me, describe the issue you are having as completely as possible.
:-			"dude me form don't work see it at blah.com what's wrong??!?!" will get no response, ever. Write
:-			in detail what the problem is. Spend a minute on it, and maybe I'll take some of my time to reply.
/*
//================================================================================
* ! ATTENTION !
//================================================================================
:- Please read the above FAQ before emailing me/
*/

// This will show in the browsers title bar and at the top of the form.
$websitename="Your Website"; 

// Allowed file types. Please remember to keep the format of this array, add the file extensions you want
// WITHOUT the dot. Please also be aware that certain file types (such as exe) may contain malware.
$allowtypes=array("zip", "rar", "txt", "doc", "jpg", "png", "gif", "odt", "xml");

// What's your email address? Seperate email addresses with commas for multiple email addresses.
$myemail="your_email@domain.com";

// What priority should the script send the mail? 1 (Highest), 2 (High), 3 (Normal), 4 (Low), 5 (Lowest).
$priority="3"; 

// Should we allow visitors to attach files? How Many? 0 = Do not allow attachments,
// 1 = allow only 1 file to be attached, 2 = allow two files etc.
$allowattach="1"; 

// Maximum file size for attachments in KB NOT Bytes for simplicity. MAKE SURE your php.ini can handel it,
// post_max_size, upload_max_filesize, file_uploads, max_execution_time!
// 2048kb = 2MB,       1024kb = 1MB,     512kb = 1/2MB etc..
$max_file_size="1024";

// Maximum file size for all attachments combined in KB. MAKE SURE your php.ini can handel it,
// post_max_size, upload_max_filesize, file_uploads, max_execution_time!
// 2048kb = 2MB,       1024kb = 1MB,     512kb = 1/2MB etc..
$max_file_total="2048";

// Value for the Submit Button
$submitvalue=" Send Email "; 

// Value for the Reset Button
$resetvalue=" Reset Form ";

// Default subject? This will be sent if the user does not type in a subject
$defaultsubject="No Subject"; 

// Because many requested it, this feature will add a drop down box for the user to select a array of
// subjects that you specify below. 
// True = Use this feature, False = do not use this feature
$use_subject_drop=false;

// This is an array of the email subjects the user can pick from. Make sure you keep the format of
// this array or you will get errors.
// Look at <http://novahq.net/forum/showthread.php?t=38718> for examples on how to use this feature.
$subjects=array("Department 1", "Department 2", "Department 3");

// This is an array of the email addresses for the array above. There must be an email FOR EACH
// array value specified above. You can have only 1 department if you want.
// YOU MUST HAVE THE SAME AMMOUNT OF $subjects and $emails or this WILL NOT work correctly!
// The emails also must be in order for what you specify above!
// Seperate email addresses by a comma to send an email to multiple addresses.
$emails=array("dept_1@domain.com", "dept_2@domain.com", "dept_3@domain.com");

// This is the message that is sent after the email has been sent. You can use html here.
// If you want to redirect users to another page on your website use this:
// <script type=\"text/javascript\">window.location=\"http://www.YOUR_URL.com/page.html\";</script>
$thanksmessage="Thank you! Your email has been sent, we will respond shortly."; 

/*
//================================================================================
* ! ATTENTION !
//================================================================================
: Don't edit below this line.
*/

// Function to get the extension of the uploaded file.
function get_ext($key) { 
	$key=strtolower(substr(strrchr($key, "."), 1));
	$key=str_replace("jpeg", "jpg", $key);
	return $key;
}

// Function used to attach files to the message
function phattach($file, $name, $boundary) {
	
	$fp=fopen($file, "r");
	$str=fread($fp, filesize($file));
	$str=chunk_split(base64_encode($str));
	$message="--".$boundary."\n";
	$message.="Content-Type: application/octet-stream; name=\"".$name."\"\n";
	$message.="Content-disposition: attachment; filename=\"".$name."\"\n"; 
	$message.="Content-Transfer-Encoding: base64\n";
	$message.="\n";
	$message.="$str\n";
	$message.="\n";

	return $message;
}

//Little bit of security from people forging headers. People are mean sometimes :(
function clean_msg($key) {
	$key=str_replace("\r", "", $key);
	$key=str_replace("\n", "", $key);
	$find=array(
		"/bcc\:/i",
		"/Content\-Type\:/i",
		"/Mime\-Type\:/i",
		"/cc\:/i",
		"/to\:/i"
	);
  $key=preg_replace($find, "", $key);
  return $key;
}

// Initilize some variables
$error="";
$sent_mail=false;

// When the form is submitted
If($_POST['submit']==true) {
	extract($_POST, EXTR_SKIP);

		// Check the form for errors
		If(trim($yourname)=="") { 
			$error.="You did not enter your name!<br />";
		}
		
		If(trim($youremail)=="") { 
			$error.="You did not enter your email!<br />";
		} Elseif(!preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/", $youremail)) {
			$error.="Invalid email address.<br />";
		}

		If(trim($emailsubject)=="") {
			$emailsubject=$defaultsubject;
		}

		If(trim($yourmessage)=="") { 
			$error.="You did not enter a message!<br />";
		}
		
		// Verify Attchment info
		If($allowattach > 0) {
			
			// Get the total size of all uploaded files
			If((array_sum($_FILES['attachment']['size'])) > ($max_file_total*1024)) {
				
				$error.="The max size allowed for all your files is ".$max_file_total."kb<br />";
				
			} Else {

				//Loop through each of the files
				For($i=0; $i <= $allowattach-1; $i++) {
					
					If($_FILES['attachment']['name'][$i]) {
	
						//Check if the file type uploaded is a valid file type. 
						If(!in_array(get_ext($_FILES['attachment']['name'][$i]), $allowtypes)) {
							
							$error.= "Invalid file type for your file: ".$_FILES['attachment']['name'][$i]."<br />";
							
						//Check the size of each file
						} Elseif(($_FILES['attachment']['size'][$i]) > ($max_file_size*1024)) {
							
							$error.= "Your file: ".$_FILES['attachment']['name'][$i]." is to big.<br />";
							
						} // If in_array
						
					} // If Files
					
				} // For
				
			} // Else array_sum($_FILES['attachment']['size'])
			
		} // If Allowattach

	If($error) {
	
		$display_message=$error;

	} Else {
		
		If($use_subject_drop AND is_array($subjects) AND is_array($emails)) {
			$subject_count=count($subjects);
			$email_count=count($emails);
			
			If($subject_count==$email_count) {
				
				$myemail=$emails[$emailsubject];
				$emailsubject=$subjects[$emailsubject];

			} // If $subject_count
			
		} // If $use_subject_drop

		$boundary=md5(uniqid(time()));
		
		//Headers
		$headers="Return-Path: <".clean_msg($youremail).">\n";
		$headers.="From: ".clean_msg($yourname)." <".clean_msg($youremail).">\n";
		$headers.="X-Mailer: PHP/".phpversion()."\n";
		$headers.="X-Sender: ".$_SERVER['REMOTE_ADDR']."\n";
		$headers.="X-Priority: ".$priority."\n"; 
		$headers.="MIME-Version: 1.0\n";
		$headers.="Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n";
		$headers.="This is a multi-part message in MIME format.\n";

		//Message
		$message = "--".$boundary."\n";
		$message.="Content-Type: text/html; charset=\"iso-8859-1\"\n";
		$message.="Content-Transfer-Encoding: quoted-printable\n";
		$message.="\n";
		$message.=clean_msg(nl2br(strip_tags($yourmessage)));
		$message.="\n";

		//Add attachments to message
		If($allowattach > 0) {
			
			For($i=0; $i <= $allowattach-1; $i++) {
				
				If($_FILES['attachment']['tmp_name'][$i]) {
					
					$message.=phattach($_FILES['attachment']['tmp_name'][$i], $_FILES['attachment']['name'][$i], $boundary);
					
				} //If $_FILES['attachment']['name'][$i]
				
			} //For
			
		} // If
		
		// End the message
		$message.="--".$boundary."--\n";
		
		// Send the completed message
		If(!mail($myemail, clean_msg($emailsubject), $message, $headers)) {
			
			Exit("An error has occured, please report this to the website administrator.\n");
			
		} Else {
		
			$sent_mail=true;
			
		}

	} // Else

} // $_POST

/*
//================================================================================
* Start the form layout
//================================================================================
:- Use the html below to customize the form.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Language" content="en-us" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $websitename; ?> - Powered By phMailer</title>

<style type="text/css">
	body{
		background-color:#FFFFFF;
		font-family: Verdana, Arial, sans-serif;
		font-size: 12pt;
		color: #000000;
	}
	
	.error_message{
		font-family: Verdana, Arial, sans-serif;
		font-size: 11pt;
		color: #FF0000;
	}
	
	.thanks_message{
		font-family: Verdana, Arial, sans-serif;
		font-size: 11pt;
		color: #000000;
	}
	
	a:link{
		text-decoration:none;
		color: #000000;
	}
	a:visited{
		text-decoration:none;
		color: #000000;
	}
	a:hover{
		text-decoration:none;
		color: #000000;
	}
	
	.table {
		border-collapse:collapse;
		border:1px solid #000000;
		width:500px;
	}
	
	.table_header{
		border:1px solid #070707;
		background-color:#C03738;
		font-family: Verdana, Arial, sans-serif;
		font-size: 11pt;
		font-weight:bold;
		color: #FFFFFF;
		text-align:center;
		padding:2px;
	}
	
	.attach_info{
		border:1px solid #070707;
		background-color:#EBEBEB;
		font-family: Verdana, Arial, sans-serif;
		font-size: 8pt;
		color: #000000;
		padding:4px;
	}
	
	
	.table_body{
		border:1px solid #070707;
		background-color:#EBEBEB;
		font-family: Verdana, Arial, sans-serif;
		font-size: 10pt;
		color: #000000;
		padding:2px;
	}
	
	.table_footer{
		border:1px solid #070707;
		background-color:#C03738;
		text-align:center;
		padding:2px;
	}
	
	input,select,textarea {
		font-family: Verdana, Arial, sans-serif;
		font-size: 10pt;
		color: #000000;
		background-color:#AFAEAE;
		border:1px solid #000000;
	}
	
	.copyright {
		border:0px;
		font-family: Verdana, Arial, sans-serif;
		font-size: 9pt;
		color: #000000;
		text-align:right;
	}
	
	form{
		padding:0px;
		margin:0px;
	}
</style>

<script type="text/javascript">
var error="";
e_regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/;

function Checkit(theform) {
	if(theform.yourname.value=="") {
		error+="You did not enter your name\n";
	}
	
	if(theform.youremail.value=="") {
		error+="You did not enter your email\n";
	} else if(!e_regex.test(theform.youremail.value)) {
		error+="Invalid email address\n";
	}
		
	if(theform.yourmessage.value=="") {
		error+="You did not enter your message\n";
	}
	
	if(error) {
		alert('**The form returned the following errors:**\n\n' + error);
		error="";
		return false;
	} else {
		return true;
	}
}
</script>

</head>
<body>
<?If($display_message) {?>

<div align="center" class="error_message"><b><?=$display_message;?></b></div>
<br />

<?}?>

<?If($sent_mail!=true) {?>

<form method="post" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data" name="phmailer" onsubmit="return Checkit(this);">
<table align="center" class="table">
	<tr>
		<td colspan="2" class="table_header" width="100%"><?=$websitename;?></td>
	</tr>
	<?If($allowattach > 0) {?>
		<tr>
			<td width="100%" class="attach_info" colspan="2">
				<b>Valid Attachment Types:</b> <?=implode($allowtypes, ", ");?><br />
				<b>Max size per file:</b> <?=$max_file_size?>kb.<br />
				<b>Max combined file size:</b> <?=$max_file_total?>kb.
			</td>
		</tr>
	<?}?>
	
	<tr>
		<td width="30%" class="table_body">Your Name:</td>
		<td width="70%" class="table_body"><input name="yourname" type="text" size="30" value="<?=stripslashes(htmlspecialchars($yourname));?>" /><span class="error_message">*</span></td>
	</tr>
	<tr>
		<td width="30%" class="table_body">Your Email:</td>
		<td width="70%" class="table_body"><input name="youremail" type="text" size="30" value="<?=stripslashes(htmlspecialchars($youremail));?>" /><span class="error_message">*</span></td>
	</tr>
	<tr>
		<td width="30%" class="table_body">Subject:</td>
		<td width="70%" class="table_body">
		
			<?If($use_subject_drop AND is_array($subjects)) {?>
					<select name="emailsubject" size="1">
						<?while(list($key,$val)=each($subjects)) {?>

							<option value="<?=intval($key);?>"><?=htmlspecialchars(stripslashes($val));?></option>
						
						<?}?>
					</select>
				
			
			<?} Else {?>
				
				<input name="emailsubject" type="text" size="30" value="<?=stripslashes(htmlspecialchars($emailsubject));?>" />
				
			<?}?>
			
		</td>
	</tr>

	<?For($i=1;$i <= $allowattach; $i++) {?>
		<tr>
			<td width="30%" class="table_body">Attach File:</td>
			<td width="70%" class="table_body"><input name="attachment[]" type="file" size="30" /></td>
		</tr>
	<?}?>
	
	<tr>
		<td colspan="2" width="100%" class="table_body">Your Message:<span class="error_message">*</span><br />
			<div align="center">
				<textarea name="yourmessage" rows="8" cols="60"><?=stripslashes(htmlspecialchars($yourmessage));?></textarea>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" width="100%" class="table_footer">
			<input type="hidden" name="submit" value="true" />
			<input type="submit" value="<?=$submitvalue;?>" /> &nbsp;
			<input type="reset" value="<?=$resetvalue;?>" />
		</td>
	</tr>
</table>
</form>

<?} Else {?>

	<div align="center" class="thanks_message"><?=$thanksmessage;?></div>
	<br />
	<br />

<?}
//Please leave this here.. It's very small and non-obtrusive. ?>
<table class="table" style="border:0px;" align="center">
	<tr>
		<td><div class="copyright">&copy;<a href="http://www.phphq.net/?script=phMailer" target="_blank" title="Powered By phMailer 1.5.1 &lt;www.phphq.net&gt;">phMailer</a></div></td>
	</tr>
</table>
</body>
</html>
