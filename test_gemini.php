<?php
$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models?key=AQ.Ab8RN6K6JStaECz_VKAbmrD0cc3WoFB_nYMsZtVqV3PNzYwfxQ');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($ch);
echo $resp;
$resp = curl_exec($ch);
echo $resp;
