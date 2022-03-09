
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
                if(!array_key_exists($name,$reportData)){
                    // $reportData[]= $name;
                }
            }else{
                foreach($v as $day=>$time){
                    if(is_string($time)){
                        $check_time =explode(" ",$time);
                        // var_dump($check_time);
                        $times=[];
                        foreach($check_time as $k=>$t){
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
// ob_end_clean();
// $content = file('fileExport.php');
// $html2pdf->writeHTML($content);
// $html2pdf->output('myPdf.pdf'); // Generate and load the PDF in the browser.
// 
?>
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
    
    <table class="table table-striped border" style="width:50%;" align="center" id="Table" dir="rtl">
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
                // multi or miss inputs                  
                if(count($time) < 2){
                    $enter = $time[0];
                    $exit = $time[0];

                 }else if(count($time) > 2){
                     $enter = $time[1];
                     $exit = $time[2];
                 }else{
                    $enter = $time[0];
                    $exit = $time[1];
                 }
                 //  diff between times
                 $days++;
                    $start = strtotime($enter);
                    $end = strtotime($exit);
                    $diff = ($end - $start);
                    
                    // add hours 
                    $hours = $hours + $diff;
                    // if working hours less than hour make red marker 
                    if($diff <= 3600){
                        echo "<tr style='background-color:#ff6666;'><td align='center'> <b>".($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'> ".date('H:i',$diff)."</td></tr>";
                    }else{
                        echo "<tr style=''><td align='center'> <b>".($day+1)."</b></td> <td align='center'> ".$enter."</td><td align='center'> ".$exit."</td><td align='center'> ".date('H:i',$diff)."</td></tr>";
                    }
               
                 
                
             }
                
            
            $hours = ($hours / 60/60);
            $hours = sprintf('%02d:%02d', (int) $hours, fmod($hours, 1) * 60);
                echo"</table></td><td align='center'> <b>".($days)."</b></td><td align='center'> <b>".$hours."</b></td></tr>";
            }
        ?>

    </table>
</body>
</html>

<script>
$(document).ready(function(){
 $('#Table').DataTable({
    buttons: ['pdf']
  });
})

</script>    
