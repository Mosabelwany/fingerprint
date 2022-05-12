
<?php
ini_set('display_errors',1);
error_reporting(E_ALL);


require 'vendor/autoload.php';
require 'constant.php';

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

    // get sheet 
    $d=$spreadsheet->getSheet(0)->toArray();
    $reportData = [];

       foreach($d as $k=>$v){
           if($v[0] == "Originality Record：")
           {
                // $currentMonth = intval($v[20]) < 10 ?  '0'.intval($v[20]) : intval($v[20]);
                $currentMonth = intval($v[20]);
                $currentYear = intval($v[17]);
                $dateFrom = $v[9].$v[11].$v[12].$v[13].$v[14];
                $dateTo = $v[17].$v[19].$currentMonth.$v[21].$v[22];
                // $dateForm = $v[17].$v[19].$currentMonth.$v[21];
           }
            else if($v[0] ==  "ID:")
            {
                $name=$v[6];
            }
            else
            {
                foreach($v as $day=>$time){
                    if(is_string($time))
                    {
                        $check_time =explode(" ",$time);
                        $times=[];
                        foreach($check_time as $k=>$t){
                            if($t != "" && !in_array($t,$times))
                            {
                                $times[]=$t;
                            }
                        }
                        $reportData[$name][$day] = $times;
                    }
                }
            }
    }

}
// var_dump();
// die;
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
        <!--         
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
         -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
</head>
<body>
    <br>
    <h2 align="center">قائمة ساعات العمل لكل موظف</h2>
    <h3 align="center">من <?php echo $dateFrom." الي ".$dateTo;?></h3>
    
    <table class="table table-striped border" style="width:100%;" align="center" id="example" dir="rtl">
            <thead>
            <tr>
                <td align="center">الاسم</td>
                <td align="center">التفاصيل</td>
                <td align="center">ايام العمل</td>
                <td align="center">ايام الغياب</td>
                <td align="center">ايام العطلات</td>
                <td align="center">ساعات عمل اساسية</td>
                <td align="center">ساعات التاخيرات</td>
                <td align="center">ساعات الزيادة</td>
                <td align="center">ألراتب</td>
                <td align="center"></td>
            </tr>
            </thead>
            <tbody>
        <?php
            foreach($reportData as $k =>$v ){
             echo "<tr><td align='center'> ".$k."</td><td><table class='table' style='width:75%;' align='center'> <thead> <tr align='center'><td>يوم</td><td>من</td><td>إلي</td><td>المدة</td><td class='text text-danger'>تاخير</td></tr></thead><tbody>";
             $days = 0;
             $hours = 0;
             $hours_late = 0;
             $hours_extra = 0;

             $off_days_worked = 0;
             $halfDay =1647351000;
             date_default_timezone_set("UTC");
             foreach($v as $day=>$time){
                $miss=NULL;
                $total_session = NULL;
                $off_day_flag = NULL;
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
                
                 //  diff between times IN -OUT
                    $start = strtotime($enter);
                    $end = strtotime($exit);
                    $diff = $end - $start;

                    // ShifT Hours Late Enterance and exit before Constant Starting & Ending shif time 
                    if( $start > $start_shift){
                        $late_entrance = $start - $start_shift;
                        $early_exit = $end_shift -$end;
                        // Calculate Hours Late
                        if($end < $end_shift)
                            $early_exit =  $end_shift -$end;
                        else
                            $early_exit =  NULL;

                        if($early_exit)    
                            $late = $late_entrance + $early_exit;
                        else
                            $late = $late_entrance; 

                    }else{
                        $late = 0;   
                    }

                    // when there are many logins
                    if($total_session){
                        $diff = $total_session;
                    }
                    
                    // Calculate work hours 
                    $hours = $hours + $diff;

                    // Calculate late Hours 
                    $hours_late = $hours_late + $late;
                    
                    // Extra Hours +8 Hours
                    if($diff > $working_hours)
                        $hours_extra = $hours_extra + ($diff-$working_hours);
                    
                        
                    
                    if($miss){
                        if($start < $halfDay){
                            $exit = '-';
                        }else{
                            $enter = '-';
                        }
                    }
                    $d=mktime( 0,0,0, $currentMonth,$day+1,$currentYear);
                    $day_name  =  date("D", $d);
                    
                    // Holidays Working 
                    if($day_name == 'Fri' || $day_name == 'Sat'){
                        $off_days_worked++;
                        $off_day_flag =true;
                        // didn't sum HolidaysWork with days 
                        $days--;
                    }
                    // if working hours less than hour make red marker or more than 15 hours 
                    if($diff <= 3600 || $diff > 3600*15){
                        echo "<tr style='background-color:#ff6666;'><td align='center'> <b>".($off_day_flag != NULL ? '<b style="color:red">'.$day_name.'</b>':$day_name).' - '.($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'class='text text-danger'> ".date('H:i',$diff)."</td><td align='center'> - </td></tr>";
                    }else{
                        echo "<tr style=''><td align='center'> <b>".($off_day_flag != NULL ? '<b style="color:red">'.$day_name.'</b>':$day_name).' - '.($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'> ".date('H:i',$diff)."</td><td align='center'class='text text-danger'> ".date('H:i',$late)."</td></tr>";
                    }
                }
            
                $main_hours_diff =$hours-$hours_extra;
                //Expected Salary Formula 
                $Salary = ($main_hours_diff/60/60)*$hour_salary + ($hours_extra/60/60)*$hour_salary_dayoff;
                
                $main_hours = diffInHours($main_hours_diff);
                $hours_late = diffInHours($hours_late);
                $hours_extra = diffInHours($hours_extra);

              echo"</tbody></table></td><td align='center'> <b>".($days)."</b></td><td align='center'> <b>".($working_days-$days)."</b></td><td align='center'> <b>".($off_days_worked)."</b></td><td align='center'> <b>".$main_hours."</b></td><td align='center'> <b>".$hours_late."</b></td><td align='center'> <b>".$hours_extra."</b></td><td align='center'> <b>".$Salary."</b> </td> </tr>";
            }
            
            // formater Time in H:M
            function diffInHours ($hours){
                $hours = ($hours / 60/60);
                $hours = sprintf('%02d:%02d', (int) $hours, fmod($hours, 1) * 60);  
            return $hours;
            }
            
        ?>
</tbody>

    </table>
</body>
</html>

<script>
// $(document).ready(function() {
//     // $(row.child()).DataTable();
//     $('#example').DataTable( {
//         dom: 'Bfrtip',
//         searching: false,
//         responsive:true,
//         buttons: [
//             'copy', 'csv', 'excel', 'pdf', 'print'
//         ]
//     } );
// } );
</script>    
