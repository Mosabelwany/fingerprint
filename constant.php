<?php 
    // new constant to calculate net salary 
    date_default_timezone_set("UTC");
    // working time
    $start_shift = "09:00";
    $end_shift = "17:00";
    $start_shift = strtotime($start_shift);
    $end_shift = strtotime($end_shift);
    $working_hours = 3600*8 ;
    $working_days = 20 ;
    
    // holudays 
    $holiday1 = 'Saturday';
    $holiday1 = 'Friday';
    
    // minus 
    $late_rate_cash = 10;
    $absense_rate_cash = 100;
    
    // extra
    $overtime_rate_cash = 10;
    $holiday_rate_cash = 15;
    
    // salary 
    $fixed_salary = 3500;
    $hour_salary = 10;
    $hour_salary_dayoff = 20;

 ?>