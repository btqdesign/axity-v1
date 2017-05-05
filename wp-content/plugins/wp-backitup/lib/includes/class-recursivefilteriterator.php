<?php


class WPBackItUp_RecursiveFilterIterator extends RecursiveFilterIterator {

	protected $exclude;

	/**
	 * Constructor that takes an array of folders and files to exclude
	 *
	 * @param RecursiveIterator $iterator
	 * @param array $exclude
	 */
	public function __construct($iterator, $exclude)
	{
		if (!is_array($exclude)) {
			$exclude= array(); //empty array
		}
		parent::__construct($iterator);
		$this->exclude = $exclude;
	}

	/**
	 * Filter FILES & FOLDERS contained in exclude array
	 *  -  Folders use wildcard search
	 *
	 * @return bool
	 */
	public function accept()
	{
		//wildcard search only used on folders
		if ($this->isDir()){
			return ! $this->strposa0($this->getFilename(), $this->exclude);
		}

		//files
		return ! in_array($this->getFilename(), $this->exclude);

		//Filters folders only
		//return !($this->isDir() && in_array($this->getFilename(), $this->exclude));
	}

	/**
	 * Filter dirs on exclude array
	 * @return WPBackItUp_RecursiveFilterIterator
	 */
	public function getChildren()
	{
		return new WPBackItUp_RecursiveFilterIterator($this->getInnerIterator()->getChildren(), $this->exclude);
	}


	/**
	 * Search for item name in exclude array
	 *  - MUST be in position 0 because we are looking for the folder root, not children
	 *
	 * @param $item_name
	 * @param $exclude_array
	 * @param int $offset
	 *
	 * @return bool
	 */
	private function strposa0($item_name, $exclude_array, $offset=0) {

		foreach($exclude_array as $query) {
			if (!empty($query)){
				$pos = strpos($item_name, $query, $offset);
				//looking for position 0 - string must start at the beginning
				if($pos === 0) return true; // stop on first true result
			}
		}
		return false;
	}
}