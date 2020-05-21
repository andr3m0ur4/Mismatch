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

		public function getUser()
		{
			$query = '
				SELECT username, first_name, last_name, gender, birthdate, city, state, picture
                FROM mismatch_user WHERE user_id = :user_id
            ';

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue('user_id', $this->__get('user_id'));
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
            	return $stmt->fetchObject('User');
            }

            return false;
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

		public function update()
		{
			$query = '
				UPDATE mismatch_user SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    gender = :gender,
                    birthdate = :birthdate,
                    city = :city,
                    state = :state,
                    picture = :picture
                WHERE user_id = :user_id
            ';

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':first_name', $this->__get('first_name'));
            $stmt->bindValue(':last_name', $this->__get('last_name'));
            $stmt->bindValue(':gender', $this->__get('gender'));
            $stmt->bindValue(':birthdate', $this->__get('birthdate'));
            $stmt->bindValue(':city', $this->__get('city'));
            $stmt->bindValue(':state', $this->__get('state'));
            $stmt->bindValue(':picture', $this->__get('picture'));
            $stmt->bindValue(':user_id', $this->__get('user_id'));
            $stmt->execute();

            return true;
		}
	}
