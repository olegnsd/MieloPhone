#!/usr/bin/php
<?php

                $command = "/usr/local/bin/show_peers.sh";
                exec ($command, $out);

                $callers = array();
                for ($i = 1; $i < count($out)-1; $i++) {
                        $name = substr($out[$i], 0, 25);
                        if (strpos($name, "/") !== false)
                                $name = substr($name, 0, strpos($name, "/"));

                        $state = substr($out[$i], 105, 10);
                        if (strpos($state, " ") !== false)
                                $state = substr($state, 0, strpos($state, " "));

                        $callers[trim($name)] = trim($state);
                }

print_r($callers);
?>