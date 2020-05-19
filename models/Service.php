<?php 

	class Service
	{
		protected $pdo;

		public function __construct()
		{
			$pdo = Db::connect();
			$this->pdo = $pdo;
		}
	}
