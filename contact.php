<?PHP
$pageTitle = "- Contact";
include ("includes/header.php");
?>
<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<form method="post" name="myemailform" action="form-to-email.php">
	<p>
		<label for='name'>Enter Name: </label><br>
		<input type="text" name="name" size="30">
	</p>
	<p>
		<label for='email'>Enter Email Address:</label><br>
		<input type="text" name="email" size="30">
	</p>
	<p>
		<label for='message'>Enter Message:</label> <br>
		<textarea name="message" cols="30"></textarea>
	</p>
	<input type="submit" name='submit' value="submit">
</form>
<script language="JavaScript">
var frmvalidator  = new Validator("myemailform");
frmvalidator.addValidation("name","req","Please provide your name"); 
frmvalidator.addValidation("email","req","Please provide your email"); 
frmvalidator.addValidation("email","email","Please enter a valid email address"); 
</script>