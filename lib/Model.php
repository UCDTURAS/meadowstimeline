<?php
namespace FAC;
use FAC;
/**
 * MySql database class file.
 * @package FAC
 * @author daithi coombes <david.coombes@futureanalytics.ie>
 */

/**
 * Databse class.
 * @todo have as singleton (means not extending PDO, but instead referencing it internally?).
 */
class Model{

	/** @var PDO The PDO object */
	protected $db;

  /** @var array An array of categories */
  private $categories = array('',
    'Infrastructure',
    'Development',
    'Housing',
    'Recreation',
    'Community Facilities',
    'Events',
    'Memories',
    'Transport',
    'Industry and Employment',
  );

	/**
	 * Factory method.
	 * Tries to return instance of db in global space, if exists
	 * @return \PDO Returns a singleton PDO instance ($db).
	 */
	public static function factory()
	{

		global $db;

		if(isset($db) && is_object($db) && get_class($db)==__CLASS__)
			return $db;

		$config = Config::getInstance()->get('db');

		$pdo = new \PDO("mysql:host={$config['host']};dbname={$config['name']}", $config['user'], $config['pass']);
		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		$obj = new Model();
		return $obj->setDb($pdo);
	}

	/**
	 * Insert row into table.
	 * Will sanatize each value and use keys as columns.
	 * @uses \htmlspecialchars()
	 * @param  string $table The table name.
	 * @param  array  $row   An associative array of column=>value pairs.
	 * @return integer        The last_insert_id.
	 */
	public function insert($table, array $row)
	{


		$fields = array_keys($row);
		foreach($row as $key=>$val)					//sanatize input
			$row[$key] = htmlspecialchars($val);

		//set query fields
		$qry = "INSERT INTO {$table} (`"
			. implode("`,`", array_keys($row))
			. "`) VALUES ";

    //set query bind placeholders
		foreach($row as $field=>$value)
			$qry_values[] = ":{$field}";
		$stmt = $this->db->prepare($qry . "(".implode(", ", $qry_values).")");

		//bind values
		foreach($row as $field=>$value)
			$stmt->bindParam(":{$field}", $$field);

		foreach($row as $field=>$value){
			${$field} = $value;
		}
		$stmt->execute();

		return $this->getDb()->lastInsertId();
	}

	/**
	 * Download entries as a csv file.
	 * @param  array  $col_head The csv column headers.
	 */
	public function downloadCSV(array $col_head){

		$entries = $this->query("SELECT * FROM Entry");
		$handle = fopen("php://memory", "w");

		fputcsv($handle, $col_head, ",");
		foreach($this->query("SELECT * FROM Entry") as $key=> $row){
      if($row['image']!='') $row['image'] = "/uploads/{$row['image']}";
      if($row['file']!='') $row['file'] = "/uploads/{$row['file']}";
      $row['description'] .= '<br>Category: '.$this->categories[$row['category']];
			fputcsv($handle, $row, ",");
		}
		fseek($handle, 0);

		//header('Content-Type: application/csv');
		//header('Content-Disposition: attachement; filename="timemapper.csv"');
		fpassthru($handle);
	}

  /**
   * Download json of data.
   * Used for the ajax search - see self::downloadCSV for an overall api.
   * @param  string $query The query string.
   * @return json        Returns a json string.
   */
  public function downloadJSON($query){

    $qry = "SELECT * FROM Entry WHERE title LIKE '%$query%'";

    if($_GET['catId']!='null')
      $qry .= " AND category=".$_GET['catId'];

    $entries = $this->query($qry);
    $res = array('suggestions'=>array());

    foreach($entries as $entry){

      $start = \DateTime::createFromFormat('Y-m-d H:i:s', $entry['start'])->format('M n, Y');
      $category = $this->categories[$entry['category']];

      $res['suggestions'][] = array(
        'title' => "{$start} - {$category} - {$entry['title']}",
        'id' => $entry['id'],
      );
    }

    return json_encode($res);
  }

	/**
	 * Get the db instance.
	 * @return PDO
	 */
	public function getDb()
	{

		return $this->db;
	}

	/**
	 * Set the database object.
	 * @param PDO $db The database object.
	 * @return Model Returns this for chaining.
	 */
	public function setDb(\PDO $db)
	{

		$this->db = $db;
		return $this;
	}

	/**
	 * Check if db table exists.
	 * @param string $table The table name.
	 * @return boolean Default false.
	 */
	public function tableExists($table)
	{

		$test = $this->query("SELECT 1 FROM {$table}");

		if(!Error::isError($test))
			return true;
		else
			return false;
	}

	/**
	 * Update a row.
	 * @param  string $table The table name
	 * @param  array $data  An array of column=>value pairs
	 * @param  string $where The column to check against
	 */
	public function update($table, $data, $where)
	{

		$where_value = $data[$where];
		unset($data[$where]);

		$db = Model::factory();
		$sql = "UPDATE {$table} SET ";
		$sets = array();

		//build statement
		foreach($data as $key => $value){
			$sets[] = "{$key}=:{$key}";
		}
		$stmt = $this->db->prepare($sql . implode(", ", $sets));

		//bind values
		foreach($data as $field=>$value){
			$stmt->bindParam(":{$field}", $$field);
			${$field} = $value;
		}

		$stmt->execute();
	}

	/**
	 * Query the database WITHOUT preparing statements.
	 * @param string $qry The raw mysql query.
	 * @return array Returns an array of row objects or Error.
	 */
	public function query($qry, $return=\PDO::FETCH_ASSOC)
	{

		try{
			$res = $this->db->query($qry);
		}catch(\PDOException $e){
			return new Error($e->getMessage());
		}

		$results = array();
		while($row = $res->fetch(\PDO::FETCH_ASSOC)){
			$results[] = $row;
		}

		return $results;
	}
}
