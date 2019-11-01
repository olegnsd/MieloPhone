<?php
    include "sms.php";

    $sms = new Transport;

    $sms->send(["text" => "222312 31"], ["79262204080"]);

?>