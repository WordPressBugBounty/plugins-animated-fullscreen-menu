

jQuery.ajax({
			type : "POST",
			dataType : "json",
			url : ajaxurl,
			data : {action: "render_heatmap"},
			success: function(response) {

				var decodedResponse = JSON.parse(response['data']);
				console.log(decodedResponse);

				foreach( decodedResponse as key => value ) {
				
				}
			}
	});   
