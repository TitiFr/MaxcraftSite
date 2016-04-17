$(document).ready(function(){
$("#maxcraft").click(function() {
var display = $("#amospace").css('display');
if(display == 'none'){
  $("#amospace").show();
}
if(display == 'block'){
  //$("#amospace").css('display', 'none');
  $("#amospace").hide();
}
});


/*
$(".mainmenu").hover( function(){
	$('ul li', this).slideToggle('medium');

});
*/

$("li.mainmenu").click(function() { //When trigger is clicked...
	
	//Following events are applied to the subnav itself (moving subnav up and down)
	$(this).find("ul.submenu").slideDown('fast').show(); //Drop down the subnav on click

	$(this).hover(function() {
	}, function(){	
		$(this).parent().find("ul.submenu").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
	});

	//Following events are applied to the trigger (Hover events for the trigger)
	}).hover(function() { 
		$(this).addClass("subhover"); //On hover over, add class "subhover"
	}, function(){	//On Hover Out
		$(this).removeClass("subhover"); //On hover out, remove class "subhover"
});

$(".commentbutton").click(function(){

	$(this).next().slideToggle('medium');
});

$(".commentformbutton").click(function(){

	$(this).next().slideToggle('medium');
});

$(".miniature").hover(function(){

	$(".action", this).toggle();
});

$(".mphead").click(function(){

	$(this).next().toggle();
});





	var myArr = [];

	$.ajax({
		type: "GET",
		url: "http://www.maxcraft.fr/playerlist.xml", 
		dataType: "xml",
		success: parseXml,
		complete: [setupAC,setupACsearch],
		failure: function(data) {
			alert("XML File could not be found");
			}
	});

	function parseXml(xml)
	{
		//find every query value
		$(xml).find("player").each(function()
		{
			myArr.push($(this).attr("pseudo"));
		});	
	}
	
	function setupAC() {
		$("#form_pseudo").autocomplete({
				source: myArr,
				minLength: 1
		});
		
	}
	
	function setupACsearch() {
		$("#search").autocomplete({
				source: myArr,
				minLength: 1
		});
	}



	



});


