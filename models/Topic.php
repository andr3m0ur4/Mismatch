<?php 

	class Topic extends Service
	{
		private $topic_id;
		private $name;
		private $category;
		private $category_id;

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
			$query = 'SELECT topic_id FROM mismatch_topic ORDER BY category_id, topic_id';
			$stmt = $this->pdo->query($query);
			return $stmt->fetchAll(PDO::FETCH_CLASS, 'Topic');
		}
	}
