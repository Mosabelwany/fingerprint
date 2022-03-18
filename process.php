
<?php
ini_set('display_errors',1);

// include 'vendor/autoload.php';
require 'vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
// $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();

if($_FILES['Sheet']){
    
    // convert file format from XLS to Xlsx 
    $xls_to_convert = $_FILES['Sheet']['tmp_name'];
    $objPHPExcel = IOFactory::load($xls_to_convert);
    $objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx');
    $objWriter->save(str_replace('.xls', '.xlsx', $xls_to_convert));
    $spreadsheet = $reader->load($xls_to_convert);
    

    // get count of sheets in xlsx file 
    $num_of_sheets=$spreadsheet->getSheetCount();
    
    // get sheet Names 
    $sheetNames =$spreadsheet->getSheetNames();
    
    // output array  
    $sheets =array();

    session_start();
    // get sheet 
    $d=$spreadsheet->getSheet(0)->toArray();
    $reportData = [];
       foreach($d as $k=>$v){
           if($v[0] == "Originality Record："){
                $dateFrom = $v[9].$v[11].$v[12].$v[13].$v[14];
                $dateTo = $v[17].$v[19].$v[20].$v[21].$v[22];
                // $reportData['from'] = $dateFrom;
                // $reportData['to'] = $dateTo;
            }else if($v[0] ==  "ID:"){
                $name=$v[6];
                // if(!array_key_exists($name,$reportData)){
                //     // $reportData[]= $name;
                // }
            }else{
                foreach($v as $day=>$time){
                    if(is_string($time)){
                        $check_time =explode(" ",$time);
                        $times=[];
                        foreach($check_time as $k=>$t){
                            // $log1 = $
                            // if()

                            if($t != "" && !in_array($t,$times)){
                                $times[]=$t;
                            }
                            
                        }
                        $reportData[$name][$day] = $times;
                    }
                }
            }
    }
    // var_dump($reportData);
}
$html2pdf = new Html2Pdf();
?>

<!--  report file -->
<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قراءة الملف</title>
        <!-- Latest compiled and minified CSS -->
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        
</head>
<body>
    <br>
    <h2 align="center">قائمة ساعات العمل لكل موظف</h2>
    <h3 align="center">من <?php echo $dateFrom." الي ".$dateTo;?></h3>
    
    <table class="table table-striped border" style="width:50%;" align="center" id="example" dir="rtl">
            <thead>
            <tr>
                <td align="center">الاسم</td>
                <td align="center">التفاصيل</td>
                <td align="center">ايام العمل</td>
                <td align="center">ساعات العمل</td>
            </tr>
            </thead>
            <tbody>

            
        <?php
            foreach($reportData as $k =>$v ){
             echo "<tr><td align='center'> ".$k."</td><td><table class='table' style='width:50%;' align='center'> <thead> <tr align='center'><td>يوم</td><td>من</td><td>إلي</td><td>المدة</td></tr></thead><tbody>";
             $days = 0;
             $hours = 0;
             $halfDay =1647351000;
             date_default_timezone_set("UTC");
             foreach($v as $day=>$time){
                $miss=NULL;
                $total_session = NULL;
                // multi or miss inputs                  
                if(count($time) < 2){

                    $enter = $time[0];
                    $exit = $time[0];
                    $miss = 1;
                 }else if(count($time) > 2){
                     $enter = $time[0];
                     $exit = $time[count($time)-1];
                     // many login & logout
                    if(count($time)%2 == 0){
                        $total_session = 0;
                        for($i=0;$i<=count($time)-1;$i+=2){
                            $login = strtotime($time[$i]); 
                            $logout = strtotime($time[$i+1]); 
                            $diff_multi = $logout -$login;
                            $total_session = $total_session+ $diff_multi;
                        }
                    }
                    
                 }else{
                    $enter = $time[0];
                    $exit = $time[1];
                 }

                 $days++;
                 //  diff between times
                    $start = strtotime($enter);
                    $end = strtotime($exit);
                    $diff = ($end - $start);
                    // when there are many logins
                    if($total_session){
                        $diff = $total_session;
                    }
                    // add hours 
                    $hours = $hours + $diff;
                    if($miss){
                        if($start < $halfDay){
                            $exit = '-';
                        }else{
                            $enter = '-';

                        }
                    }
                    // if working hours less than hour make red marker or more than 15 hours 
                    if($diff <= 3600 || $diff > 3600*15){
                        echo "<tr style='background-color:#ff6666;'><td align='center'> <b>".($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'> ".date('H:i',$diff)."</td></tr>";
                    }else{
                        echo "<tr style=''><td align='center'> <b>".($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'> ".date('H:i',$diff)."</td></tr>";
                    }
                }
            $hours = ($hours / 60/60);
            $hours = sprintf('%02d:%02d', (int) $hours, fmod($hours, 1) * 60);
              echo"</tbody></table></td><td align='center'> <b>".($days)."</b></td><td align='center'> <b>".$hours."</b></td></tr>";
            }
        ?>
</tbody>

    </table>
</body>
</html>

<script>
$(document).ready(function() {
    // $(row.child()).DataTable();
    $('#example').DataTable( {
        dom: 'Bfrtip',
        searching: false,
        responsive:true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );
</script>    
