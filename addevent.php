<?php
session_start();
if (empty($_SESSION["signed-in"])) {
    header("Refresh:0; url=403.php");
    die("Lammer ATTACK!");
}

error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to admin dashboard.</title>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>

    <link href="css/style.css" rel="stylesheet">
    <link href="css/color/default.css" rel="stylesheet" id="color_theme">

</head>

<body>
    <?php
        if (isset($_POST["eklebuton"])) {   //ekle butonuna basıldıysa

            $etkinlik_basligi = htmlspecialchars(trim($_POST["etkinlik_basligi"]));
            $etkinlik_aciklamasi = htmlspecialchars(trim($_POST["etkinlik_aciklamasi"]));
            $etkinlik_icerigi = htmlspecialchars(trim($_POST["etkinlik_icerigi"]));
            $etkinlik_resmi = addslashes(file_get_contents($_FILES['etkinlik_resmi']['tmp_name']));
            
            if (!empty($etkinlik_basligi) && !empty($etkinlik_aciklamasi) && !empty($etkinlik_icerigi) && !empty($etkinlik_resmi)) {
                require("configs.php");
                require($dbFileUrl);

                $db = new Database();

                $process = $db->insert("events", array(
                    "header" => $etkinlik_basligi,
                    "description" => $etkinlik_aciklamasi,
                    "content" => $etkinlik_icerigi,
                    "image" => $etkinlik_resmi
                ));

                if($process){
                    $_DURUM['mesaj']="İşlem başarıyla gerçekleşti.";
                    echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('basari').animate({opacity:'1',height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
                }else{
                    $_DURUM['mesaj']="Bilinmeyen bir hata oluştu.";
                    echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('hata').animate({opacity:'1',height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
                }

            }else{
                $_DURUM['mesaj']="Bilgilerde eksik olamaz.";
                echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('hata').animate({opacity:'1',height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
            }
        }
    ?>
    <section id="iletisim" class="section contact-us">
        <div class="container">
            <div class="section-title">
                <h2>Etkinlik<span> Girişi</span></h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-4 col-xs-4 col-md-4 col-md-offset-4">
                    <div class="row justify-content-center mb-10">
                        <div class="col">
                            <div class="uyari" id="uyari-kutusu" style="opacity: 0; height: 0px;"> <?= $_DURUM['mesaj']; ?> </div>
                        </div>
                    </div>
                    <div class="contact-form">
                        <form id="admin_giris" method="post" action="" enctype="multipart/form-data">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="sr-only">Başlık</label>
                                        <input class="form-control" name="etkinlik_basligi" placeholder="Başlık" type="text">
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only">Açıklama</label>
                                        <input class="form-control" name="etkinlik_aciklamasi" placeholder="Açıklama" type="text">
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only">İçerik</label>
                                        <textarea class="form-control" name="etkinlik_icerigi" placeholder="İçerik"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only">Resim</label>
                                        <input class="form-control" name="etkinlik_resmi" placeholder="Resim" type="file" accept="image/png">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group action">
                                            <input type="submit" name="eklebuton" class="m-btn" value="Ekle">
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>

</html>