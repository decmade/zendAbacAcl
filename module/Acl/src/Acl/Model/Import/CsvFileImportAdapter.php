<?php
namespace Acl\Model\Import;


class CsvFileImportAdapter implements ImportAdapterInterface
{
	/**
	 * import data from a path to a CSV file
	 *
	 * @param string $filePath
	 *
	 * @return array<A>
	 */
	public function import($filePath)
	{
		$fp = $this->getFilePointer($filePath);
		$unAssociativeData = $this->getUnassociatedArray($fp);

		/*
		 * if no data is return, return no data
		 */
		if (count($unAssociativeData) == 0 ) {
			return array();
		} else {
			return $this->convertToAssociativeArray($unAssociativeData);
		}
	}

	/**
	 * get the file pointer associated with the file at the path
	 * if the file does not exist, return null
	 *
	 * @param string $filePath
	 *
	 * @return resource
	 */
	private function getFilePointer($filePath)
	{
		if (is_file($filePath)) {
			return fopen($filePath, 'r');
		} else {
			return ull;
		}
	}

	/**
	 * pull the data from the csv file as is without
	 * delineating the columns from the other rows
	 *
	 * @param resource $fp
	 *
	 * @return array
	 */
	private function getUnassociatedArray($fp)
	{

		/*
		 * if there is no file pointer, just return an empty array
		 */
		if ($fp == null ) {
			return array();
		}

		$output = array();

		while ($row = fgetcsv($fp, 0, ',', '"') ) {
			if ($row != null){
				$output[] = $row;
			}
		}

		return $output;
	}

	/**
	 * convert the array of CSV data into an associative
	 * array with the values in the column row as the indexes
	 *
	 * @param array $data
	 *
	 * @return array<A>
	 */
	private function convertToAssociativeArray(array $data)
	{
		$columns = null;
		$output = array();


		foreach($data as $row) {
			switch(true) {
				case ( $columns == null ) :
					$columns = $row;
					break;
				case (count($row) != count($columns) ) :
					continue;
					break;
				default :
					$columnCount = count($columns);
					$thisRow = array();

					for ($i = 0; $i < $columnCount; $i++) {
						$thisRow[$columns[$i]] = $row[$i];
					}

					$output[] = $thisRow;
			}
		}

		return $output;

	}

}