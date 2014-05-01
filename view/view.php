<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" media="screen" href="js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
<title>Contacts</title>
<style type="text/css">
table.contacts {
width: 100%;
}

table.contacts thead {
background-color: #eee;
text-align: left;
}

table.contacts thead th {
border: solid 1px #fff;
padding: 3px;
}

table.contacts tbody td {
border: solid 1px #eee;
padding: 3px;
}

a, a:hover, a:active, a:visited {
color: blue;
text-decoration: underline;
}
</style>

</head>
<body>

	
<div><a href="index.php?op=new">Add new contact</a></div>
<table class=contacts border="0" cellpadding="0" cellspacing="0">
<thead>
<tr>
<?php 

foreach ($results[0] as $key=>$value)
{
	echo "<th>{$key}</th>";
}
?>
<!--  <th><a href="?orderby=name">Name</a></th>
<th><a href="?orderby=phone">Phone</a></th>
<th><a href="?orderby=email">Email</a></th>
<th><a href="?orderby=address">Address</a></th>
<th>&nbsp;</th>
 -->
</tr>
</thead>
<tbody>
<?php 

$i=0;
while ($results[$i]) {
	echo "<tr>";
	foreach ($results[$i] as $key=>$value)
	{
		if ($key == $keyField) {
			echo "<td><a href=\"index.php?op=show&id={$value}\">{$value}</a></td>";
		}
		else {
			echo "<td>{$value}</td>";
		}	
	}
	$i++;
	echo "</tr>";
}
echo "</table>";


?>

<!--
<tr>
<td><a href="index.php?op=show&id=">ID</a></td>   (FIRST COLUMN)
<td><a href="index.php?op=delete&id=" >">delete</a></td> (LAST COLUMN)
</tr>
</tbody>
</table>
-->
</body>
</html>
