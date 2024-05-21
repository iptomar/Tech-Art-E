<?php
require "../verifica.php";
require "../config/basedados.php";

/**
 * 
 * Query obtém todos os projetos relacionados com o investigador logado
 * Se for admin logado, mostra todos os projetos
 * 
 */
$innerjoinSQL = "";
$autenticado = $_SESSION["autenticado"];
if($autenticado != "administrador"){
	$innerjoinSQL = "inner join investigadores_projetos inpj on pj.id = inpj.projetos_id where inpj.investigadores_id = '$autenticado'";
}
$sql = "SELECT id, nome, referencia, areapreferencial, financiamento, fotografia, concluido FROM projetos pj $innerjoinSQL ORDER BY nome";
$result = mysqli_query($conn, $sql);

?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style type="text/css">
	<?php
	$css = file_get_contents('../styleBackoffices.css');
	echo $css;
	?>
</style>

<div class="container-xl">
	<div class="container-xl">
		<div class="table-responsive">
			<div class="table-wrapper">
				<div class="table-title">
					<div class="row">
						<div class="col-sm-6">
							<h2>Projetos</h2>
						</div>
						<div class="col-sm-6">
							<a href="create.php" class="btn btn-success"><i class="material-icons">&#xE147;</i> <span>Adicionar
									Novo Projeto</span></a>
						</div>
					</div>
				</div>
				<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Estado</th>
            <th>Referência</th>
            <th>TECHN&ART Área Preferencial</th>
            <th>Financiamento</th>
            <th>Fotografia</th>
            <th>Ações</th> <!-- Movido para o final -->
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row["nome"] . "</td>";
                if($row["concluido"]){
                    echo "<td>Concluído</td>";
                }else{
                    echo "<td>Em Curso</td>";
                }
                echo "<td>" . $row["referencia"] . "</td>";
                echo "<td>" . $row["areapreferencial"] . "</td>";
                echo "<td>" . $row["financiamento"] . "</td>";
                echo "<td><img src='../assets/projetos/$row[fotografia]' width='100px' height='100px'></td>";
                // Ações movidas para o final
                echo "<td>";
                $sql1 = "SELECT investigadores_id, isManager FROM investigadores_projetos WHERE projetos_id = " . $row["id"];
                $result1 = mysqli_query($conn, $sql1);
                $isManager = 0;
                if (mysqli_num_rows($result1) > 0) {
                    while (($row1 = mysqli_fetch_assoc($result1))) {
                        $isManager = $row1['isManager'];
                    }
                }
                if ($_SESSION["autenticado"] == "administrador" || $isManager == 1) {
					echo "<a href='edit.php?id=" . $row["id"] . "' class='btn btn-primary' style='min-width: 85px;'><span>Alterar</span></a>";
					echo "<br><br>";
                    echo "<a href='remove.php?id=" . $row["id"] . "' class='btn btn-danger' style='min-width: 85px'><span>Apagar</span></a>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>
			</div>
		</div>
	</div>

	<?php
	mysqli_close($conn);
	?>