<?php 
    
$servername = "d26893.mysql.zonevs.eu";
$username = "d26893_busstops";
$password = "******";
$dbname = "d26893_busstops";


    $conn = new mysqli($servername,$username,$password,$dbname);
    mysqli_set_charset($conn,"utf8");
    if($conn->connect_error){
        die('Failed to connect!' .$conn->connect_error);
    }
    if(isset($_POST['query'])){
        $inpText = $_POST['query'];
        $query="SELECT stop_area FROM aleksandrmastakov_stops WHERE stop_area LIKE '%$inpText%' limit 100
        ";
        mysqli_set_charset($conn,"utf8");
        if ($result = $conn->query($query)){
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC )){
             $myArray[] = $row;
                
        };
        if($result->num_rows>0){
        $array = array();
        $array = array_unique($myArray, SORT_REGULAR);

        foreach($array as $user){
        echo "<a href='#' class='list-group-item list-group-item-action border-1'>".$user['stop_area']."</a>";
        }
        
        }
        else{
            echo "<p class='list-group-item border-1'>No Record</p>";
        }
    }
    }

    $conn->close();
?>