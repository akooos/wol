<!DOCTYPE html>
<html>
<head>
<title>Gép ébresztő</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
<link rel="icon" type="img/ico" href="wol-wakeonlan.png">
<link rel="stylesheet" type="text/css" href="bootstrap.css">
<link rel="stylesheet" type="text/css" href="wolApp.css">
</head>
<body >
<div id="cont" class="shadowedcurvy" ng-app="appWOL" ng-controller="ctrlComputers" ng-init="init()">
<nav>
<ul>
<div class"btn-group">
<li><button class="orangebuttonstyle" ng-click="computers.showAddDialog()" >Hozzáadás</button></li>
<li><button class="orangebuttonstyle" ng-click="computers.showEditDialog()" >Szerkesztés</button></li>
<li><button class="orangebuttonstyle" ng-click="computers.showRmDialog()" >Eltávolítás</button></li>
<li><button class="orangebuttonstyle" ng-click="computers.doRefresh(item,$event)" >Frissítés</button></li>
<li><button class="orangebuttonstyle" ng-click="computers.showWakeUpDialog()">Ébresztés</button></li>
<li><button class="orangebuttonstyle" 
	    ng-click="computers.doPinging()" 
	    ng-class="isSelectedInPings() ? 'ping_enabled' : '' " 
     >Ping </button></li>
</div>
</ul>
</nav>
	<table id="tableComputers" >
      <tr>
	<th>Név</th>
	<th>IP</th>
	<th>MAC cím</th>
      </tr>
      <tr ng-repeat="computer in computers.data" 
	  ng-click="setSelected(computer)" 
	  ng-class="{selected: computer === idSelectedComputer}" >
        <td>{{computer.name}}</td>
        <td><div id="statusLeds">
            <div title="Ping LED" ng-class="computer['pingStatus']== 1 ? 'ping_circle' : 'base_circle'"></div>
	    <div title="Power LED" ng-class="computer['powerStatus']== 1 ? 'power_on_circle' : 'base_circle'"></div>
	   </div>{{computer.ipaddress}}
	</td>

        <td>{{computer.macaddress}}</td>
      </tr>
    </table>
<div id="dlgComputer" class="shadowedcurvy" role="dialog" ng-show="dlgComputerDisplay" ng-cloak >
<div class="modal-header">
        <button type="button" class="close" ng-click="closeComputerDialog()">&times;</button>
        <h4 id="dlgComputerTitle" class="modal-title"></h4>
      </div>
      <div class="modal-body">
      <form id="formComputerAdd" method="post" action="" novalidation>
	<label for="computerName">Név</label>
	<input type="text"
		id="computerName"
		name="computerName" 
		placeholder="Számítógép neve..." 
		maxlength="255" 	 
		required></input>
	<label for="computerMACAddr">MAC cím</label>
	<input type="text"		
		id="computerMACAddr" 
		name="computerMACAddr" 
		placeholder="XX:XX:XX:XX:XX:XX" 
		maxlength="17" 
		 required>
	</input>
	<label for="computerIPAddr">IP cím</label>
	<input type="text"		
		id="computerIPAddr" 
		name="computerIPAddr" 
		placeholder="IP cím ..." 
		 required>
	</input>
	</form>
	</div>
<div class="modal-footer">
    <span><button class="orangebuttonstyle"  ng-click="computers.okBtnHandlerComputerDialog()">Rendben</button>
	  <button class="orangebuttonstyle btn-default"  ng-click="closeComputerDialog()">Mégsem</button>
    </span>
</div>
</div>
<div id="dlgYoN" class="shadowedcurvy" role="dialog" ng-show="dlgYoNDisplay" ng-cloak>
<div class="modal-header">
        <button type="button" class="close" ng-click="closeYoNDialog()">&times;</button>
        <h4 id="dlgYoNTitle" class="modal-title"></h4>
      </div>
      <div class="modal-body">
	<p id="dlgYoNMessage" ></p>
	</div>
<div class="modal-footer">
    <span><button class="orangebuttonstyle btn-primary" ng-click="yesBtnHandlerYoNDialog()">Igen</button>
	  <button class="orangebuttonstyle btn-default" ng-click="closeYoNDialog()">Nem</button>
    </span>
</div>
</div>

</div>

<script type="text/javascript" src="jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="jquery.validate.min.js"></script>
<script type="text/javascript" src="wolApp.js"></script>
<script type="text/javascript" src="angular.min.js"></script>
</body>
</html>
