<?php date_default_timezone_set("Australia/Perth");

$date_today = date('Y-m-d');
$date_next = date('Y-m-d', strtotime('+1 day', strtotime($date_today)));





$date_time_today = date('Y-m-d h:i A');
$am_pm = date('A');




$time_limit_a = '09:00 PM';
$time_limit_b = '04:00 AM';


echo '<pre>';var_dump($date_time_today );echo '</pre>';



if($am_pm == 'PM'){






}else{







}





echo date('Y-m-d');

echo '<p class=""></p>';


echo(strtotime("now") . "<br>"); // current time stamp
echo '<p>&nbsp;</p>';



echo( date('Y-m-d h:i A',1653628822) );

echo '<p>&nbsp;</p>';



echo( date('h:i A') );


echo '<p class="">---------------------------</p>';




