<!--
PHP code to fetch ongoing projects from the database
-->
<?php
include 'config/dbconnection.php';
include 'models/functions.php';

$pdo = pdo_connect_mysql();
$language = ($_SESSION["lang"] == "en") ? "_en" : "";
// Set up pagination variables
$results_per_page = 6; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$start_from = ($page - 1) * $results_per_page; // Start index for the SQL query

$query = "SELECT id, COALESCE(NULLIF(nome{$language}, ''), nome) AS nome, fotografia 
          FROM projetos 
          WHERE concluido = false 
          ORDER BY nome 
          LIMIT :start_from, :results_per_page";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
$stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of records
$total_query = "SELECT COUNT(*) FROM projetos WHERE concluido = false";
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute();
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $results_per_page);
?>

<!DOCTYPE html>
<html>

<!--
HTML header section including template header with translated title
-->
<?= template_header(change_lang("projects-ongoing-page-heading")); ?>

<!-- product section -->
<section class="product_section layout_padding">
    <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
        <div class="container">
            <div class="heading_container3">
                <!-- Translated heading -->
                <h3 style="margin-bottom: 5px;">
               <?= change_lang("projects-ongoing-page-heading") ?>
            </h3>

            <h5 class="heading2_h5">
               <?= change_lang("projects-ongoing-page-description") ?>
            </h5>
            </div>
        </div>
    </div>
</section>
<!-- end product section -->

<section class="product_section layout_padding">
    <div style="padding-top: 20px;">
        <div class="container">
            <div class="layout_padding">
                <div class="col">
                    <!-- Search input field -->
                    <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
            <div class="row justify-content-center mt-3" id="projectsContainer">
                <!-- Loop through ongoing projects -->
                <?php foreach ($projetos as $projeto) : ?>
                    <div class="ml-5 imgList">
                        <a href="projeto.php?projeto=<?= $projeto['id'] ?>">
                            <div class="image_default">
                                <!-- Display project image -->
                                <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/projetos/<?= $projeto['fotografia'] ?>" alt="">
                                <!-- Display project name -->
                                <div class="imgText justify-content-center m-auto"><?= $projeto['nome'] ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<!-- end product section -->

<!-- Pagination Links -->
<nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3">
                    <?php if($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="projetos_em_curso.php?page=<?= $page - 1 ?>">Anterior</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="projetos_em_curso.php?page=<?= $i ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <?php if($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="projetos_em_curso.php?page=<?= $page + 1 ?>">Próximo</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

<?= template_footer(); ?>

</body>

<script>
    // Pass PHP variable to JavaScript
    var projetos = <?= json_encode($projetos); ?>;

    /**
     * Function to generate HTML for filtered projects.
     * @param {Array} filteredProjects - Array containing filtered projects.
     * @returns {string} - HTML for filtered projects.
     */
    function generateFilteredProjectsHTML(filteredProjects) {
        var html = "";
        for (var i = 0; i < filteredProjects.length; i++) {
            html += '<div class="ml-5 imgList">';
            html += '<a href="projeto.php?projeto=' + filteredProjects[i]['id'] + '">';
            html += '<div class="image_default">';
            html += '<img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/projetos/' + filteredProjects[i]['fotografia'] + '" alt="">';
            html += '<div class="imgText justify-content-center m-auto">' + filteredProjects[i]['nome'] + '</div>';
            html += '</div>';
            html += '</a>';
            html += '</div>';
        }
        return html;
    }

    /**
     * Function to filter projects based on search input.
     */
    function filterProjects() {
        // Get the value of the search input
        var searchInputValue = document.getElementById("searchInput").value.toLowerCase();

        // Filter projects based on the search input value
        var filteredProjetos = projetos.filter(function(projeto) {
            return projeto.nome.toLowerCase().includes(searchInputValue);
        });

        // Update the HTML with the filtered projects
        var filteredProjectsHTML = generateFilteredProjectsHTML(filteredProjetos);
        document.getElementById("projectsContainer").innerHTML = filteredProjectsHTML;
    }

    // Add event listener to the search input to trigger filtering of projects
    document.getElementById("searchInput").addEventListener("input", filterProjects);
</script>

</html>
