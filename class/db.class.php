<?
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                                       *
 * Использование:                                                                        *
 *                                                                                       *
 * require ("db.class.php"); // Подключаем настоящий класс                               *
 *                                                                                       *
 * db::connect_db('Серевер_DB','Имя_DB','Логин_DB','Пароль_DB'); // Подключаемся к БД    *
 *                                                                                       *
 * $dbarray=db::db_to_array('Имя таблицы','Доп.параметры SQL-запроса','Имя поля id');    *
 * // экспортируем таблицу в массив вида $dbarray[$id, ИмяПоля]                          *
 *                                                                                       *
 * echo db::db_size(); // Размер БД                                                      *
 *                                                                                       *
 * echo db::cound_bd($table, $where); // Колличество записей в таблице					 *
 *                                                                                       *
 *                                                                                       *
 * echo db::formatfilesize(Размер в байтах); // Преобразовываем байты в кБ и МБ (Бонус)  *
 *                                                                                       *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Db
{
	public function connect_db($db_host, $db_name, $db_login, $db_pass)
	{
		$db = mysql_connect($db_host,$db_login,$db_pass) or die("MySQL сервер недоступен!<br>\n".mysql_error());; /*Подключение к серверу */
        mysql_select_db($db_name,$db) or die("Нет соединения с БД<br>\n".mysql_error()); /*Подключение к базе данных на сервере*/
        mysql_query("SET NAMES UTF8"); // UTF-8
        mysql_query("SET CHARACTER SET UTF8");
		mysql_query("SET NAMES 'utf8'");
		mysql_query("SET CHARACTER SET 'utf8'");
		mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");
	}



	public function db_to_array($db_str, $db_param, $id)
	{
	    $db_connect="SELECT * FROM {$db_str} {$db_param}";
		$result = mysql_query($db_connect);
        $myrow = mysql_fetch_array($result);
         do
         {
		    $id_db=$myrow[$id];
			foreach ($myrow as $key => $value)
			if (($key!=$id_db) And ($key!=$id)) $dbarray[$id_db][$key] = $value;
		 }
         while ($myrow = mysql_fetch_array($result));
		 return $dbarray;
	}

	public function formatfilesize( $data )
	{
     // bytes
        if( $data < 1024 ) {
        return $data . " bytes";   }
     // kilobytes
        else if( $data < 1024000 ) {
        return round( ( $data / 1024 ), 1 ) . "k"; }
     // megabytes
        else {
        return round( ( $data / 1024000 ), 1 ) . " MB"; }
    }

	public function cound_bd($table, $where="",$print=0)
	{
		$db_connect="SELECT COUNT(1) FROM `{$table}`";
		if (stripos($where, 'where') === false) {
		if (($where!="") AND ($where!=" ")) $db_connect.=" WHERE {$where}";
		} else {
			$db_connect.=" {$where}";
		}
		if ($print!=0) { echo $db_connect."\n<br>"; 
	file_put_contents('temp_log.txt', date("H:i:s")."\n".$db_connect."\n\n"); // !!!!!!!!!
		 }
		$result = mysql_query($db_connect);
        $myrow = mysql_fetch_array($result);
		return $myrow[0];
	}

		public function count_bd($table, $where="",$print=0)
	{
		$db_connect="SELECT COUNT(1) FROM `{$table}`";
		if (stripos($where, 'where') === false) {
		if (($where!="") AND ($where!=" ")) $db_connect.=" WHERE {$where}";
		} else {
			$db_connect.=" {$where}";
		}
		if ($print!=0) { echo $db_connect."\n<br>"; 
	file_put_contents('temp_log.txt', date("H:i:s")."\n".$db_connect."\n\n"); // !!!!!!!!!
		 }
		$result = mysql_query($db_connect);
        $myrow = mysql_fetch_array($result);
		return $myrow[0];
	}


	public function cound_bd_uniq($table, $st, $where="",$print=0) // уникальные значения по $st
	{ // SELECT  COUNT(DISTINCT(`prod_zakup_id`))  FROM `zakup`
		$db_connect="SELECT COUNT(DISTINCT(`{$st}`)) FROM `{$table}`";
		if (stripos($where, 'where') === false) {
		if (($where!="") AND ($where!=" ")) $db_connect.=" WHERE {$where}";
		} else {
			$db_connect.=" {$where}";
		}
		if ($print!=0) { echo $db_connect."\n<br>"; }
		$result = mysql_query($db_connect);
        $myrow = mysql_fetch_array($result);
		return $myrow[0];
	}


	


	public function cound_bd_big($table, $where="",$print=0)
	{
		$db_connect="SELECT COUNT(1) FROM $table";
		if (stripos($where, 'where') === false) {
		if (($where!="") AND ($where!=" ")) $db_connect.=" WHERE {$where}";
		} else {
			$db_connect.=" {$where}";
		}
		if ($print!=0) { echo $db_connect."\n<br>"; }
		$result = mysql_query($db_connect);
        $myrow = mysql_fetch_array($result);
		return $myrow[0];
	}

	public function cound_bd_full($table, $sql="")
	{
		$db_connect="SELECT COUNT(1) FROM `{$table}`";

		if ($sql!="") $db_connect.=" {$sql}";
		//echo $db_connect;
		$result = mysql_query($db_connect);

        $myrow = mysql_fetch_array($result);
		return $myrow[0];
	}

	public function db_size()
	{
	  $result = mysql_query( "SHOW TABLE STATUS" );
      $dbsize = 0;
      while( $row = mysql_fetch_array( $result ) ) {
      $dbsize += $row[ "Data_length" ] + $row[ "Index_length" ];
      $mysql = db::formatfilesize( $dbsize );
      }
	  return $mysql;
    }


    public function is_or_no($table, $row='*', $where="")
    {
    	if ($where!="") $dbw="WHERE {$where}";
    	$db="SELECT {$row} FROM `{$table}` {$dbw}";
    	//echo "SELECT {$row} FROM `{$table}` {$dbw}";

    	$result = mysql_query($db);
  			if(mysql_num_rows($result) > 0 ) {  
  			return 1; // такой есть
			}else{
				return 0; // Такого нет
			}
    }

	public function lastrec($table, $row) // Последняя запись в базу - таблица и столбец ID
	{
		$result = mysql_query ("SELECT * FROM  `{$table}` ORDER BY  `{$row}` DESC LIMIT 1");
	$myrow = mysql_fetch_array($result);
	return $myrow[$row];
	}

	public function summ($table, $column, $where="")
	{

		$result = mysql_query ("SELECT SUM(`{$column}`) FROM `{$table}` {$where}");
		//echo ("SELECT SUM(`{$column}`) FROM `{$table}` {$where}");
	$myrow = mysql_fetch_array($result);

	if ($myrow[0]=="") return 0; else 	return $myrow[0];
	}

	public function norm_text($text)
	{
		$search = array('"',"'");
		$replace= array('″','’');
		$return = str_replace($search,$replace,$text);
		return htmlspecialchars($return);
	}

	public function DuplicateMySQLRecord ($table, $id_field, $id) {
  // load the original record into an array
  $result = mysql_query("SELECT * FROM {$table} WHERE {$id_field}={$id}");
  $original_record = mysql_fetch_assoc($result);

  // insert the new record and get the new auto_increment id
  mysql_query("INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
  $newid = mysql_insert_id();

  // generate the query to update the new record with the previous values
  $query = "UPDATE {$table} SET ";
  foreach ($original_record as $key => $value) {
    if ($key != $id_field) {
        $query .= '`'.$key.'` = "'.str_replace('"','\"',$value).'", ';
    }
  }
  $query = substr($query,0,strlen($query)-2); # lop off the extra trailing comma
  $query .= " WHERE {$id_field}={$newid}";
  mysql_query($query);

  // return the new id
  return $newid;
}
}
?>
