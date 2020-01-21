<?php 
	session_start();
	require_once("db.php");
	$db = new DB("127.0.0.1","bpinsicp_varietyreg","root","");
	
	function check_tok($db,$token){
			//validate
		
				if($db->query("SELECT value from token WHERE value=:token", array(":token"=>$token))){
					//if existing, instantiate session (validated)
					session_regenerate_id(true);
					$_SESSION['token'] = time();
					setcookie('user',$_SESSION['token'], $_SESSION['token'] +  3600, "/");
					return true;
					//add expiration of session via timer
				}else http_response_code(401);
	}

/*	class VarGet
{		private $varg;

		private function __construct($db,$nsicid, $crop){
			switch($nsicid){
				case 'Philippine Seed Board':  
				break;
				//default:  //nsic number
			}
		}
	}
*/

	function get_variety($db,$nsicid,$crop){
		//echo $nsicid;
		/*
		if(($nsicid !== '' || $nsicid !== 'Philippine Seed Board') && $crop === 'Rice'){
			$vr= $db->query("SELECT varname, expname, nsicregnum, yearapp, crop, vartype, breeder, breed_address FROM regvar WHERE nsicregnum = :nsicid  ", array(":nsicid"=>$nsicid));
			
			$vname = $vr[0]['varname'];
			$chx = $db->query("SELECT yield_ds, yield_ws, maturity_ds, maturity_ws, plantheight_ds, plantheight_ws, tillers, culm_strength, green_leafhopper, brown_planthopper, stem_borrer, whorl_maggot, riceblack_bug, whiteheads, deadhearts, blast_bacterial_blight, bacterial_leaf_blight, shealth_blight, tungro, hel_leaf_spot, grassy_stunt, ragged_stunt, amylose_content, adaptation FROM char_rice WHERE varname =:varname ",  array(":varname"=>$vname));
			
	//	return	$vresults = array_push($vr,$chx);
		
			array_push($vr,$chx);
			
			return $vr;
			//add characteristics
		}else if($nsicid === 'Philippine Seed Board'){
			$vr= $db->query("SELECT varname, expname, nsicregnum, yearapp, crop, vartype, breeder, breed_address FROM regvar WHERE nsicregnum = :nsicid  ", array(":nsicid"=>$nsicid));
			
			$vname = $vr[0]['varname'];
			$chx = $db->query("SELECT yield_ds, yield_ws, maturity_ds, maturity_ws, plantheight_ds, plantheight_ws, tillers, culm_strength, green_leafhopper, brown_planthopper, stem_borrer, whorl_maggot, riceblack_bug, whiteheads, deadhearts, blast_bacterial_blight, bacterial_leaf_blight, shealth_blight, tungro, hel_leaf_spot, grassy_stunt, ragged_stunt, amylose_content, adaptation FROM char_rice WHERE varname =:varname ",  array(":varname"=>$vname));
		}
		*/
		//PSB needs to be accompanied by a CROP  Varname and year approved
		
		if($nsicid === '' || $nsicid === NULL){
			http_responsecode(400);
		}else {
			/*$vr= $db->query("SELECT varname, expname, nsicregnum, yearapp, crop, vartype, breeder, breed_address FROM regvar WHERE nsicregnum = :nsicid AND crop = :crop ", array(":nsicid"=>$nsicid,":crop"=>$crop));
			*/
			$vr= $db->query("SELECT regvar.varname, regvar.expname, regvar.nsicregnum, regvar.yearapp, regvar.crop, regvar.vartype, regvar.breeder, regvar.breed_address FROM regvar INNER JOIN char_rice ON regvar.varname = char_rice.varname ", array());
			

			$vname = $vr[0]['varname'];
			$chx = $db->query("SELECT yield_ds, yield_ws, maturity_ds, maturity_ws, plantheight_ds, plantheight_ws, tillers, culm_strength, green_leafhopper, brown_planthopper, stem_borrer, whorl_maggot, riceblack_bug, whiteheads, deadhearts, blast_bacterial_blight, bacterial_leaf_blight, shealth_blight, tungro, hel_leaf_spot, grassy_stunt, ragged_stunt, amylose_content, adaptation FROM char_rice WHERE varname =:varname ",  array(":varname"=>$vname));

			return $vr;
			
		}

	}	



	if($_SERVER['REQUEST_METHOD'] == "GET"){
		 //http_response_code(405);
		if(isset($_GET['gval'])){
			unset($_SESSION['']);
		}
	}else if($_SERVER['REQUEST_METHOD'] == "POST"){

		$clientPost = file_get_contents("php://input");
		$clientPost = json_decode($clientPost);

		if (isset($clientPost->token) != ''){
			//check validity of token, skip if there is existing session
			$token = $clientPost->token;
			$nsicid = $clientPost->id;
			$crop = "Rice";
			if(isset($_SESSION['token']) && isset($_COOKIE["user"])){
				//with existing token proceed to process data else validate

				//$crop = $clientPost->crop; //default is rice
		//	echo json_encode(get_variety($db,$nsicid,$crop));
			
			echo json_encode(get_variety($db,$nsicid,$crop));
			
			//	$vg = new VarGet($db,$nsicid,$crop);
			}else {
				if(check_tok($db,$token) == true){
					echo json_encode(get_variety($db,$nsicid,$crop));
				} 
			}
			
		}else http_response_code(401);
 

	}else http_response_code(405);



/*
		$postBody  = file_get_contents("php://input");
		$postBody = json_decode($postBody);


		if(isset($_SESSION['tok'])){
			$id = $postBody->id;
			$crop = $postBody->crop;

			if($crop === 'any'){
				$baseI = $db->query('SELECT crop, nsicregnum, varname, yearapp, breeder FROM regvar WHERE nsicregnum=:id',array(':id'=>$id));
				echo json_encode($baseI);
				http_response_code(200);
				die();
			}else{
				$baseI = $db->query('SELECT crop, nsicregnum, varname, yearapp, breeder FROM regvar WHERE nsicregnum=:id AND crop=:crop', array(':id'=>$id, ':crop'=>$crop));

				$varname = $baseI[0]['varname'];
				
				
				switch($crop){
					case 'Rice'  : $Chxtbl ='char_rice';  
									break;
				}

				$charX = $db->query('SELECT yield_ds, yield_ws, maturity_ds, maturity_ws, plantheight_ds, plantheight_ws FROM '.$Chxtbl.' WHERE varname=:varname',array(':varname'=>$varname));
				
				//echo json_encode($baseI);

				echo json_encode($charX);
				http_response_code(200);
				die();
			}

			
		}else
		//if($_GET['url'] == "auth"){
			  if(isset($postBody->token)){
				
				$token = $postBody->token;
				
			
				if($db->query('SELECT value FROM token WHERE value=:token', array(':token'=>$token))){
					$_SESSION['tok'] = 1;
					echo 'valid';
					//setTimeout(function(){  unset($_SESSION['tok']);}, 3000);
					http_response_code(200);
				}else{
					echo 'error';
					http_response_code(400);
				}
			}else{ http_response_code(401);} 

		}else{
		http_response_code(405);
	}*/

 ?>