<?php
$Err='';
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
				{	$sql="SELECT * FROM rewards_mapping";
					$result=mysqli_query($conn,$sql);
					if(!$result)
         				{	$Err="Error executing query";
					}
					else
					{	$num_rows=mysqli_num_rows($result);
						if($num_rows==0)
						{	$Err='No mappings defined';
						}
						else
						{	$ib_rat=$mb_rat=$upi_rat=$pos_rat=0;
							for($i=1;$i<=$num_rows;$i++)
							{	$row=mysqli_fetch_array($result);
								switch($row['channel'])
								{	case 'IB':$ib_rat=$row['point_ratio'];
										  break;
									case 'MB':$mb_rat=$row['point_ratio'];
										  break;
									case 'UPI':$upi_rat=$row['point_ratio'];
										  break;
									case 'POS':$pos_rat=$row['point_ratio'];
										  break;
								}
							}
							$sql1='SELECT seqno,amount,channel from cons_txn_info'; 
							$result1=mysqli_query($conn,$sql1);
							if(!$result1)
         						{	$Err="Error executing query 2";
							}
							else
							{	for($i=0;$i<mysqli_num_rows($result1);$i++)
								{	$row1=mysqli_fetch_array($result1);
									$reward=0;
									switch($row1['channel'])
									{	case 'IB':$reward=$ib_rat*$row1['amount'];
										 	 break;
										case 'MB':$reward=$mb_rat*$row1['amount'];
										  	break;
										case 'UPI':$reward=$upi_rat*$row1['amount'];
										  	break;
										case 'POS':$reward=$pos_rat*$row1['amount'];
										  	break;
									}
									$seqno=$row1['seqno'];
									$sql2="UPDATE cons_txn_info SET reward=$reward WHERE seqno=$seqno";
									$result2=mysqli_query($conn,$sql2);
									if(!$result2)
         								{	$Err="Error updating txn seq no $seqno";
									}
								}
							}
						}
					}
				}
			}
		}
	

echo $Err;
?>
									