
<?php 

$data[] = array(
		'TotalRows' => "{$totalRows}",
		'Rows' => $results
);

echo json_encode($data);
//echo json_encode($results);
//echo 'Ext.util.JSONP.callback('. json_encode($data) . ');' ;

?>
