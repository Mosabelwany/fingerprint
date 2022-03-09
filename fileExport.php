<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قراءة الملف</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
        <!-- Popper JS -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <br>
    <h2 align="center">قائمة ساعات العمل لكل موظف</h2>
    <h3 align="center">من <?php echo $dateFrom." الي ".$dateTo;?></h3>
    
    <table class="table table-striped border" style="width:50%;" align="center" dir="rtl">
            <tr>
                <td align="center">الاسم</td>
                <td align="center">التفاصيل</td>
                <td align="center">ايام العمل</td>
                <td align="center">ساعات العمل</td>
            </tr>
        <?php
            foreach($reportData as $k =>$v ){
             echo "<tr><td align='center'> ".$k."</td><td><table align='center' > <tr><td>يوم</td><td>من</td><td>إلي</td><td>المدة</td></tr>";
             $days = 0;
             $hours = 0;
             date_default_timezone_set("UTC");
             foreach($v as $day=>$time){
                $redflag = 0;
                // multi or miss inputs                  
                if(count($time) < 2){
                    $enter = $time[0];
                    $exit = "00:00";
                    $redflag = 1;
                 }else if(count($time) > 2){
                     $enter = $time[1];
                     $exit = $time[2];
                 }else{
                    $enter = $time[0];
                    $exit = $time[1];
                 }
                 //  diff between times
                 $days++;
                // if($redflag = 0){
                    $start = strtotime($enter);
                    $end = strtotime($exit);
                    $diff = ($end - $start);
                    $hours = $hours + $diff;
                    echo "<tr><td align='center'> <b>".($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'> ".date('H:i',$diff)."</td></tr>";
                // }
                // else{
                    // echo "<tr style=' background-color:red;'><td align='center'> <b>".($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'></td></tr>";
                // } 
                 
                
             }
                echo"</table></td><td align='center'> <b>".($days)."</b></td><td align='center'> <b>".date('H:i',$hours)."</b></td></tr>";
            }
        ?>

    </table>
    
</body>
</html>
