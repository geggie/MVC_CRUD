<?php
require_once './model/entityService.php';
class CrudController {
	
	private $entityService = null;
	
	public function __construct($table, $keyField) {
		$this->entityService = new entityService();
		$this->entityService->setTable($table);
		$this->entityService->setPrimaryKey($keyField);
	}
	public function redirect($location) {
		header ('Location: '.$location);
	}
	
	public function handleRequest() {
		$op = isset($_GET['op'])?$_GET['op']:null;
		
		$filterquery = "";
		
		try {  
			
				if (isset($_POST['update'])) {
					self::updateContent();
				}
				else if (isset($_GET['filterscount'])){
					self::filterContent();
					//call_user_func('CrudController::listContent');
				}
				else if (isset($_POST['insert'])){
					self::createContent();
				}
				else if (isset($_POST['delete'])){
					self::deleteContent();
				}	
				else {
					call_user_func('CrudController::listContent');
				}
			}
			catch (Exception $e) {
				$this->showError ("Operation {$op} was not found!");
			}
		}	

	function listContent() {
		//$orderby = isset($_GET['orderby'])?$_GET['orderby']:null;
		$this->entityService->getAll();
		$rowcount = $this->entityService->getRowCount();
		$totalRows = $this->entityService->getTotalRows();
		$results = $this->entityService->getDataResults();
		$keyField = $this->entityService->getPrimaryKey();
	//	include 'view/view.php';
	    include 'view/JSONView.php';
	}
	function filterContent() {
		//$orderby = isset($_GET['orderby'])?$_GET['orderby']:null;
		// filter data.
		
		
		$this->entityService->getByFilter();
		$rowcount = $this->entityService->getRowCount();
		$totalRows = $this->entityService->getTotalRows();
		$results = $this->entityService->getDataResults();
		$keyField = $this->entityService->getPrimaryKey();
		//	include 'view/view.php';
		include 'view/JSONView.php';
	}
	function showContent() {
		$id = isset($_GET['id'])?$_GET['id']:null;
		$this->entityService->loadRecord($id);
		$results = $this->entityService->getDataResults();
		$keyField = $this->entityService->getPrimaryKey();
		include 'view/details.php';
	}	
	function updateContent(){
		if (isset($_POST['update'])) {
			
			// UPDATE COMMAND
			$this->entityService->update();
			
		}
	}
	function createContent(){
		if (isset($_POST['insert'])) {
				
			// create COMMAND
			$this->entityService->create();
				
		}
	}
	function deleteContent(){
		if (isset($_POST['delete'])) {
	
			// create COMMAND
			$this->entityService->delete();
	
		}
	}
}