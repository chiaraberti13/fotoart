<?php
/*
* Paging class
*/
class paging
{
	public $totalPages;
	public $perPage;
	public $totalResults;
	public $currentPage;
	public $pageName;
	public $nextPage;
	public $prefix;
	public $pageVar;
	
	public function __construct($prefix)
	{
		$this->prefix = $prefix;
	}
	
	/*
	* Set the total number of results from the MySQL query
	*/
	public function setTotalResults($totalResults)
	{
		$this->totalResults = $totalResults;
	}
	
	/*
	* Get the total number of pages
	*/
	public function getPages()
	{
		$this->totalPages = ceil($this->totalResults / $this->perPage);
		return $this->totalPages;
	}
	
	/*
	* Set a page name variable
	*/
	public function setPageVar($pageVar='')
	{
		$this->pageVar = ($pageVar) ? $pageVar : 'page';
	}
	
	/*
	* Set the current page that is being displayed
	*/
	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
		$_SESSION[$this->prefix.'CurrentPage'] = $currentPage;
	}
	
	/*
	* Get the number of the next page or return false if there is no next page
	*/
	public function getNextPage()
	{
		if($this->currentPage < $this->totalPages)
			return $this->currentPage + 1;
		else
			return false;
	}
	
	/*
	* Get the number of the previous page or return false if there is no previous page
	*/
	public function getPreviousPage()
	{
		if($this->currentPage > 1)
			return $this->currentPage - 1;
		else
			return false;
	}
	
	/*
	* Return an array of the paging results to use in the page
	*/
	public function getPagingArray()
	{
		$pages['totalPages'] = $this->getPages();
		$pages['perPage'] = $this->perPage;
		$pages['totalResults'] = $this->totalResults;
		$pages['currentPage'] = $this->currentPage;
		$pages['nextPage'] = $this->getNextPage();
		$pages['previousPage'] = $this->getPreviousPage();
		$pages['pageName'] = $this->pageName;
		$pages['pageVar'] = $this->pageVar;
		return $pages;	
	}
	
	/*
	* Set the page name to be used
	*/
	public function setPageName($pageName)
	{
		$this->pageName = $pageName;
	}
	
	/*
	* Get the record the db should start at
	*/
	public function getStartRecord()
	{
		return $this->perPage * ($this->currentPage - 1);
	}
	
	/*
	* Set the number of results that are shown perpage
	*/
	public function setPerPage($perPage)
	{
		$this->perPage = $perPage;
	}
}
?>