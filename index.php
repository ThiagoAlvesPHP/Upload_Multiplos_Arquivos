<?php
require 'Upload.php';
$sql = new Upload();
$dados = $sql->getUpload();

if (isset($_FILES['arquivo'])) {
	if (count($_FILES['arquivo']['tmp_name']) > 0) {
		for ($i=0; $i < count($_FILES['arquivo']['tmp_name']); $i++) {
			if ($_FILES['arquivo']['type'][$i] == 'image/jpeg') {

				$largura = 300;
				$altura = 300;

				//CAPTURANDO LARGURA E ALTURA ORIGINAL DA IMAGEM
				list($larguraOri, $alturaOri) = getimagesize($_FILES['arquivo']['tmp_name'][$i]);
				$ratio = $larguraOri / $alturaOri;
				
				if ($largura / $altura > $ratio) {
					$largura = $altura * $ratio;
				} else {
					$altura = $largura / $ratio;
				}	

				//CRIAR IMAGEM COM ALTURA E ALTURA
				$imagem_final = imagecreatetruecolor($largura, $altura);
				$imagem_original = imagecreatefromjpeg($_FILES['arquivo']['tmp_name'][$i]);
				imagecopyresampled($imagem_final, $imagem_original, 0, 0, 0, 0, $largura, $altura, $larguraOri, $alturaOri);

				$arquivo = md5($imagem_final.time().rand(0,999)).'.jpeg'; 
				$texto = $_FILES['arquivo']['name'][$i];
				
				imagejpeg($imagem_final, "arquivos/".$arquivo, 100);
				
				$sql->setUpload($arquivo, $texto);
				header('Location: index.php');
			}
		}
	}
}

//DELETANDO IMAGEM DO DB E DA PASTA
if (isset($_GET['idDel']) && !empty($_GET['idDel'])) {
	$id = addslashes($_GET['idDel']);
	$arquivo = $sql->getArquivoID($id);
	unlink('arquivos/'.$arquivo['img']);

	$sql->delUpload($id);
	header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="favicon.png" />
	<title>Upload Arquivos</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
	<h3>Formulário de envio de Multiplos Arquivos</h3>
	<form method="POST" enctype="multipart/form-data">
		Arquivos:<br>
		<input type="file" class="form-control" name="arquivo[]" multiple=""><br>
		<button class="btn btn-primary btn-block btn-lg">Enviar</button>
	</form>
	<hr>
	<div class="row">
	<?php
	if (!empty($dados)):
		foreach ($dados as $img):
		?>
		<div class="col-sm-3">
			<img class="img-responsive" style="height: 200px;" width="100%" src="arquivos/<?=$img['img']; ?>" data-toggle="modal" data-target="#<?=$img['id']; ?>">
			<input readonly="" class="form-control" value="<?=str_replace('.jpg', '', $img['descricao']); ?>">
			<a class="btn btn-danger btn-block" href="index.php?idDel=<?=$img['id']; ?>">Excluir</a>
			<br>
		</div>
		<!-- Modal -->
		<div id="<?=$img['id']; ?>" class="modal fade" role="dialog">
		  <div class="modal-dialog">
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Imagem de número <?=$img['id']; ?></h4>
		      </div>
		      <div class="modal-body">
		        <img class="img-responsive" width="100%" src="arquivos/<?=$img['img']; ?>">
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
		      </div>
		    </div>
		  </div>
		</div>
		<?php
		endforeach;
	endif;
	?>
	</div>
</div>
</body>
</html>