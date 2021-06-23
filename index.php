<html>
    <head>

        <link rel="stylesheet" type="text/css" href="css/style.css" media="all">
    </head>
    <body>
        <div id="container">
            <section id="header"> <h1>Package Update (check_for_updates)</h1> </section>
            <section id="content">

<!--

TODO:    
    Create / Read from Folder Name:
        Use it to refer back to it to a previous created a new folder / csv file
    Parent folder ... 
        'libs'/
            <package>/
                2.7.3/
                2.7.4/
                2.7.5/
                    details.csv
    
    Parent / Child / Version directory
        Assumes there is at least one/last details.csv file
    
    Create / Read *.csv file
        Name,URL,reg_exp_syntax
        mathjax,https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.9/MathJax.js, /^([A-Za-z0-9\._-]+)$/i

-->

<?php

$directory = 'libs';

foreach(array_diff(scandir($directory), array('..', '.')) as $package) {

    $details = array_fill_keys(
      array(
        0, // => package name directory (CCleaner)
        1, // => last version directory (584)
        2, // => last CSV File (./libs/CCleaner/584/details.csv)
        3, // => row[0] => (ccsetup) ???
        4, // => array ('scheme' => 'https', 'host' => 'download.ccleaner', 'path' => '/', 'file' => 'ccsetup584.exe',)
        5, // => array ('name' => 'ccsetup', 'version' => '585', 'extension' => 'exe')
        6, // => new version (585)
        7  // => new url
      ),
    '');

    if (!is_dir($directory . '/' . $package)) continue;
    $details[0] = $package;
    // $child_directory = array_diff(scandir($directory . '/' . $details[0]), array('..', '.'));
    foreach(array_diff(scandir($directory . '/' . $details[0]), array('.','..')) as $f) if(is_dir($directory . '/' . $details[0].'/'.$f)) $l[]=$f; 
    //sort($l, SORT_NUMERIC);
    usort($l, function ($a, $b) { return version_compare($a, $b); });
    //uksort($l, function ($a, $b) { return version_compare($b, $a); });
    // $child_directory = glob($directory."/".$details[0],GLOB_ONLYDIR);

    $details[1] = end($l); //$child_directory
    $l = array();

    echo '<p style="font-weight: bold; line-height: 2.2; margin:-10px 0 -10px 0;">Package: ' . $details[0] . '/' . $details[1] . '</p>';

     // Last Sub-directory
    print '<p style="text-indent: 40px; line-height: 2.2; margin:-10px 0 -10px 0;">';
  
    if (!empty($csv_files = glob('./' . $directory . '/' . $details[0] . '/' . $details[1] . '/' . '*.csv'))) {
  
        $details[2] = end($csv_files); // Last CSV File

        print 'CSV Path: ' . $details[2] . ''; // $details[0]

        $fileHandle = fopen($details[2], "r");
         //Loop through the CSV rows.
        while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
            //Print out my column data.
            echo '<p style="text-indent: 80px; line-height: 2.2; margin:-10px 0 -10px 0;">Package: ' . $row[0] . '</p>';
            echo '<p style="text-indent: 80px; line-height: 2.2; margin:-10px 0 -10px 0;">URL: <a href="' . $row[1] . '">' . $row[1] . '</a></p>';
 
            $details[3] = $row[0];
            $details[4] = parse_url($row[1]); // $url
            $details[4]['path'] = implode("/", array_slice(explode("/", $details[4]['path']), 0, -1)) . '/'; // string
            $details[4]['file'] = basename(parse_url($row[1], PHP_URL_PATH)); 
            // implode("/", array_filter(explode("/", parse_url($row[1], PHP_URL_PATH))));
            // explode("/", trim(parse_url($row[1], PHP_URL_PATH), "/"))[0]
            $details[5] = $row[2];
            
            echo '<p style="text-indent: 80px; line-height: 2.2; margin:-10px 0 -10px 0;">Path: ' . $details[4]['path'] . '&nbsp;&nbsp;&nbsp;' . 'File: '. $details[4]['file'] . "</p>";             
            echo '<p style="text-indent: 80px; line-height: 2.2; margin:-10px 0 -10px 0;">Reg Ex. Syntax: ' . $details[5] . "</p>"; 
            break;
        }
        fclose($fileHandle);
    //if ($details[4]['host'] == 'globalcdn.nuget.org') die(var_dump($l));
    } else {
        print ' <b>&lt;empty&gt;</b>';
        echo '<hr />';
        continue;
    }
    
    $details[6] = array_fill_keys(array('name', 'version', 'extension'), '');
    
    if ($details[4]['host'] == 'download.ccleaner.com') {
        preg_match($details[5], $details[4]['file'], $file_name);

        $details[6]['name'] = $file_name[1];
        $details[6]['version'] = $file_name[2];
        $details[6]['extension'] = $file_name[3];
    } elseif ($details[4]['host'] == 'pear.horde.org') {
        preg_match($details[5], $details[4]['file'], $file_name);

        $details[6]['name'] = $file_name[1];
        $details[6]['version'] = $file_name[2];
        $details[6]['extension'] = $file_name[3];
    } elseif ($details[4]['host'] == 'cdnjs.cloudflare.com') {

        $details[6]['name'] = $details[3];
        $details[6]['version'] = $details[1]; // +1
        $details[6]['extension'] = '.js';
    }  elseif ($details[4]['host'] == 'globalcdn.nuget.org') {
        preg_match($details[5], $details[4]['file'], $file_name);

        $details[6]['name'] = $file_name[1];
        $details[6]['version'] = $file_name[2];
        $details[6]['extension'] = $file_name[3];
    }

    if (preg_match("/^(?:(\d+))$/", $details[6]['version'], $matches)) {
        // semantic version = implode('.', str_split($details[6]['version']));

        for ( $version = explode( ".",  $matches[0] ), $i = count( $version ) - 1; $i >= 0; --$i ) {
            //print('Version: (Old) ' . implode( ".", $version ) . "<br />");  
            //++$version[ $i ];

            if ($i == 0 || !$i) {
                ++$version[ $i ];
                //print('Version: (New)' . implode( ".", $version ) . "<br />");
                $details[6]['version'] = implode( ".", $version );
                break;
            }
        }
        
    } elseif (preg_match("/^(?:(\d+)\.)?(\*|\d+)$/", $details[6]['version'], $matches)) {
      print('Matches 2');
    } elseif (preg_match("/^(?:(\d+)\.)?(?:(\d+)\.)?(\*|\d+)$/", $details[6]['version'], $matches)) {
        for ( $version = explode( ".",  $matches[0] ), $i = count( $version ) - 1; $i >= 0; --$i ) {
            //print('Version: (Old) ' . implode( ".", $version ) . "<br />");  
            if ( ++$version[ $i ] >= 0 || !$i ) {
                //++$version[ $i-1 ]; //
                //$version[ $i ] = 0;
                //print('Version: (New)' . implode( ".", $version ) . "<br />");
                $details[6]['version'] = implode( ".", $version );
                break; //continue; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            }

/*
            if ( ++$version[ $i-1 ] >= 0 || !$i ) {
                ++$version[ $i-2 ];
                $version[ $i-1 ] = 0;
                print('Version: 3 ' . implode( ".", $version ) . "<br />");
                //continue;
            }    
            
            if ( ++$version[ $i-2 ] >= 0 || !$i ) {
                $version[ $i-1 ] = 0;
                print('Version: 4 ' . implode( ".", $version ) . "<br />");
                //continue;
            }
            break;
            //if ( ++$version[ $i ] >= 0 || !$i ) {
                //++$version[ $i ]; //= 0;
                //$version[ $i ] = 0;
                
                // continue;
                //print('Version: ' . implode( ".", $version ) . "<br />");
                //break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            //}
            
            //$version[ $i-1 ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
            //$version[ $i ] = 0;
*/
        }
    }

    echo '<br /><p style="text-indent: 40; font-weight: bold; line-height: 2.2; margin:-10px -10px -10px -10px;">Checking for New Version: ' . $details[6]['version'] . '</p>';

    if ($details[4]['host'] == 'download.ccleaner.com') {
        $details[7] = $details[4]['scheme'] . '://' . $details[4]['host'] . $details[4]['path'] . $details[6]['name'] . $details[6]['version'] . $details[6]['extension'] . (isset($details[4]['query']) ? '?' . $details[4]['query'] : '');
    } elseif ($details[4]['host'] == 'pear.horde.org') {
        $details[7] = $details[4]['scheme'] . '://' . $details[4]['host'] . $details[4]['path'] . $details[6]['name'] . '-' . $details[6]['version'] . $details[6]['extension'] . (isset($details[4]['query']) ? '?' . $details[4]['query'] : '');
    } elseif ($details[4]['host'] == 'cdnjs.cloudflare.com') {
        $details[7] = $details[4]['scheme'] . '://' . $details[4]['host'] . dirname($details[4]['path']) . '/' . $details[6]['version'] . '/' . $details[6]['name'] . (isset($details[4]['query']) ? '?' . $details[4]['query'] : '');
    } elseif ($details[4]['host'] == 'globalcdn.nuget.org') {
        $details[7] = $details[4]['scheme'] . '://' . $details[4]['host'] . $details[4]['path'] . $details[6]['name'] . '.' . $details[6]['version'] . $details[6]['extension'] . (isset($details[4]['query']) ? '?' . $details[4]['query'] : '');
    }
    
/*        
        join('/', array_filter(
            array_merge(
                array(
                    join('', array_slice($details[4], 0, -1)) // $details[5]
                )
            ), 'strlen')
        );
*/

    echo '<p style="text-indent: 80; line-height: 2.2; margin:-10px 0 -10px 0;">New URL: ' . $details[7] . '</p>';

    $handle = curl_init($details[7]);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

    /* Get the HTML or whatever is linked in $new_url. */
    $response = curl_exec($handle);

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if($httpCode == 404) {
        /* Handle 404 here. */
        echo '<p style="text-indent: 80; font-weight: bold; line-height: 2.2; margin:-10px 0 -10px 0;">File does not exist. 404</p>';
    } else {
        echo '<p style="text-indent: 80; font-weight: bold; line-height: 2.2; margin:-10px 0 -10px 0;">File does exist.</p>';

        mkdir($directory.'/'.$details[0].'/'.$details[6]['version'].'/');

        touch($directory.'/'.$details[0].'/'.$details[6]['version'].'/'. 'details.csv');

        $file = fopen($directory . '/' . $details[0] . '/' . $details[6]['version'] . '/' . 'details.csv', "w");
        fwrite($file, $details[3] . ',' . $details[7] . ',' . $details[5]);
        fclose($file);

        echo '<p style="text-indent: 80; font-weight: bold; line-height: 2.2; margin:-10px 0 -10px 0;">File was created: <b>' . $directory.'/'.$details[0].'/'.$details[6]['version'].'/'. 'details.csv' . '</p>';
    }

    echo '</p>';

    echo '<hr />';
}
?>
            </section>
            <section id="footer"><h2 style="text-align: right;">Programmer: <a href="mailto:barryd.it@gmail.com">Barry Dick (barryd.it@gmail.com)</a></h3></section>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>

    </body>
</html>