<?php
ini_set("display_errors", 'Off');

//Connection with DB

$servername = "d26893.mysql.zonevs.eu";
$username = "d26893_busstops";
$password = "******";
$dbname = "";


$conn = new mysqli($servername,$username,$password,$dbname);
mysqli_set_charset($conn,"utf8");
if($conn->connect_error){
    die('Failed to connect!' .$conn->connect_error);
}

//MySQL request for first autocomplete 

if(isset($_POST['submit'])){
    $data=$_POST['search'];
    $sql="SELECT * FROM aleksandrmastakov_stops WHERE stop_area='$data' ";
    $result=$conn->query($sql);
    $row=$result->fetch_assoc();
    
    $stopArea = $row['stop_area'];
        
}

//MySQL request to get buss stop area names 

if(isset($stopArea)){
    
    $query = "SELECT stop_name FROM aleksandrmastakov_stops WHERE stop_area = '$stopArea'";
    if ($results = $conn->query($query)){
        while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC )) {
            $busslist[] = $row['stop_name'];
        };
    };
    $bAreaList = array_unique($busslist, SORT_REGULAR);
    sort($bAreaList);
}
else {
    $bAreaList = (array) null;
    
};

//Get user input stop area name and initialize it in PHP section

if(isset($_GET['foo'])){
    $busArea = $_GET['foo'];
    
    $quer = "SELECT * FROM aleksandrmastakov_stops WHERE stop_name = '$busArea' " ;
    if ($res = $conn->query($quer)){
        while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC )) {
            $busList[] = $row['stop_id'];
        };

    }else {
        $busList = (array) null;
    };

   
    date_default_timezone_set("Europe/Tallinn");
    $startTime = date("H:i:s");
    $keyvalue = array();
    foreach($busList as $b){
    
        $q = "SELECT * FROM aleksandrmastakov_stop_times WHERE stop_id = '$b'" ;
        if ($res = $conn->query($q)){
            
            while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC )){
                if ( $row['arrival_time'] > $startTime) {
                  
                    $keyvalue[$b][] = $row['arrival_time'];
                   
                    
                }
                
            }
             
        }
        
    }
};


if(empty($busList) || empty($keyvalue)){
    $dispNone = "display:none";
}



mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"></meta>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <title>Find your route</title>
    <style>
		h1 {
			text-align: center;
		}
        p {
			text-align: center;
		}
	</style>
</head>
<body>

<div class="container">

	<h1>Welcome to buss route application</h1>
	<p>Today is <?php echo date("F j, Y"); ?></p>
    <p>The current time is <?php echo date("h:i:s A"); ?></p>


    
    <div class="row">
        <div class="col-md-8 offset-md-2 p-4 mt-3 rounded">
            <form action="index.php" method="post" class="form-inline p-3" autocomplete="off">
                <input class="form-control form-control-lg rounded-0 border-info" type="text" name="search" id="search" style="width:70%;" placeholder="Search..." >
                <input type="submit" name="submit" value="Search" class="btn btn-info btn-lg rounded-0" style="width:20%;">
            </form>

        </div>
        <div class="col-md-5" style="position:absolute; margin-top:106px; margin-left: 215px; z-index: 150;">
            <div class="list-group w-75" id="show-list">           
            </div>
        </div>
    </div>
    <div id="ButtonArray" class="btn-toolbar" role="toolbar"> 
        <div class="btn-group mr-2" role="group" aria-label="First group">
        <form action="" method="GET" role="form" name="foo">
            <?php foreach ($bAreaList as $key): ?>
            <input class="stopButtons" type="submit" name="foo"  id="<?=$key?>" value="<?= $key?>"></button>
            <?php endforeach; ?>
        </form>
        </div>
    </div>
    <table id="mnTable" class="table" style="border: 2px solid black; <?=$dispNone?>">
    <thead class="thead-light" style="background-color: #cf9d9d; ">
    <p id="pTable" style="font-weight: bold;"></p>
    <tr>     
        <td scope="col" style="font-weight: bold;">Bus number</td>
        <td scope="col" style="font-weight: bold;">Arrival time</td>
        <td scope="col" style="font-weight: bold;">Bus number</td>
        <td scope="col" style="font-weight: bold;">Arrival time</td>
        
    </tr>
    </thead>
    <tbody>
   


    <?php error_reporting(E_ALL & ~E_NOTICE); foreach ($keyvalue as $kv => $v): ?>
    <?php echo "<tr>"; ?>
                <td><?=$kv?></td>         
    <?php $i=0;?>
    <?php foreach ($v as $val): ?>
        <?php if ($i<3): ?>
            <td><?=$val?></td>
        <?php $i++;?>
        
    <?php endif; ?>
    
    <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
    </table>
    <button id="rmvbtn" type="button" class="btn btn-info btn-sm rounded-0" onclick='removeBtn()';>Remove all</button>     
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#search").keyup(function(){
            var searchText = $(this).val();
            if(searchText!=''){
                $.ajax({
                    url: 'action.php',
                    method: 'post',
                    data:{query:searchText},
                    success:function(response) {
                        $("#show-list").html(response);
                    }
                });
            }
            else{
                $("#show-list").html('');
            }
        });
        $(document).on('click', 'a', function(){
            $("#search").val($(this).text());
            $("#show-list").html('');
        });
    });

function removeBtn(){
    document.getElementById("mnTable").style.display="none";
    document.getElementById("rmvbtn").style.display="none";
    document.getElementById("pTable").style.display="none";
    document.getElementById("ButtonArray").style.display="none";
}
</script>
<!-- Footer -->
<footer class="page-footer font-small blue" style="margin-top: 300px;">



</footer>
<!-- Footer -->
</body>
</html>