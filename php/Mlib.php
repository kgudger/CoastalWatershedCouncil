<?php

//error_reporting(E_ALL | E_STRICT);

class DB
{
    private $db;
	function __construct()
	{
    		$db = $this->connect();
	}

	function connect()
	{
	    if ($this->db == 0)
	    {
	        require_once("db2convars.php");
		try {
	        /* Establish database connection */
	        	$this->db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpwd);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (Exception $e) {
			echo "Unable to connect: " . $e->getMessage() ."<p>";
			die();
		}


	    }
	    return $this->db ;
	}

	function send($lat,$lon,$nam,$tdate,$atime,$site,$siteid,$teammates) 
	{
		echo "Site Id is " . $siteid . "\n";
		$nam = strtoupper($nam);
		$sql = "INSERT INTO `Collector` 
			(`name`, `lat`, `lon`, `tdate`, `atime`, `site`, `siteid`, `teammates`)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?) ";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($nam,$lat,$lon,$tdate,$atime,$site,$siteid,$teammates));
		$lastId = $this->db->lastInsertId();
		$iid = 1;
		
//		echo "Collector Entered";
		foreach ( $_REQUEST as $key => $value )
		{	
//			echo "key is " . $key . " and value is " . $value . "\n";
			if ( strpos($key,'-in') 
                              && !empty($value) )
			{
				$sql = "SELECT type, iid
					FROM `items`
					WHERE `aname` = ?";
				$stmt = $this->db->prepare($sql);
				$stmt->execute(array($key));
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$rowid = $row['iid'];
				$table_name = $row['type'] . '-tally' ;
				if ( isset($rowid) ) {
					$sql = "INSERT INTO `$table_name`
						(`cid`, `iid`,`value`)
						VALUES(?, ? , ?) ";
				
					$stmt = $this->db->prepare($sql);
					echo $key . " = " . $value . " row is " . $rowid . " table is " . $table_name . "\n" ;
					$stmt->execute(array($lastId,$rowid,$value));
				} //else
//					echo $table_name . " doesn't exist\n";
			} //else if ( empty($value) )
//				echo $value . " is empty\n";
		}
//		$this->getTally($nam);
		echo "Entered";
	}

	function getTally($name)
	{
	  $output = array();
	  if ( $name != "" ) {
		$sql = "SELECT SUM(number * weight) 
			FROM tally, items, Collector 
			WHERE (Collector.name = ?) AND
				Collector.cid = tally.cid AND
				tally.iid = items.iid AND
				items.recycle IS FALSE";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(strtoupper($name)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
//		$output[] = $row[0] / 50 ;
//		echo ($row["SUM(number)"]) ;
		$trash = is_null($row["SUM(number * weight)"]) ?
			0 : $row["SUM(number * weight)"] ;
		$output["trash"] = round($trash, 0, PHP_ROUND_HALF_UP);
	
		$sql = "SELECT SUM(number * weight) 
			FROM tally, items, Collector 
			WHERE (Collector.name = ?) AND
				Collector.cid = tally.cid AND
				tally.iid = items.iid AND
				items.recycle IS TRUE";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($name));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
//		echo ($row["SUM(number)"]) ;
//		$output[] = $row[0] / 50 ;
		$recycle = is_null($row["SUM(number * weight)"]) ?
			0 : $row["SUM(number * weight)"] ;
		$output["recycle"] = round($recycle, 0, PHP_ROUND_HALF_UP);
	  }
	  else {
		$output["trash"] = 0 ;
		$output["recycle"] = 0;
	  }
	  echo json_encode($output) ;
    }

	function getCats()
	{
		$sql = "SELECT name, item, aname 
			FROM `items`, `Categories` 
			WHERE items.category = Categories.catid 
			ORDER BY category";
		$result = $this->db->query($sql);
		$output = array();
		$cname = "" ;
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ( $row[name] != $cname ) {
				if ( $cname != "" ) {
					$output[$cname] = $o2;
				}
				$o2 = array();
				$cname = $row[name];
			}
			$o2[$row[item]] = $row[aname];
		}
		$output[$cname] = $o2 ;
		echo json_encode($output) ;
        }

	function getPlace($lat,$lon)
	{
        $sql = "SELECT pid,
        ( 3959 * acos( cos( radians(lat) ) * cos( radians( ? ) ) * 
		cos( radians( ? ) - radians(lon) ) + sin( radians(lat) ) * 
		sin( radians( ? ) ) ) ) 
		AS distance 
		FROM `Places` HAVING distance < 1.0
		ORDER BY distance
		LIMIT 1";
		 
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($lat,$lon,$lat));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$output = array();
		if ( is_null($result[pid]) ) {
			$output[place] = 0 ;
		} else {
			$output[place]=$result[pid];
		}
		$temp = array();
		$sql = "SELECT pid, name, lat, lon
			FROM `Places`";
		$result = $this->db->query($sql);
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[$row[name]] = array(pid=>$row[pid],lat=>$row[lat],lon=>$row[lon]);
		}
		$output[places] = $temp ;
		echo json_encode($output) ;
        }

	function getCategory()
	{
		$sql = "SELECT name
			FROM `Categories`
			ORDER BY name";
		$result = $this->db->query($sql);
		$output = array();
		$output[Category] = "Category" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row[name] ;
		}
		$output[results] = $temp ;
		echo json_encode($output) ;
        }

	function getName()
	{
		$sql = "SELECT DISTINCT name
			FROM `Collector`
			ORDER BY name";
		$result = $this->db->query($sql);
		$output = array();
		$output[Name] = "Name" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row[name] ;
		}
		$output[results] = $temp ;
		echo json_encode($output) ;
        }

	function getDate()
	{
		$sql = "SELECT Date
			FROM `Date`";
		$result = $this->db->query($sql);
		$output = array();
		$output[Date] = "Date" ;
		if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp = $row[Date] ;
		}
		$output[results] = $temp ;
		echo json_encode($output) ;
        }

	function getItem()
	{
		$sql = "SELECT item
			FROM `items`
			ORDER BY item";
		$result = $this->db->query($sql);
		$output = array();
		$output[Item] = "Item" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row[item] ;
		}
		$output[results] = $temp ;
		echo json_encode($output) ;
        }

     function getVoid($lat,$lon)
     {
        $sql = "SELECT id 
		,( 3959 * acos( cos( radians(37) ) * cos( radians( '$lat' ) ) * 
		cos( radians( '$lon' ) - radians(-122) ) + sin( radians(37) ) * 
		sin( radians( '$lat' ) ) ) ) 
		AS distance 
		FROM `kill` HAVING distance < 5"; 
        $result = $this->db->query($sql);
        $output = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
          $output[] = $row ;
        }
		$sql = "DELETE FROM `kill`
				WHERE ? > 0 ";
		$stmt = $this->db->prepare($sql);
//		$stmt->execute(array(1));

        echo json_encode($output) ;
     }
}
/* Below is Haversine select for distance of 25 miles
 *
 SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * 
                cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * 
                sin( radians( lat ) ) ) ) 
                AS distance 
                FROM markers HAVING distance < 25 
                ORDER BY distance LIMIT 0 , 20;
 *
 */

