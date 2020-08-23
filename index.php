<!--

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
foreach(array_diff(scandir('libs'), array('..', '.')) as $parent_directory) {
  //echo 'Hello world';
  if (!is_dir('libs' . '/' . $parent_directory)) continue;
  echo '<b>Parent Directory: </b>' . $parent_directory . '<br />';
  $child_scanned_directory = array_diff(scandir('libs/' . $parent_directory), array('..', '.')); //$directory = 'libs';
  $version_directory = end($child_scanned_directory);
  print '&nbsp;&nbsp;Child Directory: ' . $version_directory . '<br />';
  
  if(!empty($csv_files = glob('libs/' . $parent_directory . '/' . $version_directory . '/' . '*.csv'))) {
  
    $csv_file = end($csv_files);
    
    //explode("/", trim(end($csv_files), "/"));
    //$csv_file = implode( '/', $csv_file ); //end($csv_file);
  
    print '&nbsp;&nbsp;&nbsp;&nbsp;CSV File: ' . $csv_file . '<br />';
    
    $fileHandle = fopen($csv_file, "r");
 
    //Loop through the CSV rows.
    while (($row = fgetcsv($fileHandle, 0, ",")) !== FALSE) {
        //Print out my column data.
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name: ' . $row[0] . '<br>';
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;URL: ' . $row[1] . '<br>';
        $path = parse_url($row[1], PHP_URL_PATH);
        $url = parse_url($row[1]);
        break;
    }

    fclose($fileHandle);   
    
    $pathComponents = explode("/", trim($path, "/")); // trim to prevent
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Path: ' . implode('/', $pathComponents) . "<br />";    
    $pathComponents2 = $url; //explode("/", trim($url, "/")); // trim to prevent
    $new_url = $pathComponents2['scheme'] . '://' . $pathComponents2['host'] . '/';
    //var_dump($pathComponents2);
    //echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $new_url . '<br />';
    
    echo '<pre>';
    //var_dump($pathComponents);
    //var_dump($pathComponents2);
    echo '</pre>';

    if ($pathComponents2['host'] == 'www.nuget.org') {  // https://www.nuget.org/api/v2/package/TinyMCE/4.9.10 -> globalcdn.nuget.org

      print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Version: ' . $pathComponents[count($pathComponents)-1] . '<br />'; //prev($array);

      preg_match('/\d+\.\d+\.\d+/', $pathComponents[count($pathComponents)-1], $output);
      
        $version = $output[0];

        for ( $new_version = explode( ".", $version ), $i = count( $new_version ) - 1; $i > -1; --$i ) {
            if ( ++$new_version[ $i ] > -1 || !$i ) break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            $new_version[ $i ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
        }

        $new_version = implode( ".", $new_version );
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Check for New Version: ' . $new_version . '<br />';
        
        $pathComponents[count($pathComponents)-1] = $new_version;
        
        //$new_url .= implode('/', $pathComponents) . (isset($pathComponents2['query']) ? '?' . $pathComponents2['query'] : '');
 
        $new_url = 'https://globalcdn.nuget.org/packages/' . strtolower($pathComponents[3]) . '.' . $pathComponents[4] . '.nupkg';
        
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $new_url . '<br />';
        
        //die();

        $handle = curl_init($new_url); //'https://globalcdn.nuget.org/packages/tinymce.4.9.12.nupkg'
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
            
            mkdir('libs'.'/'.$pathComponents[3].'/'.$pathComponents[4].'/');
            
            touch('libs'.'/'.$pathComponents[3].'/'.$pathComponents[4].'/'.$pathComponents[3] . '.csv');
            
            $file = fopen('libs'.'/'.$pathComponents[3].'/'.$pathComponents[4].'/'.$pathComponents[3] . '.csv',"w");
            fwrite($file,$pathComponents[3] . ',' . $pathComponents2['scheme'] . '://' . $pathComponents2['host'] . '/' . implode('/', $pathComponents) . (isset($pathComponents2['query']) ? '?' . $pathComponents2['query'] : ''));
            fclose($file);
            
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;File was created: <b>' . $pathComponents[3].'/'.$pathComponents[4].'/'.$pathComponents[3] . '.csv' . '</b><br />';
        }
      
    } else if ($pathComponents2['host'] == 'cdnjs.cloudflare.com') {

      print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Version: ' . $pathComponents[count($pathComponents)-2] . '<br />'; //prev($array);

      preg_match('/\d+\.\d+\.\d+/', $pathComponents[count($pathComponents)-2], $output);
      
        $version = $output[0];

        for ( $new_version = explode( ".", $version ), $i = count( $new_version ) - 1; $i > -1; --$i ) {
            if ( ++$new_version[ $i ] > -1 || !$i ) break; // break execution of the whole for-loop if the incremented number is below 10 or !$i (which means $i == 0, which means we are on the number before the first period)
            $new_version[ $i ] = 0; // otherwise set to 0 and start validation again with next number on the next "for-loop-run"
        }

        $new_version = implode( ".", $new_version );
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Check for New Version: ' . $new_version . '<br />';
        
        $pathComponents[count($pathComponents)-2] = $new_version;
        
        $new_url .= implode('/', $pathComponents) . (isset($pathComponents2['query']) ? '?' . $pathComponents2['query'] : '');
        
        echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New URL: ' . $new_url . '<br />';

        $handle = curl_init($new_url);
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
            
            mkdir($pathComponents[1].'/'.$pathComponents[2].'/'.$pathComponents[3].'/');
            
            touch($pathComponents[1].'/'.$pathComponents[2].'/'.$pathComponents[3].'/'.$pathComponents[4] . '.csv');
            
            $file = fopen($pathComponents[1].'/'.$pathComponents[2].'/'.$pathComponents[3].'/'.$pathComponents[4] . '.csv',"w");
            fwrite($file,$pathComponents[4] . ',' . $new_url);
            fclose($file);
            
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;File was created: <b>' . $pathComponents[2].'/'.$pathComponents[3].'/'.$pathComponents[4] . '.csv' . '</b><br />';
        }
    }

    //if ($pathComponents[count($pathComponents)-2] == $output[0]) {
    

    //}

  } else {
    print '&nbsp;&nbsp;&nbsp;&nbsp;CSV File: <b>&lt;empty&gt;</b> <br />';
  }

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
