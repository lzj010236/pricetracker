<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="pcStylesheet.css"></head>
<body>

<h1><img src="projectPriceFetcher.png" height="100px"></h1>


<?php

// $dom = new DOMDocument;

// // Load the XML
// $dom->loadXML("http://www.amazon.com/Sony-Compact-Interchangeable-Digital-Camera/dp/B00JIWXTXG/");

// // Print XPath for each element
// foreach ($dom->getElementsByTagName('*') as $node) {
//     echo $node->getNodePath() . "\n";
// }

echo '
<form action="amazon.php" method="post">
	<table>
  		<tr>
			<th>Enter url: </th>
			<td><input type="text" name="link" placeholder="http://www.amazon.com/dp/B00SRMTY6O" ></td>
		</tr>
		<tr>
			<th>Enter email: </th>
			<td><input type="text" name="email" placeholder="123@gmail.com"></td>
		</tr>
	</table>

</br>
	<input type="submit" id="i01">
</form>

';

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

// if ($conn->query($sql_weblinks) === TRUE) {
//     echo "New record created successfully";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo '<br>';
echo "Records on file:";
echo "<table id=\"t01\">
  <tr>
    <th>url</th>
    <th>price</th>		
    <th>last updated</th>
  </tr>";

$sql = "SELECT * FROM price;";
// echo $sql;

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["link"]."</td><td>".$row["curPrice"]."</td><td>".$row["timestamp"]. "</td></tr>";
    }
} else {
    echo "0 results";
}

echo "</table>";
$conn->close();


?>

</body>
</html>