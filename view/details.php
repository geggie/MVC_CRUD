<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>
<?php print htmlentities($title) ?>
</title>
</head>
<body>

<form method="POST" action="">

<?php 
foreach ($results[0] as $key=>$value)
{
	echo "<label for=\"{$key}\">{$key}:</label><br/>";	
	echo "<input type=\"text\" name=\"{$key}\" value=\"{$value}\" >";
	echo "<br/>";
}

?>

<input type="hidden" name="form-submitted" value="1" />
<input type="submit" value="Submit" />
</form>

</body>
</html>
