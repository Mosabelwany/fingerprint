

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> مرحبا بك </title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="background-color:#1222;">
<div class="container" style="margin-top:7%;">
    
<center><h2> استخراج تقرير حضور و انصراف العمال  </h2>
<br>
<form action="/fingerprint/process.php" method="POST" enctype="multipart/form-data"> 
<label for="fileSelect">ملف الجهاز</label>
<input class="form-control" style=" width:30%" id="fileSelect" name="Sheet"  type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />  

<br>

<br>

<input type="submit" class="btn btn-success" value="الحصول علي التقرير">
</center>
</form>
</div>
<footer style="margin-top:30%;" align="center" >
    All CopyRights Reserved to <a href="https://www.lnksync.com/">Lnksync</a> &copy; <?php echo date("Y");?> 
</footer>
</body>
</html>


<?php
?>