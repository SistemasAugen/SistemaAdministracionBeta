<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './credentials.php';
// require_once './PseudoCrypt.php';

$db = initConnection();
$sql = "SELECT * FROM laboratories WHERE id NOT IN (9)";

$laboratories = [];

$result = $db->query($sql);

while($row = $result->fetch_object()) {
    $laboratories[] = $row;
}

$showList = (isset($_GET['action']) && $_GET['action'] == 'showList');

if(isset($_GET['lab'])) {
    $hash = $_GET['lab'];
    $laboratory = null;
    foreach ($laboratories as $lab) {
        if(substr(md5($lab->id), 0, 5) == $hash) {
            $laboratory = $lab;
            break;
        }
    }

    if(is_null($laboratory)) exit;
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Laboratorios <?php if (isset($laboratory)): ?>
            <?php echo utf8_encode(sprintf(' - %s', $laboratory->name)) ?>
        <?php endif; ?></title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css" />
        <link rel="stylesheet" href="main.css">
        <?php if (isset($laboratory)): ?>
            <?php if ($laboratory->id == 3): ?>
                <style media="screen">
                body {
                    margin-top: 35px;
                }

                .main-content {
                    margin-left: calc(-125px / 2);
                }


                </style>
            <?php endif; ?>
        <?php endif; ?>
        <style media="screen">
        .main-content table td {
            font-size: 0.6em !important;
        }
        </style>
    </head>
    <body>
        <div id="app">
            <div class="<?php echo isset($laboratory) ? 'container' : 'container'; ?>">
                <div class="container">
                    <img src="LOGO AUGEN LABS-01.png" alt="">
                    <?php if (isset($laboratory)): ?>
                        <h3><?= utf8_encode($laboratory->name);  ?></h3>
                    <?php endif; ?>
                </div>
                <?php if ($showList): ?>
                    <table class="table table-stripped laboratory-table">
                        <thead>
                            <tr>
                                <!-- <th>ID</th> -->
                                <th>Laboratorio</th>
                                <th>Código</th>
                                <th>URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laboratories as $laboratory): ?>
                                <?php  $hash = substr(md5($laboratory->id), 0, 5); ?>
                                <tr>
                                    <!-- <td><?= $laboratory->id ?></td> -->
                                    <td><?= utf8_encode($laboratory->name); ?></td>
                                    <td><?= $hash ?></td>
                                    <td> <a href="http://sistema.augenlabs.com/laboratorio?lab=<?= $hash ?>" target="_blank">
                                        <!-- <i class="fas fa-sign-out-alt"></i> -->
                                        Link
                                    </a> </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="row no-gutters main-content" v-if="data.length > 0">
                        <div class="col-3" v-for="(row, i) in data" :key="i">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>RX</th>
                                        <th>Origen</th>
                                        <th>Días</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in row" :key="order.id" :class="'days-' + order.days_diff + ' ' + (order.status == 'garantia' ? 'warranty' : '')">
                                        <td>{{ order.formatted_date }} </td>
                                        <td>{{ order.rx }}</td>
                                        <td>{{ order.branch.name }}</td>
                                        <td>{{ order.days_diff }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" charset="utf-8"></script>
        <script src="https://unpkg.com/vue-router"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js" charset="utf-8"></script>
        <script src="core.js" charset="utf-8"></script>
    </body>
</html>
