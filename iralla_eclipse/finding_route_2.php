<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'tools_to_look_for_roads.php';

$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid 
$foot_speed = 0.7; //0.7 m/s ~2.5km/h
$bus_speed = 7; //13 m/s ~30km/h

$path_of_roads = "c:/roads2/";
$path_of_squares = "c:/squares3/";

$time_lost_when_changing_bus_line = 600;

$request = $_POST['q'];
//$request = '{"start":{"lat":-2.172744609908308,"lng":-79.80077972412107},"end":{"lat":-2.210482487616563,"lng":-79.90377655029295}}';

//$request ='{"start":{"lat":-2.0907472653611823,"lng":-79.94669189453127},"end":{"lat":-2.1210250353406597,"lng":-79.95574703216555}}';
//$request = '{"start":{"lat":-2.076423017151715,"lng":-79.91639366149904},"end":{"lat":-2.0957221234194163,"lng":-79.91124382019045}}';

//from square is not a bus station
//$request = '{"start":{"lat":-2.1142490416697988,"lng":-79.9559616088867},"end":{"lat":-2.0950359370399525,"lng":-79.90446319580076}}';

//to square and from square are first and end bus station
//$request = '{"start":{"lat":-2.119738456543628,"lng":-79.97192611694334},"end":{"lat":-2.126257111418689,"lng":-79.87510910034177}}';	
//$request = '{"start":{"lat":-2.110689488832385,"lng":-79.94280805587766},"end":{"lat":-2.0856437307947853,"lng":-79.9060725212097}}';	

///erreur pretreatment
//$request = '{"start":{"lat":-2.106701064525724,"lng":-79.97518768310545},"end":{"lat":-2.125914025005733,"lng":-79.85502471923826}}';

//debria selerccionar a from square instead of a bus station
//$request = '{"start":{"lat":-2.126771740895112,"lng":-79.90841140747068},"end":{"lat":-2.2178584157769348,"lng":-79.90068664550779}}';


//$request = '{"start":{"lat":-2.134491162419215,"lng":-79.91630783081052},"end":{"lat":-2.2072233448863363,"lng":-79.90411987304685}}';
/*$request = '
{
	"start":{
		"lat":-2.192814417860611,"lng":-79.8878120422363},"end":{
			"lat":-2.1142490416697988,"lng":-79.91373291015623}
}
';*/
$request = json_decode($request);
//TO DEBUG
/*
$request = array();
$request[start] = array();
$request[start][lat] = -2.1949;
$request[start][lng] = -79.89035;

$request[end] = array();
$request[end][lat] = -2.2325;
$request[end][lng] = -79.890;*/
//END TO DEBUG

$start_real['lat'] = $request->start->lat;
$start_real['lng'] = $request->start->lng;
$end_real['lat'] = $request->end->lat;
$end_real['lng'] = $request->end->lng;

//test if start point and end point are possible:
if(!isset($start_real['lat']) || !is_numeric($start_real['lat']) || ($start_real['lat'] < -3) || ( -1 < $start_real['lat'])){
	return;
}
if(!isset($start_real['lng']) || !is_numeric($start_real['lng']) || ($start_real['lng'] < -80) || ( -78 < $start_real['lng'])){
	return;
}
if(!isset($end_real['lat']) || !is_numeric($end_real['lat']) || ($end_real['lat'] < -3) || ( -1 < $end_real['lat'])){
	return;
}
if(!isset($end_real['lng']) || !is_numeric($end_real['lng']) || ($end_real['lng'] < -80) || ( -78 < $end_real['lng'])){
	return;
}

//find nearst bus stations :
$interval = 0.005;

//$start_nearest_bus_stations = nearest_bus_stations($start, $interval, "bus_stations");
//$end_nearest_bus_stations = nearest_bus_stations($end, $interval, "bus_stations");
//end find nearest bus stations

//change start and end calcul to fit with to square and end square coordinates
$start['lat'] =abs( bcmul($request->start->lat, $grid_path_mult));
$start['lng'] =abs( bcmul($request->start->lng, $grid_path_mult));
$end['lat'] =abs( bcmul($request->end->lat, $grid_path_mult));
$end['lng'] =abs( bcmul($request->end->lng, $grid_path_mult));

//from square and to square
$interval = 5;
$ecart_min_between_d_min_and_d_max = 6;
$max_group_size = 15;

$start_squares_by_bs_id = nearest_squares($start, $interval, "from_square", $ecart_min_between_d_min_and_d_max, $max_group_size);
$end_squares_by_bs_id = nearest_squares($end, $interval, "to_square", $ecart_min_between_d_min_and_d_max, $max_group_size);

//create false start square of length = 0
//add_bus_stations_to_end_start_squares(&$start_squares, $start_nearest_bus_stations);
//add_bus_stations_to_end_start_squares(&$end_squares, $end_nearest_bus_stations);	

//end of creation of false  start and end square

$first = true;
foreach ($start_squares_by_bs_id as $bus_station_id => $start_square){
	if($first){
		$start_bus_stations_string = "( start_bus_station_id = ?";
		$first = false;
	}
	else{
		$start_bus_stations_string .= " OR start_bus_station_id = ?" ;
	}
	$values_for_mysql[] = $bus_station_id;
	$bus_stations_from_square[] = $bus_station_id;
}
$start_bus_stations_string .= ")";



$first = true;
foreach ($end_squares_by_bs_id as  $bus_station_id =>  $end_square){
	if($first){
		$end_bus_stations_string = "( end_bus_station_id = ?";
		$first = false;
	}
	else {
		$end_bus_stations_string .= " OR end_bus_station_id = ?" ;
	}
	$values_for_mysql[] = $bus_station_id;
	$bus_stations_to_square[] = $bus_station_id;
}
$end_bus_stations_string .= ")";


//find all the bus station to bus station from $start_squares and $end_squares
$req = $bdd->prepare("
	SELECT *
	FROM bus_stations_to_bus_stations
	WHERE $start_bus_stations_string
	AND $end_bus_stations_string
	ORDER BY time
");

$req->execute($values_for_mysql);

$shortest_road_time = +INF;

/////////////////////////////////////////////////////////////////////////////


// add the distance /////////////////////////////////

//then extrac path speed from database


//php my admin ; verifier si path non enregistré ds bdd pour le first and end bus line part
foreach($start_squares_by_bs_id as $bs_id => $squares_by_bl_id){
	$start_squares[$bs_id] = array();
	foreach($squares_by_bl_id as $bl_id => $square){
		$square = new My_square($square['id'], $square['lat'], $square['lng'], $square['bus_line_id'], $square['bus_line_name'], null, $square['time_to_bus_station'], 0);
		$start_squares[$bs_id][] = $square;
	}
}

foreach($end_squares_by_bs_id as $bs_id => $squares_by_bl_id){
	$end_squares[$bs_id] = array();
	foreach($squares_by_bl_id as $bl_id => $square){
		$square = new My_square($square['id'], $square['lat'], $square['lng'], $square['bus_line_id'], $square['bus_line_name'], null, $square['time_to_bus_station'], 0);
		$end_squares[$bs_id][] = $square;
	}
}

$start_bus_stations_list = array();
$end_bus_stations_list = array();
//todebug:
$count = 0;
//end to debug

$shortest_road = new Shortest_road();
//calculate the complete time of each possibility:
//and fusion the sames roads
while($bs2bss = $req->fetch()){
	//to debug
	$count++;
	//end to debug
	
	$road = json_decode($bs2bss['road_datas']);
	$bus_stations = /*&*/$road->bus_stations;
		
	//init $selected_from_square and $selected_to_square
	if(!key_exists($bs2bss['start_bus_station_id'], $start_bus_stations_list)){
		$start_bus_station = array();
		$start_bus_station['lat'] = $bs2bss['start_lat'];
		$start_bus_station['lng'] = $bs2bss['start_lng'];
		$start_bus_station['time_by_foot'] = real_distance_between_2_vertex($start_real, $start_bus_station) / $foot_speed;
		$start_bus_stations_list[$bs2bss['start_bus_station_id']] = $start_bus_station;
	}
	$selected_from_square = new stdClass();
	$selected_from_square->time_by_foot = $start_bus_stations_list[$bs2bss['start_bus_station_id']]['time_by_foot'];
	$selected_from_square->time = 0;
	$selected_from_square->time_lost = 0;
	
	if(!key_exists($bs2bss['end_bus_station_id'], $end_bus_stations_list)){
		$end_bus_station = array();
		$end_bus_station['lat'] = $bs2bss['start_lat'];
		$end_bus_station['lng'] = $bs2bss['start_lng'];
		$end_bus_station['time_by_foot'] = real_distance_between_2_vertex($end_real, $end_bus_station) / $foot_speed;
		$end_bus_stations_list[$bs2bss['end_bus_station_id']] = $end_bus_station;
	}
	$selected_to_square = new stdClass();
	$selected_to_square->time_by_foot = $end_bus_stations_list[$bs2bss['end_bus_station_id']]['time_by_foot'];
	$selected_to_square->time = 0;
	$selected_to_square->time_lost = 0;
	
	//find the start square and the end square matching
	
	$from_squares = $start_squares[$bs2bss['start_bus_station_id']];
	$to_squares = $end_squares[$bs2bss['end_bus_station_id']];
	
	//calculate the time by foot to reach the beginning of the path in the squares:
	time_by_foot_calculation(&$from_squares, $start_real);
	time_by_foot_calculation(&$to_squares, $end_real);
	
	//look for same bus line in $bs2bss than in from square and to square
	//$bus_lines_parts_length = count($road->bus_lines_parts);
	
	$added_time_first_bus_line_part = 0;
	$first_bus_line_part_selected = $road->first_and_last_bstobss_to_mysql[0][0];
	$merged_first_bus_line_part_with_from_square = false;
	
	//from square
	if(is_array($road->first_and_last_bstobss_to_mysql[0])){
		if($road->first_and_last_bstobss_to_mysql[0][0] != null){
			/*TODO : improve the execution speed using bus_line_id like:
			 * $road->first_and_last_bstobss_to_mysql[0]['bus_line_id']
			 * must modify bus_station_2_bus_station first
			*/
			
			//TODO: keep the differents from squares similar to the one selected

			foreach ($road->first_and_last_bstobss_to_mysql[0] as $key => $bus_line_part){
				foreach($from_squares as $from_square){
					if($bus_line_part->name == $from_square->name){
						//calculate the difference time necesary than to take the current
						//$selected_to_square
						$time_lost = $road->first_and_last_bstobss_to_mysql[0][$key]->time
							- $road->first_and_last_bstobss_to_mysql[0][0]->time;
						
						$time_diff = ($from_square->time + $from_square->time_by_foot + $time_lost)  -
						($selected_from_square->time + $selected_from_square->time_by_foot);
						
						if($time_diff < 0){
							$first_bus_line_part_selected = $bus_line_part;
							$merged_first_bus_line_part_with_from_square = true;
							$selected_from_square = $from_square;
							$selected_from_square->time_lost = $time_lost;
						}
					}
					else{
						$time_diff = ($from_square->time + $from_square->time_by_foot + $time_lost_when_changing_bus_line)
						- ($selected_from_square->time + $selected_from_square->time_by_foot);

						if($time_diff < 0){
							$first_bus_line_part_selected = $bus_line_part;
							$merged_first_bus_line_part_with_from_square = false;
							$selected_from_square = $from_square;
							$selected_from_square->time_lost = $time_lost_when_changing_bus_line;
						}
					}
				}
			}
		}
	}
	
	real_distance_between_2_vertex($end_real, $end_bus_station) / $foot_speed;
	
	$added_time_last_bus_line_part = 0;
	$last_bus_line_part_selected = null;
	$merged_last_bus_line_part_with_to_square = false;
	//to square
	if(is_array($road->first_and_last_bstobss_to_mysql[0])){
		if((isset($road->first_and_last_bstobss_to_mysql[1]))&&
		isset($road->first_and_last_bstobss_to_mysql[1][0])){
			$first_or_last = 1;
		}
		else{
			$first_or_last = 0;
		}
		$last_bus_line_part_selected = $road->first_and_last_bstobss_to_mysql[$first_or_last][0];
			
		foreach ($road->first_and_last_bstobss_to_mysql[$first_or_last] as $key => $bus_line_part){
			foreach($to_squares as $to_square){
				if($bus_line_part->name == $to_square->name){
					//calculate the added time necesary than take the
					//shortest $bus_lin_part:
					$time_lost = $road->first_and_last_bstobss_to_mysql[$first_or_last][$key]->time
					- $road->first_and_last_bstobss_to_mysql[$first_or_last][0]->time;
					
					$time_diff = $to_square->time + $to_square->time_by_foot + $time_lost
					- ($selected_to_square->time + $selected_to_square->time_by_foot);
					
					if($time_diff < 0){
						$last_bus_line_part_selected = $bus_line_part;
						$merged_last_bus_line_part_with_to_square = true;
						$selected_to_square = $to_square;
						$selected_to_square->time_lost = $time_lost;
					}
					else{
						$time_diff = $to_square->time + $to_square->time_by_foot + $time_lost_when_changing_bus_line
						- ($selected_to_square->time + $selected_to_square->time_by_foot);
					
						if($time_diff < 0){
							$last_bus_line_part_selected = $bus_line_part;
							$merged_last_bus_line_part_with_to_square = false;
							$selected_to_square = $to_square;
							$selected_to_square->time_lost = $time_lost_when_changing_bus_line;
						}
					}
				}
			}
		}
	}
	
	//if the last and first bus_line_part is the same one:
	if($first_or_last == 0){
		if(($merged_first_bus_line_part_with_from_square == true)
		&& ($merged_last_bus_line_part_with_to_square == true)){
			//if the bus_line_part selected is not the same one :
			if($first_bus_line_part_selected->name != $last_bus_line_part_selected->name){
				//select the quicker one:
				if($added_time_first_bus_line_part < $added_time_last_bus_line_part){
					$last_bus_line_part_selected = null;
					$added_time_last_bus_line_part = 0;
					$merged_last_bus_line_part_with_to_square = false;
				}
				else{
					$first_bus_line_part_selected = null;
					$added_time_first_bus_line_part = 0;
					$merged_first_bus_line_part_with_from_square = false;
				}
			}
		}
		else if ($merged_first_bus_line_part_with_from_square == true){
			$last_bus_line_part_selected = null;
		}
		else if ($merged_last_bus_line_part_with_to_square == true) {
			$first_bus_line_part_selected = null;
		}
		else{
			$last_bus_line_part_selected = null;
		}
	}
	
	//keep the shortest road:
	$total_time = $bs2bss['time'] + $added_time_first_bus_line_part + $added_time_last_bus_line_part
		+ $selected_to_square->time + $selected_to_square->time_by_foot + $selected_to_square->time_lost
		+ $selected_from_square->time + $selected_from_square->time_by_foot + $selected_from_square->time_lost;

	if( $total_time < $shortest_road->total_time){
		$shortest_road->bs2bss = $bs2bss;
		$shortest_road->first_bus_line_part = $first_bus_line_part_selected;
		$shortest_road->last_bus_line_part = $last_bus_line_part_selected;
		$shortest_road->total_time = $total_time;
		$shortest_road->from_square = $selected_from_square;
		$shortest_road->to_square = $selected_to_square;
		$shortest_road->merged_last_bus_line_part_with_to_square = $merged_last_bus_line_part_with_to_square;
		$shortest_road->merged_first_bus_line_part_with_from_square = $merged_first_bus_line_part_with_from_square;
	}
}


$road_to_send = $shortest_road->bs2bss;
$road_datas = json_decode($road_to_send['road_datas']);
$bus_stations = $road_datas->bus_stations;

//exctracts the complete datas of the selected road:
$file_to_open = $path_of_roads . "$road_to_send[start_bus_station_id]/$road_to_send[end_bus_station_id]";
$road = file_get_contents($file_to_open) or die("can't open file\n");
$road = json_decode($road);

$nbr_of_bus_lines_part = count($road);

//if the first bus line part is selected
//(it s not only in case the road has only one bus line part
//and when the last bus line part is selected instead)
if($shortest_road->first_bus_line_part != null){
	$bus_line_to_keep = null;
	///keep that one as the first bus line part:
	foreach ($road[0] as $bus_line_part) {
		if($bus_line_part->name == $shortest_road->first_bus_line_part->name){
			$bus_line_to_keep = $bus_line_part;
			break;
		}
	}
	if($bus_line_to_keep == null){
		exit("error 3333");
	}
	$road[0] = $bus_line_to_keep;
}

//if the last bus line part is selected
//(it s not only in case the road has only one bus line part
//and when the first bus line part is selected instead)
if($shortest_road->last_bus_line_part != null){
	$bus_line_to_keep = null;
	///keep that one as the last bus line part:
	foreach ($road[$nbr_of_bus_lines_part-1] as $bus_line_part) {
		if($bus_line_part->name == $shortest_road->last_bus_line_part->name){
			$bus_line_to_keep = $bus_line_part;
			break;
		}
	}
	if($bus_line_to_keep == null){
		exit("error 2569");
	}
	$road[$nbr_of_bus_lines_part-1] = $bus_line_to_keep;
}

//add the from square and to square to the road:
$from_square = $shortest_road->from_square;
$to_square = $shortest_road->to_square;

//found the path if exists
if(isset($from_square->id) && ($from_square->id > 0)){
	$file_to_open = $path_of_squares . "$from_square->lat/$from_square->lng/$from_square->id";
	$path = file_get_contents($file_to_open) or die("can't open file\n");
	$path = json_decode($path);
	$from_square->path = $path;
	//divide_all_coordinates_of_path($from_square->path, $denominator_to_get_real_values);
}

if(isset($to_square->id) && ($to_square->id > 0)){
	$file_to_open = $path_of_squares . "$to_square->lat/$to_square->lng/$to_square->id";
	$path = file_get_contents($file_to_open) or die("can't open file\n");
	$path = json_decode($path);
	$to_square->path = $path;
	//divide_all_coordinates_of_path($to_square->path, $denominator_to_get_real_values);
}

if($shortest_road->merged_last_bus_line_part_with_to_square == true){
	//replace the last bus station by a temporary one:
	$bus_stations[$nbr_of_bus_lines_part-1] = null;
	
	//merged the to square path with the last bus line:
	$road[$nbr_of_bus_lines_part-1]->path = 
		array_merge($road[$nbr_of_bus_lines_part-1]->path, $to_square->path);
}
else{
	//add a temporary bus station:
	 $bus_stations[] = null;
	 
	//add the to square as the last bus line:
	unset($to_square->bus_line_id);
	$road[] = $to_square;
}

//if to_square is not only a path by foot
if($to_square->name != null ){
	//add the foot path to go at the arrival point:
	$square_to_add = new My_square(0, 0, 0, 0,null,array(),0, $to_square->time_by_foot);
		
	//add a tempory bus station:
	 $bus_stations[] = null;
	 
	//add the $square_to_add as the last bus line:
	unset($square_to_add->bus_line_id);
	$road[] = $square_to_add;
}

if($shortest_road->merged_first_bus_line_part_with_from_square == true){
	//replace the first bus station by a temporary one:
	$bus_stations[0] = null;
	
	//merged the from square path with the first bus line:
	$road[0]->path = array_merge($from_square->path,$road[0]->path);
}
else{
	//add a tempory bus station:
	 array_unshift($bus_stations, null);
	 
	//add the from square as the first bus line:
	unset($from_square->bus_line_id);
	array_unshift($road, $from_square);
}

//if from_square is not only a path by foot
if($from_square->name != null ){
	//add the foot path to go the bus line:
	$square_to_add = new My_square(0, 0, 0, 0,null,array(),0, $from_square->time_by_foot);
		
	//add a tempory bus station:
	 array_unshift($bus_stations, null);
	 
	//add the $square_to_add as the first bus line:
	unset($square_to_add->bus_line_id);
	array_unshift($road, $square_to_add);
}

$to_send = new stdClass();
$to_send->bus_stations = $bus_stations;
$to_send->bs2bss = $road;
$to_send->time = $shortest_road->total_time;

//to debug
$to_send->from_square = $from_square;
$to_send->to_square = $to_square;

//end to debug
echo json_encode($to_send);

?>
