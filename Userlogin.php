<?php
session_start();
?>
<html>
<head>
	<title>SyndRewards</title>
	<!--Link the stylesheet-->

	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<?php
$Username=$Password="";
$Usernameerr=$Passworderr="";
if($_SERVER["REQUEST_METHOD"] == "POST")
  { 	if(empty($_POST['Username']))
      	 {	$Usernameerr="Please enter the Username";
       	 }
   	else
       	 {	$Username=test_input($_POST['Username']);
       	 }
   	if(empty($_POST['Password']))
      	 {	$Passworderr="Please enter the password";
      	 }
    	else
      	 {	$Password=test_input($_POST['Password']);
      	 }
  }
function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>
<?php
$Err="";
if(empty($Usernameerr)&&empty($Passworderr)&&(strlen($Username)!=0)&&(strlen($Password)!=0))
{	
	//Connect to MySQL server
	$conn=mysqli_connect('localhost','rewardsadmin','Manipal@11');
	//Connection failure
	if(!$conn)
	{	$Err="Unable to connect to database";
	}
	//Connection success
	else
	{	//Set character set utf-8
		if(!mysqli_set_charset($conn,'utf8'))
		{	$Err="Unable to set UTF-8 encoding";
		}
		else
		{	//Select the relevant database
			if(!mysqli_select_db($conn,'syndicate'))
			{	$Err="Unable to locate database";
			}
			else
			{
      				$sql="SELECT * FROM admin WHERE username='$Username'";
				$result=mysqli_query($conn,$sql);
				if(!$result)
         			{	$Err="Error executing query";
				}
				else
				{
					$number_of_rows=mysqli_num_rows($result);
           				if($number_of_rows == 0)
              				{	$Err="Invalid credentials";
              				}
					else
             				{	$row = mysqli_fetch_array($result);
               					if($row['password']!=$Password)
                 				{	$Err="Invalid credentials";
                  				}
						else
                  				{	$_SESSION['username']=$row['username'];
                   					redirect("userpage.php");
                   				}
					}
				}
			}
		}
	}
}
function redirect($url,$statusCode=303)
{
   header('Location: ' . $url, true, $statusCode);
   die();
}
?>

<div id='topbox'>
	<img id='logo' src="Syndlogo.jpg">
	<h1 class="Tophead">SyndRewards</h1>
	 	
</div>

<div class="centerbox">
<center>
<h1 class="contenthead">User login</h1>
<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
<table>
<tr><td>Username</td><td><input type="text" name="Username" size="25" value="<?php echo $Username; ?>"></td><td class="error"><?php echo $Usernameerr; ?></td></tr>
<tr><td>Password</td><td><input type="password" name="Password" size="25" value="<?php echo $Password; ?>"></td><td class="error"><?php echo $Passworderr ?></td></tr>
</table>
<input type="submit" value="Login">
<br><br>
</form>
<p class=error>
<?php
echo $Err;
?>
</p>
</center>
</div>
</body>
</html>
