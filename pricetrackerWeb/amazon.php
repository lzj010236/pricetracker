



<?php

// $dom = new DOMDocument;

// // Load the XML
// $dom->loadXML("http://www.amazon.com/Sony-Compact-Interchangeable-Digital-Camera/dp/B00JIWXTXG/");

// // Print XPath for each element
// foreach ($dom->getElementsByTagName('*') as $node) {
//     echo $node->getNodePath() . "\n";
// }

echo '<html>
<body>

<form action="amazon.php" method="post">
Enter url: <input type="text" name="link">
<br \>
Enter email: <input type="text" name="email">
<br \>
<input type="submit">
</form>

</body>
</html>';

// $dom = new DOMDocument;
// $dom->loadXML($_POST['link']);
// $xpath = new DOMXPath($dom);
// $elements = $xpath->query("*/div[@id='productTitle']");
// // echo $entry->nodeValue;//*[@id="productTitle"]

// if (!is_null($elements)) {
//   foreach ($elements as $element) {
//     echo "<br/>[". $element->nodeName. "]";

//     $nodes = $element->childNodes;
//     foreach ($nodes as $node) {
//       echo $node->nodeValue. "\n";
//     }
//   }
// }


$conn = new mysqli("localhost", "root", "", "amazon");
//CREATE TABLE webLinks (link CHARACTER(100), email CHARACTER(30), timestamp TIMESTAMP); 
//CREATE TABLE price (link CHARACTER(100), title CHARACTER(100), curPrice FLOAT(10,2), timestamp TIMESTAMP);

//SELECT email FROM webLinks WHERE webLinks.link='http://www.amazon.com/Bayou-Blend-Cajun-Style-Gourmet/dp/B00MXDM3CK/';

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
// echo 'connected';


$sql_weblinks = 'INSERT INTO webLinks (link, email,timestamp) VALUES (\''.$_POST['link'].'\',\''.$_POST['email'].'\', NOW())';
// $sql_weblinks .= 'INSERT INTO webLinks (email) VALUES (\''.$_POST['email'].'\')';
// echo $sql_weblinks;

if ($conn->query($sql_weblinks) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
echo '<br>';

$sql = "SELECT * FROM price;";
// echo $sql;

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "url:".$row["link"]."    price:".$row["curPrice"]."    time:".$row["timestamp"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();


?>