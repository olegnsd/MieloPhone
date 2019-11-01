<?php
    $db = mysql_connect ("db.h","easyring_user","EGaPBfH0ID");
    mysql_select_db ("easyring_db",$db);
    $all_call=0;
    $un_time = time();
    $time = date( '20y-m-d H:i:s', $un_time ); 
    $un_time=$un_time-3600;
    $time_end = date( '20y-m-d H:i:s', $un_time ); 
    $results=mysql_query("SELECT * FROM `tasks_base` WHERE `datering` BETWEEN '$time_end' AND '$time' ",$db);
    while($row=mysql_fetch_array($results))
    {
      $all_call++;  
    }
    $all_calls=0;
    $results=mysql_query("SELECT * FROM `tasks_base` WHERE `datering` BETWEEN '$time_end' AND '$time' && state='ANSWERED'",$db);
    while($row=mysql_fetch_array($results))
    {
      $all_calls++;  
    } 

    $otklik=0;
    $results=mysql_query("SELECT * FROM `tasks_base` WHERE `datering` BETWEEN '$time_end' AND '$time' && send='Y' && press='Y'",$db);
    while($row=mysql_fetch_array($results))
    {
      $otklik++;  
    }
      
    $resulthati=mysql_query("select * from taho",$db);
    $rowsn=mysql_fetch_array($resulthati);
    $max_call=$rowsn['calls'];
    $max_otv=$rowsn['otv'];
    $max_resp=$rowsn['resp'];
    if($all_call>$max_call){
        {mysql_query("update taho set calls='$all_call'",$db);}
        $all_call_max=$all_call;
    }else{
        $all_call_max=$max_call;
    }
    if($all_calls>$max_otv){
        {mysql_query("update taho set otv='$all_calls'",$db);}
        $all_calls_max=$all_calls;
    }else{
        $all_calls_max=$max_otv;
    }
    if($otklik>$max_resp){
        {mysql_query("update taho set resp='$otklik'",$db);}
        $otklik_max=$otklik;
    }else{
        $otklik_max=$max_resp;
    }
    //test
    ?>
<br />
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Звонки', <? echo $all_call;?>]
        ]);

        var options = {
          width: 400, height: 150,
          redFrom: <?$all_callt1=$all_call_max/100; $all_call_t2=$all_callt1*25; $all_call_t3=$all_call_max-$all_call_t2; echo $all_call_t3;?>, redTo: <?= $all_call_max;?>,
          yellowFrom:<?$all_callt1=$all_call_max/100; $all_call_t2=$all_callt1*50; $all_call_t3=$all_call_max-$all_call_t2; echo $all_call_t3;?>, yellowTo: <?$all_callt1=$all_call/100; $all_call_t2=$all_callt1*25; $all_call_t3=$all_call_max-$all_call_t2; echo $all_call_t3;?>,
          minorTicks: 5, max: <?= $all_call_max;?>
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div1'));

        chart.draw(data, options);



      }
    </script>
       <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Снятые', <?= $all_calls;?>]
        ]);

        var options = {
          width: 400, height: 150,
          redFrom: <?$all_callt1=$all_calls_max/100; $all_call_t2=$all_callt1*25; $all_call_t3=$all_calls_max-$all_call_t2; echo $all_call_t3;?>, redTo: <?= $all_calls_max;?>,
          yellowFrom:<?$all_callt1=$all_calls_max/100; $all_call_t2=$all_callt1*50; $all_call_t3=$all_calls_max-$all_call_t2; echo $all_call_t3;?>, yellowTo: <?$all_callt1=$all_calls_max/100; $all_call_t2=$all_callt1*25; $all_call_t3=$all_calls_max-$all_call_t2; echo $all_call_t3;?>,
          minorTicks: 5, max: <?= $all_calls_max;?>
          

          
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div2'));

        chart.draw(data, options);



      }
    </script>
       <script type="text/javascript">
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['Отклики', <?= $otklik;?>]
        ]);

        var options = {
          width: 400, height: 150,
          redFrom: <?$all_callt1=$otklik_max/100; $all_call_t2=$all_callt1*25; $all_call_t3=$otklik_max-$all_call_t2; echo $all_call_t3;?>, redTo: <?= $otklik_max;?>,
          yellowFrom:<?$all_callt1=$otklik_max/100; $all_call_t2=$all_callt1*50; $all_call_t3=$otklik_max-$all_call_t2; echo $all_call_t3;?>, yellowTo: <?$all_callt1=$otklik_max/100; $all_call_t2=$all_callt1*25; $all_call_t3=$otklik_max-$all_call_t2; echo $all_call_t3;?>,
          minorTicks: 5, max: <?= $otklik_max;?>
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div3'));

        chart.draw(data, options);



      }
    </script>
<div style="width: 600px; height: 155px; margin: auto;">
    <div id="chart_div1" style="width: 200px; height: 150px; float: left;"></div>
    <div id="chart_div2" style="width: 200px; height: 150px; float: left;"></div>
    <div id="chart_div3" style="width: 200px; height: 150px; float: left;"></div>
</div>
<br />