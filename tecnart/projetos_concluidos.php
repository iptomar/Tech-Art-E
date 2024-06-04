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
          WHERE concluido = true 
          ORDER BY nome 
          LIMIT :start_from, :results_per_page";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
$stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of records
$total_query = "SELECT COUNT(*) FROM projetos WHERE concluido = true";
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute();
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $results_per_page);
?>

<!DOCTYPE html>
<html>

<?= template_header(change_lang("projects-finished-page-heading")); ?>

<!-- product section -->
<section class="product_section layout_padding">
    <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
        <div class="container">
            <div class="heading_container3">

                <h3 style="margin-bottom: 5px;">
                    <?= change_lang("projects-finished-page-heading") ?>
                </h3>

                <h5 class="heading2_h5">
                    <?= change_lang("projects-finished-page-description") ?>
                </h5>

            </div>
        </div>
    </div>
</section>
<!-- end product section -->

<section class="product_section layout_padding">
    <div style="padding-top: 20px;">
        <div class="container">
        <div class="row mt-3">
    <div class="col">
        <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar...">
    </div>
</div>
            <div class="row justify-content-center mt-3">

            

                <?php foreach ($projetos as $projeto) : ?>

                    <div class="ml-5 imgList">
                        <a href="projeto.php?projeto=<?= $projeto['id'] ?>">
                            <div class="image_default">
                                <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/projetos/<?= $projeto['fotografia'] ?>" alt="">
                                <div class="imgText justify-content-center m-auto"><?= $projeto['nome'] ?></div>
                            </div>
                        </a>
                    </div>

                <?php endforeach; ?>

            </div>

            <!-- Pagination Links -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-3">
                    <?php if($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="projetos_concluidos.php?page=<?= $page - 1 ?>">Anterior</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"><a class="page-link" href="projetos_concluidos.php?page=<?= $i ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <?php if($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="projetos_concluidos.php?page=<?= $page + 1 ?>">Próximo</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>

    </div>
</section>

<!-- end product section -->

<?= template_footer(); ?>

</body>

</html>