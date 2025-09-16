<?php 
// Add PhpSpreadsheet use statements at the top level
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include'db_connect.php'; 
class account extends db_connect
{
    public $superAdmin;
	 public $accessModules = [];
	public function __construct()
    {
        parent::__construct();
		$CurrentEmailId=$this->getCurrentUserEmailId(); 
		$this->superAdmin = $this->hasFullAccess($CurrentEmailId); //  to check if the user has full access(db.php page ADMIN_EMAILS constant)
		$this->accessModules = $this->getAccessModules($CurrentEmailId);
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
	 static function getCurrentUserEmailId()
    {
        if (isset($_SESSION) && isset($_SESSION['user_email'])) {

            return $_SESSION['user_email'];

        } else {

            return 'undefined';
        }
    }
	public function hasFullAccess($acctemail)
	{
		/*if (in_array($acctemail, ADMIN_EMAILS))
		  {
		  echo "Match found";
		  }
		else
		  {
		  echo "Match not found";
		  }*/
		  return in_array($acctemail, ADMIN_EMAILS);
  	}
	 public function getAccessModules($acctemail)
    {
        if (defined('ROLES') && array_key_exists($acctemail, ROLES)) {
            return ROLES[$acctemail];
        }
        return []; // No access by default
    }
	public function hasAccess($module)
    {
        return in_array($module, $this->accessModules);
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
		//$CurrentEmailId=$this->getCurrentUserEmailId(); 
		//if($this->hasFullAccess($CurrentEmailId)) 
		if($this->superAdmin)
		{
			$stmt = $this->db->prepare("select * from farmer_onboarding");
		}else
		{
			$stmt = $this->db->prepare("select * from farmer_onboarding where user_id = (:userid)");
			$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		}
		
		//$stmt = $this->db->prepare("select * from farmer_onboarding where user_id = (:userid)");
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
		//$stmt = $this->db->prepare("select * from company_onboarding where user_id = (:userid)");
		if($this->superAdmin)
		{
			$stmt = $this->db->prepare("select * from company_onboarding");
		}else
		{
			$stmt = $this->db->prepare("select * from company_onboarding where user_id = (:userid)");
			$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		}
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
	
	public function delete_Apartment_ById($del_id)
	{
		$stmt=$this->db->prepare("DELETE FROM locations  WHERE id = (:id) LIMIT 1");
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
		$stmt = $this->db->prepare("select  i.id as id_no, i.item_name, i.item_code,i.item_category, i.item_image,i.id,c.category_name
		 , i.kannada_name  from items i
left join items_categories c ON
i.item_category = c.id order by c.id, i.id;");
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("name"=>$frow['item_name'], 
									"kannada_name"=>$frow['kannada_name'], 
									"code"=>$frow['item_code'],
									"image"=>$frow['item_image'],
									"id"=>$frow['id'],
									"id_no"=>$frow['id_no'],
									"category_name"=>$frow['category_name']);
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
		if($this->superAdmin)
		{
			$stmt = $this->db->prepare("select * from client_onboarding where cl_clienttype = (:type)");
		}else
		{
			$stmt = $this->db->prepare("select * from client_onboarding where user_id = (:userid) and cl_clienttype = (:type)");
			$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		}
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
	
	/*public function setGoodsReceive_note($accountId,$farmer_id,$collection_center,$itemname,$code,$itemcodeid,$f_price,$f_quaty,$f_totamt)
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
	
	}*/
	public function setGoodsReceive_note($accountId,$collection_center,$vendors_list,$farmersname,$billnumber,$newfilename,$transportation,$otherExpenses,$totalamt)
	{
		
		$stmt=$this->db->prepare("INSERT INTO `goods_receive_note`(`user_id`, `collection_center`, `vendors_type`, `farmers_id`,`bill_number`, `upload_bill`, `transportation`, `other_expenses`, `totalamt`) VALUES (:userid,:collection_center,:vendors_type,:farmers_id,:bill_number,:upload_bill,:transportation,:other_expenses,:totalamt)");
		
		$stmt->bindParam(":userid", $accountId, PDO::PARAM_INT);
		//$stmt->bindParam(":farmers_id", $farmer_id, PDO::PARAM_INT);
		$stmt->bindParam(":collection_center", $collection_center, PDO::PARAM_STR);
		$stmt->bindParam(":vendors_type", $vendors_list, PDO::PARAM_INT);
		$stmt->bindParam(":farmers_id", $farmersname, PDO::PARAM_INT);
		$stmt->bindParam(":bill_number", $billnumber, PDO::PARAM_STR);
		$stmt->bindParam(":upload_bill", $newfilename, PDO::PARAM_STR);
		$stmt->bindParam(":transportation", $transportation, PDO::PARAM_STR); 
		$stmt->bindParam(":other_expenses", $otherExpenses, PDO::PARAM_STR);
		$stmt->bindParam(":totalamt", $totalamt, PDO::PARAM_STR);
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
	public function setGRN_items($insertedid,$itemId, $quantity, $price)
	{
		
		$stmt=$this->db->prepare("INSERT INTO `grn_items`(`goods_receive_note_id`, `item_id`,`quantity`, `price`) VALUES (:goods_receive_note_id,:itemId,:quantity,:price)");
		
		$stmt->bindParam(":goods_receive_note_id", $insertedid, PDO::PARAM_INT);
		$stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
		$stmt->bindParam(":quantity", $quantity, PDO::PARAM_STR);
		$stmt->bindParam(":price", $price, PDO::PARAM_STR);
		$stmt->execute();
	
	}
	
	/*public function setGoods_SupplyBill($accountId,$collection_center,$clientlist,$names_list,$itemname,$code,$itemcodeid,$quantity,$price,$total)
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
	
	}*/
public function setGoods_SupplyBill($accountId,$collection_center,$clientlist,$names_list,$billnumber,$newfilename,$transportation,$otherExpenses,$total)
	{
		$stmt=$this->db->prepare("INSERT INTO `goods_supply_bill`(`user_id`, `collection_center`, `client_type`, `clients_id`,`bill_number`, `upload_bill`, `transportation`, `other_expenses`,`totalamt`)VALUES 
		(:userid,:collection_center,:clientlist,:names_list,:bill_number,:upload_bill,:transportation,:other_expenses,:total)");
	
		
		$stmt->bindParam(":userid", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":collection_center", $collection_center, PDO::PARAM_STR);
		$stmt->bindParam(":clientlist", $clientlist, PDO::PARAM_INT);
		$stmt->bindParam(":names_list", $names_list, PDO::PARAM_INT);
		$stmt->bindParam(":bill_number", $billnumber, PDO::PARAM_STR);
		$stmt->bindParam(":upload_bill", $newfilename, PDO::PARAM_STR);
		$stmt->bindParam(":transportation", $transportation, PDO::PARAM_STR); 
		$stmt->bindParam(":other_expenses", $otherExpenses, PDO::PARAM_STR);
		$stmt->bindParam(":total", $total, PDO::PARAM_STR);
		if($stmt->execute())
		{
			$lastID = $this->db->lastInsertId();
			
		}
		 $result = array('insert_last_id'=>$lastID);	
		return $result;
	
	}
	public function setGSB_items($insertedid,$itemId, $quantity, $price)
	{
		
		$stmt=$this->db->prepare("INSERT INTO `gsb_items`(`goods_supply_bill_id`, `item_id`, `quantity`, `price`) VALUES (:goods_supply_bill_id,:itemId,:quantity,:price)");
		
		$stmt->bindParam(":goods_supply_bill_id", $insertedid, PDO::PARAM_INT);
		$stmt->bindParam(":itemId", $itemId, PDO::PARAM_INT);
		$stmt->bindParam(":quantity", $quantity, PDO::PARAM_STR);
		$stmt->bindParam(":price", $price, PDO::PARAM_STR);
		$stmt->execute();
	
	}
	
		/*public function getGoodsReceive_note($acctid)
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
	}*/
	public function getGoodsReceive_note($acctid)
	{
		$result=[];
		if($this->superAdmin)
		{
			$stmt = $this->db->prepare("SELECT grn.id,grn.approval_status,grn.regdate,grn.`bill_number`,grn.transportation,grn.other_expenses,grn.totalamt,GROUP_CONCAT(gitems.item_id) as itemids ,GROUP_CONCAT(itm.item_name) as itemnames,GROUP_CONCAT(itm.item_code) as itemcode, 
CASE 
        WHEN grn.vendors_type = 1 THEN farmer.fr_name
        WHEN grn.vendors_type = 2 THEN company.cm_name
    END AS fr_name
    FROM `goods_receive_note` grn 
LEFT JOIN 
    farmer_onboarding farmer ON grn.vendors_type = 1 AND grn.farmers_id = farmer.id
LEFT JOIN 
    company_onboarding company ON grn.vendors_type = 2 AND grn.farmers_id = company.id
left join grn_items gitems on grn.id = gitems.goods_receive_note_id
left join items itm on gitems.item_id = itm.id
GROUP BY grn.id order by grn.id desc;");
		}else
		{
			$stmt = $this->db->prepare("SELECT grn.id,grn.approval_status,grn.regdate,grn.`bill_number`,grn.transportation,grn.other_expenses,grn.totalamt,GROUP_CONCAT(gitems.item_id) as itemids ,GROUP_CONCAT(itm.item_name) as itemnames,GROUP_CONCAT(itm.item_code) as itemcode, 
CASE 
        WHEN grn.vendors_type = 1 THEN farmer.fr_name
        WHEN grn.vendors_type = 2 THEN company.cm_name
    END AS fr_name
    FROM `goods_receive_note` grn 
LEFT JOIN 
    farmer_onboarding farmer ON grn.vendors_type = 1 AND grn.farmers_id = farmer.id
LEFT JOIN 
    company_onboarding company ON grn.vendors_type = 2 AND grn.farmers_id = company.id
left join grn_items gitems on grn.id = gitems.goods_receive_note_id
left join items itm on gitems.item_id = itm.id
 where grn.user_id = (:userid) GROUP BY grn.id order by grn.id desc;");
			$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		}
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'farmers_name'=>$rows['fr_name'],'item_code'=>$rows['itemcode'],'billnumber'=>$rows['bill_number'],'transportation'=>$rows['transportation'],'otherexpenses'=>$rows['other_expenses'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['itemnames'],'regdate'=>$rows['regdate']);
				}
			}
		}
		return $result;
	}
	/*public function getGoods_SupplyBill($acctid)
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
	}*/
	public function getGoods_SupplyBill($acctid)
	{
		$result=[];
		if($this->superAdmin)
		{
			$stmt = $this->db->prepare("SELECT gsb.id,gsb.approval_status,gsb.regdate,gsb.`bill_number`,gsb.transportation,gsb.other_expenses,gsb.totalamt,GROUP_CONCAT(gitems.item_id) as itemids ,GROUP_CONCAT(itm.item_name) as itemnames,GROUP_CONCAT(itm.item_code) as itemcode,clb.cl_name,clb.cl_clienttype
    FROM goods_supply_bill gsb
left join  client_onboarding clb on clb.id = gsb.clients_id
left join gsb_items gitems on gsb.id = gitems.goods_supply_bill_id
left join items itm on gitems.item_id = itm.id
GROUP BY gsb.id order by gsb.id desc;");
		}else
		{
			$stmt = $this->db->prepare("SELECT gsb.id,gsb.approval_status,gsb.regdate,gsb.`bill_number`,gsb.transportation,gsb.other_expenses,gsb.totalamt,GROUP_CONCAT(gitems.item_id) as itemids ,GROUP_CONCAT(itm.item_name) as itemnames,GROUP_CONCAT(itm.item_code) as itemcode,clb.cl_name,clb.cl_clienttype
    FROM goods_supply_bill gsb
left join  client_onboarding clb on clb.id = gsb.clients_id
left join gsb_items gitems on gsb.id = gitems.goods_supply_bill_id
left join items itm on gitems.item_id = itm.id
 where gsb.user_id = (:userid) GROUP BY gsb.id order by gsb.id desc;");
			$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		}

		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'clients_name'=>$rows['cl_name'],'item_code'=>$rows['itemcode'],'billnumber'=>$rows['bill_number'],'transportation'=>$rows['transportation'],'otherexpenses'=>$rows['other_expenses'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'client_type'=>$rows['cl_clienttype'],'item_name'=>$rows['itemnames'],'regdate'=>$rows['regdate']);
				}
			}
		}
		return $result;
	}
	public function getGoodsReceive_noteByID($acctid,$pid)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT 
    grn.id,grn.totalamt,grn.approval_status,grn.bill_number,
    itm.item_code,
    itm.item_name,grn.transportation,grn.other_expenses,
    CASE 
        WHEN grn.vendors_type = 1 THEN farmer.fr_name
        WHEN grn.vendors_type = 2 THEN company.cm_name
    END AS fr_name,
    CASE 
        WHEN grn.vendors_type = 1 THEN farmer.fr_code
        WHEN grn.vendors_type = 2 THEN company.cm_code
    END AS fr_code,
    CASE 
        WHEN grn.collection_center = 1 THEN 'DC'
        WHEN grn.collection_center = 2 THEN 'CC'
        WHEN grn.collection_center = 3 THEN 'MKT'
        WHEN grn.collection_center = 4 THEN 'GT'
    END AS collection_center,
    grn_items.item_id,
    grn_items.quantity,
    grn_items.price
FROM 
    goods_receive_note grn 
LEFT JOIN 
    grn_items ON grn.id = grn_items.goods_receive_note_id
LEFT JOIN 
    farmer_onboarding farmer ON grn.vendors_type = 1 AND grn.farmers_id = farmer.id
LEFT JOIN 
    company_onboarding company ON grn.vendors_type = 2 AND grn.farmers_id = company.id
LEFT JOIN 
     items itm ON itm.id = grn_items.item_id 
WHERE 
    grn.id = (:id);"); // AND grn.user_id = (:userid) 
   
		//$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		$stmt->bindParam(':id',$pid,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$qures = $stmt->fetchAll();
				foreach($qures as $rows)
				{
					$result[] = array('id'=>$rows['id'],'farmers_name'=>$rows['fr_name'],'item_code'=>$rows['item_code'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['item_name'],'frcode'=>$rows['fr_code'],'collection_center'=>$rows['collection_center'],'transportation'=>$rows['transportation'],'other_expenses'=>$rows['other_expenses'],'bill_number'=>$rows['bill_number']);
				}
			}
		}
		return $result; 
	}
	public function getGoods_SupplyBillByID($acctid,$pid)
	{
		$result=[];
		$stmt = $this->db->prepare("SELECT 
    gsb.id,gsb.totalamt,gsb.approval_status,gsb.bill_number,
    itm.item_code,
    itm.item_name,gsb.transportation,gsb.other_expenses,clb.cl_name,clb.cl_clienttype,clb.cl_code,
    CASE 
        WHEN gsb.collection_center = 1 THEN 'DC'
        WHEN gsb.collection_center = 2 THEN 'CC'
        WHEN gsb.collection_center = 3 THEN 'MKT'
        WHEN gsb.collection_center = 4 THEN 'GT'
    END AS collection_center,
    gsb_items.item_id,
    gsb_items.quantity,
    gsb_items.price
FROM 
    goods_supply_bill gsb
LEFT JOIN 
    gsb_items ON gsb.id = gsb_items.goods_supply_bill_id
LEFT JOIN 
     items itm ON itm.id = gsb_items.item_id 
left join  client_onboarding clb on clb.id = gsb.clients_id

WHERE 
    gsb.id = (:id);"); //   AND gsb.user_id = (:userid) 
  
		//$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		$stmt->bindParam(':id',$pid,PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$qures = $stmt->fetchAll();
				foreach($qures as $rows)
				{
					$result[] = array('id'=>$rows['id'],'clients_name'=>$rows['cl_name'],'item_code'=>$rows['item_code'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['item_name'],'clcode'=>$rows['cl_code'],'collection_center'=>$rows['collection_center'],'client_type'=>$rows['cl_clienttype'],'transportation'=>$rows['transportation'],'other_expenses'=>$rows['other_expenses'],'bill_number'=>$rows['bill_number']);
				}
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
	
	public function getFarmerOrSupplier_list($vid)
	{
		switch ($vid) {
        case '1':
            $table = "farmer_onboarding";
			//$c_type = 1;
            break;
        case '2':
            $table = "company_onboarding";
			//$c_type = 2;
            break;
           }
					$names_list = "<option value='' disabled='disabled' selected='selected'>-Select name-</option>";
					$stmt = $this->db->prepare("SELECT * FROM $table");
					//$stmt->bindParam(":ctype", $c_type, PDO::PARAM_INT);
					if($stmt->execute())
					{
						if($stmt->rowCount() > 0)
						{
							$rows = $stmt->fetchAll();
							if($vid == 1)
							{
								foreach($rows as $frow)
								{
													
									 $names_list.= "<option value='".$frow['id']."'>".$frow['fr_name']."</option>";
								}
							}else if($vid == 2)
							{
								foreach($rows as $frow)
								{
													
									 $names_list.= "<option value='".$frow['id']."'>".$frow['cm_name']."</option>";
								}
							}
						}
					}
		 return $names_list;
	}
	
	public function gsbsearch_date($fromdate,$todate)
	{
		$result=[];
			$stmt = $this->db->prepare("SELECT gsb.id,gsb.approval_status,gsb.regdate,gsb.`bill_number`,gsb.transportation,gsb.other_expenses,gsb.totalamt,GROUP_CONCAT(gitems.item_id) as itemids ,GROUP_CONCAT(itm.item_name) as itemnames,GROUP_CONCAT(itm.item_code) as itemcode,clb.cl_name,clb.cl_clienttype
    FROM goods_supply_bill gsb
left join  client_onboarding clb on clb.id = gsb.clients_id
left join gsb_items gitems on gsb.id = gitems.goods_supply_bill_id
left join items itm on gitems.item_id = itm.id
 WHERE gsb.regdate BETWEEN CONCAT('$fromdate', ' 00:00:00') AND CONCAT('$todate', ' 23:59:59') GROUP BY gsb.id order by gsb.id desc;");

		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'clients_name'=>$rows['cl_name'],'item_code'=>$rows['itemcode'],'billnumber'=>$rows['bill_number'],'transportation'=>$rows['transportation'],'otherexpenses'=>$rows['other_expenses'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'client_type'=>$rows['cl_clienttype'],'item_name'=>$rows['itemnames'],'regdate'=>$rows['regdate']);
				}
			}
		}
		return $result;
	
		}
		
		
	public function gsbsearch_datesearch_excel($fromdate,$todate)
	{
		$result=[];
			$stmt = $this->db->prepare("SELECT 
    gsb.id as gsb_id,gsb.totalamt,gsb.approval_status,gsb.bill_number,
    gsb.transportation,gsb.other_expenses,clb.cl_name,clb.cl_clienttype,clb.cl_code,
    CASE 
        WHEN gsb.collection_center = 1 THEN 'DC'
        WHEN gsb.collection_center = 2 THEN 'CC'
        WHEN gsb.collection_center = 3 THEN 'MKT'
        WHEN gsb.collection_center = 4 THEN 'GT'
    END AS collection_center
FROM 
    goods_supply_bill gsb
left join  client_onboarding clb on clb.id = gsb.clients_id

 WHERE gsb.regdate BETWEEN CONCAT('$fromdate', ' 00:00:00') AND CONCAT('$todate', ' 23:59:59')order by gsb.id asc;");
	

		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[$rows['gsb_id']] = array('id'=>$rows['gsb_id'],'clients_name'=>$rows['cl_name'],'billnumber'=>$rows['bill_number'],'transportation'=>$rows['transportation'],'otherexpenses'=>$rows['other_expenses'],'client_type'=>$rows['cl_clienttype'],'collection_center'=>$rows['collection_center'],'totamt'=>$rows['totalamt']);
					
		$innerqry = $this->db->prepare("SELECT 
    gsb.id,gsb.totalamt,gsb.approval_status,gsb.bill_number,
    itm.item_code,
    itm.item_name,gsb.transportation,gsb.other_expenses,clb.cl_name,clb.cl_clienttype,clb.cl_code,
    CASE 
        WHEN gsb.collection_center = 1 THEN 'DC'
        WHEN gsb.collection_center = 2 THEN 'CC'
        WHEN gsb.collection_center = 3 THEN 'MKT'
        WHEN gsb.collection_center = 4 THEN 'GT'
    END AS collection_center,
    gsb_items.item_id,
    gsb_items.quantity,
    gsb_items.price
FROM 
    goods_supply_bill gsb
LEFT JOIN 
    gsb_items ON gsb.id = gsb_items.goods_supply_bill_id
LEFT JOIN 
     items itm ON itm.id = gsb_items.item_id 
left join  client_onboarding clb on clb.id = gsb.clients_id

WHERE 
    gsb.id = (:id);"); //   AND gsb.user_id = (:userid) 
  
		//$stmt->bindParam(':userid',$acctid,PDO::PARAM_INT);
		$innerqry->bindParam(':id',$rows['gsb_id'],PDO::PARAM_INT);
		if($innerqry->execute())
		{
			if($innerqry->rowCount() > 0)
			{
				$quress = $innerqry->fetchAll();
				foreach($quress as $rows)
				{
					//$result[$rows['id']] = array('id'=>$rows['gsb_id'],'clients_name'=>$rows['cl_name'],);
					
					
					$result[$rows['id']]['itemlist'][] = array('id'=>$rows['id'],'quantity'=>$rows['quantity'],'price'=>$rows['price'],'totamt'=>$rows['totalamt'],'approval_status'=>$rows['approval_status'],'item_name'=>$rows['item_name']);
				}
			}
		} // if
					
					
				}
			}
		}
		return $result;
	
		}
		
		public function gsbsearch_ledger_excel($clienttype,$namelist)
		{

		$result=[];
			$stmt = $this->db->prepare("SELECT gsb.id,date(gsb.regdate)as regdate,gsb.`bill_number`,gsb.totalamt,clb.cl_name,clb.cl_clienttype,clb.cl_mobile
    FROM goods_supply_bill gsb
left join  client_onboarding clb on clb.id = gsb.clients_id
 WHERE `client_type` = '$clienttype' and `clients_id` = '$namelist'  order by gsb.id desc;");

		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'clients_name'=>$rows['cl_name'],'billnumber'=>$rows['bill_number'],'totamt'=>$rows['totalamt'],'client_type'=>$rows['cl_clienttype'],'regdate'=>$rows['regdate'],'cl_mobile'=>$rows['cl_mobile']);
				}
			}
		}
		return $result;
	
				}
				
				
		public function grnsearch_ledger_excel($vendors_list,$namelist)
		{

		$result=[];
			$stmt = $this->db->prepare("SELECT grn.id,date(grn.regdate)as regdate,grn.`bill_number`,grn.totalamt,
CASE 
        WHEN grn.vendors_type = 1 THEN farmer.fr_contact
        WHEN grn.vendors_type = 2 THEN company.cm_pan
    END AS ven_contact,
    CASE 
        WHEN grn.vendors_type = 1 THEN farmer.fr_name
        WHEN grn.vendors_type = 2 THEN company.cm_name
    END AS fr_name,
    CASE 
        WHEN grn.vendors_type = 2 THEN company.cm_gst
    END AS ven_gst
    FROM `goods_receive_note` grn 
LEFT JOIN 
    farmer_onboarding farmer ON grn.vendors_type = 1 AND grn.farmers_id = farmer.id
LEFT JOIN 
    company_onboarding company ON grn.vendors_type = 2 AND grn.farmers_id = company.id
 where grn.vendors_type = '$vendors_list' and grn.farmers_id='$namelist' order by grn.id desc;");

		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$row = $stmt->fetchAll();
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'clients_name'=>$rows['fr_name'],'billnumber'=>$rows['bill_number'],'totamt'=>$rows['totalamt'],'regdate'=>$rows['regdate'],'contact'=>$rows['ven_contact'],'vengst'=>$rows['ven_gst']);
				}
			}
		}
		return $result;
	
				}
				
				
	public function create_apartment($aptname,$aptddress,$aptlocation,$accountId) // insert into db
	{
		$stmt=$this->db->prepare("INSERT INTO `locations`(`apartment_name`, `apartment_address`, `google_location`, `user_id`)  values  (:aptname,:aptddress,:aptlocation,:accountId)");
		$stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":aptname", $aptname, PDO::PARAM_STR);
		$stmt->bindParam(":aptddress", $aptddress, PDO::PARAM_STR);
		$stmt->bindParam(":aptlocation", $aptlocation, PDO::PARAM_STR); // serial number 
		$stmt->execute();
        //$stmt->close();
	
	}
	
	public function getAllApartmentsData()
	{
		$result=[];
		$stmt = $this->db->prepare("select * from locations");
		//$stmt->bindParam(":userid", $acctid, PDO::PARAM_INT);
		if($stmt->execute())
		{
			if($stmt->rowCount() > 0)
			{
				$rows = $stmt->fetchAll();
				foreach($rows as $frow)
				{
					$result[] = array("apartment_name"=>$frow['apartment_name'], 
									"apartment_address"=>$frow['apartment_address'],
									"google_location"=>$frow['google_location'],
									"id"=>$frow['id']);
				}
			}
		}
		return $result;
	
	}

	public function create_purchase($accountId,$date,$misc,$total_quantity,$total_amount)
	{
		$stmt=$this->db->prepare("INSERT INTO `purchases`(`user_id`, `date`, `misc`, `total_quantity`, `total_cost`)
		  values  (:accountId,:date,:misc,:total_quantity,:total_amount)  ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)");
		$stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":date", $date, PDO::PARAM_STR);
		$stmt->bindParam(":misc", $misc, PDO::PARAM_STR);
		$stmt->bindParam(":total_quantity", $total_quantity, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":total_amount", $total_amount, PDO::PARAM_STR); // serial number 
		if($stmt->execute())
		{
			$lastid = $this->db->lastInsertId();
		}
		$result = array('insert_last_id'=>$lastid);	
		return $result;
	
	}
	public function update_purchase($purchase_id,$date,$misc,$total_quantity,$total_amount)
	{
		$stmt=$this->db->prepare("update `purchases` set  `date` = :date, `misc` = :misc , `total_quantity` = :total_quantity, `total_cost` = :total_amount  where `id` = :purchase_id");
		$stmt->bindParam(":purchase_id", $purchase_id, PDO::PARAM_INT);
		$stmt->bindParam(":date", $date, PDO::PARAM_STR);
		$stmt->bindParam(":misc", $misc, PDO::PARAM_STR);
		$stmt->bindParam(":total_quantity", $total_quantity, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":total_amount", $total_amount, PDO::PARAM_STR); // serial number 
		$stmt->execute();
	
	}

	public function create_purchase_item($insertedid,$itemid,$qty,$price,$total)
	{
		$stmt=$this->db->prepare("INSERT INTO `purchase_items`(`purchase_id`, `item_id`, `quantity`, `price_per_kg`, `total_cost`) 
		 values  (:insertedid,:itemid,:qty,:price,:total)
		 ON DUPLICATE KEY UPDATE
    	 quantity = quantity + VALUES(quantity),
		 price_per_kg = (price_per_kg + VALUES(price_per_kg)) / 2,
		 total_cost = (quantity *  price_per_kg) ");
		$stmt->bindParam(":insertedid", $insertedid, PDO::PARAM_INT);
		$stmt->bindParam(":itemid", $itemid, PDO::PARAM_INT);
		$stmt->bindParam(":qty", $qty, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":price", $price, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":total", $total, PDO::PARAM_STR); // serial number 
		$stmt->execute();
		$this->recalculate_purchase_totals($insertedid);
	
	}
	public function insert_recovery_yesterday($purchase_id,$purchase_items_id,$qty,$price,$total)
	{
		$stmt=$this->db->prepare(" INSERT INTO purchase_items (purchase_id, item_id, quantity, price_per_kg, total_cost)
						VALUES (:purchase_id, :item_id, :quantity, :price_per_kg, :total_cost)
						ON DUPLICATE KEY UPDATE 
							quantity = quantity + VALUES(quantity),
							total_cost = (quantity *  price_per_kg) ");
		$stmt->bindParam(":purchase_id", $purchase_id, PDO::PARAM_INT);
		$stmt->bindParam(":item_id", $purchase_items_id, PDO::PARAM_INT); // serial number 
		$stmt->bindParam(":quantity", $qty, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":price_per_kg", $price, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":total_cost", $total, PDO::PARAM_STR); // serial number 
		$stmt->execute();
		// 3. Mark recovery as done
		$stmt = $this->db->prepare("UPDATE purchases SET recovery_done=1 WHERE id=?");
		$stmt->execute([$purchase_id]);
		$this->recalculate_purchase_totals($purchase_id);
	
	}
	public function update_purchase_item($purchaseid, $qty, $price, $total)
	{
    	$stmt=$this->db->prepare("UPDATE purchase_items SET quantity = :qty, price_per_kg = :price, total_cost = :total WHERE id = :purchaseid");
		$stmt->bindParam(":qty", $qty, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":price", $price, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":total", $total, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":purchaseid", $purchaseid, PDO::PARAM_STR); // serial number 
		$stmt->execute();
	}
	public function delete_purchase_item($purchase_item_id)
	{
   	 	$stmt=$this->db->prepare("DELETE FROM purchase_items WHERE id = :id");
		$stmt->bindParam(":id", $purchase_item_id, PDO::PARAM_INT);
    	$stmt->execute();
	}
	public function recalculate_purchase_totals($purchase_id)
	{
		$stmt = $this->db->prepare("UPDATE purchases p
		JOIN (
			SELECT purchase_id, SUM(quantity) AS total_quantity, SUM(total_cost) AS total_cost
			FROM purchase_items
			WHERE purchase_id = :purchase_id
			GROUP BY purchase_id
		) pi ON p.id = pi.purchase_id
		SET p.total_quantity = pi.total_quantity, p.total_cost = pi.total_cost
		WHERE p.id = :purchase_id");
		$stmt->bindParam(":purchase_id", $purchase_id, PDO::PARAM_INT);
		$stmt->execute();
	}
	public function getAllPurchaseData($acctid)
	{
		$result = '';
		$date = date('Y-m-d'); // today’s date
		///$acctid = 1;
		$result=[];
		
		$stmt = $this->db->prepare("SELECT it.id as id_no , it.item_name,it.kannada_name,pi.quantity,pi.item_id,pi.price_per_kg,pi.id,sp.profit_percentage,sp.selling_price FROM `purchases` pr
									INNER join purchase_items pi on pr.id = pi.purchase_id 
									Inner Join items it on pi.item_id  = it.id
									left join selling_prices sp on sp.purchase_items_id = pi.id
									WHERE pr.user_id = (:userid) and pr.date = (:dat) order by it.id");
									
									
	$stmt->bindParam(":userid",$acctid,PDO::PARAM_INT);
	$stmt->bindParam(":dat",$date,PDO::PARAM_STR);
	if($stmt->execute())
		{
		$row = $stmt->fetchAll();
			foreach($row as $frow)
			{
				$result[] = array(
					"id_no"=>$frow['id_no'],
					"item_name"=>$frow['item_name'], 
								"kannada_name"=>$frow['kannada_name'], 
								"quantity"=>$frow['quantity'],
								"price_per_kg"=>$frow['price_per_kg'],
								"id"=>$frow['id'],
								"profit_percentage"=>$frow['profit_percentage'],
								"selling_price"=>$frow['selling_price'],
							);
			}
		}
		return $result;
	}


	public function getInventoryData($acctid,$apttid)
	{
		$result = '';
		$date = date('Y-m-d'); // today’s date
		///$acctid = 1;
		
		//$stmt = $this->db->prepare("SELECT it.item_name,it.kannada_name,pi.quantity,pi.item_id,pi.id,inv.quantity_atlocation FROM `purchases` pr
		//							INNER join purchase_items pi on pr.id = pi.purchase_id 
		//							Inner Join items it on pi.item_id  = it.id
		//							left JOIN inventory inv on inv.purchase_items_id  = pi.id and pi.item_id = inv.item_id  and inv.location_id = (:aptid)
		//							WHERE pr.user_id = (:userid) and pr.date = (:dat)  order by it.id ");
		$stmt = $this->db->prepare("SELECT 
			it.id as id_no,
        it.item_name,
        it.kannada_name,
        pi.item_id,
        pi.id,
        pi.quantity AS total_purchase_qty,
        (pi.quantity - IFNULL(SUM(inv_all.quantity_atlocation),0)) AS remaining_qty,
        inv_current.quantity_atlocation AS current_qty_atloc
    FROM purchases pr
    INNER JOIN purchase_items pi ON pr.id = pi.purchase_id
    INNER JOIN items it ON pi.item_id = it.id
    
    -- total allocated qty across all apartments
    LEFT JOIN inventory inv_all 
        ON inv_all.purchase_items_id = pi.id AND inv_all.item_id = pi.item_id
    
    -- allocation for the currently selected apartment
    LEFT JOIN inventory inv_current 
        ON inv_current.purchase_items_id = pi.id 
        AND inv_current.item_id = pi.item_id 
        AND inv_current.location_id = (:aptid)
    
    WHERE pr.user_id = (:userid) 
      AND pr.date = (:dat)
    GROUP BY pi.id, it.id, inv_current.quantity_atlocation
    ORDER BY it.id");
									
	$stmt->bindParam(":userid",$acctid,PDO::PARAM_INT);
	$stmt->bindParam(":dat",$date,PDO::PARAM_STR);
	$stmt->bindParam(":aptid",$apttid,PDO::PARAM_STR);
	if($stmt->execute())
	{
		$row = $stmt->fetchAll();
		$index = 0;
		if( $stmt->rowCount() > 0)
		{
		$result = "<div class='distribute-box'>
    <label for='distributePercent'>Distribute %: </label>
    <input type='number' id='distributePercent' min='1' max='100' placeholder='Enter %'>
    <button type='button' id='applyPercent'>Distribute</button>
</div> ";
		$result .= "<form method='post' action='' ><table id='inventoryform' border='1' cellpadding='5'><thead class='thead-dark'><tr><th>Name of Item</th><th>Total Qty Available</th><th>Qty at Location</th></tr></thead><tbody>";
			foreach($row as $rows)
			{
				$result .= '<tr>';
				$result .=  '<td>'.htmlspecialchars($rows['id_no']) .
				 '. ' .htmlspecialchars($rows['item_name'])."/".htmlspecialchars($rows['kannada_name']).'<input type="hidden" name="items['.$index.'][purchaseid]" readonly value="'.$rows['id'].'"><input type="hidden" name="items['.$index.'][itemid]" readonly value="'.$rows['item_id'].'"></td>';
				$result .=  '<td><input step="any"  style="width:80px;" type="number" class="purchasequantity" name="items['.$index.'][quantity]" readonly value="'.$rows['remaining_qty'].'"></td>';
				$result .=  '<td><input step="any" style="width:80px;" type="number" class="quantityatloc" name="items['.$index.'][quantityatloc]" value="'.$rows['current_qty_atloc'].'"  min="0" step="0.01" required></td>';
				$result .=  '</tr>';
				$index++;
			}
		$result .= '</tbody></table>';
		$result .= '<div class="summary"><input type="hidden" name="misc" value="0"><br><br></div>';
		$result .= '<button type="submit" class="btn btn-primary">Submit</button>';
		$result .= '</form>';
		}else{
		$result = "No data found";
	}
	}
	 return $result;

	}
	public function create_inventory($apartmentname,$purchaseid,$itemid,$quantity,$quantityatloc,$accountId,$date,$misc)
	{
		//If no row exists with the same unique key (user_id, item_id, location_id, date), MySQL inserts a new row.
		//If such a row already exists, MySQL updates:
			//quantity_atlocation → replaced with the new value.
		
		$stmt = $this->db->prepare("INSERT INTO `inventory`
				(`user_id`, `purchase_items_id`, `item_id`, `location_id`, `quantity_atlocation`, `date`, `misc`)
				VALUES (:accountId, :purchaseid, :itemid, :apartmentname, :quantityatloc, :dat, :mis)
				ON DUPLICATE KEY UPDATE 
					quantity_atlocation = VALUES(quantity_atlocation),
					misc = VALUES(misc)");
		$stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":purchaseid", $purchaseid, PDO::PARAM_INT);
		$stmt->bindParam(":itemid", $itemid, PDO::PARAM_INT);
		$stmt->bindParam(":apartmentname", $apartmentname, PDO::PARAM_STR);
		$stmt->bindParam(":quantityatloc", $quantityatloc, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":dat", $date, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":mis", $misc, PDO::PARAM_STR); // serial number
		$stmt->execute();
	}
	public function getDataForSales($acctid,$aptid)
	{
		$result = '';
		$date = date('Y-m-d'); // today’s date
		///$acctid = 1;
		
		$stmt = $this->db->prepare("SELECT  it.id as id_no, inv.item_id,it.item_name,it.kannada_name, inv.quantity_atlocation , pi.quantity,pi.price_per_kg,inv.purchase_items_id,inv.location_id,inv.misc,sp.selling_price,sa.`quantity_sold`,sa.`quantity_remaining`,sa.overallturnover ,sa.totprofitorloss FROM `inventory` inv
									inner join purchase_items pi on inv.purchase_items_id = pi.id and pi.item_id = inv.item_id
									INNER join purchases p on pi.purchase_id = p.id
									INNER JOIN items it on inv.item_id = it.id
									left join selling_prices  sp on inv.purchase_items_id = sp.purchase_items_id
                                    left join sales  sa on inv.purchase_items_id = sa.purchase_items_id and sa.location_id = (:aptid)
									WHERE inv.location_id = (:aptid)  and p.date = (:dat) order by it.id "); // and p.user_id = (:userid)
									
									
	//$stmt->bindParam(":userid",$acctid,PDO::PARAM_INT);
	$stmt->bindParam(":dat",$date,PDO::PARAM_STR);
	$stmt->bindParam(":aptid",$aptid,PDO::PARAM_STR);
	if($stmt->execute())
	{
		$row = $stmt->fetchAll();
		$result = "<form method='post' action='' ><table border='1' id='salesform' cellpadding='5'><thead class='thead-dark'><tr><th>Name of Item</th><th>Purchase Price</th><th>Qty at Location</th><th>Selling Price</th><th>Remaining Qty</th><th>Sold Qty</th><th>OverAll TurnOver</th></tr></thead><tbody>";
		$index = 0;
		$totqtyatloc = 0;
		$soldqtyatloc = 0;
		$totinvestaptamt = 0;
		$totturnover = 0;
		if( $stmt->rowCount() > 0)
		{
			foreach($row as $rows)
			{
			 //   // '<input type="hidden" class="priceperkgfrompurchases" name="priceperkgfrompurchases" value="'.$rows['price_per_kg'].'">
				$result .= '<tr>';
				$result .=  '<td><b>'.htmlspecialchars($rows['id_no']).'</b>.&nbsp;'.htmlspecialchars($rows['item_name'])."/".htmlspecialchars($rows['kannada_name']).
	          	'<input type="hidden" name="items['.$index.'][id_no]" value="'.htmlspecialchars($rows['id_no']).'">
				<input type="hidden" name="items['.$index.'][purchase_items_id]" readonly value="'.$rows['purchase_items_id'].'"><input type="hidden" name="items['.$index.'][location_id]" readonly value="'.$rows['location_id'].'"><input type="hidden" name="items['.$index.'][item_id]" readonly value="'.$rows['item_id'].'"><input type="hidden" id="misc" name="items['.$index.'][misc]" readonly value="'.$rows['misc'].'"></td>';
				$result .=  '<td><input style="width:80px;" type="number" class="priceperkgfrompurchases" name="priceperkgfrompurchases" value="'.$rows['price_per_kg'].'" readonly></td>';
				$result .=  '<td><input style="width:80px;" type="number" class="quantityatloc" name="items['.$index.'][quantityatloc]"  value="'.$rows['quantity_atlocation'].'"  min="0" step="any" readonly></td>';
				$result .=  '<td><input style="width:80px;" type="number" class="sellingprice"  name="items['.$index.'][sellingprice]"  value="'.$rows['selling_price'].'"  min="0" step="any" readonly></td>';
				$result .=  '<td><input required  style="width:80px;" type="number" class="remainingqty" name="items['.$index.'][remainingqty]"  value="'.$rows['quantity_remaining'].'"  min="0" step="any"></td>';
				$result .=  '<td><input style="width:80px;" type="number" class="soldqty" name="items['.$index.'][soldqty]"   value="'.$rows['quantity_sold'].'"  min="0" step="any" readonly></td>';
				$result .=  '<td><input  style="width:80px;" type="number" class="ovrallturnover"  name="items['.$index.'][ovrallturnover]" value="'.$rows['overallturnover'].'"  step="any" readonly></td>';
				$result .=  '</tr>';
				$index++;
				$totqtyatloc += $rows['quantity_atlocation'];
				$soldqtyatloc += $rows['quantity_sold'];
				$totinvestaptamt += $rows['price_per_kg'] * $rows['quantity_atlocation'];
				$totturnover += $rows['overallturnover'];
			}
			$result .= '</tbody><tfoot>';
			$result .= '<tr style="font: size 16px; font-weight:bold; background-color:#999999"><td><h5>Total:</h5></td> 
			<td><input style="width:80px;" type="number" id="tbl_total_purprice" readonly></td>
			<td><input style="width:80px;" type="number" id="tbl_total_qtyloc" readonly></td>
			<td><input style="width:80px;" type="number" id="tbl_total_sellingprice" readonly></td>
			<td><input style="width:80px;" type="number" id="tbl_total_remqty" readonly></td>
			<td><input style="width:80px;" type="number" id="tbl_total_soldqty" readonly></td>
			<td><input style="width:80px;"  type="number" id="tbl_total_turnover" readonly></td> 
			</tr>';
			$result .= '</tfoot></table>';

$result .= '
<div class="summary">
    <h4>Sales Summary</h4>
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 60%; text-align: left;">
        <tr>
            <th>Total Qty Purchased for the Apartment</th>
            <td><input value="'.$totqtyatloc.'"  style="width:80px;" type="number" id="total_qty_location" readonly></td>
        </tr>
        <tr>
            <th>Total Sold Qty at the Apartment</th>
            <td><input value="'.$soldqtyatloc.'" style="width:80px;" type="number" id="total_sold_qty" readonly></td>
        </tr>
        <tr>
            <th>Total Invested Amount for the Apartment</th>
            <td><input value="'.$totinvestaptamt.'"  style="width:80px;" type="number" id="total_invest_amt" readonly></td>
        </tr>
         <tr>
        <th>Total Revenue</th>
        <td>
            <table style="border-collapse: collapse; width:100%;">
                <tr>
                    <td style="width:80px;">Total</td>
                    <td><input value="'.$totturnover.'" style="width:80px;" type="number" id="total_turnover" readonly></td>
                </tr>
                <tr>
                    <td>Cash</td>
                    <td><input required value="" style="width:80px;" type="text" id="cash" name="cash"></td>
                </tr>
                <tr>
                    <td>Online</td>
                    <td><input required value="" style="width:80px;" type="text" id="onlinepay" name="onlinepay"></td>
                </tr>
                <tr>
                    <td>Scanner</td>
                    <td><input required value="" style="width:80px;" type="text" id="scanner" name="scanner">
					<input value="'.$aptid.'" style="width:80px;" type="hidden" id="aptid" name="aptid">
					<input value="'.$acctid.'" style="width:80px;" type="hidden" id="acctid" name="acctid"></td>
                </tr>
            </table>
        </td>
    </tr>
        <tr>
            <th>Profit / Loss</th>
            <td><input style="width:80px;" value="'.$rows['totprofitorloss'].'"  type="text" name="profit_loss" id="profit_loss" readonly></td>
        </tr>
    </table>
</div>';

		}else
		{
			$result = "<p>No data found for the selected location.</p>";
		}
	}


	 return $result;

	}
	public function create_sales($item_id,$purchase_items_id,$location_id,$sellingprice,$quasoldqtyntity,$quantity_remaining,$ovrallturnover,$accountId,$date,$profit_loss)
	{
		$stmt=$this->db->prepare("INSERT INTO `sales`(`user_id`, `location_id`, `purchase_items_id`, `item_id`, `quantity_sold`,`quantity_remaining`, `selling_price_per_kg`,overallturnover,`date`,`totprofitorloss`)
								 VALUES  (:accountId,:location_id,:purchase_items_id,:item_id,:quasoldqtyntity,:quantity_remaining,:sellingprice,:ovrallturnover,:dat,:profit_loss)
		 							ON DUPLICATE KEY UPDATE 
									quantity_sold = VALUES(quantity_sold),
									quantity_remaining = VALUES(quantity_remaining),
									overallturnover = VALUES(overallturnover),
									totprofitorloss = VALUES(totprofitorloss)");
		$stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":purchase_items_id", $purchase_items_id, PDO::PARAM_INT);
		$stmt->bindParam(":item_id", $item_id, PDO::PARAM_INT);
		$stmt->bindParam(":location_id", $location_id, PDO::PARAM_INT); // serial number
		$stmt->bindParam(":sellingprice", $sellingprice, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":quasoldqtyntity", $quasoldqtyntity, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":quantity_remaining", $quantity_remaining, PDO::PARAM_STR); // serial number quantity_remaining
		$stmt->bindParam(":sellingprice", $sellingprice, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":ovrallturnover", $ovrallturnover, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":dat", $date, PDO::PARAM_STR); // serial number 
		$stmt->bindParam(":profit_loss", $profit_loss, PDO::PARAM_STR); // serial number 
		$stmt->execute();
		
	}
	public function create_selingPrice($accountId,$purchase_items_id,$profit,$sellingprice,$date)
	{
		$stmt=$this->db->prepare("INSERT INTO `selling_prices`(`user_id`, `purchase_items_id`, `profit_percentage`, `selling_price`, `date`) 
					VALUES (:accountId,:purchase_items_id,:profitper,:sellingprice,:dat)
					ON DUPLICATE KEY UPDATE 
					profit_percentage = VALUES(profit_percentage),
					selling_price = VALUES(selling_price)");
		$stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
		$stmt->bindParam(":purchase_items_id", $purchase_items_id, PDO::PARAM_INT);
		$stmt->bindParam(":profitper", $profit, PDO::PARAM_INT);
		$stmt->bindParam(":sellingprice", $sellingprice, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":dat", $date, PDO::PARAM_STR); // serial number 
		$stmt->execute();
		
	}
public function getDataForSalesList($aptid,$dat)
	{
		$result = '';
		//$date = date('Y-m-d'); // today’s date
		///$acctid = 1;
		
		$stmt = $this->db->prepare("SELECT i.item_name, l.apartment_name, s.quantity_sold,s.selling_price_per_kg, s.date, s.overallturnover,s.totprofitorloss,pi.price_per_kg,inv.quantity_atlocation,inv.quantity_atlocation, s.quantity_remaining  FROM `sales` s
				JOIN locations l  on s.location_id = l.id
				join purchase_items pi on s.purchase_items_id = pi.id and s.item_id = pi.item_id
				join purchases p on pi.purchase_id = p.id
				JOIN items i on s.item_id = i.id
				join inventory inv on inv.purchase_items_id = s.purchase_items_id and inv.location_id = s.location_id
				WHERE s.location_id = :aptid and s.date = :dat  order by i.id ");
									
	//$stmt->bindParam(":userid",$acctid,PDO::PARAM_INT);
	$stmt->bindParam(":dat",$dat,PDO::PARAM_STR);
	$stmt->bindParam(":aptid",$aptid,PDO::PARAM_INT);
	if($stmt->execute())
	{
		$row = $stmt->fetchAll();
		$result = "<form method='post' action=''><table border='1' id='salesform' cellpadding='5'><thead class='thead-dark'><tr><th>Name of Item</th><th>Qty at Location</th><th>Sold Qty</th><th>UnSold Qty</th><th>Purchase Price</th><th>Sold Price</th><th>profit</th></tr></thead><tbody>";
		$index = 0;
		$pl = 0;
		if( $stmt->rowCount() > 0)
		{
			foreach($row as $rows)
			{
				$pl = $rows['totprofitorloss'];
				$result .= '<tr>';
				$result .=  '<td>'.$rows['item_name'].'</td>';
				$result .=  '<td>'.$rows['quantity_atlocation'].'</td>';
				$result .=  '<td>'.$rows['quantity_sold'].'</td>'; 
				$result .=  '<td>'.$rows['quantity_remaining'].'</td>'; 
				$result .=  '<td>'.$rows['price_per_kg'].'</td>';
				$result .=  '<td>'.$rows['selling_price_per_kg'].'</td>';
			//	$result .=  '<td>'.$rows['date'].'</td>';  
				$result .=  '<td>'.$rows['overallturnover'].'</td>';  
				$result .=  '</tr>';
				$index++;
			}
			$result .= '</tbody></table><br>';
			$result .= '<div class="summary">
			Total Profit/Loss(Total - misc):<br> <input type="number" name="misc" value="'.$pl.'" readonly><br><br></div>';

		}else
		{
			$result = "<p>No data found for the selected location.</p>";
		}
	}

	return $result;
}
public function fetch_dataforexcel($aptid,$dat,$exceldata=NULL)
	{
		$result = '';
		$date = date('d-m-Y'); // today’s date
		///$acctid = 1;
		
		$stmt = $this->db->prepare("select 
		i.id as id_no,
		inv.item_id, l.apartment_name, inv.location_id, inv.quantity_atlocation, sp.selling_price,i.item_name ,i.units from inventory inv
									join items i on inv.item_id = i.id
									JOIN locations l  on inv.location_id = l.id
									JOIN selling_prices sp on sp.purchase_items_id = inv.purchase_items_id
									WHERE inv.location_id = :aptid and inv.date = :dat  order by i.id "); 
									
	//$stmt->bindParam(":userid",$acctid,PDO::PARAM_INT);
	$stmt->bindParam(":dat",$dat,PDO::PARAM_STR);
	$stmt->bindParam(":aptid",$aptid,PDO::PARAM_INT);
	if($stmt->execute())
	{
		$rows = $stmt->fetchAll();
		if($exceldata == 'excel')
		{
			// Include PhpSpreadsheet
			require 'vendor/autoload.php';


			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			// Set default font
			$spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

			$sheet->setCellValue('A1', 'Apartment Name');
			$sheet->setCellValue('A2', 'Date');

			// Column headers
			$sheet->setCellValue('A4', 'Item no');
			$sheet->setCellValue('B4', 'Name of Item');
			$sheet->setCellValue('C4', 'Selling Price');
			$sheet->setCellValue('D4', 'units');

			// Make the header row bold
			$headerStyleArray = [
				'font' => [
					'bold' => true,
					'size' => 12,
					'name' => 'Calibri',	
					'background' => [
						'color' => ['rgb' => 'FFFF00'],
						'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					],
				],
			];
			$sheet->getStyle('A1:A2')->applyFromArray($headerStyleArray);
			$sheet->getStyle('A4:D4')->applyFromArray($headerStyleArray);

			// Fill data
			$rowNum = 5;
			foreach ($rows as $r) {
				$sheet->setCellValue('A'.$rowNum, $r['id_no']);
				$sheet->setCellValue('B'.$rowNum, $r['item_name']);
				$sheet->setCellValue('C'.$rowNum, $r['selling_price']);
				$sheet->setCellValue('D'.$rowNum,"price per ". $r['units']);
				$aptname = $r['apartment_name'];
				$rowNum++;
			}

			// Auto-size columns
			foreach (range('A','C') as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			}
			// Fill the data 
			$sheet->setCellValue('B1', $aptname);
			$sheet->setCellValue('B2',$date);
			// File name with today’s date
			$filename = "{$aptname}_sales_" . date('Ymd') . ".xlsx";

			// Send to browser as download
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=\"$filename\"");
			header('Cache-Control: max-age=0');

			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');
			exit;
		}else
		{
			$result = "<form method='post' action=''><div><button type='button' class='btn btn-warning'><a href='excel_aptwisesellingpr.php?aptid=$aptid&date=$dat&'>Download Excel</a></button></div><table border='1' id='salesform' cellpadding='5'><thead class='thead-dark'><tr><th>Sl_no</th><th>Name of Item</th><th>Selling Price</th><th>Quantity at location</th>
			<th>Units</th></tr></thead><tbody>";
			$index = 0;
			$pl = 0;
			if( $stmt->rowCount() > 0)
			{
				foreach($rows as $rows)
				{
					$result .= '<tr>';
					$result .=  '<td>'.$rows['id_no'].'</td>';
					$result .=  '<td>'.$rows['item_name'].'</td>';
					$result .=  '<td>'.$rows['selling_price'].'</td>';
					$result .=  '<td>'.$rows['quantity_atlocation'].'</td>';  
					$result .=  '<td>'. "price per ".$rows['units'].'</td>';  
					$result .=  '</tr>';
					$index++;
				}
				$result .= '</tbody></table><br>';

			}else
			{
				$result = "<p>No data found for the selected location.</p>";
			}
		}
	}

	return $result;
}

public function get_purchase_items_by_purchase_id($id)
{
	$result = [];
	$stmt = $this->db->prepare("select * from purchase_items where purchase_id = :id");
	$stmt->bindParam(":id",$id,PDO::PARAM_INT);
	if($stmt->execute())
	{
		if($stmt->rowCount() > 0)
		{
			$row = $stmt->fetchAll();
			{
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'item_id'=>$rows['item_id'],'quantity'=>$rows['quantity'],'price_per_kg'=>$rows['price_per_kg'],'total_cost'=>$rows['total_cost'],'purchaseid'=>$rows['purchase_id']);
				}
			}
		}
	}

	return $result;

}
public function get_purchase_by_date($user_id, $date)
 {
    	$result = '';
		$stmt = $this->db->prepare("SELECT * FROM purchases WHERE user_id = :user_id AND date = :date");
		$stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR); // serial number
		$stmt->bindParam(":date", $date, PDO::PARAM_STR); // serial number
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function get_purchase_by_id($purchase_id)
{
    $stmt = $this->db->prepare("SELECT * FROM purchases WHERE id = :id");
	$stmt->bindParam(":id", $purchase_id, PDO::PARAM_INT); // serial number
	$stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function get_purchase_items_forrecovery()
{
    $result = [];
	$stmt = $this->db->prepare("SELECT 
	i.id as id_no,
    i.item_name AS item_name,i.kannada_name AS kannada_name,   
    pi.item_id,
	pi.price_per_kg,
	sp.selling_price,
	 COALESCE((pi.quantity), 0) AS purchased_qty,
    COALESCE(SUM(s.quantity_sold), 0) AS sold_qty,
    (COALESCE((pi.quantity), 0) - COALESCE(SUM(s.quantity_sold), 0)) AS remaining_stock
FROM purchase_items pi
JOIN purchases p 
    ON pi.purchase_id = p.id
JOIN items i
    ON pi.item_id = i.id
LEFT JOIN sales s 
    ON pi.id = s.purchase_items_id 
    AND s.date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
left join selling_prices sp on sp.purchase_items_id = pi.id
WHERE p.date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
GROUP BY pi.item_id, i.item_name;");



//	$stmt->bindParam(":id", $purchase_id, PDO::PARAM_INT); // serial number
	if($stmt->execute())
	{
		if($stmt->rowCount() > 0)
			{
				// this block ( below3 lines)is to check  is data is fetching or not in backend 
			// $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
			// echo "<pre>";
			// print_r($row);
			// exit;
			$row = $stmt->fetchAll();
			{
				foreach($row as $row)
				{
					$result[] = [
								 "id_no"=>$row["id_no"],
								"item_id"  => $row["item_id"],
								"item_name"     => $row["item_name"],
								"purchased_qty" => $row["purchased_qty"],
								"sold_qty"    => $row["sold_qty"],
								"remaining_stock"    => $row["remaining_stock"],
								"price_per_kg" => $row["price_per_kg"],
								"selling_price" => $row["selling_price"],
								 "kannada_name" => $row["kannada_name"]
   							 ];
				}
			}
		}
	}
	return $result;
}
public function create_apartment_revenue($acct_id,$apt_id,$cash,$onlinepay,$scanner,$profit_loss,$date)
{
	$stmt=$this->db->prepare("INSERT INTO `apartment_revenue_types`(`user_id`, `location_id`, `cash`, `onlinepay`, `scanner`, `totprofitorloss`, `date`)  
				values  (:acct_id,:apt_id,:cash,:onlinepay,:scanner,:profit_loss,:dat)
				ON DUPLICATE KEY UPDATE 
				cash = VALUES(cash),
				onlinepay = VALUES(onlinepay),
				scanner = VALUES(scanner),
				totprofitorloss = VALUES(totprofitorloss)");
	$stmt->bindParam(":acct_id", $acct_id, PDO::PARAM_INT);
	$stmt->bindParam(":apt_id", $apt_id, PDO::PARAM_INT);
	$stmt->bindParam(":cash", $cash, PDO::PARAM_STR); // serial number
	$stmt->bindParam(":onlinepay", $onlinepay, PDO::PARAM_STR); // serial number
	$stmt->bindParam(":scanner", $scanner, PDO::PARAM_STR); // serial number
	$stmt->bindParam(":profit_loss", $profit_loss, PDO::PARAM_STR); // serial number
	$stmt->bindParam(":dat", $date, PDO::PARAM_STR); // serial number 
	$stmt->execute();
}	

public function getConversionData()
{
	$result = [];
	$stmt = $this->db->prepare("SELECT it.item_name, it.kannada_name,le.conversion_factor, it.id FROM unit_conversions le left join items it on it.id = le.item_id order by it.id; ");
	if($stmt->execute())
	{
		if($stmt->rowCount() > 0)
		{
			$row = $stmt->fetchAll();
			{
				foreach($row as $rows)
				{
					$result[] = array('id'=>$rows['id'],'item_name'=>$rows['item_name'],'kannada_name'=>$rows['kannada_name'],'conversion_factor'=>$rows['conversion_factor']);
				}
			}
		}
	}
	return $result;
}

public function create_Conversionunits($item_id,$conversion_factor)
{
	$stmt=$this->db->prepare("INSERT INTO `unit_conversions`(`item_id`, `conversion_factor`)  
				values  (:item_id,:conversion_factor)
				ON DUPLICATE KEY UPDATE 
				conversion_factor = VALUES(conversion_factor)");
	$stmt->bindParam(":item_id", $item_id, PDO::PARAM_INT);
	$stmt->bindParam(":conversion_factor", $conversion_factor, PDO::PARAM_STR); // serial number
	$stmt->execute();
}

//  //analysis_three_day.php
public function view_anaysis_day_three($acct_id)
{
	$result =[];

	$stmt=$this ->db->prepare("
	SELECT i.id as id_no,
	 p.date, 
	  i.item_name,
	pi.quantity,
	pi.price_per_kg,
	pi.total_cost
FROM purchases p 
 LEFT JOIN purchase_items pi on p.id = pi.purchase_id
 LEFT JOIN  items i  on  pi.item_id =i.id 
 WHERE p.user_id =:user_id
AND p.date >=DATE_SUB(CURDATE(), INTERVAL 3  DAY)
ORDER BY p.date DESC , i.item_name   LIMIT 100  ");

if($stmt->execute(['user_id'=>$acct_id])){
	if($stmt->rowCount()>0){
		$rows=$stmt->fetchAll();
		foreach ($rows as $row) {
			$result[] =[
				"id_no" =>$row["id_no"],
				"date" =>$row["date"],
				"item_name"=> $row['item_name'],
				"quantity" =>$row['quantity'],
				"price_per_kg" =>$row ["price_per_kg"],
				"total_cost"=> $row["total_cost"]
 			];
		}
	}
}
return $result;
}

}// final end





