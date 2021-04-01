$( document ).ready(function(){
    
	$('#btnListar').click( function(){
		$.ajax({
			type: "GET",
			url: endpoint,
			//crossDomain: true,
			contentType: "application/json",
			dataType: "json",
			success: function(data, status){
                if(status === "success") {
                    console.log("Data: " + data + "\nStatus: " + status);
                    //var parsedData = JSON.stringify(data);
                    //console.log(data);
                    fncShowResults(data);
                }
			}
		});
	});

});
