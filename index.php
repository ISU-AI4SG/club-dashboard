<?php
session_start();
ob_start();
require('configs.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoşgeldiniz!</title>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" referrerpolicy="no-referrer"></script>

    <link href="css/style.css" rel="stylesheet">
    <link href="css/color/default.css" rel="stylesheet" id="color_theme">

    <style type="text/css">
      
    </style>
    <script>
        history.pushState(null, "null", "");
    </script>
</head>

<body class="grey-bg">
    <?php
    //error_reporting(E_ALL);
    require($dbFileUrl);
    $db = new Database();
    $_DURUM["mesaj"] = "";
    if (isset($_POST["girisbuton"])) {
        if (!empty($_POST['kuladi']) and !empty($_POST['sifre'])) {
            $kuladi = htmlspecialchars(trim(strtolower($_POST["kuladi"])));
            $sifre = htmlspecialchars(trim($_POST["sifre"]));
            $result = $db->row('*', 'user', array('nickname' => $kuladi));
            if ($result) {
                $kriptolusifre = hash('sha512', $sifre, FALSE);
                if ($kuladi === strtolower($result['nickname']) and $kriptolusifre === $result['password']) {
                    $_SESSION["kul-adi"] = $result['nickname'];
                    $_SESSION["signed-in"] = true;
                    $_POST = array();
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    $_DURUM['mesaj'] = "Giriş başarılı.";
                    echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('basari').animate({opacity:'1', height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
                } else {
                    $_DURUM['mesaj'] = "Şifre yanlış. Tekrar deneyiniz.";
                    echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('hata').animate({opacity:'1',height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
                }
            } else {

                $_DURUM['mesaj'] = "Kullanıcı bulunamadı. <span style='color:wheat;'>Adminle iletişime geç.</span>";
                echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('hata').animate({opacity:'1',height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
            }
        } else {
            $_DURUM['mesaj'] = "Herhangi bir hesap bilgisi girilmedi.";
            echo "<script type='text/javascript'>$(function(){ $('#uyari-kutusu').addClass('hata').animate({opacity:'1',height:'100%', 'padding':'10px 0px 10px 0px'}, 1000)});</script>";
        }
    }
    ?>
    <?php
    if (isset($_SESSION["signed-in"])) :
    ?>
    <div class="komple" style="display: flex;">
        <div class="solmenu">
            <div class="logo-conteynir">
                <div class="logo"></div>
                <div class="logo-baslik">Yönetim Paneli</div>
            </div>
            <div class="sayfalar">
                <div class="sayfa acik" onclick="sayfadegis('homepage.php')"><i class="fas fa-home"></i>&nbsp;&nbsp;&nbsp; Homepage</div>
                <div class="sayfa" onclick="sayfadegis('example.php')"><i class="fas fa-key"></i>&nbsp;&nbsp;&nbsp; Example Page</div>
                <div class="kapa"><i onclick="kapa()" id="kapa" class="fas fa-caret-square-left"></i></div>
                <i class="fas fa-power-off cikis"></i>
            </div>
        </div>
        <div class="sag">
            <iframe id="iframe" src="homepage.php" frameborder="0" style="overflow:scroll;" height="100%" width="100%"></iframe>
        </div>
    </div>
    <script>
        history.pushState(null, "null", "");
        var acik = true;

        function sayfadegis(sayfa) {
            $("#iframe").attr('src', sayfa);
            sessionStorage.setItem('lastOpenPage', sayfa);

        }

        if (sessionStorage.getItem("lastOpenPage")) {
            $("#iframe").attr('src', sessionStorage.getItem("lastOpenPage"));
        }

        $(".sayfa").click(function(event) {
            $(".sayfa").each(function(index, el) {
                $(this).removeClass('acik');
            });
            $(this).addClass('acik')
        });
        $(".cikis").click(function(event) {
            if (confirm("Gerçekten çıkış yapmak istiyor musunuz?")) {
                $.post('logout.php', {}, function(data, textStatus, xhr) {
                    location.reload();
                });
            }
        });

        function kapa() {
            acik = !acik;
            if (acik) {
                $(".solmenu").removeClass('kapali').animate({
                    width: "15%"
                }, 1000, function() {
                    $(".logo-conteynir").html("").append('<div class="logo"></div><div class="logo-baslik">Yönetim Paneli</div>');
                    $(".logo-conteynir").css({
                        "padding-left": '15px',
                        "padding-right": '15px'
                    });
                });
                $(".sag").animate({
                    width: "85%"
                }, 1000)
                $("#kapa").removeClass('fa-caret-square-right').addClass('fa-caret-square-left');
            } else {
                $(".solmenu").addClass('kapali').animate({
                    width: "65px"
                }, 1000);
                $(".sag").animate({
                    width: "96%"
                }, 1000)
                $(".logo-conteynir").html("").append('<div class="logo"></div>');
                $(".logo-conteynir").css({
                    "padding-left": '0px',
                    "padding-right": '0px'
                });
                $("#kapa").removeClass('fa-caret-square-left').addClass('fa-caret-square-right');
            }
        }
    </script>
<?php
    else :
?>
    <section id="loginsection" class="section login-section">
        <div class="container">
            <div class="section-title">
                <h2>Yetkili<span> Girişi</span></h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-4 col-xs-4 col-md-4 col-md-offset-4">
                    <div class="row justify-content-center mb-10">
                        <div class="col">
                            <div class="uyari" id="uyari-kutusu" style="opacity: 0; height: 0px;"> <?= $_DURUM['mesaj']; ?> </div>
                        </div>
                    </div>
                    <div class="login-form">
                        <form id="admin_giris" method="post" action="">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="sr-only">Kullanıcı adı</label>
                                        <input class="form-control" name="kuladi" placeholder="Kullanıcı adı" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="sr-only">Şifre</label>
                                        <input class="form-control" name="sifre" placeholder="Şifre" type="password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group action">
                                        <input type="submit" name="girisbuton" class="m-btn" value="GİRİŞ YAP">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
    endif; ?>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>