<?php 

	class User extends Service
	{
		private $user_id;
		private $username;
		private $password;
		private $join_date;
		private $first_name;
		private $last_name;
		private $gender;
		private $birthdate;
		private $city;
		private $state;
		private $picture;

		public function __get($attr)
		{
			return $this->$attr;
		}

		public function __set($attr, $value)
		{
			$this->$attr = $value;
		}

		public function getAll()
		{
			$query = '
				SELECT user_id, first_name, picture FROM mismatch_user
				WHERE first_name IS NOT NULL
				ORDER BY join_date DESC
				LIMIT 5
			';

			$data = $this->pdo->query($query);

			return $data->fetchAll(PDO::FETCH_CLASS, 'User');
		}

		public function verifyUser()
		{
			$query = '
				SELECT user_id, username FROM mismatch_user
                WHERE username = :username AND password = :password
            ';

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $this->__get('username'));
            $stmt->bindValue(':password', sha1($this->__get('password')));
            $stmt->execute();

            return $stmt;
		}

		public function countUser()
		{
			$query = 'SELECT * FROM mismatch_user WHERE username = :username';
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(':username', $this->username);
			$stmt->execute();
			return $stmt->rowCount();
		}

		public function insert()
		{
			$query = '
				INSERT INTO mismatch_user (username, password, join_date) 
                VALUES (:username, :password, NOW())
            ';

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $this->__get('username'));
            $stmt->bindValue(':password', sha1($this->__get('password')));
            $stmt->execute();

            return true;
		}
	}
