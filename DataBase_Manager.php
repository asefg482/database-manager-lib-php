<?php

// This Lib Created By Asef Ghorbani From IRan  -- Instagram : asef.dev -- github : asefg482


define('TRY_CATCH_RETURN_EXCEPTION_VALUE','Sum Error !');


/**
 * Objects DB
 * @package Kernel
 */
class DB
{

	public static function Error_Handle($Error_Message = 'Error Not Found !',$Path = 'Path Not Found',$Show_Error = true)
	{
		if ($Show_Error)
		{
			echo "\n\n\n\n\n\n\n\n\n";
			echo $Error_Message;
			echo "\n\n";
			echo $Path;
		}
	}

	/**
	 * @var null
	 */
	public static $Con = null;

	/**
	 * @param string $Server
	 * @param string $User_Name
	 * @param string $Password
	 * @param string $DataBase_Name
	 * @return bool|int|mixed|string|null
	 */
	public static function Init(
		$DataBase_Name = 'gbs',
		$Server = 'localhost',
		$User_Name = 'root',
		$Password = ''
	)
	{
		try
		{
			self::$Con = new PDO('mysql:host=' . $Server . ';dbname=' . $DataBase_Name,$User_Name,$Password);
			self::$Con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			return self::$Con->exec('set names utf8');
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Set_DB()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param $SQL_Query
	 * @param array $Values
	 * @return false
	 */
	public static function Do_Query_Static($SQL_Query,$Values = [])
	{
		try
		{
			$Res = self::$Con->prepare($SQL_Query);
			foreach ($Values as $Key => $Item)
			{
				$Res->bindValue($Key + 1,$Item);
			}
			return $Res->execute();
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Do_Query_Static()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param string|null $SQL_Query
	 * @param array|null $Values
	 * @param mixed|bool $Fetch
	 * @return mixed
	 */
	public static function Do_Select_Query_Static($SQL_Query = null,array $Values = [],$Fetch = true)
	{
		try
		{
			$Res = self::$Con->prepare($SQL_Query);
			foreach ($Values as $Key => $Item)
			{
				$Res->bindValue($Key + 1,$Item);
			}

			$Res->execute();


			if (is_null($Fetch))
			{
				return $Res;
			}
			else if (is_array($Fetch))
			{
				if (isset($Fetch[0]))
				{
					$Mode = is_bool($Fetch[0]) ? : $Fetch[0];
					if ($Mode == true)
					{
						if (isset($Fetch[1]))
						{
							return $Res->fetchAll($Fetch[1]);
						}
						else
						{
							return $Res->fetchAll();
						}
					}
					else
					{
						if (isset($Fetch[1]))
						{
							return $Res->fetch($Fetch[1]);
						}
						else
						{
							return $Res->fetch();
						}
					}
				}
				else
				{
					return "ERROR : " . __FILE__ . ' - ' . __LINE__ . ' Fetch ERROR';
				}
			}
			else
			{
				if ($Fetch == true)
				{
					return $Res->fetchAll();
				}
				else if ($Fetch == false)
				{
					return $Res->fetch();
				}
				else
				{
					return $Res->fetch($Fetch);
				}
			}
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Do_Select_Query_Static()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param $SQL_Query
	 * @param null $SEARCH_Values
	 * @param bool $Fetch
	 * @return false
	 */
	public static function Execute_Select_Query($SQL_Query,$SEARCH_Values = null,$Fetch = true)
	{
		try
		{
			if ($SEARCH_Values == null)
			{
				return self::Do_Select_Query_Static($SQL_Query,[],$Fetch);
			}
			else
			{
				return self::Do_Select_Query_Static($SQL_Query,$SEARCH_Values,$Fetch);
			}
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Execute_Select_Query()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}

	/**
	 * @param string $SQL_Query
	 * @param array $Values
	 * @param bool $Fetch
	 * @return mixed
	 */
	public static function Query($SQL_Query,array $Values = [],$Fetch = true)
	{
		try
		{
			return self::Do_Select_Query_Static($SQL_Query,$Values,$Fetch);
		}
		catch (Exception $Ex)
		{
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param string|null $Table
	 * @param mixed|bool $Fetch
	 * @param string|array|null $Select_Values
	 * @param array|null $Where_Values
	 * @param string $Where_Char
	 * @param string|null $Add_Query
	 * @param bool $Set_On_SQL
	 * @return mixed
	 */
	public static function Select(
		$Table = null,
		$Fetch = true,
		$Select_Values = '*',
		$Where_Values = null,
		$Where_Char = "=",
		$Add_Query = null,
		$Set_On_SQL = true
	)
	{
		try
		{
			if ($Select_Values == null || $Select_Values == '*')
			{
				if ($Where_Values == null || $Where_Values == [])
				{
					if ($Set_On_SQL == true)
					{
						return self::Do_Select_Query_Static("SELECT * FROM `$Table` " . $Add_Query,[],$Fetch);
					}
					else
					{
						return "SELECT * FROM `$Table` " . $Add_Query;
					}
				}
				else
				{
					$Counter = 0;
					$Where = "WHERE ";
					$Where_Q = "WHERE ";
					$Where_Q_Items = [];
					foreach ($Where_Values as $Key => $Item)
					{
						$Where .= intval($Item) == false ? "`$Key` $Where_Char `$Item`" : "`$Key` $Where_Char $Item";
						//							$Where .= "`$Key` $Where_Char `$Item`";
						$Where_Q .= "`$Key` $Where_Char ?";


						array_push($Where_Q_Items,$Item);
						if (count($Where_Values) != 1 && $Counter < count($Where_Values) - 1)
						{
							$Where .= " AND ";
							$Where_Q .= " AND ";
						}
						$Counter++;
					}

					if ($Set_On_SQL == true)
					{
						return self::Do_Select_Query_Static("SELECT * FROM `$Table` $Where_Q " . $Add_Query,$Where_Q_Items,$Fetch);
					}
					else
					{
						return [
							"Query" => "SELECT * FROM `$Table` $Where " . $Add_Query,
							"Query_Q" => "SELECT * FROM `$Table` $Where_Q " . $Add_Query,
						];
					}
				}
			}
			else
			{
				$Select_Data = null;
				foreach ($Select_Values as $Key => $Item)
				{
					$Select_Data .= "`$Item`";
					if ((count($Select_Values) - 1) != 1 && (count($Select_Values) - 1) != $Key)
					{
						$Select_Data .= ",";
					}
				}

				if ($Where_Values == null || $Where_Values == [])
				{
					if ($Set_On_SQL == true)
					{
						return self::Do_Select_Query_Static("SELECT $Select_Data FROM `$Table` " . $Add_Query,[],$Fetch);
					}
					else
					{
						return "SELECT $Select_Data FROM `$Table` " . $Add_Query;
					}
				}
				else
				{
					$Counter = 0;
					$Where = "WHERE ";
					$Where_Q = "WHERE ";
					$Where_Q_Items = [];
					foreach ($Where_Values as $Key => $Item)
					{


						$Where .= intval($Item) == false ? "`$Key` $Where_Char `$Item`" : "`$Key` $Where_Char $Item";
						//							$Where .= "`$Key` $Where_Char `$Item`";

						$Where_Q .= "`$Key` $Where_Char ?";
						array_push($Where_Q_Items,$Item);
						if (count($Where_Values) != 1 && $Counter < count($Where_Values) - 1)
						{
							$Where .= " AND ";
							$Where_Q .= " AND ";
						}
						$Counter++;
					}

					if ($Set_On_SQL == true)
					{
						return self::Do_Select_Query_Static("SELECT $Select_Data FROM `$Table` $Where_Q " . $Add_Query,$Where_Q_Items,$Fetch);
					}
					else
					{
						return [
							"Query" => "SELECT $Select_Data FROM `$Table` $Where " . $Add_Query,
							"Query_Q" => "SELECT $Select_Data FROM `$Table` $Where_Q " . $Add_Query,
						];
					}
				}
			}
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Select()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param null $Table
	 * @param array $Values
	 * @param bool $Set_On_SQL
	 * @return false|string[]
	 */
	public static function Insert(
		$Table = null,
		array $Values = [],
		$Set_On_SQL = true
	)
	{
		try
		{
			$Values_Name = null;
			$Values_Val = null;
			$Values_Val_Q = null;
			$Vals = [];
			$Counter = 1;
			foreach ($Values as $Key => $Item)
			{
				$Values_Name .= "`$Key`";
				$Values_Val .= "'$Item'";
				$Values_Val_Q .= "?";
				array_push($Vals,$Item);

				if (count($Values) != 1 && count($Values) != $Counter)
				{
					$Values_Name .= ",";
					$Values_Val .= ",";
					$Values_Val_Q .= ",";
				}
				$Counter++;
			}

			if ($Set_On_SQL == true)
			{
				return self::Do_Query_Static("INSERT INTO `$Table` ($Values_Name) VALUES ($Values_Val_Q)",$Vals);
			}
			else
			{
				return [
					"INSERT INTO `$Table` ($Values_Name) VALUES ($Values_Val)",
					"INSERT INTO `$Table` ($Values_Name) VALUES ($Values_Val_Q)"
				];
			}
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Insert()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param null $Table
	 * @param null $Condition_Data
	 * @param string $Condition_Name
	 * @param bool $Set_On_SQL
	 * @return false|string|string[]
	 */
	public static function Delete(
		$Table = null,
		$Condition_Data = null,
		$Condition_Name = "ID",
		$Set_On_SQL = true
	)
	{
		try
		{
			if ($Condition_Data == null)
			{
				if ($Set_On_SQL == true)
				{
					return self::Do_Query_Static("DELETE FROM `$Table`",[]);
				}
				else
				{
					return "DELETE FROM `$Table`";
				}
			}
			else
			{
				if ($Set_On_SQL == true)
				{
					return self::Do_Query_Static("DELETE FROM `$Table` WHERE `$Condition_Name` =?",[$Condition_Data]);
				}
				else
				{
					return [
						"DELETE FROM `$Table` WHERE `$Condition_Name` =$Condition_Data",
						"DELETE FROM `$Table` WHERE `$Condition_Name` =?"
					];
				}
			}
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Delete()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}


	/**
	 * @param null $Table
	 * @param array $Updates_Data
	 * @param null $Condition_Value
	 * @param string $Condition_Name
	 * @param bool $Set_On_SQL
	 * @return false|string|string[]
	 */
	public static function Update(
		$Table = null,
		$Updates_Data = [],
		$Condition_Value = null,
		$Condition_Name = "ID",
		$Set_On_SQL = true
	)
	{
		try
		{
			$Update_Data = null;
			$Update_Data_Q = null;
			$Counter = 0;
			foreach ($Updates_Data as $Key => $Item)
			{
				$Update_Data .= "`$Key`='$Item'";
				$Update_Data_Q .= "`$Key`=?";
				if (count($Updates_Data) != 1 && (count($Updates_Data) - 1) != $Counter)
				{
					$Update_Data .= ",";
					$Update_Data_Q .= ",";
				}
				$Counter++;
			}

			array_push($Updates_Data,$Condition_Value);
			$Updates_Data = array_values($Updates_Data);

			if ($Set_On_SQL == true)
			{
				return self::Do_Query_Static("UPDATE `$Table` SET $Update_Data_Q WHERE `$Condition_Name` = ?",$Updates_Data);
			}
			else
			{
				return [
					"UPDATE `$Table` SET $Update_Data WHERE `$Condition_Name` = '$Condition_Value'",
					"UPDATE `$Table` SET $Update_Data_Q WHERE `$Condition_Name` = ?"
				];
			}
		}
		catch (Exception $Ex)
		{
			//                DB::Error_Handle($Ex,'Kernel::DB=>Update()');
			DB::Error_Handle($Ex,__FILE__ . " : " . __LINE__ . " => " . __METHOD__ . "()");
			return TRY_CATCH_RETURN_EXCEPTION_VALUE;
		}
	}

}

