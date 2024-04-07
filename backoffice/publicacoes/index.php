<?php
require "../verifica.php";
require "../config/basedados.php";
require "bloqueador.php";

function toJson($str) {
    // Regular expression pattern to extract key-value pairs
    $pattern = '/(\w+)\s*=\s*(\{.*?\}|[^{},]+)(?:,|\s*})/';

    // Match all key-value pairs
    preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

    $data = [];
    foreach ($matches as $match) {
        $key = trim($match[1]);
        $value = trim($match[2], '{}');
        $data[$key] = $value;
    }

    // Encode array into JSON
    return json_encode($data, JSON_PRETTY_PRINT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "SELECT p.dados, YEAR(p.data) AS publication_year, p.tipo, pt.valor_site_pt, p.idPublicacao, pi.investigador, i.tipo AS investigador_tipo
					FROM publicacoes p
					LEFT JOIN publicacoes_tipos pt ON p.tipo = pt.valor_API
					INNER JOIN publicacoes_investigadores pi ON p.idPublicacao = pi.publicacao
					INNER JOIN investigadores i ON i.id = pi.investigador
					WHERE visivelGeral = true
					ORDER BY publication_year DESC, pt.valor_site_pt, p.data DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // Array que irá conter todas as publicações do investigador
    $publications = array();
    if ($result !== false) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Guardar a row como um array no array publicações com a key sendo o seu id
            $publications[$row["idPublicacao"]] = $row;
        }
    }
    mysqli_stmt_close($stmt);

	//Guardar os resultados da publicacoes selecionadas como visíveis, e os não selecionados como não visíveis
	if (isset($_POST["saveChanges"])) {
		// Preparar comando SQL
		$sql = "UPDATE publicacoes SET visivelGeral = ? WHERE idPublicacao = ?";
		$stmt = mysqli_prepare($conn, $sql);

		// Vincular parâmetros
		mysqli_stmt_bind_param($stmt, "is", $visibilitySite, $checkboxId);

		//Obter as os ids de todas as publicações do investigador
		$existingIds = array_keys($publications);

		// Percorrer todas as publicações do investigador
		foreach ($existingIds as $checkboxId) {
			// Checkboxs não está selecionado, definir visivel como 1
			$visibilitySite = 1;

			// Verificar se o idCheckbox está presente nos dados POST
			if (isset($_POST["publicacao"][$checkboxId]))
			{
				// Checkbox Geral está selecionado, definir visivel como 0
				$visibilitySite = 0;
			}

			mysqli_stmt_execute($stmt);
		}
		mysqli_stmt_close($stmt);
	}
}

?>

<section class='product_section layout_padding'>
    <div style='padding-top: 50px; padding-bottom: 30px;'>
        <div class='container'>
            <div class='heading_container3'>
                <h3 class="heading_h3" style="text-transform: uppercase;">
                    Remover Publicações
                </h3><br><br>
                <?php
                if (!isset($_SESSION["lang"])) {
                    $lang = "pt";
                } else {
                    $lang = $_SESSION["lang"];
                }
                $valorSiteName = "valor_site_$lang";
                $sql = "SELECT p.dados, YEAR(p.data) AS publication_year, p.tipo, pt.valor_site_pt, p.idPublicacao, pi.investigador, i.tipo AS investigador_tipo
								FROM publicacoes p
								LEFT JOIN publicacoes_tipos pt ON p.tipo = pt.valor_API
								INNER JOIN publicacoes_investigadores pi ON p.idPublicacao = pi.publicacao
								INNER JOIN investigadores i ON i.id = pi.investigador
								WHERE visivelGeral = true
								ORDER BY publication_year DESC, pt.valor_site_pt, p.data DESC";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_execute($stmt);
				$publicacoes = mysqli_stmt_get_result($stmt);
				mysqli_stmt_close($stmt);

                $groupedPublicacoes = array();
                foreach ($publicacoes as $publicacao) {
					//print_r($publicacao);
                    $year = $publicacao['publication_year'];
                    if ($year == null) {
                        $year = "Desconhecido";
                    }

                    $site = $publicacao[$valorSiteName];

                    if (!isset($groupedPublicacoes[$year])) {
                        $groupedPublicacoes[$year] = array();
                    }

                    if (!isset($groupedPublicacoes[$year][$site])) {
                        $groupedPublicacoes[$year][$site] = array();
                    }

                    $groupedPublicacoes[$year][$site][] = array($publicacao['dados'], $publicacao['idPublicacao'], $publicacao['investigador'], $publicacao['investigador_tipo']);
                }
                ?>
                <script src="../assets/js/citation-js-0.6.8.js"></script>
                <script>
                    const Cite = require('citation-js');
                </script>

                <form id="publications" method="post">
                    <?php foreach ($groupedPublicacoes as $year => $yearPublica) : ?>
                        <div class="mb-5">
                            <b><?= $year ?></b><br>
                            <?php foreach ($yearPublica as $site => $publicacoes) : ?>
                                <div style="margin-left: 10px;" class="mt-3"><b><?= $site ?></b><br></div>
                                <div style="margin-left: 20px;" id="publications<?= $year ?><?= $site ?>">

                                    <script>
                                        <?php
                                            $publicacoesJson = [];

                                            foreach ($publicacoes as $publicacao) {
                                                // Add JSON to the entries array
                                                $publicacoesJson[] = toJson($publicacao[0]);
                                            }
                                        ?>

                                        var publicacoes = <?= json_encode($publicacoesJson) ?>;

                                        var publicacoesFiltradas = [...new Map(publicacoes.map(item => {
                                            item = JSON.parse(item);
                                            let url = item['url'] ? item['url'].toLowerCase().replace(/^http:\/\/dx.doi.org/, 'https:\/\/doi.org') : '';
                                            url = url ? url.replace(/^http:/, 'https:') : '';
                                            return [url === '' ? item['title'] : url, item];
                                        })).values()]

                                        <?php foreach ($publicacoes as $publicacao) : ?>

                                            var publicacao = <?= toJson($publicacao[0]) ?>;
                                                
                                            var formattedCitation = new Cite(<?= json_encode($publicacao[0]) ?>).format('bibliography', {
                                                format: 'html',
                                                template: 'apa',
                                                lang: 'en-US'
                                            });

                                            var citationContainer = document.createElement('div');

                                            var id = <?= json_encode($publicacao[1]) ?>;
                                            var check = publicacoesFiltradas.some(e => e.title === publicacao.title) ? false : true;

                                            var checkboxInv = document.createElement('input');
                                            checkboxInv.type = 'checkbox';
                                            checkboxInv.name = `publicacao[${id}]`;
                                            checkboxInv.value = id;
                                            checkboxInv.checked = check;
                                            checkboxInv.classList.add('mr-3');

                                            citationContainer.appendChild(checkboxInv);

                                            var tipo = <?= json_encode($publicacao[3]) ?>.toLowerCase();
                                            
                                            var invLink = document.createElement('a');
                                            invLink.href = '../../tecnart/${tipo}.php?${tipo}=<?= json_encode($publicacao[2]) ?>';
                                            invLink.textContent = 'Investigador';
                                            invLink.classList.add('mr-3');
                                            invLink.style.alignContent = 'center';

                                            citationContainer.appendChild(checkboxInv);
                                            citationContainer.appendChild(invLink);

                                            var citation= document.createElement('div');
                                            citation.innerHTML = formattedCitation;
                                            formattedCitation = citation.firstChild;

                                            citationContainer.appendChild(formattedCitation);
                                            citationContainer.classList.add('mb-3');
                                            citationContainer.style.display = "inline-flex";
                                            document.getElementById('publications<?= $year ?><?= $site ?>').appendChild(citationContainer);

                                            publicacoesFiltradas = publicacoesFiltradas.filter(e => e.title !== publicacao.title);
                                        <?php endforeach; ?>
                                    </script>
                                </div>
                            <?php endforeach; ?>
                        </div><br>
                    <?php endforeach; ?>

                    <div class="form-group">
                        <button type="submit" name="saveChanges" class="btn btn-primary btn-block">Gravar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
