<?php
// echo the asterisks between getting the url and echoing it,
// to ensure that get_url has no output of its own.
$url = $mojl->get_url(__DIR__ . '/images/spacer.gif', true);
echo '***';
echo $url;
