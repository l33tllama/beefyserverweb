//Global variables
var saved_left = '<button id = "prev_day" onclick="prev_day()"></button>';
var saved_right = '<button id = "next_day" onclick="next_day()"></button>';

//Date object that stores currently chosen date
var current_date = new Date();

//Today's date'
var today = new Date();

// Has started cahrt auto update?
var started_update = false;

//Chart update timer id
var chart_update_id = 0;

var days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

window.onload = function(){
	check_page_setters();
	// alert("Annoying alert");
}

// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChart);

// Callback that creates and populates a data table, 
// instantiates the pie chart, passes in the data and
// draws it.
function drawChart() {
	//Continue to update chart if day is today
	if(current_date == today && !started_update){
		chart_update_id = window.setInterval("drawChart()",30000);
		started_update = true;
	}
	
	// Convert current date to string for presenting on chart title
	//var selected_date_date = new Date(selected_date);
	var month_full = months[current_date.getMonth()];
	var day_full = days[current_date.getDay()];
	var date_full = current_date.getDate();
	var year_full = current_date.getFullYear();
	
	var current_date_string = year_full + "-" + monthStr(current_date.getMonth() + 1) + "-" + monthStr(date_full);
	// alert("trying a ajax query!");	
	// Create the data table.
	var jsonData = $.ajax({
	url: "./misc-php/minecraft/get_mc_player_data.php?date=" + current_date_string,
	dataType:"json",
	async: false
	}).responseText;
	// alert(jsonData);
	var jsonDataManual = { 
		cols:[	{id:"",label:"Time", pattern: "",type:"string"},
			{id:"",label:"Player Count", pattern:"",type:"number"},
			{id:"",label:"Players", pattern: "", type: "string", p:{ role:"tooltip", html:true}}],
		rows:[	{c:[
				{v:"15:29",f:null},
				{v:0,f:null},
				{v:"15:29 : 0 : ",f:null}]
			},
			{c:[
				{v:"15:48",f:null},
				{v:0,f:null},
				{v:"15:48 : 0 : ",f:null}]
			},
			{c:[
				{v:"16:29",f:null},
				{v:0,f:null},
				{v:"16:29 : 0 : ",f:null}]
			}
		]
	}
	
	var testData = {
       cols: [{id: 'task', label: 'Task', type: 'string'},
                {id: 'hours', label: 'Hours per Day', type: 'number'}],
       rows: [{c:[{v: 'Work'}, {v: 11}]},
              {c:[{v: 'Eat'}, {v: 2}]},
              {c:[{v: 'Commute'}, {v: 2}]},
              {c:[{v: 'Watch TV'}, {v:2}]},
              {c:[{v: 'Sleep'}, {v:7, f:'7.000'}]}
             ]
     }
    
	// alert(JSON.stringify(eval("(" + jsonData + ")")));
	
	var data = new google.visualization.DataTable(jsonData, 0.6);

	//alert ("created gcharts datatable.");
	
	var formatted_date = day_full + ", " + date_full + " " + month_full + ", " + year_full;
	
	if (started_update){
		formatted_date += " (Auto-updating every 30s)";
	}
	
  	//Set chart options
  	var options = { 'title': 'Players on BeefyServer on ' + formatted_date,
			'width': 500,
			'height': 320,
			vAxis : { maxValue: 16 },
			tooltip: { isHtml: true },
			legend: 'none' };

	// Instantiate and draw our chart, passing in some options.
  	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
  	chart.draw(data, options);
	// alert("chart drawn!?!?!");
}

//Convert month string to date format for numbers less than 10
function monthStr(input){
	var outp = "";
	if(input < 10){
		outp = "0" + input.toString();
	} else {
		outp = input.toString();
	}
	return outp;
}

//Remove appropriate button if prev or next day doesn't exist
function check_page_setters(){

	var first_date = new Date("2013-05-17");
	
	var prev_content = "";
	var next_content = "";
	
	//If current date is day before no entries
	//Remove left button
	if(current_date <= first_date){
		prev_content = "";
	} else {
		prev_content = saved_left;
	}
	
	//If current date is today
	//Remove right button
	if (current_date >= today){
		next_content = "";
	} else {
		next_content = saved_right;
	}
	
	document.getElementById('prev_day').innerHTML = prev_content;
	document.getElementById('next_day').innerHTML = next_content;
	
	//Stop update interval if not today or start again if reached today 
	// If timer has started but date is not today, stop updating chart
	if(started_update && (current_date < today)){
		clearInterval(chart_update_id);
		chart_update_id = 0;
		started_update = false;
	//Otherwise if current date is today and not started updating timer, start timer
	} else if ((current_date >= today) && !started_update){
		chart_update_id = setInterval("drawChart()",30000);
		started_update = true;
	}
}
function prev_day(){
	current_date.setDate(current_date.getDate() - 1);
	check_page_setters();
	drawChart();
}
function next_day(){
	current_date.setDate(current_date.getDate() + 1);
	check_page_setters();
	drawChart();
}
