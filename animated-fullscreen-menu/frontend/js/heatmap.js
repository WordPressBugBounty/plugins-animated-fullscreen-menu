// Listen all the possible clicks in the afsmenu 

window.addEventListener('load', function() {

	var afsmenu = document.querySelector('.animatedfsmenu');

	if(!afsmenu) return;

	afsmenu.addEventListener('click', function(e) {
		// Get the dimensions of the afsmenu element
		var afsmenuRect = afsmenu.getBoundingClientRect();
		
		// Calculate the percentage coordinates
		var percentageCoordinates = {
			x: (e.clientX - afsmenuRect.left) / afsmenuRect.width * 100,
			y: (e.clientY - afsmenuRect.top) / afsmenuRect.height * 100
		};
		
		// Get if the is mobile or desktop: 
		var isMobile = window.matchMedia("only screen and (max-width: 1024px)").matches;
		// Get the page name and page url 
		var pageName = document.querySelector('title').innerHTML;
		var pageUrl = window.location.href;
		// Send all the information to the server, json
		var data = {
			"pageName": pageName,
			"pageUrl": pageUrl,
			"percentageCoordinates": percentageCoordinates,
			"isMobile": isMobile
		};
		// Send the data to the server, using a REST API endpoint (GET method)
		var xhr = new XMLHttpRequest();
		xhr.open("GET", "/wp-json/animatedfsmenu/v1/saveheatmap?data=" + JSON.stringify(data), true);
		xhr.send();
	
		

		console.log('Clicked at percentage coordinates:', percentageCoordinates);
	});
});
