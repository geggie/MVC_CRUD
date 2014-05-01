<?php
/**
 * Uses Active Record design pattern to implement a class that
 *  closely matches a given database table.  This class contains
 *  methods for binding the class members and methods to CRUD
 *  database management principles.  This type of class is sometimes
 *  referred to a "Table Model", and in many designs it is implemented
 *  as an abstract base class, which is then extended and specialized
 *  as necessary.
 */
require_once './model/config.php';
require_once './model/database.php';
class EntityService
{
	protected $primaryKey = null;
	protected $id = null;
	protected $table = null;
	protected $dataResults = array();
	protected $filter = null;
	protected $sqlRowCount = null;
	protected $totalRows;

	function __construct()
	{
		$dbo = DatabaseGateway::getInstance();
		try {
			$dbo->connect(DB_HOST, DB_USER, DB_PASS, 'testdb');
		}
	   catch (mysqli_sql_exception $e) {
				throw $e;
		}
	}

	/**
	 * Takes assoc array as param $data.  Dynamically creates class properties
	 *  with key as property name and value as its value.
	 *
	 * @param array $data key value pairs
	 * @return
	 * @access
	 */
	public function setTable($t)
	{
		$this->table = $t;
	}
	public function setPrimaryKey($kf)
	{
		$this->primaryKey = $kf;
	}
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}	
	public function getTable()
	{
		return $this->table;
	}
	public function getRowCount()
	{
		$dbo = DatabaseGateway::getInstance();
		return $dbo->getNumRows();
	}
	public function getTotalRows()
	{
		$dbo = DatabaseGateway::getInstance();
		$dbo->doQuery($this->sqlRowCount);
		return $dbo->getTotalRows();
	}
	public function getDataResults()
	{
		return $this->dataResults;
	}
	function bind($data)
	{
		//print_r($data);
		foreach ($data as $key=>$value)
		{
			$this->$key = $value;
			//echo $key."--".$value;
		}
	}

	function loadRecord($id)
	{
		$this->id = $id;
		$dbo = DatabaseGateway::getInstance();
		$sql = $this->buildQuery('loadRecord');
		$this->dataResults = null;
		$dbo->doQuery($sql);

		$this->dataResults[0] = $dbo->loadObjectList();
		$dbo->freeResults();

	}
	
	// write database object contents to database
	function store()
	{
		$dbo = DatabaseGateway::getInstance();
		$sql = $this->buildQuery('store');
		$dbo->doQuery($sql);
	}

	protected function buildQuery($task)
	{

		$sql = "";
		$pagenum = $_GET['pagenum'];
		$pagesize = $_GET['pagesize'];
//		$pagenum = "0";
//		$pagesize = "10";
		
		$start = $pagenum * $pagesize;
		
		if ($task == "store")
		{
			// if no id value has been store in this object yet,
			//  add new record to the database, else just update the record.
			if ($this->id == "")
			{
				$keys = "";
				$values = "";
				$classVars = get_class_vars(get_class($this));
				$sql .= "INSERT INTO {$this->table}";
				 
				foreach ($classVars as $key=>$value)
				{
					if ($key == "id" || $key == "table")
					{
						continue;
					}

					$keys .= "{$key},";
					$values .= "{$value},";
				}
				 
				// NOTE: all substr($keys, 0, -1) does is gets rid of the comma
				// at the on the last array element.
				$sql .= "(".substr($keys, 0, -1).") Values (".substr($values, 0, -1).")";
				 
			}else{
				 
				$classVars = get_class_vars(get_class($this));
				$sql .= "UPDATE {$this->table} SET ";
				foreach ($classVars as $key=>$value)
				{
					if ($key == "{$this->primaryKey}" || $key == "table")
					{
						continue;
					}

					$sql .= "{$key} = '{$this->$key}'";

				}
				$sql .= substr($sql, 0, -2)." WHERE {$this->primaryKey} = {$this->id}";
				 
			}
		}
		elseif ($task == "loadRecord")
		{
		   	$sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = {$this->id}";
		}
		elseif ($task == "getAll")
		{
		 	$sql = "SELECT * FROM {$this->table} LIMIT $start, $pagesize";
		 	$this->sqlRowCount ="SELECT * FROM {$this->table}";
		}
		elseif ($task == "getByFilter")
		{
			$sql = "SELECT * FROM {$this->table} {$this->filter} LIMIT $start, $pagesize";
			$this->sqlRowCount ="SELECT * FROM {$this->table} {$this->filter}";
		}
		elseif ($task == "update")
		{
			if (!empty($_POST)) 
			{
				$sql = "UPDATE {$this->table} SET";
				foreach ($_POST as $key=>$value)
				{
					if ($key != "Save" && $key !="Cancel" && $key != "update" && $key != "table"  && $key != "keyname")
					{
						if ($key == $this->primaryKey)
						{
							$where = " WHERE {$this->primaryKey} = '". $value."'";
						}	
						else 
						{	
							$sql.=" {$key} = '{$value}',"; 
						}	
					}	
				}
				$sql = substr($sql, 0, -1);
			}
			$sql .= $where;	
		}
		elseif ($task == "create")
		{
			if (!empty($_POST))
			{
				$sql = "INSERT INTO {$this->table}";
				foreach ($_POST as $key=>$value)
				{
					if ($key != "Save" && $key !="Cancel" && $key != "insert" && $key != $this->primaryKey  && $key != "table"  && $key != "keyname")
					{
						$keys .= "{$key},";
						$values .= "'{$value}',";	
					}					
				}
				$sql .= "(".substr($keys, 0, -1).") Values (".substr($values, 0, -1).")";
			}
		}
		elseif ($task == "delete")
		{
			if (!empty($_POST))
			{
				$sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = {$_POST[$this->primaryKey]}";
			}
		}	
		return $sql;
	}
	function getAll()
	{
		$dbo = DatabaseGateway::getInstance();
		$sql = $this->buildQuery('getAll');
		$this->dataResults = null;
			
		$dbo->doQuery($sql);
		
		//$this->dataResults = $dbo->returnResults();
		
		if (!$dbo) {
			die("Query to list the table failed");
		}
		
		$fields_num = $dbo->getNumFields();
		$i=0;
		while ($row = $dbo->loadObjectList()) {
			$this->dataResults[$i] = $row;
			$i++;
		}
		$dbo->freeResults();
		
	}
	function getByFilter()
	{
		$dbo = DatabaseGateway::getInstance();
		
		$filterscount = $_GET['filterscount'];
		$op = "filter";
		$where = null;
		
		if ($filterscount > 0)
		{
			$where = " WHERE (";
			$tmpdatafield = "";
			$tmpfilteroperator = "";
				
			for ($i=0; $i < $filterscount; $i++)
			{
			// get the filter's value.
				$filtervalue = $_GET["filtervalue" . $i];
				// get the filter's condition.
				$filtercondition = $_GET["filtercondition" . $i];
				// get the filter's column.
				$filterdatafield = $_GET["filterdatafield" . $i];
				// get the filter's operator.
				$filteroperator = $_GET["filteroperator" . $i];
		
				if ($tmpdatafield == "")
				{
					$tmpdatafield = $filterdatafield;
				}
				else if ($tmpdatafield <> $filterdatafield)
				{
					$where .= ")AND(";
				}
				else if ($tmpdatafield == $filterdatafield)
				{
					if ($tmpfilteroperator == 0)
					{
						$where .= " AND ";
					}
					else
					{	 
						$where .= " OR ";
					}	
				}
		
					// build the "WHERE" clause depending on the filter's condition, value and datafield.
				switch($filtercondition)
				{
					case "CONTAINS":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."%'";
						break;
					case "DOES_NOT_CONTAIN":
						$where .= " " . $filterdatafield . " NOT LIKE '%" . $filtervalue ."%'";
						break;
					case "EQUAL":
						$where .= " " . $filterdatafield . " = '" . $filtervalue ."'";
						break;
					case "NOT_EQUAL":
						$where .= " " . $filterdatafield . " <> '" . $filtervalue ."'";
						break;
					case "GREATER_THAN":
						$where .= " " . $filterdatafield . " > '" . $filtervalue ."'";
						break;
					case "LESS_THAN":	
						$where .= " " . $filterdatafield . " < '" . $filtervalue ."'";
						break;
					case "GREATER_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " >= '" . $filtervalue ."'";
						break;
					case "LESS_THAN_OR_EQUAL":
						$where .= " " . $filterdatafield . " <= '" . $filtervalue ."'";
						break;
					case "STARTS_WITH":
						$where .= " " . $filterdatafield . " LIKE '" . $filtervalue ."%'";
						break;
					case "ENDS_WITH":
						$where .= " " . $filterdatafield . " LIKE '%" . $filtervalue ."'";
						break;
				}

				if ($i == $filterscount - 1)
				{
					$where .= ")";
				}
	
				$tmpfilteroperator = $filteroperator;
				$tmpdatafield = $filterdatafield;
			}
		}
		if (isset($_GET['sortdatafield']))
		{
	
			$sortfield = $_GET['sortdatafield'];
			$sortorder = $_GET['sortorder'];
		
			if ($sortorder != '')
			{
				if ($_GET['filterscount'] == 0)
				{
					if ($sortorder == "desc")
					{
						$where .= "ORDER BY" . " " . $sortfield . " DESC";
					}
					else if ($sortorder == "asc")
					{
						$where .= "ORDER BY" . " " . $sortfield . " ASC";
					}
				}
			}	
		}		
		$this->filter = $where;
	
		$sql = $this->buildQuery('getByFilter');
		$this->dataResults = null;
			
		$dbo->doQuery($sql);
	
		//$this->dataResults = $dbo->returnResults();
	
		if (!$dbo) {
			die("Query to list the table failed");
		}
	
		$fields_num = $dbo->getNumFields();
		$i=0;
		while ($row = $dbo->loadObjectList()) {
			$this->dataResults[$i] = $row;
			$i++;
		}
	
		$dbo->freeResults();
	}
	function update()
	{
		$dbo = DatabaseGateway::getInstance();
		$sql = $this->buildQuery('update');
		$dbo->doQuery($sql);
	
		//$this->dataResults = $dbo->returnResults();
	
		if (!$dbo) {
			die("Query to list the table failed");
		}	
	}
	function create()
	{
		$dbo = DatabaseGateway::getInstance();
		$sql = $this->buildQuery('create');
		$dbo->doQuery($sql);
	
		//$this->dataResults = $dbo->returnResults();
	
		if (!$dbo) {
			die("Query to list the table failed");
		}
	}
	function delete()
	{
		$dbo = DatabaseGateway::getInstance();
		$sql = $this->buildQuery('delete');
		$dbo->doQuery($sql);
	
		//$this->dataResults = $dbo->returnResults();
	
		if (!$dbo) {
			die("Query to list the table failed");
		}
	}
} // end class
?>