<!--

https://semver.org/ Semantic Versioning 2.0.0

TODO:
    
    Parent folder ... 'libs/' 
    Create / Read from Folder Name:
        Use it to refer back to it to a previous created a new folder / csv file
        
        code/2.7.3 ... 2.7.4 ... 2.7.5/code_name.csv
    
    Parent / Child / Version directory
        Assumes there is one/last *.csv file
    
    Create / Read *.csv file
        Name,URL
        mathjax,https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.3/MathJax.js

-->

<?php
/*
$d = dir(getcwd().'/libs');

echo "Handle: " . $d->handle . "<br>";
echo "Path: " . $d->path . "<br>";

while (($file = $d->read()) !== false){
  echo "filename: " . $file . "<br>";
}
$d->close(); */

/*
$str = 'ver=4.7.3/asdasd, ver=1, ver=2.5?, ver=4.7, ver=a124bcd12345';
preg_match_all('/(?<=ver=)[\d.]+/', $str, $output);
print_r($output);
*/
//$str = '4.7.14/asdasd, 1, 2.5?, 4.7, a124bcd12345';
//preg_match_all('/[\d.]+/', $str, $output);
//print_r($output);

//$str = '4.7.14';
//preg_match('/\d+\.\d+\.\d+/', $str, $output); //\d+(\.\d){1,2}

//var_dump($output);

//preg_match('@^(?:http://)?([^/]+)@i', "http://www.php.net/index.html", $matches); // www.php.net
//$host = $matches[1];

//$parent_scanned_directory = array_diff(scandir('libs'), array('..', '.')); //$directory = 'libs';
// break;

/*
    $path_comp = array(
        0 => $version_directory  // current_version
        1 => $parent_directory
        2 => $csv_file
        3 => $row[0]  // project/software name
        4 => array( // $url = parse_url($row[1]);   // parse url
            "scheme" => 'https'
            "host" => 'www...com'
            "path" => '/...'
        )
        5 => array ( // explode("/", trim(parse_url($path_comp[4], PHP_URL_PATH), "/"));  // parse url path
                0 => '...'
                ...
                (last) -> '...' //path'
        )
        6 => $new_version // current_version + 1
        7 => GET URL
    )
*/

$directory = 'libs';

//die(var_dump($path_comps));

foreach(array_diff(scandir($directory), array('..', '.')) as $parent_directory) {

    $path_comps = array_fill_keys(array(0, 1, 2, 3, 4, 5, 6, 7), ''); 

    $path_comps[4] = array_fill_keys(array('scheme', 'host', 'path'), ''); 

    //echo 'Hello world';
    if (!is_dir($directory . '/' . $parent_directory)) continue;
    $path_comps[1] = $parent_directory;
    echo '<b>Parent Directory: </b>' . $path_comps[1] . '<br />';
    $child_directory = array_diff(scandir($directory . '/' . $path_comps[1]), array('..', '.')); //;
    $path_comps[0] = end($child_directory); // Last Sub-directory
    print '&nbsp;&nbsp;Child Directory: ' . $path_comps[0] . '<br />';
  
    if (!empty($csv_files = glob($directory . '/' . $path_comps[1] . '/' . $path_comps[0] . '/' . '*.csv'))) {
  
        $path_comps[2] = end($csv_files); // Last CSV File

        //explode("/", trim(end($csv_files), "/"));
        //$csv_file = implode( '/', $csv_file ); //end($csv_file);

        print '&nbsp;&nbsp;&nbsp;&nbsp;CSV File: ' . $path_comps[2] . '<br />';

        $fileHandle = fopen($path_comps[2], "r");
         //Loop through the CSV rows.
        while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
            //Print out my column data.
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name: ' .$row[0] . '<br>';
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;URL: ' . $row[1] . '<br>';
            $path_comps[3] = $row[0];
            $path_comps[4] = parse_url($row[1]); // $url
            //$path_comps[4]['path'] = parse_url($row[1], PHP_URL_PATH); // string
            $path_comps[5] = explode("/", trim($path_comps[4]['path'], "/")); // explode
            break;
        }
        fclose($fileHandle);

    } else {
        print '&nbsp;&nbsp;&nbsp;&nbsp;CSV File: <b>&lt;empty&gt;</b> <br />';
        continue;
    }

    //$pathComponents = explode("/", trim($path_comps[4], "/")); // trim to prevent
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Path: ' . (is_array($path_comps[5]) ? implode('/', $path_comps[5]) : false) . "<br />";    
    //$pathComponents2 = $path_comps[4]; //explode("/", trim($url, "/")); // trim to prevent

    //var_dump($pathComponents2);
    //echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $new_url . '<br />';
    
    echo '<pre>';
    //var_dump($pathComponents);
    //var_dump($pathComponents2);
    //var_dump($path_comps);
    echo '</pre>';
    //die();
    echo '&nbsp;&nbsp;&nbsp;&nbsp;Version: ' . $path_comps[0] . '<br />'; // $path_comps[4]['path'][count($path_comps[4]['path'])-1]; count == 2 - 1


    if ($path_comps[4]['host'] == 'pear.horde.org') {

        preg_match('/\d+\.\d+\.\d+/', $path_comps[0], $output);
      
        $version = $output[0];

        for ( $new_version = explode( ".", $version ), $i = count( $new_version ) - 1; $i > -1; --$i ) {
            if ( ++$new_version[ $i ] > -1 || !$i ) break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            $new_version[ $i ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
        }
    
        $path_comps[6] = implode( ".", $new_version );
        
        echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking for New Version: ' . $path_comps[6] . '<br />';

        $path_comps[7] = $path_comps[4]['scheme'] . '://' . $path_comps[4]['host'] . '/' . join('/', array_filter(
            array_merge(
                array(
                    join('', array_slice($path_comps[5], 0, -1))
                )
            ), 'strlen')
        ) . '/';

        $path_comps[7] .= $path_comps[3] . '-' . $path_comps[6] . '.tgz' . (isset($path_comps[4]['query']) ? '?' . $path_comps[4]['query'] : '');
        
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $path_comps[7] . '<br />';

    } else if ($path_comps[4]['host'] == 'download.ccleaner.com') {  // https://download.ccleaner.com/ccsetup570.exe -> globalcdn.nuget.org

        preg_match('/(\d+)/', $path_comps[0], $output);

        $version = $output[0];

        $path_comps[6] = $version + 1;
        echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking for New Version: ' . $path_comps[6] . '<br />';

        $path_comps[7] = $path_comps[4]['scheme'] . '://' . $path_comps[4]['host'] . '/' . join('/', array_filter(
            array_merge(
                array(
                    join('', array_slice($path_comps[5], 0, -1))
                )
            ), 'strlen')
        );

        $path_comps[7] .= $path_comps[3] . $path_comps[6] . '.exe' . (isset($path_comps[4]['query']) ? '?' . $path_comps[4]['query'] : '');

        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $path_comps[7] . '<br />';

    } elseif ($path_comps[4]['host'] == 'www.nuget.org') {  // https://www.nuget.org/api/v2/package/TinyMCE/4.9.10 -> globalcdn.nuget.org

        preg_match('/\d+\.\d+\.\d+/', $path_comps[0], $output);
      
        $version = $output[0];

        for ( $new_version = explode( ".", $version ), $i = count( $new_version ) - 1; $i > -1; --$i ) {
            if ( ++$new_version[ $i ] > -1 || !$i ) break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            $new_version[ $i ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
        }

        $path_comps[6] = implode( ".", $new_version );
        echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking for New Version: ' . $path_comps[6] . '<br />';

        $path_comps[7] = 'https://globalcdn.nuget.org/packages/' . strtolower($path_comps[3]) . '.' . $path_comps[6] . '.nupkg';

        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $path_comps[7] . '<br />';

    } elseif ($path_comps[4]['host'] == 'cdnjs.cloudflare.com') {

        preg_match('/\d+\.\d+\.\d+/', $path_comps[0], $output);
      
        $version = $output[0];

        for ( $new_version = explode( ".", $version ), $i = count( $new_version ) - 1; $i > -1; --$i ) {
            if ( ++$new_version[ $i ] > -1 || !$i ) break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            $new_version[ $i ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
        }

        $path_comps[6] = implode( ".", $new_version );
        echo '&nbsp;&nbsp;&nbsp;&nbsp;Checking for New Version: ' . $path_comps[6] . '<br />';

        $path_comps[7] = $path_comps[4]['scheme'] . '://' . $path_comps[4]['host'] . '/' . join('/', array_filter(
            array_merge(
                array(
                    join('/', array_slice($path_comps[5], 0, -2))
                )
            ), 'strlen')
        ) . '/';

        $path_comps[7] .= $path_comps[6] . '/' . $path_comps[3] . (isset($path_comps[4]['query']) ? '?' . $path_comps[4]['query'] : '');

        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $path_comps[7] . '<br />';
    }

    $handle = curl_init($path_comps[7]);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

    /* Get the HTML or whatever is linked in $new_url. */
    $response = curl_exec($handle);

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if($httpCode == 404) {
        /* Handle 404 here. */
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>File does not exist. 404</b><br />';
    } else {
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>File does exist.</b><br />';
            
        mkdir($directory.'/'.$path_comps[1].'/'.$path_comps[6].'/');
            
        touch($directory.'/'.$path_comps[1].'/'.$path_comps[6].'/'.$path_comps[3] . '.csv');
            
        $file = fopen($directory . '/' . $path_comps[1] . '/' . $path_comps[6] . '/' . $path_comps[3] . '.csv',"w");
        fwrite($file,$path_comps[3] . ',' . $path_comps[7]);
        fclose($file);
            
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;File was created: <b>' . $directory.'/'.$path_comps[1].'/'.$path_comps[6].'/'.$path_comps[3] . '.csv' . '</b><br />';
    }
    //if ($pathComponents[count($pathComponents)-2] == $output[0]) { }


}

die();

//$url = "http://www.mydomain.com/abc/";
$path = parse_url($url, PHP_URL_PATH);
$pathComponents = explode("/", trim($path, "/")); // trim to prevent
                                                  // empty array elements
//echo $pathComponents[0]; // prints 'ajax'
var_dump($pathComponents);

echo 'Number of components: ' . (count($pathComponents)) . '<br />';

$version = $pathComponents[3];

for ( $new_version = explode( ".", $version ), $i = count( $new_version ) - 1; $i > -1; --$i ) {
    if ( ++$new_version[ $i ] < 10 || !$i ) break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
    $new_version[ $i ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
}
$new_version = implode( ".", $new_version );

die($new_version);

$activeUsers = /** Query to get the active users */

/** Following is the Variable to store the Users data as 
    CSV string with newline character delimiter, 

    its good idea of check the delimiter based on operating system */

$userCSVData = "Name,Email,CreatedAt\n";

/** Looping the users and appending to my earlier csv data variable */
foreach ( $activeUsers as $user ) {
    $userCSVData .= $user->name. "," . $user->email. "," . $user->created_at."\n";
}
/** Here you can use with H:i:s too. But I really dont care of my old file  */
$todayDate  = date('Y-m-d');
/** Create Filname and Path to Store */
$fileName   = 'Active Users '.$todayDate.'.csv';
$filePath   = public_path('uploads/'.$fileName); //I am using laravel helper, in case if your not using laravel then just add absolute or relative path as per your requirements and path to store the file

/** Just in case if I run the script multiple time 
    I want to remove the old file and add new file.

    And before deleting the file from the location I am making sure it exists */
if(file_exists($filePath)){
    unlink($filePath);
}
$fp = fopen($filePath, 'w+');
fwrite($fp, $userCSVData); /** Once the data is written it will be saved in the path given */
fclose($fp);
