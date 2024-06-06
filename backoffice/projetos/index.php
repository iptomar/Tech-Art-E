<?php
require "../verifica.php";
require "../config/basedados.php";

/**
 * Query obtém todos os projetos relacionados com o investigador logado.
 * Se for admin logado, mostra todos os projetos.
 */
$innerjoinSQL = "";
$autenticado = $_SESSION["autenticado"];
if ($autenticado != "administrador") {
    $innerjoinSQL = "INNER JOIN investigadores_projetos inpj ON pj.id = inpj.projetos_id WHERE inpj.investigadores_id = '$autenticado'";
}
$sql = "SELECT id, nome, referencia, areapreferencial, financiamento, fotografia, concluido FROM projetos pj $innerjoinSQL ORDER BY nome";
$result = mysqli_query($conn, $sql);
// Gerar um array de projetos para ser usado no JavaScript
$projects_data = [];

if (mysqli_num_rows($result) > 0) {
    // Loop through each row and add it to the $projects_data array
    while ($row = mysqli_fetch_assoc($result)) {
        $projects_data[] = $row;
    }
}
mysqli_close($conn);
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
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>Projetos</h2>
                    </div>
                    <div class="col-sm-6">
                        <a href="create.php" class="btn btn-success"><i class="material-icons">&#xE147;</i> <span>Adicionar Novo Projeto</span></a>
                    </div>
                </div>
                <div class="row mt-3">
					<div class="col">
						<input type="text" id="searchInput" class="form-control" placeholder="Pesquisar...">
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
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="projectsTableBody">
                    <!-- O conteúdo será preenchido dinamicamente pelo JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Array de projetos passado do PHP para o JavaScript
var projectsData = <?php echo json_encode($projects_data); ?>;
var autenticado = '<?php echo $_SESSION["autenticado"]; ?>';

function generateProjectHTML(project) {
    var actions = '';
    if (autenticado === "administrador" || project.isManager) {
        actions += "<a href='edit.php?id=" + project.id + "' class='btn btn-primary' style='min-width: 85px;'><span>Alterar</span></a>";
        actions += "<br><br>";
        actions += "<a href='remove.php?id=" + project.id + "' class='btn btn-danger' style='min-width: 85px'><span>Apagar</span></a>";
    }

    return `
        <tr>
            <td>${project.nome}</td>
            <td>${project.concluido ? "Concluído" : "Em Curso"}</td>
            <td>${project.referencia}</td>
            <td>${project.areapreferencial}</td>
            <td>${project.financiamento}</td>
            <td><img src='../assets/projetos/${project.fotografia}' width='100px' height='100px'></td>
            <td>${actions}</td>
        </tr>
    `;
}

function generateProjectsHTML(projects) {
    var html = "";
    for (var i = 0; i < projects.length; i++) {
        html += generateProjectHTML(projects[i]);
    }
    return html;
}


// Obter o elemento tbody da tabela
var tbody = document.getElementById("projectsTableBody");

// Gerar e inserir HTML de todos os projetos no tbody da tabela
tbody.innerHTML = generateProjectsHTML(projectsData);

document.getElementById('searchInput').addEventListener('input', function() {
    var searchTerm = this.value.toLowerCase();
    var filteredProjects = projectsData.filter(function(project) {
        var estado = project.concluido ? "concluído" : "em curso";
        return project.nome.toLowerCase().includes(searchTerm) ||
               project.referencia.toLowerCase().includes(searchTerm) ||
               project.areapreferencial.toLowerCase().includes(searchTerm) ||
               project.financiamento.toLowerCase().includes(searchTerm) ||
               estado.toLowerCase().includes(searchTerm);
    });
    tbody.innerHTML = generateProjectsHTML(filteredProjects);
});



</script>