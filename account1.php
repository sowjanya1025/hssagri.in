<?php 
include'db_connect.php';
class account extends db_connect
{
    public function __construct()
    {
        parent::__construct();
    }
	public function setSession($ID,$email)
	{
		$_SESSION['user_id'] = $ID;
		$_SESSION['user_email'] = $email;
	}
	public  function getCurrentUserId()
    {
		//echo $_SESSION['user_id'];
        if (isset($_SESSION) && isset($_SESSION['user_id'])) {

            return $_SESSION['user_id'];

        } else {

            return 0;
        }
    }
    public function signup($username,$password,$mobile,$email)
    {
        $stmt = $this->db->prepare("insert into `users`(`username`, `password`, `mobile`, `email`) values (:username,:password,:mobile,:email)");
        $stmt->bindParam(':username',$username,PDO::PARAM_STR);
        $stmt->bindParam(':password',md5($password),PDO::PARAM_STR);
        $stmt->bindParam(':mobile',$mobile,PDO::PARAM_INT);
        $stmt->bindParam(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
    }

    public function checkemail_availability($emailid)
    {
        $result='';
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = (:email) LIMIT 1");
        $stmt->bindParam(':email', $emailid, PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $result= 1; // same email exists
            }else
            {
                $result = 0;
            }
        }
        return $result;
    }
	
	
	public function login($email,$pswd)
	{
		 $access_data = '';
		$stmt = $this->db->prepare("select * from users where email = (:email) limit 1");
		$stmt->bindParam(':email',$email,PDO::PARAM_STR);
		if($stmt->execute())
		{
			if($stmt->rowCount()>0)
			{
				$row = $stmt->fetch();
				$hashFromDatabase = $row['password'];
				$userEnteredPasswort = $pswd;
				if ($hashFromDatabase === md5($userEnteredPasswort))
				 {
				 	$message="<label class='text-danger'>Valid</label>";
					$access_data = array("error" => false,
                                     	 "message" => $message,
										 "accountID"=>$row['id'],
										 "email"=>$row['email']);

				 }else
				 {
				 	$message="<label class='text-danger'>Invalid email/password</label>";
					$access_data = array("error" => true,
                                     	 "message" => $message);
				 }
			}else
			{
				 	$message="<label class='text-danger'>Invalid email/password</label>";
					$access_data = array("error" => true,
                                     	 "message" => $message);
			}
		}
		
		 return $access_data;
	}
	
	
	public function setfarmer_Onboarding($fname,$contact,$email,$pan,$adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename,$acctid)
	{
		$result = '';
		//  code to fetch serail number and increment by 1//
		$qry = $this->db->prepare("select fr_code from farmer_onboarding order by id desc limit 1");
		if($qry->execute())
		{
			if($qry->rowCount() > 0)
			{
				$row = $qry->fetch();
				$serialno = $row['fr_code'];
				$number = (int)substr($serialno, 1); // Extract the numeric part
        		$newNumber = $number + 1;
				$newserailno =  sprintf('F%03d',$newNumber);
				//return 'C' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // 
			}
			else
		  {
			$newserailno = 'F001';
		  }
		}
		// end code for serial number //
		$stmt=$this->db->prepare("INSERT INTO `farmer_onboarding`(user_id,`fr_name`,`fr_code` , `fr_contact`, `fr_email`, `fr_pan`, `fr_adhar`, `fr_image`,`fr_kyc`,`fr_bank_acctholdername`, `fr_bank_acctnumber`, `fr_bank_ifsccode`, `fr_bank_branchname`, `fr_bank_cancelcheq`) VALUES (:userid,:fname,:fcode,:fcontact,:femail,:fpan,:fadhar,:fimage,:fkyc,:acctname,:acctnumber,:ifsccode,:branchname,:cancelcheqnewfilename)");
		$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		$stmt->bindParam(":fname", $fname, PDO::PARAM_STR);
		$stmt->bindParam(":fcode", $newserailno, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":fcontact", $contact, PDO::PARAM_INT);
		$stmt->bindParam(":femail", $email, PDO::PARAM_STR);
		$stmt->bindParam(":fpan", $pan, PDO::PARAM_STR);
		$stmt->bindParam(":fadhar", $adhar, PDO::PARAM_STR); 
		$stmt->bindParam(":fimage", $newfilename, PDO::PARAM_STR); 
		$stmt->bindParam(":fkyc", $kycFilesSerialized, PDO::PARAM_STR); 
		$stmt->bindParam(":acctname", $acctname, PDO::PARAM_STR);
		$stmt->bindParam(":acctnumber", $acctnumber, PDO::PARAM_STR);
		$stmt->bindParam(":ifsccode", $ifsccode, PDO::PARAM_STR);
		$stmt->bindParam(":branchname", $branchname, PDO::PARAM_STR);
		$stmt->bindParam(":cancelcheqnewfilename", $cancelcheqnewfilename, PDO::PARAM_STR);

		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
/*	public function setfarmer_Onboarding_kyc($newfilename,$lastinsert,$accountId)
	{
		if($lastinsert!="")
		{
			$stmt =  $this->db->prepare("INSERT INTO `farmer_kyc`(`kyc_doc`, `farmer_onboarding_id`, `users_id`)VALUES(:kyc,:lastid,:id)");
			$stmt->bindParam(":kyc",$newfilename,PDO::PARAM_STR);
			$stmt->bindParam(":lastid",$lastinsert,PDO::PARAM_STR);
			$stmt->bindParam(":id",$accountId,PDO::PARAM_STR);
			$stmt->execute();
		}
	}
*/	public function updatefarmer_Onboarding($fid,$fname,$contact,$email,$pan,$adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename)
	{
		
		if($fid!="")
		{
			//echo "here dss";
			$stmt = $this->db->prepare("UPDATE `farmer_onboarding`
			 							SET `fr_name`=(:name),`fr_contact`=(:contact),`fr_email`=(:email),`fr_pan`=(:pan),
										`fr_adhar`=(:adhar),`fr_image`=(:image),`fr_kyc`=(:kyc),`fr_bank_acctholdername`=(:acctholdername), `fr_bank_acctnumber`=(:acctnumber),`fr_bank_ifsccode`=(:ifsccode),`fr_bank_branchname`=(:branchname),`fr_bank_cancelcheq`=(:cancelcheq) WHERE id = (:id) ");
			$stmt->bindParam(":name", $fname, PDO::PARAM_STR);
			$stmt->bindParam(":contact", $contact, PDO::PARAM_INT);
			$stmt->bindParam(":email", $email, PDO::PARAM_STR);
			$stmt->bindParam(":pan", $pan, PDO::PARAM_STR);
			$stmt->bindParam(":adhar", $adhar, PDO::PARAM_STR);
			$stmt->bindParam(":image", $newfilename, PDO::PARAM_STR);
			$stmt->bindParam(":kyc", $kycFilesSerialized, PDO::PARAM_STR);
			$stmt->bindParam(":acctholdername", $acctname, PDO::PARAM_STR);
			$stmt->bindParam(":acctnumber", $acctnumber, PDO::PARAM_STR);
			$stmt->bindParam(":ifsccode", $ifsccode, PDO::PARAM_STR);
			$stmt->bindParam(":branchname", $branchname, PDO::PARAM_STR);
			$stmt->bindParam(":cancelcheq", $cancelcheqnewfilename, PDO::PARAM_STR);
			$stmt->bindParam(":id", $fid, PDO::PARAM_INT);
			$stmt->execute();
		}
		
	}
	public function getfarmer_OnBoardingData($acctid)
	{
		$result=[];
		$stmt = $this->db->prepare("select * from farmer_onboarding where user_id = (:userid)");
		$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("fname"=>$frow['fr_name'], 
									"fcode"=>$frow['fr_code'],
									"fmobile"=>$frow['fr_contact'],
									"fadhar"=>$frow['fr_adhar'],
									"fimage"=>$frow['fr_image'],
									"id"=>$frow['id']);
				}
			}
		}
		return $result;
	
	}
	public function getAllfarmers_OnBoardingData()
	{
		$result=[];
		$stmt = $this->db->prepare("select * from farmer_onboarding");
		//$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("fname"=>$frow['fr_name'], 
									"fcode"=>$frow['fr_code'],
									"fmobile"=>$frow['fr_contact'],
									"fadhar"=>$frow['fr_adhar'],
									"fimage"=>$frow['fr_image'],
									"id"=>$frow['id']);
				}
			}
		}
		return $result;
	
	}
	public function getfarmer_OnBoardingData_ById($id)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT * FROM `farmer_onboarding` where id=(:id)");
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$result = $stmt->fetch();
			}
		}
		return $result;
	
	}


	public function setCompany_Onboarding($c_name,$c_pan,$c_reg,$c_gst,$c_adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$accountId)
	{
		$result = '';
		//  code to fetch serail number and increment by 1//
		$qry = $this->db->prepare("select cm_code from company_onboarding  order by id desc limit 1");
		if($qry->execute())
		{
			if($qry->rowCount() > 0)
			{
				$row = $qry->fetch();
				$serialno = $row['cm_code'];
				$number = (int)substr($serialno, 1); // Extract the numeric part
        		$newNumber = $number + 1;
				$newserailno =  sprintf('C%03d',$newNumber);
				//return 'C' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // 
			}
			else
		  {
			$newserailno = 'C001';
		  }
		}
		// end code for serial number //
		

		$stmt=$this->db->prepare("INSERT INTO `company_onboarding`(user_id,`cm_name`,`cm_code` , `cm_pan`, `cm_reg`, `cm_gst`, `cm_adhar`, `cm_cheque`, `cm_kyc`, `cm_bank_acctholdername`, `cm_bank_acctnumber`, `cm_bank_ifsccode`, `cm_bank_branchname`) VALUES (:userid,:cname,:ccode,:cpan,:creg,:cgst,:cadhar,:ccheque,:ckyc,:acctname,:acctnumber,:ifsccode,:branchname)");
		$stmt->bindParam(":userid", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":cname", $c_name, PDO::PARAM_STR);
		$stmt->bindParam(":ccode", $newserailno, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":cpan", $c_pan, PDO::PARAM_STR);
		$stmt->bindParam(":creg", $c_reg, PDO::PARAM_STR);
		$stmt->bindParam(":cgst", $c_gst, PDO::PARAM_STR);
		$stmt->bindParam(":cadhar", $c_adhar, PDO::PARAM_STR);
		$stmt->bindParam(":ccheque", $newfilename, PDO::PARAM_STR); 
		$stmt->bindParam(":ckyc", $kycFilesSerialized, PDO::PARAM_STR); 
		$stmt->bindParam(":acctname", $acctname, PDO::PARAM_STR);
		$stmt->bindParam(":acctnumber", $acctnumber, PDO::PARAM_STR);
		$stmt->bindParam(":ifsccode", $ifsccode, PDO::PARAM_STR);
		$stmt->bindParam(":branchname", $branchname, PDO::PARAM_STR);
		//$stmt->bindParam(":cancelcheqnewfilename", $cancelcheqnewfilename, PDO::PARAM_STR);
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
	
	public function setCompany_Onboarding_kyc($kycfilename,$lastinsert,$accountId)
	{
		if($lastinsert!="")
		{
			$stmt =  $this->db->prepare("INSERT INTO `company_kyc`(`kyc_doc`, `company_onboarding_id`, `users_id`)VALUES(:kyc,:lastid,:id)");
			$stmt->bindParam(":kyc",$kycfilename,PDO::PARAM_STR);
			$stmt->bindParam(":lastid",$lastinsert,PDO::PARAM_STR);
			$stmt->bindParam(":id",$accountId,PDO::PARAM_STR);
			$stmt->execute();
		}
	} 
	
	public function getcompany_OnBoardingData($acctid)
	{
		$result=[];
		$stmt = $this->db->prepare("select * from company_onboarding where user_id = (:userid)");
		$stmt->bindParam(":userid",$acctid,PDO::PARAM_STR);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("name"=>$frow['cm_name'], 
									"code"=>$frow['cm_code'],
									"gst"=>$frow['cm_gst'],
									"adhar"=>$frow['cm_adhar'],
									"pan"=>$frow['cm_pan'],
									"id"=>$frow['id']);
				}
			}
		}
		return $result;
	
	}
	public function getCompany_OnBoardingData_ById($id)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT * FROM `company_onboarding` where id=(:id)");
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$result = $stmt->fetch();
			}
		}
		return $result;
	
	}
	public function updateCompany_Onboarding($id,$c_name,$c_pan,$c_reg,$c_gst,$c_adhar,$newfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname)
	{
		
		if($id!="")
		{
			echo "here dss";
			$stmt = $this->db->prepare("UPDATE `company_onboarding` SET 				`cm_name`=(:name),`cm_pan`=(:pan),`cm_reg`=(:reg),`cm_gst`=(:gst),`cm_adhar`=(:adhar),`cm_cheque`=(:cheque),`cm_kyc`=(:kyc),`cm_bank_acctholdername`=(:acctholdername),`cm_bank_acctnumber`=(:acctnumber),`cm_bank_ifsccode`=(:ifsccode),`cm_bank_branchname`=(:branchname) WHERE id = (:id) ");
			$stmt->bindParam(":name", $c_name, PDO::PARAM_STR);
			$stmt->bindParam(":pan", $c_pan, PDO::PARAM_STR);
			$stmt->bindParam(":reg", $c_reg, PDO::PARAM_STR);
			$stmt->bindParam(":gst", $c_gst, PDO::PARAM_STR);
			$stmt->bindParam(":adhar", $c_adhar, PDO::PARAM_STR);
			$stmt->bindParam(":cheque", $newfilename, PDO::PARAM_STR);
			$stmt->bindParam(":kyc", $kycFilesSerialized, PDO::PARAM_STR);
			$stmt->bindParam(":acctholdername", $acctname, PDO::PARAM_STR);
			$stmt->bindParam(":acctnumber", $acctnumber, PDO::PARAM_STR);
			$stmt->bindParam(":ifsccode", $ifsccode, PDO::PARAM_STR);
			$stmt->bindParam(":branchname", $branchname, PDO::PARAM_STR);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
		}
		
	}
	public function delete_company_ById($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM company_onboarding  WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":id", $del_id, PDO::PARAM_INT);
		$stmt->execute();
	} 
	public function delete_farmer_ById($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM farmer_onboarding  WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":id", $del_id, PDO::PARAM_INT);
		$stmt->execute();
	} 
	public function delete_Item_ById($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM items  WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":id", $del_id, PDO::PARAM_INT);
		$stmt->execute();
	} 
	public function delete_Client_ById($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM client_onboarding  WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":id", $del_id, PDO::PARAM_INT);
		$stmt->execute();
	} 


	public function create_Item($itm_name,$item_category,$itm_code,$itm_qty,$itm_image,$acctid)
	{
		$stmt=$this->db->prepare("INSERT INTO `items`(user_id,item_category,`item_name`, `item_code`, `item_quantity`, `item_image`) VALUES (:userid,:cat,:name,:code,:qty,:image)");
		$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		$stmt->bindParam(":cat", $item_category, PDO::PARAM_INT);
		$stmt->bindParam(":name", $itm_name, PDO::PARAM_STR);
		$stmt->bindParam(":code", $itm_code, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":qty", $itm_qty, PDO::PARAM_STR);
		$stmt->bindParam(":image", $itm_image, PDO::PARAM_STR);
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
	public function get_create_ItemData()
	{
		$result=[];
		$stmt = $this->db->prepare("select * from items");
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("name"=>$frow['item_name'], 
									"code"=>$frow['item_code'],
									"image"=>$frow['item_image'],
									"id"=>$frow['id']);
				}
			}
		}
		return $result;
	
	}
	public function check_itemAvailability($code)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT * FROM `items` where item_code=(:id)");
		$stmt->bindParam(':id',$code,PDO::PARAM_STR);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$record = $stmt->fetch();
				$result = array('code'=>1,'itemid'=>$record['id']);
				//$result=array('err_code'=>0,'err_msg'=>'','user_id'=>$row['id']);
				//$result = 1; // found
			}else
			{
				///$result = array("error"=>false,"errordesc"=>"Available");
				$result = array('code'=>0,'itemid'=>'');
				//$result = 0; // not found
			}
		}
		return $result;
	}
	public function getAll_item_names()
	{
		$result=array();
		$stmt = $this->db->prepare("select item_name,id,item_code from items");
		//$searchTerm = '%' . $term . '%';
		//$stmt->bindParam(':searchterm',$searchTerm);
		//$stmt->bind_param('s', $searchTerm);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					//$result[] = array("name"=>$frow['item_name']);
					$result[] = ['id' => $frow['id'],'name' => $frow['item_name'], 'code' => $frow['item_code']];
				}
			}
		}
		return $result;
	
	}
	public function get_item_names($term)
	{
		$result=[];
		$stmt = $this->db->prepare("select item_name,id from items where item_name LIKE (:searchterm) ");
		$searchTerm = '%' . $term . '%';
		$stmt->bindParam(':searchterm',$searchTerm);
		//$stmt->bind_param('s', $searchTerm);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					//$result[] = array("name"=>$frow['item_name']);
					$result[] = ['label' => $frow['item_name'], 'value' => $frow['item_name']];
				}
			}
		}
		return $result;
	
		}
	public function get_item_codes($itemname)
	{
		$result=[];
		$stmt = $this->db->prepare("select item_code,id from items where item_name = (:itemname) ");
		$stmt->bindParam(':itemname',$itemname);
		//$stmt->bind_param('s', $searchTerm);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetch();
				//$result[] = array('item_code' => $rows['item_code']);
				$result = ['item_code' => $rows['item_code'], 'id' => $rows['id']];
			}
		}
		return $result;
	
		}
	public function update_resetToken($token,$expires,$femail)
	{
		
		if($femail!="")
		{
			//echo "here dss";
			$stmt = $this->db->prepare("UPDATE `users` SET `reset_token`=(:token),`reset_expires`=(:expires) WHERE email = (:femail) ");
			$stmt->bindParam(":token", $token, PDO::PARAM_STR);
			$stmt->bindParam(":expires", $expires, PDO::PARAM_STR);
			$stmt->bindParam(":femail", $femail, PDO::PARAM_STR);
			$stmt->execute();
		}
		
	}
	public function update_resetPassword($token,$password)
	{
		//echo "here1" ; 
		//echo $token; exit;// exit;
		if($token!="")
		{
			//echo "here dss";
			//echo "here2" ; exit;
			$stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token = (:token) AND reset_expires > NOW() limit 1 ");
			$stmt->bindParam(":token", $token, PDO::PARAM_STR);
			if($stmt->execute())
			{
				if($stmt->rowCount() > 0)
				{
					// Hash the new password using MD5
					$hashed_password = md5($password);
					//echo $hashed_password;
		
					// Update the password and clear the token
					$stmt = $this->db->prepare("UPDATE users SET password = (:hashed_pswd), reset_token = NULL, reset_expires = NULL WHERE reset_token = (:token) limit 1 ");
					$stmt->bindParam(":hashed_pswd", md5($hashed_password), PDO::PARAM_STR);
					$stmt->bindParam(":token", $token, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
		}
		
	}
	 public function setClient_Onboarding($clienttype,$clname,$contact,$email,$agreementcopynewfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename,$acctid)
	 {

		$result = '';
		switch ($clienttype) {
        case '1':
			$codetype = 'MT';
            break;
        case '2':
			$codetype = 'OR';
            break;
        case '3':
		   $codetype = 'GT';
            break;
        case '4':
			$codetype = 'RT';
            break;
           }
		//  code to fetch serail number and increment by 1//
		$qry = $this->db->prepare("select cl_code from client_onboarding where cl_clienttype = (:clienttype) order by id desc limit 1");
		$qry->bindParam(":clienttype", $clienttype, PDO::PARAM_INT);
		if($qry->execute())
		{
			if($qry->rowCount() > 0)
			{
				//echo "if cont";
				$row = $qry->fetch();
				$serialno = $row['cl_code'];
				$number = (int)substr($serialno, 2); // Extract the numeric part
        		$newNumber = $number + 1;
				$newserailno =  sprintf($codetype.'%03d',$newNumber);
				//return 'C' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // 
			}
			else
		  {
			//echo "else cont";
			$newserailno = $codetype.'001';
		  }
		}
		// end code for serial number //
		//echo $newserailno;
		//exit;
		$stmt=$this->db->prepare("INSERT INTO `client_onboarding`(user_id,`cl_clienttype`, `cl_code`,cl_name, `cl_mobile`, `cl_email`, `cl_kyc`, `cl_agreementcopy`, `cl_bank_acctholdername`, `cl_bank_acctnumber`, `cl_bank_ifsccode`, `cl_bank_branchname`, `cl_bank_cancelcheq`)
		 VALUES (:userid,:clienttype,:fcode,:clname,:contact,:email,:kycFilesSerialized,:Agreementcopy,:acctname,:acctnumber,:ifsccode,:branchname,:cancelcheqnewfilename)");
		$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		$stmt->bindParam(":clienttype", $clienttype, PDO::PARAM_INT);
		$stmt->bindParam(":fcode", $newserailno, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":clname", $clname, PDO::PARAM_STR);
		$stmt->bindParam(":contact", $contact, PDO::PARAM_INT);
		$stmt->bindParam(":email", $email, PDO::PARAM_STR);
		$stmt->bindParam(":kycFilesSerialized", $kycFilesSerialized, PDO::PARAM_STR); 
		$stmt->bindParam(":Agreementcopy", $agreementcopynewfilename, PDO::PARAM_STR);
		$stmt->bindParam(":acctname", $acctname, PDO::PARAM_STR);
		$stmt->bindParam(":acctnumber", $acctnumber, PDO::PARAM_STR);
		$stmt->bindParam(":ifsccode", $ifsccode, PDO::PARAM_STR);
		$stmt->bindParam(":branchname", $branchname, PDO::PARAM_STR);
		$stmt->bindParam(":cancelcheqnewfilename", $cancelcheqnewfilename, PDO::PARAM_STR);
		 
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
		 } 
		 
	public function getClient_OnBoardingData($acctid,$type)
	{
		$result=[];
		$stmt = $this->db->prepare("select * from client_onboarding where user_id = (:userid) and cl_clienttype = (:type) ");
		$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		$stmt->bindParam(":type", $type, PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("name"=>$frow['cl_name'], 
									"code"=>$frow['cl_code'],
									"mobile"=>$frow['cl_mobile'],
									"email"=>$frow['cl_email'],
									"id"=>$frow['id']);
				}
			}
		}
		return $result;
	
	}
	public function getClient_OnBoardingData_ById($id)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT * FROM `client_onboarding` where id=(:id)");
		$stmt->bindParam(':id',$id,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$result = $stmt->fetch();
			}
		}
		return $result;
	
	}
	public function updateClient_Onboarding($id,$clname,$contact,$email,$agreementcopynewfilename,$kycFilesSerialized,$acctname,$acctnumber,$ifsccode,$branchname,$cancelcheqnewfilename)
	{
		
		if($id!="")
		{
/*			echo "here dss";
			echo $cancelcheqnewfilename;
			exit;
*/			$stmt = $this->db->prepare("UPDATE `client_onboarding` SET `cl_name`=(:name),`cl_mobile`=(:contact),`cl_email`=(:email),`cl_kyc`=(:kyc),`cl_agreementcopy`=(:agreementcopy),`cl_bank_acctholdername`=(:acctholdername),`cl_bank_acctnumber`=(:acctnumber),`cl_bank_ifsccode`=(:ifsccode),`cl_bank_branchname`=(:branchname),`cl_bank_cancelcheq`=(:cancelcheq) WHERE id = (:id)");
			$stmt->bindParam(":name", $clname, PDO::PARAM_STR);
			$stmt->bindParam(":contact", $contact, PDO::PARAM_INT);
			$stmt->bindParam(":email", $email, PDO::PARAM_STR);
			$stmt->bindParam(":agreementcopy", $agreementcopynewfilename, PDO::PARAM_STR);
			$stmt->bindParam(":kyc", $kycFilesSerialized, PDO::PARAM_STR);
			$stmt->bindParam(":acctholdername", $acctname, PDO::PARAM_STR);
			$stmt->bindParam(":acctnumber", $acctnumber, PDO::PARAM_STR);
			$stmt->bindParam(":ifsccode", $ifsccode, PDO::PARAM_STR);
			$stmt->bindParam(":branchname", $branchname, PDO::PARAM_STR);
			$stmt->bindParam(":cancelcheq", $cancelcheqnewfilename, PDO::PARAM_STR);
			$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			$stmt->execute();
		}
		
	}
	
	public function getCategories()
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT * FROM `items_categories` ");
		//$stmt->bindParam(':id',$id,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'categoryname'=>$rows['category_name']);
				}
			}
		}
		return $result;
	}
	
	public function setGoodsReceive_note($accountId,$farmer_id,$collection_center,$itemname,$code,$itemcodeid,$f_price,$f_quaty,$f_totamt)
	{
		
		$stmt=$this->db->prepare("INSERT INTO `goods_receive_note`(user_id,`farmers_id`,`collection_center`,`item_name`, `items_code`, items_code_id, `quantity`, `price`,`totalamt`) VALUES (:userid,:farmers_id,:collection_center,:item_name,:items_code,:items_code_id,:quantity,:price,:totalamt)");
		
		$stmt->bindParam(":userid", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":farmers_id", $farmer_id, PDO::PARAM_INT);
		$stmt->bindParam(":collection_center", $collection_center, PDO::PARAM_STR);
		$stmt->bindParam(":item_name", $itemname, PDO::PARAM_STR);
		$stmt->bindParam(":items_code", $code, PDO::PARAM_STR);
		$stmt->bindParam(":items_code_id", $itemcodeid, PDO::PARAM_INT);
		$stmt->bindParam(":quantity", $f_quaty, PDO::PARAM_STR); 
		$stmt->bindParam(":price", $f_price, PDO::PARAM_STR);
		$stmt->bindParam(":totalamt", $f_totamt, PDO::PARAM_STR);
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
	public function setGoods_SupplyBill($accountId,$collection_center,$clientlist,$names_list,$itemname,$code,$itemcodeid,$quantity,$price,$total)
	{
		$stmt=$this->db->prepare("INSERT INTO `goods_supply_bill`(`user_id`, `collection_center`, `client_type`, `clients_id`, `item_name`, `items_code`, `items_code_id`, `quantity`, `price`, `totalamt`)VALUES (:userid,:collection_center,:clientlist,:names_list,:itemname,:code,:itemcodeid,:quantity,:price,:total)");
		
		$stmt->bindParam(":userid", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":collection_center", $collection_center, PDO::PARAM_STR);
		$stmt->bindParam(":clientlist", $clientlist, PDO::PARAM_INT);
		$stmt->bindParam(":names_list", $names_list, PDO::PARAM_INT);
		$stmt->bindParam(":itemname", $itemname, PDO::PARAM_STR);
		$stmt->bindParam(":code", $code, PDO::PARAM_STR);
		$stmt->bindParam(":itemcodeid", $itemcodeid, PDO::PARAM_INT);
		$stmt->bindParam(":quantity", $quantity, PDO::PARAM_STR); 
		$stmt->bindParam(":price", $price, PDO::PARAM_STR);
		$stmt->bindParam(":total", $total, PDO::PARAM_STR);
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
	public function getGoodsReceive_note($acctid)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT grn.*,fb.fr_name , itm.item_code,itm.item_name FROM `goods_receive_note` grn
left join farmer_onboarding fb on fb.id = grn.farmers_id
left join items itm on itm.id = grn.items_code_id where grn.user_id = (:userid);");
		$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'farmers_name'=>$rows['fr_name'],'item_code'=>$rows['item_code'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['item_name']);
				}
			}
		}
		return $result;
	}
	public function getGoods_SupplyBill($acctid)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT gsb.*,clb.cl_name,clb.cl_clienttype,itm.item_code,itm.item_name FROM `goods_supply_bill` gsb
left join  client_onboarding clb on clb.id = gsb.clients_id
left join items itm on itm.id = gsb.items_code_id where gsb.user_id = (:userid);");
		$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'clients_name'=>$rows['cl_name'],'item_code'=>$rows['item_code'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'client_type'=>$rows['cl_clienttype'],'item_name'=>$rows['item_name']);
				}
			}
		}
		return $result;
	}
	public function getGoodsReceive_noteByID($acctid,$pid)
	{
		//$result=[];
		$stmt = $this->db->prepare("SELECT grn.*,fb.fr_name ,fb.fr_code as frcode , itm.item_code,itm.item_name FROM `goods_receive_note` grn
left join farmer_onboarding fb on fb.id = grn.farmers_id
left join items itm on itm.id = grn.items_code_id where grn.user_id = (:userid) and grn.id = (:id);");
		$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		$stmt->bindParam(':id',$pid,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetch();
				//foreach($row as $rows)
				//{
					$result = array('id'=>$rows['id'],'farmers_name'=>$rows['fr_name'],'item_code'=>$rows['item_code'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['item_name'],'frcode'=>$rows['frcode'],'collection_center'=>$rows['collection_center']);
				//}
			}
		}
		return $result;
	}
	public function getGoods_SupplyBillByID($acctid,$pid)
	{
		//$result=[];
		$stmt = $this->db->prepare("SELECT gsb.*,clb.cl_name ,clb.cl_code as clcode,clb.cl_clienttype , itm.item_code,itm.item_name FROM `goods_supply_bill` gsb
left join  client_onboarding clb on clb.id = gsb.clients_id
left join items itm on itm.id = gsb.items_code_id where gsb.user_id = (:userid) and gsb.id = (:id);");
		$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		$stmt->bindParam(':id',$pid,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetch();
				//foreach($row as $rows)
				//{
					$result = array('id'=>$rows['id'],'clients_name'=>$rows['cl_name'],'item_code'=>$rows['item_code'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['item_name'],'clcode'=>$rows['clcode'],'collection_center'=>$rows['collection_center'],'client_type'=>$rows['cl_clienttype']);
				//}
			}
		}
		return $result;
	}

		public function delete_GoodsReceive_note($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM goods_receive_note  WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":id", $del_id, PDO::PARAM_INT);
		$stmt->execute();
	} 
		public function deleteGoods_SupplyBill($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM goods_supply_bill  WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":id", $del_id, PDO::PARAM_INT);
		$stmt->execute();
	} 
	public function approvalstatus_GoodsReceive_note($status,$id)
	{
		$stmt=$this->db->prepare("update goods_receive_note  set approval_status = (:status) WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":status", $status, PDO::PARAM_INT);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
	} 
	public function approvalstatus_Good_SupplyBill($status,$id)
	{
		$stmt=$this->db->prepare("update goods_supply_bill  set approval_status = (:status) WHERE id = (:id) LIMIT 1");
		$stmt->bindParam(":status", $status, PDO::PARAM_INT);
		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
		$stmt->execute();
	} 
	public function getClientList($clientType)
	{
		$names_list = "<option value='' disabled='disabled' selected='selected'>-Select Clientname-</option>";
		switch ($clientType) {
        case '1':
            $list = "modern";
			$c_type = 1;
            break;
        case '2':
            $list = "oraca";
			$c_type = 2;
            break;
        case '3':
           $list = "general";
		   $c_type = 3;
            break;
        case '4':
            $list = "retail";
			$c_type = 4;
            break;
           }
					$stmt = $this->db->prepare("SELECT * FROM `client_onboarding` WHERE cl_clienttype = (:ctype)");
					$stmt->bindParam(":ctype", $c_type, PDO::PARAM_INT);
					if($stmt->execute())
					{
						if($stmt->rowCount() > 0)
						{
							$rows = $stmt->fetchAll();
							foreach($rows as $frow)
							{
												
								 $names_list.= "<option value='".$frow['id']."'>".$frow['cl_name']."</option>";
							}
						}
					}
		 return $names_list;
	}
	
}// final end





