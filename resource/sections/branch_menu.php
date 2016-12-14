<div id='cssmenu'>
	<ul>
		<li <?php if($page_type==1) echo "class='active'"; ?> ><a href='index.php'>Home</a></li>
		<li <?php if($page_type==2) echo "class='active'"; ?> ><a href='users.php'>Users</a></li>
		<li <?php if($page_type==3) echo "class='active has-sub'"; else echo "class='has-sub'"; ?> ><a href='#'>Clients</a>
			<ul>
				<li><a href='clients.php'>Clients</a></li>
				<li><a href='clients_new.php'>New Client</a></li>
			</ul>
		</li>
		
		<li <?php if($page_type==4) echo "class='active has-sub'"; else echo "class='has-sub'"; ?> ><a href='#'>Inventory</a>
			<ul>
				<li><a href='inventory.php'>Available Inventory</a></li>
				<li><a href='inventory.php?type=4'>Borrowed Inventory</a></li>
				<li><a href='inventory.php?type=3'>Sold Inventory</a></li>
				<li><a href='inventory.php?type=5'>Repair Requests</a></li>		
				<li><a href='inventory.php?type=6'>Repair Released</a></li>
				<li><a href='inventory_all.php'>All Inventory</a></li>
			</ul>
		</li>		
		<li <?php if($page_type==5) echo "class='active has-sub'"; else echo "class='has-sub'"; ?> ><a href='#'>Transactions</a>
			<ul>
				<li><a href='transactions.php'>Transactions History</a></li>
				<li class='active has-sub'><a href='#'> New Buy/Sell</a>
					<ul>
						<li><a href='t_receiving.php'>New Buy (Receive)</a></li>
						<li><a href='t_sales.php'>New Sell</a></li>
					</ul>
				</li>
				<li class='active has-sub'><a href='#'> New Borrow/Return</a>
					<ul>
						<li><a href='t_borrow.php'>New Borrow</a></li>
						<li><a href='t_return.php'>New Return</a></li>
					</ul>
				</li>
				<li class='active has-sub'><a href='#'> New Repair/Release</a>
					<ul>
						<li><a href='t_repair.php'>New Repair Request</a></li>
						<li><a href='t_release.php'>New Release</a></li>
					</ul>
				</li>
			</ul>
		</li>
		<li <?php if($page_type==7) echo "class='active has-sub'"; else echo "class='has-sub'"; ?> ><a href='#'>Payments</a>
			<ul>
				<li><a href='payments.php'>Payments</a></li>
				<li><a href='payments_new.php'>New Payment</a></li>
			</ul>
		</li>
		<li <?php if($page_type==8) echo "class='active has-sub'"; else echo "class='has-sub'"; ?> ><a href='#'>myTangent</a>
			<ul>
				<li><a href='about_profile.php'>My Profile</a></li>
				<li><a href='about.php'>About</a></li>
			</ul>
		</li>
	</ul>
</div>