<?
/**
 * JUANdirectory PHP Model-View-Controller Setup
 *
 * class.database.php V1.4
 *
 * Author/Contributor : John Virdi V. Alfonso
 * Date   : 19 July 2017
 * Email  : jva.ipampanga@gmail.com
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
**/

namespace MVC;

require_once(ROOTDIR.DS.'libraries'.DS.'vendors'.DS.'class.password.php');

use PDO;

(!defined('ROOTDIR'))?die('ILLEGAL ACCESS OF FILE'):'';

class Database extends Application{
  private $PDODB;
  private $config;
  function __construct(){
	parent :: __construct();
		// DBMConfig will load master DB settings or load local DB settings if available ROOTDIR.APPLICATIONFOLDERDIR.DS.'configs/DBconfig.php';
		$this -> loadFile(array(
			'name' => 'DBMconfig.php',
			'path' => 'configs'.DS
		));	
		$DBconfig =  new \DBMConfig();

		$this -> config = $DBconfig -> config();
		$this -> connectSQL();//database, user, password, pkey
  }
  
  private function connectSQL(){
		if(isset($this->config['pkey'])){
			if (strlen($this->config['pkey']) !== 32){
			throw new \Exception("aes_pkey must be 32 characters in length! ".strlen($this->config['pkey'])." provided");
			die;
			}
		}
		try{
			$host = 'localhost';

			if(isset($this->config['host']))
				$host = $this->config['host'];

			$this -> PDODB = new \PDO('mysql:host='.$host.';port=3306;dbname='.$this->config['database'].';charset=utf8', $this->config['user'], $this->config['password']);
			$this -> PDODB ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this -> PDODB ->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		}catch (\PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die;
		}
  }
  
	public function beginTransaction(){
		$this -> PDODB -> beginTransaction();
	}
	
	public function rollbackTransaction(){
		$this -> PDODB -> rollBack();
	}
	
	public function commitTransaction(){
		$this -> PDODB -> commit();
	}
	
	public function errorInfo(){
		return 'Error occurred:'.implode(":",$this -> PDODB -> errorInfo());
	}
	
  public function select($query){
	/*
	query(
	  'tables' => {comma separated table names},
	  'columns' => {comma separated columns},
	  'conditions' => array({column name}=> {value},{column name}=> {value}),
	  'extends' => {extended MYSQL statements like ORDER BY or LIKE},
	  'decrypt' => array({column name}, {column name})
	)
	*/
	$tables = $query['tables'];
	$columns = (isset($query['columns']))?$query['columns']:'*';
	$statement = "SELECT $columns FROM $tables";
	$conditionStatement = NULL;
	$conditionValue = array();
	if(isset($query['conditions']) && $query['conditions'] !== NULL){
	  foreach($query['conditions'] as $key => $value){
	    $conditionStatement = ($conditionStatement===NULL)?"$key = ?":"$conditionStatement AND $key = ?";
		array_push($conditionValue,$value);
	  }
	  $statement = "$statement WHERE $conditionStatement";
	}
	$statement = (isset($query['extends']))?"$statement ".$query['extends']:$statement;
	$queryString = $this -> PDODB -> prepare($statement);
	if(count($conditionValue)>=1){
	  $queryString -> execute($conditionValue);
	}else{
	  $queryString -> execute();
	}
	$results = $queryString -> fetchAll(\PDO::FETCH_ASSOC);
	if(isset($query['decrypt']) && $results){
	  $resultsKeys = array_keys($results[0]);
	  $encryptedFields = array();
	  
	  foreach($query['decrypt'] as $encrypted){
		if(in_array($encrypted, $resultsKeys)){
		  array_push($encryptedFields, $encrypted);
		}
	  }
	  if($encryptedFields){
		$decryptedResults = array();
		foreach($results as $result){
		  foreach($encryptedFields as $encryptedField){
			$result[$encryptedField] = $this -> aesDecrypt($result[$encryptedField]);
		  }
		  array_push($decryptedResults, $result);
		}
		$results = $decryptedResults;
	  }
	}
	return($results);
  }
  
  public function insert($query){
	/*
	query(
	  'table' => {table name},
	  'values' => array({column name}=> {value},{column name}=> {value})
	)
	*/
    $table = $query['table'];
	$columns = NULL;
	$placeHolders = NULL;
	$values = array();
		
	foreach($query['values'] as $key => $value){
	  $columns = ($columns===NULL)?"`$key`":"$columns, `$key`";
	  $placeHolders = ($placeHolders===NULL)?"?":"$placeHolders, ?";
	  array_push($values, $value);
	}
	if($this -> getColumnNames($table,'ipaddress')){
	  $address = null;
	  foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
		  foreach (explode(',', $_SERVER[$key]) as $ip) {
			if (filter_var($ip, \FILTER_VALIDATE_IP) !== false) {
			  $address = $ip;
			}
		  }
		}
	  }
	  $columns .= ', `ipaddress`';
	  $placeHolders .=', ?';
	  array_push($values, $address);
	}

	for($i=0;$i<=count($values)-1;$i++){
	  if(is_array($values[$i])){
		$temp = NULL;
		$arrayKey = array_keys($values[$i]);
		$arrayVal = array_values($values[$i]);
	    
		if(strtolower($arrayKey[0])==='encrypt'){
		  $temp = $this -> aesEncrypt($arrayVal[0]);
		}else{
		  $temp = $arrayVal[0];
		}
		
		$values[$i] = $temp;
	  }
	}
	//$statement = "INSERT INTO `$table` ($columns) VALUES ($placeHolders)";
	$statement = (isset($query['replace']) && $query['replace'] === true)  ? "REPLACE" : "INSERT";
	$statement .= " INTO `$table` ($columns) VALUES ($placeHolders)";
	$queryString = $this -> PDODB -> prepare($statement);
	$queryString -> execute($values);
	return $this -> PDODB -> lastInsertId(); //rowCount();
  }
  
  public function update($query){
	/*
	query(
	  'table' => {table name},
	  'values' => array({column name}=> {value},{column name}=> {value}),
	  'conditions' => array({column name}=> {value},{column name}=> {value})
	)
	*/
    $table = $query['table'];
	$statement = "UPDATE $table SET";
	$conditionStatement = NULL;
	$conditionValue = array();
	$values = array();
	
	foreach($query['values'] as $key => $value){
	  $columns = ($columns===NULL)?"$key = ?":"$columns, $key =?";  
	  array_push($values, $value);
	}
	for($i=0;$i<=count($values)-1;$i++){
	  if(is_array($values[$i])){
		$temp = NULL;
		$arrayKey = array_keys($values[$i]);
		$arrayVal = array_values($values[$i]);
		
		if(strtolower($arrayKey[0])==='encrypt'){
		  $temp = $this -> aesEncrypt($arrayVal[0]);
		}else{
		  $temp = $arrayVal[0];
		}
		
		$values[$i] = $temp;
	  }
	}
	$statement = "$statement $columns";
	if(isset($query['conditions'])){
	  foreach($query['conditions'] as $key => $value){
	    $conditionStatement = ($conditionStatement===NULL)?"$key = ?":"$conditionStatement AND $key = ?";
		array_push($values,$value);
	  }
	  $statement = "$statement WHERE $conditionStatement";
	}
	$queryString = $this -> PDODB -> prepare($statement);
	$queryString -> execute($values);
	return $queryString -> rowCount();
  }
  
  public function delete($query){
	/*
	query(
	  'table' => {comma separated table names},
	  'conditions' => array({column name}=> {value},{column name}=> {value}),
	  'extends' => {extended MYSQL statements like ORDER BY or LIKE},
	)
	*/
	$tables = $query['table'];
	$statement = "DELETE FROM $tables";
	$conditionStatement = NULL;
	$conditionValue = array();
	if(isset($query['conditions']) && $query['conditions'] !== NULL){
	  foreach($query['conditions'] as $key => $value){
	    $conditionStatement = ($conditionStatement===NULL)?"$key = ?":"$conditionStatement AND $key = ?";
		array_push($conditionValue,$value);
	  }
	  $statement = "$statement WHERE $conditionStatement";
	}
	$statement = (isset($query['extends']))?"$statement ".$query['extends']:$statement;
	$queryString = $this -> PDODB -> prepare($statement);
	if(count($conditionValue)>=1){
	  return $queryString -> execute($conditionValue);
	}else{
	  return $queryString -> execute();
	}
  }
  
  public function sanitize($array){
	/*
	accepts the array in this format
	array(
	  {variable name} => array({variable type} => {value}),
	  {variable name} => array({variable type} => {value})
	)
	*/
	$vars = array();
	foreach($array as $var => $values){
	  $type = array_keys($values);
	  $value = array_values($values);
	  $temp = NULL;
	  switch(strtolower($type[0])){
		case 'email' : $temp = filter_var($value[0], \FILTER_SANITIZE_EMAIL); 
					   $isEmail = filter_var($temp, \FILTER_VALIDATE_EMAIL); 
					   $temp = $isEmail ? $temp : '';
		                break;
		case 'int' : $temp = filter_var($value[0], \FILTER_SANITIZE_NUMBER_INT); break;
		case 'float' : $temp = filter_var($value[0], \FILTER_SANITIZE_NUMBER_FLOAT); break;
		case 'url' : $temp = filter_var($value[0], \FILTER_SANITIZE_URL); break;
		default : $temp = filter_var($value[0], \FILTER_SANITIZE_STRING); break;
	  }
	    $vars[$var] = $temp;
	}
	
	/*
	returns sanitized variables
	vars(
	  {variable name} => {value}),
	  {variable name} => {value})
	)
	*/
	/*if(count($vars) > 1)
	  return $vars;
	else*/
	  return $vars;//[0]
  }
  
  private function getColumnNames($table,$field=NULL){
    $queryString = $this -> PDODB -> prepare("DESCRIBE $table");
	$queryString -> execute();
	$columns = $queryString -> fetchAll(PDO::FETCH_COLUMN);
	if($field){
	  return in_array($field,$columns)?true:false;
	}else{
	  return $columns;
	}
  }
  
  public function encrypt($value, $pKey = NULL){
	return $this -> aesEncrypt($value, $pKey);
  }
  
  public function decrypt($value, $pKey = NULL){
	return $this -> aesDecrypt($value, $pKey);
  }
  
  /*
	* Returns encrypted data using AES Encryption technology 
	* 		MySQL Datatype: TEXT
	* 		$value = value to be encrypted
	* 		$iv = Initialization Vector (random key prepended to the front of the encrypted data)
	*/	
	private function aesEncrypt($value, $pKey = NULL){
		if (strlen($value)>1){
			$pkey = $pKey ? $pKey : $this->config['pkey'];
			//$iv_size = mcrypt_get_iv_size(\MCRYPT_RIJNDAEL_256, \MCRYPT_MODE_CBC);
			//$iv = mcrypt_create_iv($iv_size,\MCRYPT_RAND);
			//return base64_encode($iv.mcrypt_encrypt(\MCRYPT_RIJNDAEL_256, $pKey, $value, \MCRYPT_MODE_CBC, $iv));
			$iv = openssl_random_pseudo_bytes (openssl_cipher_iv_length ('aes-256-ctr'));
			return base64_encode(openssl_encrypt ($value, 'aes-256-ctr',$pKey,OPENSSL_RAW_DATA, $iv).'::'.$iv);
		} 
		return '';
	}
	
	/*
	* Returns decrypted data
	* 		$value = value to be decrypted
	* 		$iv = Initialization Vector 
	*			(random key prepended to the front of the encrypted data)
	*/
	private function aesDecrypt($value, $pKey = NULL){
		if (strlen($value)>1){
			$pkey = $pKey ? $pKey : $this->config['pkey'];
			$value = base64_decode($value);
			//$iv_size = mcrypt_get_iv_size(\MCRYPT_RIJNDAEL_256, \MCRYPT_MODE_CBC);
			//$iv = substr($value,0,$iv_size);
			//$value = substr($value,$iv_size);
			//return trim(mcrypt_decrypt(\MCRYPT_RIJNDAEL_256, $pKey, $value, \MCRYPT_MODE_CBC, $iv));
			list($data, $iv) = explode('::',$value,2);
			return openssl_decrypt($data, 'aes-256-ctr',$pKey,OPENSSL_RAW_DATA, $iv);
		}
		return '';
	}
	
	public function passwordHash($string){
	  $hashed = password_hash($string, PASSWORD_BCRYPT);
	   if (password_verify($string, $hashed)) {
		 return $hashed;
	   } else {
		 $this -> passwordHash($string);
	   }
	  //return password_hash($string, PASSWORD_BCRYPT);//PASSWORD_DEFAULT, array('cost' => 31)); / cost (4 - 31)
	}
	
	public function passwordVerify($string, $password){
	  if(password_verify($string, $password)){
		return true;
	   /* if (password_needs_rehash($hash, $algorithm, PASSWORD_DEFAULT)) {
            $hash = password_hash($password, $algorithm, PASSWORD_DEFAULT);
		}*/
	  }else{
	    return false;
	  }
	}
	
	public function tableExists($tableName){	
	  ///ini_set('display_errors', 0);  
	  $isTable = true;
	  if ($tableName) {
		try{
		  $this -> PDODB -> query ("DESCRIBE $tableName");
		}catch (\PDOException $e){
		  $isTable = false;
		}
	  }
	  
	  return $isTable;
	}
	
}
?>
