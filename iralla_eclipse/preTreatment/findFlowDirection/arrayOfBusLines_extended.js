/**
 * @author Yoh
 */
SubMap._busStationsArray.enableAddArrow = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableAddArrow();
	}

	//add a button to deselect "AddArrow" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{SubMap._busStationsArray.disableAddArrow(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
	
};

SubMap._busStationsArray.disableAddArrow = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfAddArrow) != 'undefined') && (this[i].idOfListenerOfAddArrow >= 0 ))
			this[i].disableAddArrow();
	}
};

SubMap._busStationsArray.enableAddBoundary = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableAddBoundary();
	}
	
	//add a button to deselect "AddBoundary" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{SubMap._busStationsArray.disableAddBoundary(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
		
};

SubMap._busStationsArray.disableAddBoundary = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfAddBoundary) != 'undefined') && (this[i].idOfListenerOfAddBoundary >= 0 ))
			this[i].disableAddBoundary();
	}
};

SubMap._busStationsArray.enableRemoveArrows = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableRemoveArrows();
	}

	//add a button to deselect "RemoveArrows" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{SubMap._busStationsArray.disableRemoveArrows(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
	
};

SubMap._busStationsArray.disableRemoveArrows = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfRemoveArrows) != 'undefined') && (this[i].idOfListenerOfRemoveArrows >= 0 ))
			this[i].disableRemoveArrows();
	}
};

SubMap._busStationsArray.enableFindFlowAuto = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableFindFlowAuto();
	}

	//add a button to deselect "FindFlowAuto" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick',
			"{SubMap._busStationsArray.disableFindFlowAuto(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

SubMap._busStationsArray.disableFindFlowAuto = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfFindFlowAuto) != 'undefined') && (this[i].idOfListenerOfFindFlowAuto >= 0 )){
			this[i].disableFindFlowAuto();
		}
	}
};


SubMap._busStationsArray.enableFindFlowFromXmlArrows = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null){
		document.getElementById('button_deselect').click();
	}
	
	for(var i = 0; i < this.length; i++){
		this[i].enableFindFlowFromXmlArrows();
	}

	//add a button to deselect 'find flow from xml arrows' function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{SubMap._busStationsArray.disableFindFlowFromXmlArrows();" +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

SubMap._busStationsArray.disableFindFlowFromXmlArrows = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfFindFlowFromXmlArrows) != 'undefined') && (this[i].idOfListenerOfFindFlowFromXmlArrows >= 0 )){
			this[i].disableFindFlowFromXmlArrows();
		}
	}
};

SubMap._busStationsArray.enableReverseFlow = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null){
		document.getElementById('button_deselect').click();
	}
	
	for(var i = 0; i < this.length; i++){
		this[i].enableReverseFlow();
	}

	//add a button to deselect 'find flow from xml arrows' function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{SubMap._busStationsArray.disableReverseFlow();" +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

SubMap._busStationsArray.disableReverseFlow = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfReverseFlow) != 'undefined') && (this[i].idOfListenerOfReverseFlow >= 0 )){
			this[i].disableReverseFlow();
		}
	}
};

SubMap._busStationsArray.enableAddBidirectionalArrows = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null){
		document.getElementById('button_deselect').click();
	}
	
	for(var i = 0; i < this.length; i++){
		this[i].enableAddBidirectionalArrows();
	}

	//add a button to deselect "AddBidirectionalArrows" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', "{" +
			"SubMap._busStationsArray.disableAddBidirectionalArrows(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

SubMap._busStationsArray.disableAddBidirectionalArrows = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfAddBidirectionalArrows) != 'undefined') && (this[i].idOfListenerOfAddBidirectionalArrows >= 0 )){
			this[i].disableAddBidirectionalArrows();
		}
	}
};

SubMap._busStationsArray.findFlowOfAllBusLines = function(index){
	if (( typeof(map.allArrowsFromFileShown) == 'undefined' ) || ( map.allArrowsFromFileShown === false )){
		showArrowsOnMap();
		map.allArrowsFromFileShown = true;
	}

	var infos = getInfosPreBoxNode();
	
	//show the progression:
	if (document.getElementById('progression') === null) {
		var progression = document.createElement('p');
		progression.setAttribute('id', 'progression');
		infos.appendChild(progression);
	}
	var lastIndex = this.length - 1;
	document.getElementById('progression').innerHTML = index + ' / ' + lastIndex + 'bus lines processed <br\> bus lines for which the flow could not be determinated:';

	//in case the flow could not be determinated:
	if (this[index].findFlow(false) === false){
		//show the name of the bus line:
		var newLine = document.createElement('div');
		newLine.setAttribute('id', 'div' + this.index);
		newLine.innerHTML = index + ') ' + this[index].name + 'click here to see the bus line';
		document.getElementById('infos').appendChild(newLine);
		//make it clikcable to show only this bus line in the map:
		newLine.setAttribute('onclick', 'SubMap._busStationsArray.showOnlyOneBusLine(' + index + ');');
		//give the possibility to remove it from the SubMap._busStationsArray:
		
	}
	
	index++;
	if(index < this.length){
		setTimeout("function(){SubMap._busStationsArray.findFlowOfAllBusLines( ' + index + ' )}",100);
	}
};


SubMap._busStationsArray.showOnlyOneBusLine = function(index){
	for ( var i = 0; i <  this.length; i++){
		if ( i != index){
			this[i].setMap(null);
		}
		else{
			this[i].setOptions({
				map: map,
				strokeOpacity: 1
			});
		}
	}
	
	//create a button to show all the buslines:
	if (document.getElementById('button_show_all_bus_lines') === null) {
		newField = newLineOfTablePreTreatment();
		var button_show_all_bus_lines = document.createElement('button');
		button_show_all_bus_lines.setAttribute('id', 'button_show_all_bus_lines');
		button_show_all_bus_lines.innerHTML = 'showAllBusLines';
		newField.appendChild(button_show_all_bus_lines);
		button_show_all_bus_lines.setAttribute('onclick',
				"SubMap._busStationsArray.showAllBusLine();" +
				"removeNodeById(this);" +
				"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
				);
	}
	
};

SubMap._busStationsArray.showAllBusLine = function(index){
	for (var i = 0; i < this.length; i++) {
		this[i].setOptions({
			map: map,
			strokeOpacity: 0.5
		});
	}
};

SubMap._busStationsArray.saveFlowsInDatabase = function(){

	//create a JSON object to send to the database:
	var datasToSend = [];
	var oneBusLineFlow;

	//for each bus line:
	for (var i = 0; i < SubMap._busStationsArray.length; i++){

		//if sections, ie: flows have been determinated
		if (typeof(SubMap._busStationsArray[i].sections) != 'undefined'){
			//record the id:
			oneBusLineFlow.id = SubMap._busStationsArray[i].id;
			//init to record the flows:
			oneBusLineFlow.flows = '';

			//for each section:
			for(var j = 0; j < SubMap._busStationsArray[i].sections.length; j++){
				//if arrows in the section:
				if (typeof(SubMap._busStationsArray[i].sections[j].arrayOfArrows) != 'undefined'){
					//record the flow:
					oneBusLineFlow.flows += ' ' + SubMap._busStationsArray[i].sections[j].arrayOfArrows[0].flow;
				}
			}

			oneBusLineFlow.boundaries = '';

			if (typeof(arrayOfBoundaries) != 'undefined'){
				for( j = 0; j < SubMap._busStationsArray[i].arrayOfBoundaries.length; j++){

					oneBusLineFlow.boundaries += ',' + SubMap._busStationsArray[i].arrayOfBoundaries[j].center.lat() + ' ' +
						SubMap._busStationsArray[i].arrayOfBoundaries[j].center.lng();
				}
				oneBusLineFlow.boundaries = oneBusLineFlow.boundaries.removeFirstLetter();
			}
			datasToSend.push(oneBusLineFlow);
		}
	}
	//send datas:
	

};

SubMap._busStationsArray.showFlows = function(){
	for(var i = 0; i < SubMap._busStationsArray.length; i++){
		SubMap._busStationsArray[i].showFlow();
	}
};

SubMap._busStationsArray.hideFlows = function(){
	for(var i = 0; i < SubMap._busStationsArray.length; i++){
		SubMap._busStationsArray[i].hideFlow();
	}
};

loaded.findFlowDirection.push('SubMap._busStationsArray_extended.js');

