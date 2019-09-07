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
				{	$sql1="DELETE FROM top_perf_monthly";
					$result1=mysqli_query($conn,$sql1);
					if(!$result1)
					{	$Err='Error in deleting the rows';
					}
					else
					{
						$sql2="INSERT INTO top_perf_monthly(bic,customer_id,name,reward) 
						select bic,customer_id, name, sum(reward) from cons_txn_info
						where month(time)=(month(now())-1)
						and year(now())=year(time)
						group by bic,customer_id, name";
						$result2=mysqli_query($conn,$sql2);
						if(!$result2)
						{	$Err='Error in inserting the rows';
						}
					}	
						
					
				}
			}
		}
echo $Err;
?>