<?php
    $text = "\"Мои новости\", 6,77 руб/день с НДС\n1>Далее";
    
    preg_match_all("/\d+,\d+/", $text, $out);
    print_r($out);
    ?>