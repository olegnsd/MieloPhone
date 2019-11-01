#!/bin/bash                                                                                                                                                                                                                                  
loadav=`cat /proc/loadavg | awk '{print $1}'`
echo $loadav
/home/easyring/web/easyring24.com/tasks/check.php
[[ $loadav > 2 ]]  && exit;
#echo $loadav;

a=`ls /home/easyring/web/easyring24.com/task_pid/`                                                                                                                                                                                           
                                                                                                                                                                                                                                             
for i in $a ;                                                                                                                                                                                                                                
do                                                                                                                                                                                                                                           
pid=`head -n 1 /home/easyring/web/easyring24.com/task_pid/$i;`                                                                                                                                                                               
                                                                                                                                                                                                                                             
if  ! [ -e /proc/$pid ]                                                                                                                                                                                                                      
then                                                                                                                                                                                                                                         
   echo "$pid is not running"                                                                                                                                                                                                                
   rm -f /home/easyring/web/easyring24.com/task_pid/$i                                                                                                                                                                                     
fi 
done                                                                                                                                                                                                                                          


[ $? -ne 0 ]  && exit -1
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
sleep 2
/usr/bin/php /home/easyring/web/easyring24.com/tasks/cron.php > /dev/null 2>&1 &
