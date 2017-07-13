<?
class Users extends MVC\Application{
	private $DB;
	private $table;
	function __construct(){
		$this -> DB = new MVC\Database();
		$this -> table = 'users';
	}
	public function logMeIn($data = null){
		if(!empty($data)){
		//$device = $data['device'];
		//	unset($data['device']); $rawData['password']['string']
			//$sanitizedData = $this -> DB -> sanitize($data);
			return $this -> createNewUser($data);
			//$data['device'] = $device;
			//return ($sanitizedData);
			//return $this -> saveToDbase('users',$data);
		}
	}
	
	public function createNewUser($data = null){
		$result = array('error' => 'data');
		if(!empty($data)){
			if(isset($data['device'])){
				$device = $data['device'];
				unset($data['device']);// $rawData['password']['string']
			}
			$sanitizedData = $this -> DB -> sanitize($data);
			foreach($sanitizedData as $temp){
				if($temp == ''){
					return $result;
				}
			}
			$isEmailUser = $this -> getUserByEmail($sanitizedData['email']);
			if(empty($isEmailUser)){
				if(isset($device))
					$sanitizedData['device'] = $device;
				
				$result = $this -> saveToDbase($sanitizedData);
			}else{
				$result['error'] = "{$sanitizedData['email']} already in use.";
			}
			return $result;
		}
		
	}
	
	public function getUserByEmail($email){
		$user = $this -> DB -> select(array(
			'tables' => $this -> table,
			'conditions' => array('email' => $email),
			'columns' => 'name, uid, user_level',
			'extends' => 'LIMIT 1'
		));
		
		if(!empty($user))
			$user = $user[0];
		
		return $user;		
	}
	
  public function saveToDbase($data = null){
		//ALTER TABLE uids AUTO_INCREMENT=10000000001;
		$result = array('error' => 'data');
		$UID = $this -> DB -> insert(array(
			'table' => 'uids',
			'values' => array('value'=> mt_rand(0,100)),
			'replace' => true
		));
		$data['uid'] = $UID;
		$data['password'] = $this -> DB -> passwordHash($data['password']);
		if(!empty($data)){
			$index = $this -> DB -> insert(array(
				'table' => $this -> table,
				'values' => $data
			));
			$result = array('success' => $index);
		}
		return $result;
	}
}
?>