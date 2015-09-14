<?php
class Computer{
	public $name;
	public $macaddress;
	public $ipaddress;
	private static function isMacValid($mac)
	{
	  return (preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $mac) == 1);
	}
	public function __construct($nm,$mac){
		$this->name = $nm;
		$this->macaddress = $mac;
	}
	public function ping(){
		if( $this->ipaddress != "" && filter_var($this->ipaddress, FILTER_VALIDATE_IP) ) {
			$cmd_output = shell_exec("/bin/ping -q -c 1 $this->ipaddress");
			return strpos($cmd_output,"100%") === false ? 1 : 0;
		}
		return -3;
	}
	public function wakeUp(){
		if( $this->macaddress != "" 
			&& 
		     $this->isMacValid($this->macaddress) /*filter_var($this->macaddress, FILTER_VALIDATE_MAC)*/ 
		  ) {
			return shell_exec("/usr/bin/wakeuponethernet.sh $this->macaddress" );
		}
		return -3;
	}
}
class ComputerList{

	private $filename = "/tmp/db.dat";
	private $computers = array();

	public function delComputer($name){
		foreach($this->computers as $c){
			if( $c->name == $name ){//FIXME
				$key=array_search($c,$this->computers);
				unset($this->computers[$key]);
				$this->saveComputers();
				return true;
			}
		}
		return false;
	}
	public function hasComputer($name){
		foreach( $this->computers as $c ){
			if ( $c->name== $name ){
				return $c;
			}
		}
		return null;
	}
	public function addComputer($name,$macaddress,$ipaddress){
		$c = new Computer($name,$macaddress);
		$c->ipaddress = $ipaddress;
		$this->computers[]= $c;
		$this->saveComputers();
	}
	public function saveComputers(){
		file_put_contents($this->filename, serialize( $this->computers ) );
	}

	public function loadComputers(){
		if( $file = @file_get_contents($this->filename ) )  {
			$this->computers = unserialize( $file  );
		}else
			$this->computers = array();
	}
	public function toJSON(){
		return json_encode($this->computers);
	}
}
?>
