<?php
date_default_timezone_set('America/Sao_Paulo');
class Upload{
	private $db;

	public function __construct(){
		$optionss = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
		$file = file_get_contents('config.json');
		$options = json_decode($file, true);

		$config = array();

		$config['db'] = $options['db'];
		$config['host'] = $options['localhost'];
		$config['user'] = $options['user'];
		$config['pass'] = $options['pass'];

		try {
			$this->db = new PDO("mysql:dbname=".$config['db'].";host=".$config['host']."", "".$config['user']."", "".$config['pass']."", $optionss);
		} catch(PDOException $e) {
			echo "FALHA: ".$e->getMessage();
		}
	}

	public function setUpload($img, $descricao){

		$sql = $this->db->prepare("
			INSERT INTO arquivos 
			SET img = :img, 
			descricao = :descricao");
		$sql->bindValue(":img", $img);
		$sql->bindValue(":descricao", $descricao);
		$sql->execute();

		return true;
	}
	public function getUpload(){
		$sql = $this->db->prepare("SELECT * FROM arquivos");
		$sql->execute();

		return $sql->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getArquivoID($id){
		$sql = $this->db->prepare("SELECT img FROM arquivos WHERE id = :id");
		$sql->bindValue(":id", $id);
		$sql->execute();

		return $sql->fetch(PDO::FETCH_ASSOC);
	}
	public function delUpload($id){
		$sql = $this->db->prepare("DELETE FROM arquivos WHERE id = :id");
		$sql->bindValue(":id", $id);
		$sql->execute();

		return true;
	}
	
}