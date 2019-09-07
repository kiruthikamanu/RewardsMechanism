<?php
session_start();
$Username=$_SESSION['username'];
?>
<html>
<head>
	<title>SyndRewards</title>
	<!--Link the stylesheet-->

	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>

<?php
	$Err="";
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
      			$sql="SELECT customer_id,name,sum(reward) as rewards FROM cons_txn_info 
				WHERE customer_id='$Username'
				GROUP BY customer_id";
				$result=mysqli_query($conn,$sql);
				if(!$result)
         			{	$Err="Error executing query";
					}
				else
				{
					$number_of_rows=mysqli_num_rows($result);
					$rewards_total=0;
           			if($number_of_rows > 0)
              		{	$row = mysqli_fetch_array($result);
               			$rewards_total=$row['rewards'];		
					}
					$rewards_total=round($rewards_total,2);
				}
				$sql="SELECT * FROM cons_txn_info
				WHERE customer_id='$Username'
				ORDER BY time DESC
				LIMIT 7";
				$result=mysqli_query($conn,$sql);
				if(!$result)
         			{	$Err="Error executing query";
					}
				else
				{
					$cust_txn_recent_count=mysqli_num_rows($result);
					$cust_txn_recent=array(array());
					if($number_of_rows==0)
					{	$Err_txn='No recent transactions';
					}
					else
					{	for($i=1;$i<=mysqli_num_rows($result);$i++)
						{	$row=mysqli_fetch_array($result);
							$cust_txn_recent[$i]['acc_no']=$row['acc_no'];
							$cust_txn_recent[$i]['time']=$row['time'];
							$cust_txn_recent[$i]['channel']=$row['channel'];
							$cust_txn_recent[$i]['amount']=$row['amount'];
							$cust_txn_recent[$i]['reward']=$row['reward'];
						}
					}
				}
			}
		}
	}

?>

<div id='topbox'>
	<img id='logo' src="Syndlogo.jpg">
	<h1 class="Tophead">SyndRewards</h1>
	<div id="topboxright">
	<a href="Userlogout.php">Logout</a>
	</div>	 	 	
</div>
<div class='toppanel'>
	<div class='userpage_left'>
		<h1 class='contenthead'>Total reward points</h1>
		<h1 class='contenthead'><?php echo $rewards_total; ?></h1>
	</div>
	<div class='redeem_button'>
		<input type='submit' value='Redeem rewards'>
	</div>
</div>
<div class='txn_display'>
<h1 class='contenthead'>Recent transactions</h1>
	<?php
	for($i=1;$i<$cust_txn_recent_count;$i++)
	{	echo "<div class='txn'>";
		echo "<table>";
		echo "<tr><td>".$cust_txn_recent[$i]['acc_no']."</td>"."<td>".$cust_txn_recent[$i]['channel']."</td><td>".$cust_txn_recent[$i]['time'].
		"<td>Trans. Amount:".$cust_txn_recent[$i]['amount']."<br>Rewards:".$cust_txn_recent[$i]['reward']."</td></tr>";
		echo "</table>";
		echo "</div>";
	}
	?>	
	</div>
	<?php echo $Err; ?>
</body>
</html>
