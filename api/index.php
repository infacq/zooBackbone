<?php

require 'Slim/Slim.php';

$app = new Slim();

$app->get('/makhluk', 'senarai_binatang');
$app->get('/makhluk/:id',	'binatang');
$app->get('/makhluk/cari/:query', 'cariNama');
$app->post('/makhluk', 'tambahBinatang');
$app->put('/makhluk/:id', 'kemasKini');
$app->delete('/makhluk/:id',	'hapus');

$app->run();

function senarai_binatang() {
	$sql = "select * FROM makhluk ORDER BY nama";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$kebinatangan = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;		
		echo json_encode($kebinatangan);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function binatang($id) {
	$sql = "SELECT * FROM makhluk WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$binatang = $stmt->fetchObject();  
		$db = null;
		echo json_encode($binatang); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function tambahBinatang() {
	error_log('tambahBinatang\n', 3, '/var/tmp/php.log');
	$request = Slim::getInstance()->request();
	$binatang = json_decode($request->getBody());
	$sql = "INSERT INTO makhluk (nama, tahun_kemasukan, keterangan) VALUES (:nama, :tahun_kemasukan, :keterangan)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("nama", $binatang->nama);
		$stmt->bindParam("tahun_kemasukan", $binatang->tahun_kemasukan);
		$stmt->bindParam("keterangan", $binatang->keterangan);		
		$stmt->execute();
		$binatang->id = $db->lastInsertId();
		$db = null;
		echo json_encode($binatang); 
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function kemasKini($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$binatang = json_decode($body);
	$sql = "UPDATE makhluk SET nama=:nama, tahun_kemasukan=:tahun_kemasukan, keterangan=:keterangan WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $binatang->name);
		$stmt->bindParam("year", $binatang->year);
		$stmt->bindParam("description", $binatang->description);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($binatang); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function hapus($id) {
	$sql = "DELETE FROM makhluk WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function cariNama($query) {
	$sql = "SELECT * FROM makhluk WHERE UPPER(name) LIKE :query ORDER BY nama";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($wines);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getConnection() {
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="";
	$dbname="zoo";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>