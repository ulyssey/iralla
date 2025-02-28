<?php

require_once 'tools.php';

class My_square {
	public $id;
	public $lat;
	public $lng;
	public $bus_line_id;
	public $name;
	public $path;
	//public $distance;
	public $time;
	public $time_by_foot;

	public function __construct($id, $lat, $lng, $bus_line_id,$name, $path,/* $distance,*/ $time_to_bus_station, $time_by_foot){
		$this->id = $id;
		$this->lat = $lat;
		$this->lng = $lng;
		$this->bus_line_id = $bus_line_id;
		$this->name = $name;
		$this->path = $path;
		//$this->distance= $distance;
		$this->time = $time_to_bus_station;
		$this->time_by_foot = $time_by_foot;
	}
}

class Shortest_road{
	public $bs2bss;
	public $total_time;
	public $first_bus_line_part;
	public $last_bus_line_part;
	public $from_square;
	public $to_square;
	public $merged_last_bus_line_part_with_to_square;
	public $merged_first_bus_line_part_with_from_square;

	public function __construct(){
		$this->total_time = INF;
	}
}





function nearest_squares($from_lat_lng, $interval, $table_name, $ecart_min_between_d_min_and_d_max){
	global $bdd;
	global $foot_speed;
	global $bus_speed;
	global $grid_path_mult;

	//TODO add a coeff for the lng value depending of the latitude
	$values[0] = $from_lat_lng['lat'] - $interval;
	$values[1] = $from_lat_lng['lat'] + $interval;
	$values[2] = $from_lat_lng['lng'] - $interval;
	$values[3] = $from_lat_lng['lng'] + $interval;

	$req = $bdd->prepare('
			SELECT *
			FROM ' . $table_name . '
			WHERE ' . $table_name. '.lat BETWEEN ? AND ?
			AND	' . $table_name . '.lng BETWEEN ? AND ?
			ORDER BY id
			');

	$nearest_point = array();
	$shortest_distance = INF;
	$further_distance = 0;

	do{

		$squares = array();
		$req->execute($values);

		while($square = $req->fetch()){

			//$distance = sqrt(pow($square[lat] - $from_lat_lng[lat], 2) + pow($square[lng] - $from_lat_lng[lng], 2));

			$vertex_1 = array();
			$vertex_2 = array();
			$vertex_1['lat'] = $square['lat'] / $grid_path_mult;
			$vertex_1['lng'] = $square['lng'] / $grid_path_mult;
			$vertex_2['lat'] = $from_lat_lng['lat'] / $grid_path_mult;
			$vertex_2['lng'] = $from_lat_lng['lng'] / $grid_path_mult;

			$distance = real_distance_between_2_vertex($vertex_1, $vertex_2);
			//$distance = distanceBetweenTwoVertex($vertex_1, $vertex_2);

			if ($distance < $shortest_distance){
				$shortest_distance = $distance;
			}
			if ($distance > $further_distance){
				$further_distance = $distance;
			}
			$square['distance'] = $distance;
			$squares[] = $square;
		}

		if(!isset( $squares[0])){
			//if not any square found
			$interval *= 2;
			$values[0] -= $interval;
			$values[1] += $interval;
			$values[2] -= $interval;
			$values[3] += $interval;
		}
		else{
			//increase interval to get further_distance > shortest_distance + 300 metre environ
			//soit 15 square
			$values[0] -= $ecart_min_between_d_min_and_d_max + 2;
			$values[1] += $ecart_min_between_d_min_and_d_max + 2;
			$values[2] -= $ecart_min_between_d_min_and_d_max + 2;
			$values[3] += $ecart_min_between_d_min_and_d_max + 2;
		}
	}while(($ecart_min_between_d_min_and_d_max > ((int)$further_distance - (int)$shortest_distance) )
			||( $squares[0] == null));

	//found squares to get shortest time to go to a bus station
	$selected_squares = array();
	foreach ($squares as $square) {
		//if($selected_squares[] == null){
		if(!array_key_exists($square['id_of_bus_station_linked'], $selected_squares)){
			$selected_squares[$square['id_of_bus_station_linked']] = array();
		}
		$square['time_by_foot'] = $square['distance'] / $foot_speed;
		$square['time_by_bus'] = $square['length'] / $bus_speed;
		$square['time_to_bus_station'] = $square['time_by_foot'] + $square['time_by_bus'];
		
		if((!array_key_exists($square['bus_line_id'], $selected_squares[$square['id_of_bus_station_linked']])) ||
		 ($selected_squares[$square['id_of_bus_station_linked']][$square['bus_line_id']]['time_to_bus_station'] > $square['time_to_bus_station'] ))
		 {
			$selected_squares[$square['id_of_bus_station_linked']][$square['bus_line_id']] = $square;
		}
	}

	return $selected_squares;
}


function nearest_bus_stations($from_lat_lng, $interval, $table_name){
	global $bdd;
	global $foot_speed;

	$values[0] = $from_lat_lng['lat'] - $interval;
	$values[1] = $from_lat_lng['lat'] + $interval;
	$values[2] = $from_lat_lng['lng'] - $interval;
	$values[3] = $from_lat_lng['lng'] + $interval;

	$req = $bdd->prepare('
			SELECT *
			FROM ' . $table_name . '
			WHERE ' . $table_name. '.lat BETWEEN ? AND ?
			AND	' . $table_name . '.lng BETWEEN ? AND ?
			AND type != "invisble"
			AND type != "boundary"
			');

	$nearest_point = array();
	$bus_stations = array();
	while(count($bus_stations) == 0){

		$req->execute($values);
		$shortest_distance = INF;

		while($bus_station = $req->fetch()){

			$bus_station['distance'] = real_distance_between_2_vertex($from_lat_lng, $bus_station);
			$bus_station['time_by_foot'] =  $bus_station['distance'] / $foot_speed;
			$bus_stations[] = $bus_station;
		}
		$interval *= 2;
		$values[0] -= $interval;
		$values[1] += $interval;
		$values[2] -= $interval;
		$values[3] += $interval;
	}
	return $bus_stations;
}


/*
 // not used:
function nearest_point_from_array_to_point($array, $point){
$shortest_distance = -log(0);
foreach ($array as $point_to_compare) {
$distance = sqrt(pow($point[lat] - $point_to_compare[lat], 2) + pow($point[lng] - $point_to_compare[lng], 2));
if ($distance < $shortest_distance){
$nearest_point = $point_to_compare;
$shortest_distance = $distance;
}
}
$nearest_point[distance] = $shortest_distance * $grid_path_mult;

return $nearest_point;
}

function extract_path_from_string_2($path_as_string){

if($path_as_string == null){
return null;
}

$path = array();
$path_lat_lngs = json_decode($path_as_string);

foreach ($path_lat_lngs as $lat_lng){
$vertex = new stdClass();
$vertex->lat = $lat_lng->lat;
$vertex->lng = $lat_lng->lng;
$path[] = $vertex;
}
return $path;

}*/

function add_bus_stations_to_end_start_squares($start_or_end_squares, $start_or_end_nearest_bus_stations){
	foreach ($start_or_end_nearest_bus_stations as $start_bus_station) {
		foreach (array_keys($start_or_end_squares) as $bus_station_id_of_start_square) {
			if($bus_station_id_of_start_square == $start_bus_station['id']){
				if($start_bus_station['time_by_foot']
						< $start_or_end_squares[$bus_station_id_of_start_square]['time_to_bus_station']){
					//the time to go to the bus station by foot is quicker than by the "$start square"
					//create a false start square with the bus station:
					break;
				}
				else{
					//the time to go to the bus station by foot is slower than by the "$start square"
					//keep the one from start square
					continue 2;
				}
			}
		}
		//create a false start square with the bus station:
		$start_or_end_squares[$start_bus_station['id']]= array();
		$start_or_end_squares[$start_bus_station['id']]['id']= 0;
		$start_or_end_squares[$start_bus_station['id']]['lat']= 0;
		$start_or_end_squares[$start_bus_station['id']]['lng']= 0;
		$start_or_end_squares[$start_bus_station['id']]['time_by_foot'] = $start_bus_station['time_by_foot'];
		$start_or_end_squares[$start_bus_station['id']]['time_to_bus_station'] = 0;
		$start_or_end_squares[$start_bus_station['id']]['path'] = json_encode(array());
		$start_or_end_squares[$start_bus_station['id']]['bus_line_name'] = null;
	}
	return $start_or_end_squares;
}

function add_bus_stations_to_position_squares($start_or_end_squares, $start_or_end_nearest_bus_stations){
	return add_bus_stations_to_end_start_squares($start_or_end_squares, $start_or_end_nearest_bus_stations);
}
/*

function nearest_point($from_lat_lng, $interval, $table_name){
global $bdd;

$values[0] = $from_lat_lng[lat] - $interval;
$values[1] = $from_lat_lng[lat] + $interval;
$values[2] = $from_lat_lng[lng] - $interval;
$values[3] = $from_lat_lng[lng] + $interval;

$req = $bdd->prepare('
		SELECT *
		FROM ' . $table_name . '
		WHERE ' . $table_name. '.lat BETWEEN ? AND ?
		AND	' . $table_name . '.lng BETWEEN ? AND ?
		');

$nearest_point = array();
while(count($nearest_point) == 0){

$req->execute($values);
$shortest_distance = -log(0);

while($square = $req->fetch()){

$distance = real_distance_between_2_vertex($square, $from_lat_lng);

if ($distance < $shortest_distance){
$nearest_point = array();
$shortest_distance = $distance;
$nearest_point[] = $square;
}
elseif ($distance == $shortest_distance){
$nearest_point[] = $square;
}
}
$interval *= 2;
$values[0] -= $interval;
$values[1] += $interval;
$values[2] -= $interval;
$values[3] += $interval;
}
$nearest_point[distance] = $shortest_distance;
return $nearest_point;
}

*/

function time_by_foot_calculation(&$squares, $start_bus_station){
	global $foot_speed;
	global $grid_path_mult;
	//TODO improve it to get the 
	$calculation_coord = array();
	foreach($squares as $square){
		$calculation_coord['lat'] = $square->lat / $grid_path_mult;
		$calculation_coord['lng'] = $square->lng / $grid_path_mult;
		$square->time_by_foot = real_distance_between_2_vertex($calculation_coord, $start_bus_station) / $foot_speed;
	}
}






