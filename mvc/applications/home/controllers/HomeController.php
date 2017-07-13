<?
class Home extends MVC\Application{
	public $MVCdata;
	function __construct($data){
		/*
			$data['device'];
			$data['requests'];
			$data['paths'];
			$data['params'];
			$data['pageMeta'];
		*/
		$this -> MVCdata = $data;
		$this -> MVCdata['formSignature'] = 'formID';
	}

	public function main(){
		$_SESSION[$this -> MVCdata['formSignature']] = preg_replace("/[\/=+]/", "", base64_encode(openssl_random_pseudo_bytes(64)));
		$this -> loadFile(array(
			'name' => 'home.php',
			'path' => APPLICATIONFOLDERDIR.DS.'views'.DS
		));
	}
	
	public function login(){
		if(!empty($_POST) && !empty($_POST['form-signature']) && $_POST['form-signature'] == $_SESSION[$this -> MVCdata['formSignature']]){
			unset($_POST['form-signature']);
			$rawData = array();
			//{variable name} => array({variable type} => {value})
			foreach($_POST as $key => $data){
				switch($key){
					case 'email' :
							$type = 'email'; break;
					/*case 'contactNumber' :
							$type = 'int'; break;*/
					default :
							$type = 'string';
				}
				$rawData[$key] = array($type => $data);
			}
			$USERS = new Users();
			$rawData['device'] = json_encode($this -> MVCdata['device']);
			$isUser = $USERS -> logMeIn($rawData);
			$this -> jsonPrettyPrint($isUser);
		}
	}
}

?>