<?php
include 'config/dbconnection.php';
include 'models/functions.php';
$pdo = pdo_connect_mysql();
$language = ($_SESSION["lang"] == "en") ? "_en" : "";
?>

<!DOCTYPE html>
<html>

<?= template_header('TECHN&ART'); ?>
<!-- slider section -->
<section class="home-slider owl-carousel">
   <div class="slider-item" style="background-image:url('./assets/images/slider-index-1.jpg');">
      <div class="overlay"></div>
      <div class="row no-gutters slider-text justify-content-start" style="position: relative; height: 100%; max-width:100%;" data-scrollax-parent="true">
         <div class="align-text-slider">
            <div class="col-md-7 mobile_adjust ftco-animate mb-md-5">
               <h1 class="mb-4">
                  <?= change_lang("index-first-slide"); ?>
               </h1>
               <span class="subheading">
                  <?= change_lang("index-first-slide-desc"); ?>
               </span>
               <div><a href="sobre.php" class="btn btn-primary px-4 py-3 mt-3 btn_no_left"  style="border-radius: 0;">
                     <?= change_lang("know-more-btn-txt-slider") ?>
                  </a></div>
            </div>
         </div>
      </div>
   </div>
   </div>

   <div class="slider-item" style="background-image:url('./assets/images/slider-index-2.jpg');">
      <div class="overlay"></div>

      <div class="row no-gutters slider-text justify-content-start" style="position: relative; height: 100%; max-width:100%;" data-scrollax-parent="true">
         <div class="align-text-slider">
            <div class="col-md-7 mobile_adjust ftco-animate mb-md-5">
               <h1 class="mb-4">
                  <?= change_lang("index-second-slide"); ?>
               </h1>
               <span class="subheading">
                  <?= change_lang("index-second-slide-desc"); ?>
               </span>
               <div><a href="integrados.php" class="btn btn-primary px-4 py-3 mt-3 btn_no_left"   style="border-radius: 0;">
                     <?= change_lang("know-more-btn-txt-slider") ?>
                  </a></div>
            </div>
         </div>
      </div>
   </div>
   </div>

   <div class="slider-item" style="background-image:url('./assets/images/slider-index-3.jpg');">
      <div class="overlay"></div>
      <div class="row no-gutters slider-text justify-content-start" style="position: relative; height: 100%; max-width:100%;" data-scrollax-parent="true">
         <div class="align-text-slider">
            <div class="col-md-7 mobile_adjust ftco-animate mb-md-5">
               <h1 class="mb-4">
                  <?= change_lang("index-third-slide-slider"); ?>
               </h1>
               <span class="subheading">
                  <?= change_lang("index-third-slide-slider-desc"); ?>
               </span>
               <div><a href="projetos_em_curso.php" class="btn btn-primary px-4 py-3 mt-3 btn_no_left"   style="border-radius: 0;">
                     <?= change_lang("know-more-btn-txt-slider") ?>
                  </a></div>
            </div>
         </div>
      </div>
   </div>
   </div>

</section>
<!-- end slider section -->

<script src="assets/js/jquery.waypoints.min.js"></script>
<script src="assets/js/jquery.stellar.min.js"></script>
<script src="assets/js/main.js"></script>

<!-- why section -->
<section class="why_section layout_padding">
   <div class="container">
      <div class="heading_container heading_center">
         <h3>
            <?= change_lang("institutional-video-heading"); ?>
         </h3>
      </div>
      <div class="pt-3">
         <div class="embed-responsive embed-responsive-16by9 mx-auto" style="max-width: 800px;">
            <iframe src="https://www.youtube.com/embed/pzXQaQe3pBw"> </iframe>
         </div>
      </div>
   </div>
   </div>
   </div>
</section>
<!-- end why section -->

<!-- product section -->
<section class="product_section layout_padding">
   <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
      <div class="container">
         <div class="heading_container2 heading_center2">

            <h3>
               <?= change_lang("rd-projects-heading"); ?> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
               &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
               &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
            </h3>

            <a style="display: inline-block; padding: 5px 25px; background-color:#002169; color: #ffffff; 
                     -webkit-transition: all 0.3s; transition: all 0.3s;  font-family: 'Quicksand', sans-serif;  font-size: 20px;" href="projetos_em_curso.php">
               <?= change_lang("see-all-btn-rd-projects"); ?>
            </a>

         </div>
         <div class="row">
            <?php
            $sql = "SELECT id,
                     COALESCE(NULLIF(nome{$language}, ''), nome) AS nome,
                     COALESCE(NULLIF(descricao{$language}, ''), descricao) AS descricao,
                     fotografia FROM projetos WHERE concluido = 0 ORDER BY id DESC LIMIT 4";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($projetos as $row) {
            ?>
               <div class="col">
                  <div style="padding-top: 40px">
                     <div class="img-box">
                        <a href="projeto.php?projeto=<?= $row["id"]; ?>">
                           <img style="object-fit: cover; width:230px; height:230px;" src="../backoffice/assets/projetos/<?= $row["fotografia"]; ?>" alt="">
                        </a>
                     </div>
                  </div>
                  <div class="detail-box">
                     <div style="color: #333F50; padding-left: 15px; padding-top: 15px; text-align: center; width:210px;">
                        <a href="projeto.php?projeto=<?= $row["id"]; ?>" style="color:#333F50;">
                           <h5>
                              <?= $row["nome"]; ?>
                           </h5>
                        </a>
                     </div>
                     <div style="padding-left: 30px; text-align: center; width:210px;">
                        <h6>
                           <?=
                           strlen($row["descricao"]) > 145 ?
                              preg_split("/\s+(?=\S*+$)/", substr($row["descricao"], 0, 150))[0] . "..."
                              : $row["descricao"];
                           ?>
                        </h6>
                     </div>
                  </div>
               </div>
            <?php
            }

            ?>




         </div>
      </div>
   </div>
</section>
<!-- end product section -->

<!-- client section -->

<section class="section-margin calc-60px">
   <div style="padding-bottom: 50px;">
      <div class="container">
         <div class="section-intro pb-60px">
            <h2 style="font-family: 'Quicksand', sans-serif; padding-bottom: 20px; padding-left: 50px;">
               <?= change_lang("latest-news-heading") ?>
            </h2>
         </div>

         <div class="owl-carousel owl-theme" id="bestSellerCarousel">
            <?php
            $pdo = pdo_connect_mysql();
            //Selecionar no máximo 6 notícias, ordenadas pela data mais recente, e que tenham data anterior ou igual à atual
            $query = "SELECT id,
            COALESCE(NULLIF(titulo{$language}, ''), titulo) AS titulo,
            COALESCE(NULLIF(conteudo{$language}, ''), conteudo) AS conteudo,
            imagem,data
            FROM noticias WHERE data<=NOW() ORDER BY DATA DESC LIMIT 6";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <?php foreach ($noticias as $noticia) : ?>
               <div class="card-product">
                  <div class="absoluto">
                     <a href="noticia.php?noticia=<?= $noticia['id'] ?>" style="display: inline-block; vertical-align: top;">
                        <div style="z-index: 1;" class="image_default news-image">
                           <img class="img-fluid news-image" src="../backoffice/assets/noticias/<?= $noticia['imagem'] ?>" alt="">
                           <div class="text-block">
                              <h5 style="font-size: 20px; text-transform: uppercase; font-weight: 600;">
                                 <?php
                                 //Limitar o título a 35 caracteres e cortar pelo último espaço
                                 $titulo = trim($noticia['titulo']);
                                 if (strlen($noticia['titulo']) > 35) {
                                    $titulo = preg_split("/\s+(?=\S*+$)/", substr($noticia['titulo'], 0, 40))[0];
                                 }
                                 echo ($titulo !=  trim($noticia['titulo'])) ? $titulo . "..." : $titulo;
                                 ?>
                              </h5>
                              <h6 style="font-size: 14px; font-weight: 100;">
                                 <?php
                                 //Adicionar espaços antes das etiquetas html,
                                 $espacos = str_replace('<', ' <', $noticia['conteudo']);
                                 // Remover as etiquetas de HTML e realizar o trim para remover espaços extras, incluindo &nbsp;
                                 $textNoticiaOrig = trim(str_replace('&nbsp;', '', strip_tags($espacos)));
                                 // Verificar se o texto tem mais de 100 caracteres
                                 if (strlen($textNoticiaOrig) > 100) {
                                    $textNoticia = preg_split("/\s+(?=\S*+$)/", substr($textNoticiaOrig, 0, 105))[0];
                                 } else {
                                    $textNoticia = $textNoticiaOrig;
                                 }
                                 //Se o texto da notícia foi cortado, imprimir com reticencias
                                 echo ($textNoticia != $textNoticiaOrig) ? $textNoticia . "..." : $textNoticia;
                                 ?>
                              </h6>
                           </div>
                        </div>
                     </a>

                  </div>
               </div>
            <?php endforeach; ?>
         </div>

         <div class="text-center">
            <a style="display: inline-block; padding: 5px 25px; background-color:#002169; color: #ffffff; 
                     -webkit-transition: all 0.3s; transition: all 0.3s;  font-family: 'Quicksand', sans-serif;  font-size: 20px;" href="noticias.php">
               <?= change_lang("see-all-btn-latest-news") ?>
            </a>
         </div>

      </div>
   </div>
</section>

<script src="assets/js/main2.js"></script>

<!-- end client section -->

<?= template_footer(); ?>

</body>

</html>