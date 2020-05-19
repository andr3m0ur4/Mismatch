<?php 

	class Db
	{
		public static function connect()
		{
			// Conecta-se ao banco de dados
			try {
				$pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
			} catch (PDOException $e) {
				echo "Falha na conexão com o banco de dados: " . $e->getMessage();
				die();
			}

			/*
		    $dbc = mysqli_connect(BD_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		    mysqli_set_charset($dbc, 'utf8');*/

			return $pdo;
		}
	}
