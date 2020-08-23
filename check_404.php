<?php

$url = 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=MML_HTMLorMML';

$handle = curl_init($url);
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

/* Get the HTML or whatever is linked in $url. */
$response = curl_exec($handle);

/* Check for 404 (file not found). */
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
if($httpCode == 404) {
    /* Handle 404 here. */
    echo 'File does not exist.';
} else {
    echo 'File does exist.';
}

curl_close($handle);

echo '<pre>';
print_r(get_headers('https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.8/MathJax.js?config=MML_HTMLorMML', 1));
echo '</pre>';

?>