<?php
include 'models/functions.php';
require "./config/dbconnection.php";

//Variavel que vai determinar quando é necessário escrever uma nova seccão
$seccao = 'EMPTY';

//Fazer pesquisa à base de dados para obter os dados dinamicamente.
$pdo = pdo_connect_mysql();
$sql = "SELECT * FROM estrutura_organica WHERE lang = 'pt' ORDER BY textOrder";
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<?= template_header('Estrutura'); ?>


<!-- product section -->
<section class="product_section layout_padding">
    <div style="padding-top: 50px; padding-bottom: 30px;">
        <div class="container">
            <div class="heading_container3">

                <h3 style="font-size: 33px; text-transform: uppercase;">
                    <?= change_lang("org-struct-page-heading") ?>
                </h3>

                <div class="flex-container mobile_reverse">
                    <div class="flex-left">
                        <figure class="imgfigura">
                            <img class="imgestrutura w-100" style="max-width:330px;" src="./assets/images/estrutura.jpg" alt="Boat">
                            <figcaption class="imgs"></figcaption>
                        </figure>
                    </div>
                    <div class="flex-right">
                        <?= change_lang("org-struct-page-description") ?><br><br>

                        <!-- Vamos precorrer pelos resultados obtidos da BD -->
                        <?php foreach ($result as $row) {
                            
                            //Verificamos se é uma nova seccão: escreve uma nova seccão se for uma nova. (a primeira será sempre diferente.)
                            if($row['division'] != $seccao){
                                //Validação feita para não existir dois espaços na primeira seccão
                                if($seccao!='EMPTY'){ 
                                    echo '<br><br>';
                                }
                                //Escreve a seccão
                                $seccao = $row['division']; 
                                echo '<b>'. $seccao. '</b><br>';
                            }
                            
                            //Mostra os diferentes nomes, dependendo da informação disponível
                            if ($row['link'] != null && $row['department'] != null) {
                                echo '<a href="' . $row['link'] . '">' . $row['name'] . '</a>, ' . $row['department'] . '<br>';
                            } elseif ($row['link'] == null && $row['department'] != null) {
                                echo $row['name'] . ', ' . $row['department'] . '<br>';
                            } else {
                                echo $row['name'] . '<br>';
                            }

                        } ?>

                    </div>
                </div>

                <!--                 <div class="flex-container">
                    
                    <div class="flex-left2">
                        "tempor pulvinar. Vivamus ultrices egestas posuere. Integer magna orci, vestibulum."
                    </div>

                    <div class="flex-right">
                        luctus. Maecenas et quam rutrum, dignissim orci sodales, eleifend elit. Praesent viverra odio at volutpat aliquam. Aliquam vitae libero molestie,
                        laoreet tellus non, pretium orci. Maximus lacus sed, aliquet ex. Integer faucibus ante et magna finibus, ac consequat sapien commodo.
                        Aenean a condimentum diam. Sed facilisis felis neque, a volutpat libero euismod ut. Donec pellentesque metus at enim tempor fringilla.
                        Morbi dictum mauris sem, in commodo magna elementum eu. Uis ornare, ex ac rhoncus dictum, magna mauris tincidunt dolor
                        ed odio lacus, bibendum sed leo fringilla, vehicula finibus sem. Fusce urna sem, vehicula a accumsan sed, molestie id mi.
                        Donec tempus odio nec nisi euismod, ac volutpat purus laoreet. Luctus eu liber a fermentum. Sed pretium turpis enim.
                    </div>

                </div> 
            
            
            
            
            
            
            
            
            
            
            
            
            -->


            </div>
        </div>
    </div>
</section>
<!-- end product section -->

<?= template_footer(); ?>

</body>

</html>