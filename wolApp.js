$(document).ready(function(){

var app = angular.module('appWOL', []);
app.controller('ctrlComputers', function($scope,$http) {
$scope.idSelectedComputer = undefined;
$scope.lsPings = [];
$scope.init = function(){
	$scope.computers.doRefresh();
}
$scope.setSelected = function (idSelectedComputer) {
   $scope.idSelectedComputer = idSelectedComputer;
};
$scope.dlgComputerDisplay = false;
$scope.showComputerDialog = function(){
    $("#formComputerAdd > div > div.error").css('display','none');
    $scope.dlgComputerDisplay = true;
};
$scope.closeComputerDialog = function() {
    $scope.dlgComputerDisplay = false;
// 	  close();
 };
$scope.dlgYoNDisplay= false;

$scope.closeYoNDialog = function(){
    $scope.dlgYoNDisplay = false;
//close();
};
$scope.showYoNDialog = function(){
    $scope.dlgYoNDisplay = true;

}
$scope.isSelectedInPings = function(){
	return $scope.lsPings.indexOf($scope.idSelectedComputer) > -1;
}
function pingRequest(computer){

	if ($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') 
	    $scope.$apply( function() {
		computer['pingStatus'] = 1;
	});
	
	$.ajax({
	     url:"?ping",
	     data:{"name":computer.name},
	     success: function(data,statusTxt,jqXHR){
		var o = JSON.parse(data);
		var name = o[0];
		if ($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') 
		   $scope.$apply( function(){
			computer['powerStatus'] = o[1];	
			computer['pingStatus']  = 0;

		} );
  	     }
	});

}
function pingTimerTimeOut(){

 $scope.lsPings.forEach( pingRequest );

}
window.setInterval(pingTimerTimeOut, 10000);
$scope.computers =
{
    data: [ ],
    doPinging: function(){
       if( $scope.idSelectedComputer !== undefined ){
	var index = $scope.lsPings.indexOf($scope.idSelectedComputer);
	if( index > -1 ){
		$scope.lsPings.splice(index,1);
	}else{
		$scope.lsPings.push($scope.idSelectedComputer);
		pingRequest($scope.idSelectedComputer);
 	}
       }
    },
    doRefresh: function(item,event){
		var responsePromise = $http.get("?list");

                responsePromise.success(function(data, status, headers, config) {
	        $scope.computers.data = data ;
		//Restoring previous states, selected line etc.
		for( var j = 0; j < data.length; ++j){
		    pingRequest(data[j]);
		    if ( $scope.idSelectedComputer !== undefined 
					&& 
			data[j].name == $scope.idSelectedComputer.name ){
			$scope.idSelectedComputer = data[j];
		    }
		    for( var i = 0; i < $scope.lsPings.length; ++i){
			    if ( $scope.lsPings[i].name == data[j].name ){
				$scope.lsPings[i] = data[j];
			    }
			}
		    }
                });
                responsePromise.error(function(data, status, headers, config) {
                    alert("Nem sikerült frissíteni a táblázatot! Ajax failed! :(");
                });	
	},
      showEditDialog: function(){
	if ( $scope.idSelectedComputer !== undefined ){ 
           var title = $("#dlgComputerTitle");
           title.html("Szerkesztés");
           title.attr("mode","edit");
           var computerName = $("#computerName");
           var computerMACAddr = $("#computerMACAddr");
           var computerIPAddr = $("#computerIPAddr");
	   computerName.val($scope.idSelectedComputer.name);
           computerMACAddr.val($scope.idSelectedComputer.macaddress);
           computerIPAddr.val($scope.idSelectedComputer.ipaddress);
           $scope.showComputerDialog();
	}
      },

      showAddDialog: function(){
           var title = $("#dlgComputerTitle");
           title.html("Hozzáadás");
           title.attr("mode","add");
           var computerName = $("#computerName");
           var computerMACAddr = $("#computerMACAddr");
           var computerIPAddr = $("#computerIPAddr");
	   computerName.val("");
           computerMACAddr.val("");
           computerIPAddr.val("");
           $scope.showComputerDialog();
      },
      showRmDialog: function(){
           var title = $("#dlgYoNTitle");
           title.attr("mode","del");
           title.html("Eltávolítás");
	   var msg = $("#dlgYoNMessage");
	   msg.html("Biztosan eltávolítja \"" + $scope.idSelectedComputer.name + "\"?");
	   $scope.showYoNDialog();
      },
      showWakeUpDialog: function(){
           var title = $("#dlgYoNTitle");
           title.attr("mode","wake");
           title.html("Ébresztés");
	   var msg = $("#dlgYoNMessage");
	   msg.html("Biztosan felébreszti \"" + $scope.idSelectedComputer.name + "\"?");
	   $scope.showYoNDialog();
      },
      okBtnHandlerComputerDialog: function(){
  	   var form = $( "#formComputerAdd" );
	   form.validate();
	
           if( !form.valid()){
		return;
	   }
           var computerName = $("#computerName");
           var computerMACAddr = $("#computerMACAddr");
           var computerIPAddr = $("#computerIPAddr");
	  
           var title = $("#dlgComputerTitle");
           if( title.attr("mode") == "add"){
           $.ajax({
		  url: "?add",
		  data: {'name': computerName.val(),
			 'mac' : computerMACAddr.val(),
			 'ip'  : computerIPAddr.val()
			},
		  success:function(){
                    $scope.computers.doRefresh();
		  },
		  fail: function(){
			alert("Hozzáadás nem sikerült!");	
		  },
		  complete: function(){
			$scope.closeComputerDialog();
		  }

		});
	      return;
	   }
           if( title.attr("mode") == "edit" ){
           $.ajax({
		  url: "?edit",
		  data: {
			 'name'   : $scope.idSelectedComputer.name,
			 'newname': computerName.val(),
			 'newmac' : computerMACAddr.val(),
			 'newip'  : computerIPAddr.val()		
			},
		  success:function(){
                    $scope.computers.doRefresh();
		  },
		  fail: function(){
			alert("Hozzáadás nem sikerült!");	
		  },
		  complete: function(){
			$scope.closeComputerDialog();
		  }

		});
	      return;
	   }
      }
};
      $scope.yesBtnHandlerYoNDialog= function(){
	if( $scope.idSelectedComputer !== undefined ){
	 
           var title = $("#dlgYoNTitle");
	   if( title.attr("mode") == "del" ){
	    $scope.closeYoNDialog();
	    $.ajax({
		  url: "?del",
		  data: {'name': $scope.idSelectedComputer.name },
		  success:function(){
                    $scope.computers.doRefresh();
		  },
		  fail: function(){
			alert("Eltávolítás nem sikerült! Ajax hiba! :(");	
		  },
		  complete: function(){
		  }

		});

            return;
	  }
	   if( title.attr("mode") == "wake" ){
	    $scope.closeYoNDialog();
	    $.ajax({
		  url: "?wake",
		  data: { 'name':$scope.idSelectedComputer.name },
		  success:function(){
                   // $scope.computers.doRefresh();
		  },
		  fail: function(){
			alert("Felébresztés nem sikerült! Ajax hiba! :(");	
		  },
		  complete: function(){
		  }

		});

            return;
	  }


	}
      }
});
$.validator.addMethod('IP4Checker', function(value) {
        var ip = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
            return value.match(ip);
}, 'Invalid IP address');
$.validator.addMethod(
        "checkMacAddr",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Helytelen MAC cím!"
);
	$("#formComputerAdd").validate({
		rules:{
			computerName:{
				required:true,				
				minlength:2,
				maxlength:255
			},
			computerMACAddr:{
				required:true,
				checkMacAddr:"([0-9a-fA-F]{2})([\:-]([0-9a-fA-F]{2})){5}"  
			},
			computerIPAddr:{	
				required:true,
				IP4Checker:true
			}
		},
		messages:{
			computerName:{
				required:"Nem lehet üres a mező!",
				minlength:"Minimum 2 karakter hosszú!",
				maxlength:"Maximum 15 karakter hosszú!"

			},
			computerMACAddr:{
				required:"Nem lehet üres a mező!"
			},
			computerIPAddr:{	
				required:"Nem lehet üres a mező!",
				IP4Checker:"Nem jó IPv4 cím formátum!"
			}
		},
		errorElement: "div",
		    wrapper: "div",
		    errorPlacement: function(error, element) {
			    offset = element.offset();
		        error.insertAfter(element)
		        error.css('color','red');
    		}
	});

});

