<?php
include 'config/dbconnection.php';
include 'models/functions.php';

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

?>

<?= template_header('Publicações'); ?>
<section class='product_section layout_padding'>
    <div style='padding-top: 50px; padding-bottom: 30px;'>
        <div class='container'>
            <div class='heading_container3'>
                <h3 class="heading_h3" style="text-transform: uppercase;">
                    <?= change_lang("publications-page-heading") ?>
                </h3><br><br>
                <?php
                $pdo = pdo_connect_mysql();
                if (!isset($_SESSION["lang"])) {
                    $lang = "pt";
                } else {
                    $lang = $_SESSION["lang"];
                }
                $valorSiteName = "valor_site_$lang";
                $query = "SELECT dados, YEAR(data) AS publication_year, p.tipo, pt.$valorSiteName FROM publicacoes p
                                LEFT JOIN publicacoes_tipos pt ON p.tipo = pt.valor_API
                                WHERE visivelGeral = true
                                ORDER BY publication_year DESC, pt.$valorSiteName, data DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $groupedPublicacoes = array();
                foreach ($publicacoes as $publicacao) {
                    $year = $publicacao['publication_year'];
                    if ($year == null) {
                        $year = change_lang("year-unknown");
                    }

                    $site = $publicacao[$valorSiteName];

                    if (!isset($groupedPublicacoes[$year])) {
                        $groupedPublicacoes[$year] = array();
                    }

                    if (!isset($groupedPublicacoes[$year][$site])) {
                        $groupedPublicacoes[$year][$site] = array();
                    }

                    $groupedPublicacoes[$year][$site][] = $publicacao['dados'];
                }
                ?>
                <script src="../backoffice/assets/js/citation-js-0.6.8.js"></script>
                <script>
                    const Cite = require('citation-js');
                </script>

                <div id="publications">
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
                                                $publicacoesJson[] = toJson($publicacao);
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

                                            var publicacao = <?= toJson($publicacao) ?>;
                                            
                                            if (publicacoesFiltradas.some(e => e.title === publicacao.title)) {
                                                var formattedCitation = new Cite(<?= json_encode($publicacao) ?>).format('bibliography', {
                                                    format: 'html',
                                                    template: 'apa',
                                                    lang: 'en-US'
                                                });
                                                var citationContainer = document.createElement('div');
                                                citationContainer.innerHTML = formattedCitation;
                                                citationContainer.classList.add('mb-3');
                                                document.getElementById('publications<?= $year ?><?= $site ?>').appendChild(citationContainer);

                                                publicacoesFiltradas = publicacoesFiltradas.filter(e => e.title !== publicacao.title);
                                            }
                                        <?php endforeach; ?>
                                    </script>
                                </div>
                            <?php endforeach; ?>
                        </div><br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= template_footer(); ?>