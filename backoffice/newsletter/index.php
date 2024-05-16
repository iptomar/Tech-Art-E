<?php
require "../verifica.php";
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Projetos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        hr {
            margin-top: 30px;
            margin-bottom: 30px;
            border: 0;
            border-top: 1px solid #dee2e6;
        }
        .project-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .project {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .project-image {
            width: 120px;
            height: 120px;
            object-fit: fill;
            margin-right: 10px;
        }

        .project-info {
            flex: 1;
            max-width: calc(100% - 130px);
        }

        .description {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .checkbox,
        .checkboxReceivers,
        .checkboxNews {
            margin-left: auto;
            margin-right: 10px;
        }

        .btn-center {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <br>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Pesquisar por nome ou descrição" id="searchInput">
        </div>

        <h3>Projetos</h3>
        <div class="project-list" id="projectList">
            <!-- Script que carrega a lista de projetos -->
            <?php
            require '../config/basedados.php';

            $conn = mysqli_connect($servername, $username, $password, $dbname);

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            mysqli_set_charset($conn, $charset);

            $sql = "SELECT nome, fotografia, descricao FROM projetos ORDER BY nome";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="project">';
                    echo '<img src="../assets/projetos/' . $row["fotografia"] . '" class="project-image" alt="' . $row["nome"] . '" id="' . $row["fotografia"] . '">';
                    echo '<div class="project-info">';
                    echo '<h5>' . $row["nome"] . '</h3>';
                    echo '<p>' . $row["descricao"] . '</p>';
                    echo '<input type="checkbox" class="checkbox">';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "0 resultados";
            }

            ?>
        </div>

        <hr>
        <h3>Noticias</h3>
        <div class="project-list" id="newsList">
            <?php
            $sql = "SELECT titulo, conteudo, imagem FROM noticias ORDER BY DATA DESC, titulo";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="project">';
                    echo '<img src="../assets/noticias/' . $row["imagem"] . '" class="project-image" alt="' . $row["imagem"] . '">';
                    echo '<div class="project-info">';
                    echo '<h5>' . $row["titulo"] . '</h3>';
                    echo '<p class="conteudo">' . $row["conteudo"] . '</p>';
                    echo '<input type="checkbox" class="checkboxNews">';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "0 resultados";
            }

            ?>
        </div>

        <hr>
        <h3>Destinatários</h3>
        <div class="project-list" id="receiversList">
            <?php
            $sql = "SELECT nome, fotografia, email FROM investigadores ORDER BY nome";
            $result = $conn->query($sql);

            echo '<div class="project">';
            echo '<img src="../assets/investigadores/newlletter.avif" class="project-image" alt="Newsletter">';
            echo '<div class="project-info">';
            echo '<h5>Newsletter</h3>';
            echo '<p>Todos os emails subscritos à newsletter</p>';
            echo '<input type="checkbox" class="checkboxReceivers">';
            echo '</div>';
            echo '</div>';

            echo '<div class="project">';
            echo '<img src="../assets/investigadores/newlletter.avif" class="project-image" alt="Newsletter">';
            echo '<div class="project-info">';
            echo '<h3>Newsletter</h3>';
            echo '<p>Todos os investigadores</p>';
            echo '<input type="checkbox" class="checkboxReceivers">';
            echo '</div>';
            echo '</div>';

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="project">';
                    echo '<img src="../assets/investigadores/' . $row["fotografia"] . '" class="project-image" alt="' . $row["nome"] . '">';
                    echo '<div class="project-info">';
                    echo '<h3>' . $row["nome"] . '</h3>';
                    echo '<p class="email">' . $row["email"] . '</p>';
                    echo '<input type="checkbox" class="checkboxReceivers">';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "0 resultados";
            }

            $conn->close();
            ?>
        </div>
        <hr>
        <div class="project-list" id="projectList">
            <button type="button" class="btn btn-primary" onclick="concluir()">Selecionar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="limparSelecoes()">Fechar</button>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            var searchText = this.value.toLowerCase();
            var projects = document.querySelectorAll('.project');
            projects.forEach(function (project) {
                var projectName = project.querySelector('h3').textContent.toLowerCase();
                var projectDescription = project.querySelector('p').textContent.toLowerCase();
                if (projectName.includes(searchText) || projectDescription.includes(searchText)) {
                    project.style.display = 'flex';
                } else {
                    project.style.display = 'none';
                }
            });
        });

        function concluir() {
            var checkboxes = document.querySelectorAll('.checkbox');
            var checkboxesReceivers = document.querySelectorAll('.checkboxReceivers');
            var checkboxesNews = document.querySelectorAll('.checkboxNews');
            var checkedProjects = [];
            var checkedReceivers = [];
            var checkedNews = [];
            var verificarCheckbox = false;
            var verificarCheckboxR = false;
            var verificarCheckboxN = false;
            var sendToAll = false;
            var sendToAllR = false;

            checkboxes.forEach(function (checkbox) {
                if (checkbox.checked) {
                    verificarCheckbox = true;
                    var projeto = {
                        titulo: checkbox.parentElement.querySelector('h3').textContent,
                        descricao: checkbox.parentElement.querySelector('p').textContent,
                        imgid: checkbox.parentElement.parentElement.querySelector('img').id
                    };
                    checkedProjects.push(projeto);
                }
            });

            checkboxesNews.forEach(function (checkbox) {
                if (checkbox.checked) {
                    verificarCheckboxN = true;
                    var new123 = {
                        titulo: checkbox.parentElement.querySelector('h3').textContent,
                        descricao: checkbox.parentElement.querySelector('p').textContent,
                        imgid: checkbox.parentElement.parentElement.querySelector('img').id
                    };
                    checkedNews.push(new123);
                }
            });

            checkboxesReceivers.forEach(function (checkbox) {
                if (checkbox.checked) {
                    verificarCheckboxR = true;
                    email = checkbox.parentElement.querySelector('p').textContent;
                    if (email.includes("Todos os e")) {
                        sendToAll = true;
                    }

                    if (email.includes("Todos os i")) {
                        sendToAllR = true;
                    }

                    var receiver = {
                        email: email
                    };

                    checkedReceivers.push(receiver);
                }
            });

            if (!verificarCheckbox || !verificarCheckboxR) {
                alert('Selecione pelo menos um projeto e um destinatário!');
                return;
            } else {
                console.log(checkedProjects);
                console.log(checkedReceivers);
                console.log(checkedNews);
                fetch('newsletterSender.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json; charset=utf-8',
                    },
                    body: JSON.stringify({ checkedProjects: checkedProjects, checkedReceivers: checkedReceivers, sendToAll: sendToAll, checkedNews: checkedNews, sendToAllR: sendToAllR }),
                })
                    .then(response => {
                        if (response.ok) {
                            return response.text();
                        } else {
                            console.error('Erro ao enviar e-mail:', response.statusText);
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao enviar e-mail:', error);
                    });

                var input = document.getElementById('searchInput');
                input.value = '';
                mostrarTodosProjetos();
            }

        }

        function limparSelecoes() {
            var checkboxes = document.querySelectorAll('.checkbox');
            var input = document.getElementById('searchInput');
            input.value = '';
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            mostrarTodosProjetos();
        }

        function mostrarTodosProjetos() {
            var projects = document.querySelectorAll('.project');
            projects.forEach(function (project) {
                project.style.display = 'flex';
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>