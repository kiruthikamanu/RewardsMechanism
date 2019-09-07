<html>
<head>

<title>Dashboard</title>
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
	<?php
		$branchbic=$_POST['bic'];
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
				{	// Query for retrieving the branch name 
      					$sql="SELECT * FROM branch_bic WHERE bic='$branchbic'";
					$result=mysqli_query($conn,$sql);
					if(!$result)
         				{	$Err="Error executing query";
					}
					else
					{	if(mysqli_num_rows($result)==0)
						{	$Err='BIC not found';
						}
						else
						{	$row=mysqli_fetch_array($result);
							$branch_name=$row['brn_name'];
						}
					}
					// Query for retrieving transactions pertaining to the BIC for current date
					$sql="SELECT * FROM cons_txn_info WHERE bic='$branchbic'and date(time)=date(now())";
					$result=mysqli_query($conn,$sql);
					if(!$result)
         				{	$Err1="Error retrieving transactions for current date";
					}
					else
					{	$total_txn_count=0;
						$ib_txn_count=$mb_txn_count=$upi_txn_count=$pos_txn_count=0;
						$total_txn_count=mysqli_num_rows($result);
						for($i=1;$i<=$total_txn_count;$i++)
						{	$txn_row=mysqli_fetch_array($result);
							$delivery_channel=$txn_row['channel'];
							switch($delivery_channel)
							{	case "IB": $ib_txn_count++;
									   break;
								case "MB": $mb_txn_count++;
									   break;
								case "UPI":$upi_txn_count++;
									   break;
								case "POS":$pos_txn_count++;
									   break;
							}
						}
					}
					//Query for retrieving top 3 performers of last month
					$sql="SELECT name,reward FROM top_perf_monthly
					WHERE bic=$branchbic
					ORDER BY reward DESC
					LIMIT 3";
					$result=mysqli_query($conn,$sql);
					if(!$result)
					{	$Err=$Err.'Error retrieving top performers of previous month';
					}
					else
					{	$top_prev_month_name=array();
						$top_prev_month_reward=array();
						for($i=1;$i<=mysqli_num_rows($result);$i++)
						{	$row=mysqli_fetch_array($result);
							$top_prev_month_name[$i]=$row['name'];	
							$top_prev_month_reward[$i]=$row['reward'];
						}
					}
					//Query for retrieving top 6 performers of ongoing month
					$sql="select bic,customer_id, name, sum(reward) as reward  from cons_txn_info
						where month(time)=(month(now()))
						and year(now())=year(time)
						and bic=$branchbic
						group by bic,customer_id, name
						order by reward DESC
						LIMIT 6";
					$result=mysqli_query($conn,$sql);
					if(!$result)
					{	$Err=$Err.'Error retrieving top performers of current month';
					}
					else
					{	$top_cur_month_name=array();
						$top_cur_month_reward=array();
						for($i=1;$i<=mysqli_num_rows($result);$i++)
						{	$row=mysqli_fetch_array($result);
							$top_cur_month_name[$i]=$row['name'];	
							$top_cur_month_reward[$i]=$row['reward'];
						}
					}
				}
			}
		}
	?>
	<div id='topbox'>
	<img id='logo' src="Syndlogo.jpg">	
	<?php 
		
		if(empty($Err))
		{	echo "<h1 class='Tophead'>Branch: $branchbic </h1>";
			echo "<h1 class='Tophead'>Branch name: $branch_name </h1>";
		}
		else
		{	echo "<p>$Err</p>";
		}
		
	?>
	</div>
	<!-- <div class="albums_main"> -->
		<div class="albums">
			<h1 class="contenthead">Number of transactions today</h1>
			<hr>
			<div class='galleryitem'>
				<h1 class='subhead'>Total</h1>
				<h1 class='txn_count'><?php echo $total_txn_count; ?></h1>
			</div>
			<div class='galleryitem'>
				<h1 class='subhead'>Internet Banking</h1>
				<h1 class='txn_count'><?php echo $ib_txn_count; ?></h1>
			</div>
			<div class='galleryitem'>
				<h1 class='subhead'>Mobile Banking</h1>
				<h1 class='txn_count'><?php echo $mb_txn_count; ?></h1>
			</div>
			<div class='galleryitem'>
				<h1 class='subhead'>UPI</h1>
				<h1 class='txn_count'><?php echo $upi_txn_count; ?></h1>
			</div>
			<div class='galleryitem'>
				<h1 class='subhead'>POS</h1>
				<h1 class='txn_count'><?php echo $pos_txn_count; ?></h1>
			</div>
		</div>	
		<div class='mainleft'>
			<h1 class="contenthead">Top 3 performers of previous month</h1>
			<table>
				<tr><td>Position</td><td class='name'>Name</td><td>Rewards</td></tr>
				<?php 
				for($i=1;$i<=count($top_prev_month_name);$i++)
				{
					echo "<tr><td>$i</td><td class='name'>$top_prev_month_name[$i]</td><td>$top_prev_month_reward[$i]</td></tr>";
				}
				?>
			</table>
		</div>
		<div class='mainright'>
			<h1 class="contenthead">Top performers of current month</h1>
			<table>
				<tr><td>Position</td><td class='name'>Name</td><td>Rewards</td></tr>
				<?php 
				for($i=1;$i<=count($top_cur_month_name);$i++)
				{
					
					$rewards=round($top_cur_month_reward[$i],2);
					echo "<tr><td>$i</td><td class='name'>$top_cur_month_name[$i]</td><td>$rewards</td></tr>";
				}
				?>
			</table>
		</div>
<marquee>Always On, Always Connected*********Our e-services are always at your side**********Get rewarded each time you spend online</marquee>		
			
</body>
</html>