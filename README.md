# DIDN: searchable IDN within MySQL
A PHP Class which makes it possible to encode domain names in a way which makes them searchable through MySQL queries. The main problem with searching for IDN characters inside MySQL is that the database engine treats the keywords as their alphabetic equivalent.

So when you would search for 'Curaçao' it would come up with 'Curacao' inside the results. If the IDN data originates from a search box on a general website than it wouldnt be a problem, but for input gathered for domaining, domain parking, invoicing or other commercial processes needs to be very accurate.

For example you would like to find the complete history of a domain called 'köln.de'. Then you want to only retrieve the results having an exact match, not a result set containing also results for the domain 'koln.de'.

MySQL would make this only possible using a convert to BINARY COLLATION, making all kinds of conversions which are sometimes hard to implement due the circumstances. With this script you perform a one-time operation on record insertion to calculate the DIDN hash of a domain and save it inside your database, and use that column for all future searches.


### Requirements
- PHP version 5 or higher
- PHP module: mbstring
- MySQL access from inside PHP


## Example
An example in it's most simple form:

```PHP
<?php
require('src/didn.class.php');

$domain_name = 'köln.de';

// one-time add hash to database
$DIDN = DIDN::convert($domain_name);
$connection->query(
    "INSERT INTO table (col1, col2, didn) VALUES (?,?,?)",
    array('value1', 'value2', $DIDN)
);

// unlimited matching the saved hash
$DIDN = DIDN::convert($domain_name);
$result = $connection->query(
    "SELECT * FROM WHERE didn = ? ORDER BY didn ASC",
    array($DIDN)
); // $result contains only the exact 'köln.de' result, and not the 'koln.de' records.

?>
```

## Common problems
If you are experiencing problems with encoding or decoding of IDN characters you probably have an internal character encoding problem. You could then use this overall fix:

```PHP
<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
?>
```

If you also experience problems with input from a textbox that is sent by POST, or IDN retrieved from $_GET, then use input encoding:

```PHP
<?php
// Apply UTF-8 encoding on $_POST input
if(isset($_POST) && count($_POST) > 0) {
    foreach($_POST as $k => $v) {
        $_POST[$k] = urldecode(utf8_encode($v));
    }
}

// Apply UTF-8 encoding on $_GET input
if(isset($_GET) && count($_GET) > 0) {
    foreach($_GET as $k => $v) {
        $_GET[$k] = urldecode(utf8_encode($v));
    }
}
?>
```



## Other use cases
- Deleting making specific IDN-records inactive
- Changing the status of specific IDN-records


## Author notes
* Originally created in 2014.
* Published on GitHub for easier community driven development and making peoples life easier.