<?php 

	class Response extends Service
	{
		private $response_id;
		private $user_id;
		private $topic_id;
		private $response;

		public function __get($attr)
		{
			return $this->$attr;
		}

		public function __set($attr, $value)
		{
			$this->$attr = $value;
		}

		public function getResponses()
		{
			$query = 'SELECT * FROM mismatch_response WHERE user_id = :user_id';
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(':user_id', $this->__get('user_id'));
			$stmt->execute();

			return $stmt;
		}

		public function getResponse()
		{
			$query = '
				SELECT
					mr.response_id,
					mr.topic_id,
					mr.response,
					mt.name AS topic_name,
					mc.name AS category_name
                FROM mismatch_response AS mr
                INNER JOIN mismatch_topic AS mt
                USING (topic_id)
                INNER JOIN mismatch_category AS mc
                USING (category_id)
                WHERE mr.user_id = :user_id
            ';

            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':user_id', $this->__get('user_id'));
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Response');
		}

		public function insert()
		{
			$query = 'INSERT INTO mismatch_response (user_id, topic_id) VALUES (:user_id, :topic_id)';
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(':user_id', $this->__get('user_id'));
			$stmt->bindValue(':topic_id', $this->__get('topic_id'));
			$stmt->execute();

			return true;
		}

		public function update()
		{
			$query = 'UPDATE mismatch_response SET response = :response WHERE response_id = :response_id';
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(':response', $this->__get('response'));
			$stmt->bindValue(':response_id', $this->__get('response_id'));
			$stmt->execute();

			return true;
		}
	}
