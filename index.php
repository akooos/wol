<?php
require_once('computers.php');
function hasParam($name){
        return (isset($_GET[$name]) && !empty($_GET[$name]));
}
function hasAction($name){
	return (isset($_GET[$name]) && empty($_GET[$name]));
}

$lsComputers = new ComputerList();
$lsComputers->loadComputers();
//Listázzás
if( hasAction('list') )
{
	echo $lsComputers->toJSON();
	return;
}
//etherwake
if( hasAction('wake') ){
	if ( !hasParam('name') ){
		echo -1;
		return;
	}       
	$name = $_GET['name'];
	$c = $lsComputers->hasComputer($name);
	if ( $c == null ){
		echo -2;
		return;
	}

	echo json_encode(array( $name , $c->wakeUp() ) ,JSON_FORCE_OBJECT);
	return;
}
//ping...
if( hasAction('ping') ){
	if ( !hasParam('name') ){
		echo -1;
		return;
	}       
	$name = $_GET['name'];
	$c = $lsComputers->hasComputer($name);
	if ( $c == null ){
		echo -2;
		return;
	}

	echo json_encode(array( $name , $c->ping() ) ,JSON_FORCE_OBJECT);
       return;
}
if( hasAction('edit') ){
        if ( !hasParam('name') ){
		echo -1;
		return;
	}       
        if ( !hasParam('newname') ){
		echo -2;
		return;
	}       
	if ( !hasParam('newmac') ){
		echo -3;
		return;
	}
	if ( !hasParam('newip') ){
		echo -3;
		return;
	}

	$name = $_GET['name'];
	$newname = $_GET['newname'];
	$newmac  = $_GET['newmac'];
	$newip  = $_GET['newip'];
	$c = $lsComputers->hasComputer($name);

	if ( $c ){
	  $c->macaddress=$newmac;
	  $c->name=$newname;
	  $c->ipaddress=$newip;
	  $lsComputers->saveComputers();
    	  echo 1;
       	  return;
	}
        echo 0;
	return;
}
if( hasAction('add') ){
        if ( !hasParam('name') ){
		echo -1;
		return;
	}       
        if ( !hasParam('mac') ){
		echo -2;
		return;
	}
	if ( !hasParam('ip') ){
		echo -3;
		return;
	}
	
	$name = $_GET['name'];
	$mac  = $_GET['mac'];
	$ip   = $_GET['ip'];

	if ( $lsComputers->hasComputer($name) ){
		echo -4;
		return;
	}
	
	$lsComputers->addComputer($name,$mac,$ip);
        echo 1;
	return;
}
if( hasAction('del') ){
        if ( !hasParam('name') ){
		echo -1;
		return;
	}       
	$name = $_GET['name'];
	if( $lsComputers->delComputer($name) ){
		echo 1;
	}else 
		echo 0;
	return;
}



include "main.php"

?>
