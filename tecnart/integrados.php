<?php
/**
 * This script retrieves a list of integrated researchers from the database,
 * paginates the results, and displays them on the webpage.
 *
 * @author jmrrg & techart
 */

// Include necessary PHP files
include 'config/dbconnection.php';
include 'models/functions.php';

// Establish database connection
$pdo = pdo_connect_mysql();

// Determine language based on session variable
$language = ($_SESSION["lang"] == "en") ? "_en" : "";

// Number of records to display per page
$records_per_page = 12;

// Get current page number or default to 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate starting point for fetching records
$start_from = ($page - 1) * $records_per_page;

// Count total number of records from the database
$total_records_query = $pdo->query("SELECT COUNT(*) FROM investigadores WHERE tipo = 'Integrado'");
$total_records = $total_records_query->fetchColumn();

// Calculate total number of pages
$total_pages = ceil($total_records / $records_per_page);

// SQL query to fetch records with pagination
$query = "SELECT id, email, nome,
        COALESCE(NULLIF(sobre{$language}, ''), sobre) AS sobre,
        COALESCE(NULLIF(areasdeinteresse{$language}, ''), areasdeinteresse) AS areasdeinteresse,
        ciencia_id, tipo, fotografia, orcid, scholar, research_gate, scopus_id
        FROM investigadores WHERE tipo = 'Integrado' ORDER BY nome
        LIMIT :limit OFFSET :offset";

// Prepare and execute the SQL query
$stmt = $pdo->prepare($query);
$stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindParam(':offset', $start_from, PDO::PARAM_INT);
$stmt->execute();
$investigadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Make sure $page is set and within the valid range
$page = max(1, min($total_pages, intval($page)));
?>


<!DOCTYPE html>
<html>
<head>
    <title>Integrated Researchers</title>
    <!-- Include the header template -->
    <?=template_header('Integrados');?>
</head>
<body>

<!-- Product section -->
<section class="product_section layout_padding">
    <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
        <div class="container">
            <div class="heading_container3">
                <?php /* Heading for the integrated researchers page */ ?>
                <h3 style="margin-bottom: 5px;">
                    <?= change_lang("integrated-researchers-page-heading") ?>
                </h3>
                <?php /* Description for the integrated researchers page */ ?>
                <h5 class="heading2_h5">
                    <?= change_lang("integrated-researchers-page-heading-desc") ?>
                </h5>
            </div>
        </div>
    </div>
</section>
<!-- End product section -->

<section class="product_section layout_padding">
    <div style="padding-top: 20px;">
        <div class="container">
            <div class="row justify-content-center mt-3">
                <?php foreach ($investigadores as $investigador): ?>
                    <div class="ml-4 imgList">
                        <?php /* Link to individual researcher page */ ?>
                        <a href="integrado.php?integrado=<?=$investigador['id']?>">
                            <div class="image_default">
                                <?php /* Researcher's image */ ?>
                                <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/investigadores/<?=$investigador['fotografia']?>" alt="">
                                <?php /* Researcher's name */ ?>
                                <div class="imgText justify-content-center m-auto"><?=$investigador['nome']?></div>
                            </div>
                        </a> 
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination section -->
            <div class="pagination_container">
                <ul class="pagination justify-content-center">
                    <?php /* Previous page link */ ?>
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1"><?= change_lan("previous") ?></a>
                    </li>
                    <?php /* Page links */ ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php /* Next page link */ ?>
                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>"><?= change_lan("next") ?></a>
                    </li>
                </ul>
            </div>
            <!-- End pagination section -->


      
                      
<!--             <div class="row justify-content-center mt-3">
               
               <div  class="ml-4 imgList">
               
                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/joana-bento-rodrigues.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">teresa silva</div>
                  </div>  
               
               </div>

               <div class="ml-4 imgList">

                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/maisum.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">josé constâncio</div>
                  </div>

               </div>

               <div class="ml-4 imgList">
               
                  <div class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/pexels-photo-2272853.jpeg" alt="">
                     <div class="imgText justify-content-center m-auto">josefa vasconcelos</div>
                  </div>


               </div>
   
            </div>


            <div class="row justify-content-center mt-3">
               
               <div  class="ml-4 imgList">
               
                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/whatsapp-image-2021.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">ana maria simões</div>
                  </div>  
               
               </div>

               <div class="ml-4 imgList">

                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/55918.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">maria bettencourt</div>
                  </div>

               </div>

               <div class="ml-4 imgList">
               
                  <div class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/5591801.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">cristina marques</div>
                  </div>


               </div>
            
            </div> -->

                
         </div>

      </div>
   </section>

      <!-- end product section -->

      <?=template_footer();?>

   </body>
</html>