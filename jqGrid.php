<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="./jqwidgets/styles/jqx.base.css" type="text/css" />
<!--    
    <link rel="stylesheet" href="./jqwidgets/styles/jqx.classic.css" type="text/css" />
    <link rel="stylesheet" href="./jqwidgets/styles/jqx.darkblue.css" type="text/css" />   
-->     
    <script type="text/javascript" src="./scripts/jquery-1.10.2.min.js"></script>  
	<script type="text/javascript" src="./jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxdata.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxmenu.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxgrid.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxgrid.pager.js"></script>	
    <script type="text/javascript" src="./jqwidgets/jqxgrid.selection.js"></script>	
	<script type="text/javascript" src="./jqwidgets/jqxgrid.filter.js"></script>	
	<script type="text/javascript" src="./jqwidgets/jqxgrid.sort.js"></script>		   	
	<script type="text/javascript" src="./jqwidgets/jqxlistbox.js"></script>			
	<script type="text/javascript" src="./jqwidgets/jqxdropdownlist.js"></script>	
    <script type="text/javascript" src="./jqwidgets/jqxwindow.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxinput.js"></script>
    <script type="text/javascript" src="./scripts/gettheme.js"></script>
	
    <script type="text/javascript">
            $(document).ready(function () {
            // prepare the data
            var theme = 'classic';
 		//	var data = [{"TotalRows":"10","Rows":[{"id":"1","lastname":"Geggie","firstname":"Steve","photo":"","phone":"847 347 7859"},{"id":"2","lastname":"Tohme","firstname":"Kelvin","photo":null,"phone":null},{"id":"3","lastname":"Scully","firstname":"Bill","photo":null,"phone":null},{"id":"4","lastname":"Saflarski","firstname":"Rob","photo":null,"phone":null},{"id":"5","lastname":"Kemp","firstname":"Katie","photo":null,"phone":null},{"id":"6","lastname":"Miller","firstname":"Chris","photo":null,"phone":null},{"id":"7","lastname":"Horton","firstname":"JP","photo":null,"phone":null},{"id":"8","lastname":"Schleich","firstname":"Amanda","photo":null,"phone":null},{"id":"9","lastname":"Parello","firstname":"Paul","photo":null,"phone":null},{"id":"10","lastname":"Kim","firstname":"Moses","photo":null,"phone":null}]};     
        //   var data = [{"TotalRows":"10","Rows":[{"id":"1","lastname":"Geggie","firstname":"Steve","photo":"","phone":"847 347 7859"}]];
            var source = 
            {
                 datatype: "json",
                 datafields: [
					 { name: 'id', type: 'int'},
					 { name: 'lastname', type: 'string'},
					 { name: 'firstname', type: 'string'},
					 { name: 'photo', type: 'string'},
					 { name: 'phone', type: 'string'}
                ],

				id:  'id',
                url: "employee.php",
			//    localdata: data,
				cache: false,
				filter: function()
				{
					// update the grid and send a request to the server.
					$("#jqxgrid").jqxGrid('updatebounddata', 'filter');
				},
				sort: function()
				{
					// update the grid and send a request to the server.
					$("#jqxgrid").jqxGrid('updatebounddata', 'sort');
				},
				root: 'Rows',
				beforeprocessing: function(data)
				{		
					if (data != null)
					{
						source.totalrecords = data[0].TotalRows;
				//		source.totalrecords = 11;					
					}
				}

           };		
 //           var dataadapter = new $.jqx.dataAdapter(source);
		    var dataadapter = new $.jqx.dataAdapter(source, {
					loadError: function(xhr, status, error)
					{
						alert(error);
					}
				}
			);


	
            // initialize jqxGrid
            $("#jqxgrid").jqxGrid(
            {		
            	width: '49%',
                source: dataadapter,
                theme: theme,
				filterable: true,
				sortable: true,
				autoheight: true,
				pageable: true,
				virtualmode: true,
				rendergridrows: function(obj)
				{
					 return obj.data;    
				},
			    columns: [
                      { text: 'ID', datafield: 'id', width: 50 },
                      { text: 'Last Name', datafield: 'lastname', width: 200 },
                      { text: 'First Name', datafield: 'firstname', width: 180 },
                      { text: 'Photo', datafield: 'photo', width: 100 },
                      { text: 'Phone', datafield: 'phone', width: 125 },
                 ]
            });
       });
    </script>
</head>
<body class='default'>
    <div align="center" id="jqxWidget">
      <div align="center" id="jqxgrid"></div>
    </div> 
</body>
</html>
