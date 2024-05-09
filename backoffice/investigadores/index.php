<?php
/**
 * Inclui arquivos e configurações necessárias
 */
require "../verifica.php";
require "../config/basedados.php";

$find = "";

/**
 * Função para alterar a chave de idioma para a tradução desejada
 * @param string $key Chave a ser traduzida
 * @return string String traduzida se encontrada, string vazia caso contrário
 */
function change_lan($key) {
    $translations = array(
        "previous" => "Anterior",
        "next" => "Próximo",
    );

    return isset($translations[$key]) ? $translations[$key] : "";
}

/**
 * Consulta SQL para recuperar dados com paginação
 */
$sql = "SELECT id, nome, email, ciencia_id, sobre, tipo, fotografia, areasdeinteresse, orcid, scholar FROM investigadores 
ORDER BY CASE WHEN tipo = 'Externo' THEN 1 ELSE 0 END, tipo DESC, nome;";
$result = mysqli_query($conn, $sql);

/**
 * Define a variável de sessão 'anoRelatorio' se enviada via POST
 */
if (isset($_POST["anoRelatorio"])) {
    $_SESSION["anoRelatorio"] = $_POST["anoRelatorio"];
}

/**
 * Define idioma e outras configurações
 */
$language = isset($_SESSION["lang"]) && $_SESSION["lang"] == "en" ? "_en" : "";
$pdo = pdo_connect_mysql();
$records_per_page = 12;

/**
 * Obtém o número total de registros para paginação
 */
$total_records_query = $pdo->query("SELECT COUNT(*) FROM investigadores WHERE tipo = 'Integrado'");
$total_records = $total_records_query->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);
$remaining_records = $total_records % $records_per_page;
if ($remaining_records > 0) {
    $total_pages++;
}

/**
 * Obtém o número da página atual ou define como 1 por padrão
 */
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page = max(1, min($total_pages, intval($page)));

$start_from = ($page - 1) * $records_per_page;

/**
 * Consulta para buscar dados com paginação
 */
$query = "SELECT id, email, nome,
        COALESCE(NULLIF(sobre{$language}, ''), sobre) AS sobre,
        COALESCE(NULLIF(areasdeinteresse{$language}, ''), areasdeinteresse) AS areasdeinteresse,
        ciencia_id, tipo, fotografia, orcid, scholar, research_gate, scopus_id
        FROM investigadores ORDER BY nome
        LIMIT $start_from, $records_per_page";
$result = mysqli_query($conn, $query);

$researchers_data = [];

if (mysqli_num_rows($result) > 0) {
    // Loop through each row and add it to the $researchers array
    while ($row = mysqli_fetch_assoc($result)) {
        $researchers_data[] = $row;
    }
}

// Encode the researchers data to JSON format
$researchers_json = json_encode($researchers_data);

// Output the JSON data directly into a JavaScript variable
echo "<script>var researchersData = " . $researchers_json . ";</script>";

?>


<!-- Importa folhas de estilo e scripts necessários -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<!-- Importa fontes -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Round">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="../assets/js/citation-js-0.6.8.js"></script>

<!-- Estilos personalizados -->
<style type="text/css">
	<?php
	$css = file_get_contents('../styleBackoffices.css');
	echo $css;
	?>
</style>

<?php
// Define o ano atual para o campo de seleção de ano do relatório
if (@$_SESSION["anoRelatorio"] != "") {
	$anoAtual = $_SESSION["anoRelatorio"];
} else {
	$anoAtual = date("Y");
}
?>

<div class="container mt-3">
	<form id="formAnoRelatorio">
		<select required name="anoRelatorio" class="form-control mr-2" style="max-width: 200px; min-width: 160px; display: inline-block;">
			<?php
			// Defina o ano atual
			$anoAtual = date("Y");
			
			// Loop para gerar opções de anos, começando de 1950 até 2999
			for ($ano = 1900; $ano <= $anoAtual; $ano++) {
				// Verifique se este é o ano atual e selecione-o por padrão
				$selected = ($ano == $anoAtual) ? 'selected' : '';
				echo "<option value=\"$ano\" $selected>$ano</option>";
			}
			?>
		</select>
	</form>
</div>

<div class="container-xl">
	<div class="table-responsive">
		<div class="table-wrapper">
			<div class="table-title">
				<div class="row">
					<div class="col-sm-6">
						<h2>Investigadores/as</h2>
					</div>
					<?php if ($_SESSION["autenticado"] == 'administrador') { ?>
						<div class="col-sm-6">
							<a href="create.php" class="btn btn-success"><i class="material-icons">&#xE147;</i> <span>Adicionar Novo Perfil</span></a>
						</div>
					<?php } ?>
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
						<th>Tipo</th>
						<th>Nome</th>
						<th>Email</th>
						<th>CiênciaVitae ID</th>
						<!--<th>Sobre</th>
						<th>Áreas de interesse</th>
						<th>Orcid</th>
						<th>Scholar</th> -->
						<th>Fotografia</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody id="researchersTableBody">
					
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Seção de paginação -->
<div class="pagination_container">
    <ul class="pagination justify-content-center">
        <!-- Link da página anterior -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1"><?= change_lan("previous") ?></a>
        </li>
        <!-- Links das páginas -->
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <!-- Link da próxima página -->
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>"><?= change_lan("next") ?></a>
        </li>
    </ul>
</div>

            <!-- End pagination section -->

<script>
	/**
 * Importa a biblioteca Citation.js.
 */
const Cite = require('citation-js');

/**
 * Função executada quando o documento estiver totalmente carregado.
 */
$(document).ready(function() {
    /**
     * Função executada quando o formulário com o id 'formAnoRelatorio' é submetido.
     * @param {Event} event - O evento de submissão do formulário.
     */
    $("#formAnoRelatorio").submit(function(event) {
        // Prevenir a submissão do formulário
        event.preventDefault();
        // Verificar se o formulário é válido
        if (this.checkValidity() === true) {
            // Obter o ano inserido no input
            var anoRelatorio = $("input[name='anoRelatorio']").val();
            // Atualizar a variável de sessão utilizando AJAX
            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: {
                    anoRelatorio: anoRelatorio
                },
                success: function(response) {
                    // Atualizar o valor do input com o ano retornado pela resposta AJAX
                    $("input[name='anoRelatorio']").val(response.ano);

                    var anoSpan = document.getElementById("anoSpan");
                    if (anoSpan.className = "text-info") {
                        // Atualizar a classe e o conteúdo
                        anoSpan.className = "text-danger"; // Mudar a classe
                        $("#anoSymbol").html("&#xE002;");

                    }

                    $("#anoSubmit").html(response.msg);

                },
                error: function(xhr, status, error) {
                    console.error(xhr, status, error);
                }
            });
        }
    });

    /**
     * Função executada quando um elemento com a classe 'gerarRelatorio' é clicado.
     * @param {Event} e - O evento de clique no elemento.
     */
    $('.gerarRelatorio').on('click', function(e) {
        e.preventDefault(); // Impede o comportamento padrão do link

        // Obter o ID do investigador a partir do atributo de dados
        var investigatorId = $(this).data('id');

        // Fazer uma requisição AJAX para iniciar a geração do relatório
        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
                idGerar: investigatorId
            },
            success: function(response) {
                var reportData = response;
                // Acessar os dados 'publicacoes' e 'patents' de reportData
                var publications = reportData.publicacoes;
                var patents = reportData.patents;

                // Obter a referência APA das publicações
                for (var i = 0; i < publications.length; i++) {
                    var APAreference = processarAPA(publications[i].dados);
                    publications[i].dados = APAreference;
                }

                // Obter a referência APA das patentes
                for (var i = 0; i < patents.length; i++) {
                    var APAreference = processarAPA(patents[i].dados);
                    patents[i].dados = APAreference;
                }

                /**
                 * Função para processar a referência APA.
                 * @param {Object} data - Os dados a serem processados.
                 * @returns {string} - O conteúdo HTML da referência APA.
                 */
                function processarAPA(data) {
                    // Lógica de processamento do "citation.js"
                    var htmlContent = new Cite(data).format('bibliography', {
                        format: 'html',
                        template: 'apa',
                        lang: 'en-US'
                    });
                    return htmlContent;
                }

                // Criar um elemento de formulário
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'autoEscreveRelatorio.php?id=' + investigatorId;

                // Criar um elemento de input para armazenar as publicações
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'publicacoes';
                input.value = JSON.stringify(publications);

                // Anexar o input 'publicacoes' ao formulário
                form.appendChild(input);

                // Criar um elemento de input para armazenar as patentes
                var inputPat = document.createElement('input');
                inputPat.type = 'hidden';
                inputPat.name = 'patentes';
                inputPat.value = JSON.stringify(patents);

                // Anexar o input 'patentes' ao formulário
                form.appendChild(inputPat);

                // Anexar o formulário ao corpo do documento
                document.body.appendChild(form);

                // Submeter o formulário
                form.submit();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});


	//--------------------------------------------------------------------------------------------------------

/**
 * Função para gerar o HTML de um pesquisador.
 * @param {Object} researcher - Os dados do pesquisador.
 * @returns {string} - O HTML do pesquisador.
 */
function generateResearcherHTML(researcher) {
    var html = "<tr>" +
        "<td>" + researcher.tipo + "</td>" +
        "<td>" + researcher.nome + "</td>" +
        "<td>" + researcher.email + "</td>" +
        "<td>" + researcher.ciencia_id + "</td>" +
        "<td><img src='../assets/investigadores/" + researcher.fotografia + "' width='100px' height='100px'></td>" +
        "<td style='min-width:250px;'><a href='edit.php?id=" + researcher.id + "' class='w-100 mb-1 btn btn-primary'><span>Alterar</span></a>";

    // Se o usuário for um administrador, adicionar botão de exclusão
    if ("<?php echo $_SESSION["autenticado"]; ?>" == 'administrador') {
        html += "<a href='remove.php?id=" + researcher.id + "' class='w-100 mb-1 btn btn-danger'><span>Apagar</span></a><br>";
    }

    // Se o tipo de pesquisador não for 'Externo', adicionar botões adicionais
    if (researcher.tipo != "Externo") {
        html += "<a href='resetpassword.php?id=" + researcher.id + "' class='w-100 mb-1 btn btn-warning'><span>Alterar Password</span></a><br>" +
            "<a data-id='" + researcher.id + "' class='gerarRelatorio w-100 mb-1 btn btn-info'><span>Gerar Relatório</span></a><br>" +
            "<a href='publicacoes.php?id=" + researcher.id + "' class='w-100 mb-1 btn btn-secondary'><span>Selecionar Publicações</span></a><br>";
    }

    html += "</td></tr>";
    return html;
}

/**
 * Função para gerar dinamicamente o HTML de todos os pesquisadores.
 * @returns {string} - O HTML de todos os pesquisadores.
 */
function generateResearchersHTML() {
    var html = "";
    for (var i = 0; i < researchersData.length; i++) {
        html += generateResearcherHTML(researchersData[i]);
    }
    return html;
}

// Obter o elemento tbody da tabela
var tbody = document.getElementById("researchersTableBody");

// Gerar e inserir HTML de todos os pesquisadores no tbody da tabela
tbody.innerHTML = generateResearchersHTML();


//SEARCH BAR-------------------------------------------------------------------------

/**
 * Função para filtrar os pesquisadores com base na entrada de pesquisa.
 * Atualiza dinamicamente a tabela com os resultados filtrados.
 */
function filterResearchers() {
    // Obtém o valor do input de pesquisa
    var searchInputValue = document.getElementById("searchInput").value.toLowerCase();
    
    // Filtra os pesquisadores com base no valor de entrada
    var filteredResearchers = researchersData.filter(function(researcher) {
        return researcher.nome.toLowerCase().includes(searchInputValue) ||
               researcher.email.toLowerCase().includes(searchInputValue) ||
               researcher.ciencia_id.toLowerCase().includes(searchInputValue) ||
               researcher.tipo.toLowerCase().includes(searchInputValue);
    });
    
    // Atualiza o HTML da tabela com os pesquisadores filtrados
    tbody.innerHTML = generateFilteredResearchersHTML(filteredResearchers);
}

/**
 * Função para gerar o HTML dos pesquisadores filtrados.
 * @param {Array} filteredResearchers - Array contendo os pesquisadores filtrados.
 * @returns {string} - O HTML dos pesquisadores filtrados.
 */
function generateFilteredResearchersHTML(filteredResearchers) {
    var html = "";
    for (var i = 0; i < filteredResearchers.length; i++) {
        html += generateResearcherHTML(filteredResearchers[i]);
    }
    return html;
}

// Adiciona um listener de evento ao input de pesquisa para acionar a filtragem dos pesquisadores
document.getElementById("searchInput").addEventListener("input", filterResearchers);
</script>