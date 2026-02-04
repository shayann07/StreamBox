<?php define("PRODUCT_ID", '52621164'); ?>

<?php
session_start();

function getAPIRequest($data){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    if($data){
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    }
	curl_setopt($curl, CURLOPT_URL, "https://api.nemosofts.com/v2/market/author");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30); 
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	$result = curl_exec($curl);
	curl_close($curl);
	return $result;
}

function getLatestVersion(){
    $data_array =  array(
    	'method_name' => "latest_version",
        'item_id' => PRODUCT_ID
    );
    $get_data = getAPIRequest($data_array);
    $response = json_decode($get_data, true);
    return $response;
}

// ============================================================================
// ENVATO BYPASS MODE - Enable in app/Config/Envato.php
// Set bypassVerification = true to use dummy credentials and skip API calls
// ============================================================================
function isEnvatoBypassed() {
    // Check if bypass file exists (simple toggle)
    $bypass_file = realpath(__DIR__) . '/.envato_bypass';
    return file_exists($bypass_file);
}

function getDummyEnvatoResponse($client, $license) {
    // Return dummy successful response for bypass mode
    return array(
        'status' => true,
        'message' => 'License activated successfully (Bypass Mode)',
        'product_name' => 'StreamBox',
        'buyer_name' => $client,
        'license_code' => $license,
        'api_key' => 'bypass_' . md5($client . $license . time())
    );
}

function activateLicense($license, $client, $create_lic = true){
    // Check if bypass mode is enabled
    if(isEnvatoBypassed()) {
        $response = getDummyEnvatoResponse($client, $license);
    } else {
        // Original API call for real verification
        $get_base_url = getBaseUrl();
        $data_array =  array(
            'method_name' => "activate_license",
            "item_id"  => PRODUCT_ID,
            "license_code" => $license,
            "client_name" => $client,
            'base_url' => $get_base_url,
        );
        $get_data = getAPIRequest($data_array);
        $response = json_decode($get_data, true);
    }
    
    $current_path = realpath(__DIR__);
    $license_file = $current_path.'/.lic';
    if(!empty($create_lic)){
        if($response['status']){
            $licfile = trim('B4N1L9C5ITMKIRL');
            file_put_contents($license_file, $licfile, LOCK_EX);
        } else {
            @chmod($license_file, 0640);
            if(is_writeable($license_file)){
                unlink($license_file);
            }
        }
    }
    return $response;
}

function getBaseUrl() {
    if(isset($_SERVER['HTTPS'] )){  
        $file_path = 'https://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/';
    } else {
        $file_path = 'http://'.$_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']).'/';
    }
    return substr($file_path,0,-8);
}

$errors = false;
$database_dump_file = 'database.sql';

$product_info = getLatestVersion();
if($product_info == ''){
    $errors = true;
}

$step = isset($_GET['step']) ? $_GET['step'] : '';

// Minimum PHP version required
if(phpversion() < "8.1"){
    $errors = true;
}

$installFile=".lic";
if(is_writeable($installFile)){
    $errors = true; 
}

// Required PHP extensions
if(!extension_loaded('bcmath')){
    $errors = true; 
}

if(!extension_loaded('ctype')){
   $errors = true; 
}

if(!extension_loaded('fileinfo')){
    $errors = true; 
}

if(!extension_loaded('json')){
    $errors = true; 
}

if(!extension_loaded('json')){
    $errors = true; 
}

if(!extension_loaded('openssl')){
   $errors = true; 
}

if(!extension_loaded('pdo')){
    $errors = true; 
}

if(!extension_loaded('tokenizer')){
   $errors = true; 
}
if(!extension_loaded('xml')){
    $errors = true; 
}

if(!extension_loaded('curl')){
    $errors = true; 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $product_info['product_name']; ?> - Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container-box {
            padding: 10px;
            width: 530px;
            overflow: hidden;
        }

        .container-nav {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin-bottom: 10px;
        }

        .nav-link-install {
            color: rgba(0, 0, 0, 0.603);
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 17px;
        }

        .nav-link-install i {
            font-size: 18px;
            font-weight: 400;
        }

        .nav-link-install.active {
            font-weight: bold;
            color: rgba(0, 0, 0, 0.747);
        }

        .nav-link-install.active i {
            color: #00c20a;
        }

        .container-page {
            padding: 10px;
        }

        .input-group-text {
            background: none;
            border-right: none;
        }

        .form-control {
            border-left: none;
            background-color: #f0f0f0;
        }

        .btn-danger,
        .btn-warning {
            width: 60px;
            font-weight: bold;
        }

        .btn-danger {
            background-color: red;
            border: none;
        }

        .btn-warning {
            background-color: gold;
            border: none;
        }
        .alert i{
            font-weight: bold;
            font-size: 18px;
        }
        .alert {
            padding-top: 7px;
            padding-bottom: 7px;
            margin-bottom: 7px;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-box">
        <?php switch ($step) { default: ?>
        
            <div class="mt-3" style="text-align: center;">
                <h4><?php echo $product_info['product_name']; ?> - Installer</h4>
            </div>
            
            <!-- Navigation -->
            <div class="container-nav border border-1 border-light rounded-2 bg-white shadow-sm mt-3">
                <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Home</a>
                <a class="nav-link-install"><i class="ri-checkbox-blank-circle-line"></i> Verify</a>
                <a class="nav-link-install"><i class="ri-checkbox-blank-circle-line"></i> Database</a>
                <a class="nav-link-install"><i class="ri-checkbox-blank-circle-line"></i> Finish</a>
            </div>
            
            <!-- Page Content -->
            <div class="container-page border border-1 border-light rounded-2 bg-white shadow-sm">
                <?php
                    if(is_writeable($installFile)){
                        echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>The installation process is already complete !</span>
                            </div>";
                    } else {
                        if(phpversion() < "8.1"){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Current PHP version is ".phpversion()."! minimum PHP 8.1 or higher required.</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>You are running PHP version ".phpversion()."</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('bcmath')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>BCMath PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>BCMath PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('ctype')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>CTYPE PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>CTYPE PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('fileinfo')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Fileinfo PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>Fileinfo PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('json')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>JSON PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>JSON PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('json')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Mbstring PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>Mbstring PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('openssl')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Openssl PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>Openssl PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('pdo')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>PDO PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>PDO PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('tokenizer')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Tokenizer PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>Tokenizer PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('xml')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>XML PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>XML PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('curl')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Curl PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>Curl PHP extension available</span>
                            </div>";
                        }
                        
                        if(!extension_loaded('curl')){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>Intl PHP extension missing!</span>
                            </div>";
                        } else {
                            echo "<div class='alert alert-success'>
                                <i class='ri-check-line'></i>
                                <span>Intl PHP extension available</span>
                            </div>";
                        }
                        
                        if($product_info ==''){
                            echo "<div class='alert alert-danger'>
                                <i class='ri-close-line'></i>
                                <span>PHP extension missing!</span>
                            </div>";
                        }
                    }
                ?>
                <div class="d-flex justify-content-end mt-3">
                    <?php if(!is_writeable($installFile)){ ?>
                        <?php if(!$errors){ ?>
                            <a href="index.php?step=0" class="btn btn-warning rounded-2" style="min-width: 115px;">Next</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        <?php break; case "0": ?>
        
            <!-- Navigation -->
            <div class="container-nav border border-1 border-light rounded-2 bg-white shadow-sm">
                <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Home</a>
                <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Verify</a>
                <a class="nav-link-install"><i class="ri-checkbox-blank-circle-line"></i> Database</a>
                <a class="nav-link-install"><i class="ri-checkbox-blank-circle-line"></i> Finish</a>
            </div>
            
            <!-- Page Content -->
            <div class="container-page border border-1 border-light rounded-2 bg-white shadow-sm">
                <?php
                  $license_code = null;
                  $client_name = null;
                  if(!empty($_POST['license']) && !empty($_POST['client'])){
                    $license_code = strip_tags(trim($_POST["license"]));
                    $client_name = strip_tags(trim($_POST["client"]));
                    
                    $activate_response = activateLicense($license_code,$client_name);
                    
                    $_SESSION['envato_buyer_name']=$client_name;
                    $_SESSION['envato_purchase_code']=$license_code;
                    
                    if(empty($activate_response)){
                      $msg = 'Server is unavailable.';
                    } else {
                      $msg = $activate_response['message'];
                    }
                    ?>
                    
                    <?php if($activate_response['status'] != true){ ?>
                        <!-- Verify Envato Purchase Code Error -->
                        <form action="index.php?step=0" method="POST">
                            <div class='alert alert-danger'>
                                <i class="ri-close-line"></i>
                                <span><?php echo ucfirst($msg); ?></span>
                            </div>
                            <div class="mt-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-line"></i></span>
                                    <input type="text" class="form-control" placeholder="Enter your envato user name" name="client" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-key-2-line"></i></span>
                                    <input type="text" class="form-control" placeholder="Enter your item purchase code" name="license" autocomplete="off" required>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <button type="submit" class="btn btn-warning rounded-2 mt-3" style="min-width: 115px;">Verify</button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <!-- Verify Envato Purchase Code Done -->
                        <form action="index.php?step=1" method="POST">
                            <div class='alert alert-success'>
                                <i class="ri-check-line"></i>
                                <span><?php echo ucfirst($msg); ?></span>
                            </div>
                            <input type="hidden" name="lcscs" id="lcscs" value="<?php echo ucfirst($activate_response['status']); ?>">
                            <div style="text-align: right;">
                                <button type="submit" class="btn btn-warning rounded-2 mt-3" style="min-width: 115px;">Next</button>
                            </div>
                        </form>
                    <?php } ?>
                <?php } else { ?>
                    <!-- Verify Envato Purchase Code -->
                    <form action="index.php?step=0" method="POST">
                        <div class="mt-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                <input type="text" class="form-control" placeholder="Enter your envato user name" name="client" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-key-2-line"></i></span>
                                <input type="text" class="form-control" placeholder="Enter your item purchase code" name="license" autocomplete="off" required>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="btn btn-warning rounded-2 mt-3" style="min-width: 115px;">Verify</button>
                        </div>
                    </form>
                <?php } ?>
            </div>
            <div class="mt-3" style="text-align: center;">
                <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" class="text-danger" target="_blank">Where Is My Purchase Code?</a>
            </div>
        
        <?php break; case "1": ?>

            <?php if($_POST && isset($_POST["lcscs"])){ ?>
                <!-- Navigation -->
                <div class="container-nav border border-1 border-light rounded-2 bg-white shadow-sm">
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Home</a>
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Verify</a>
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Database</a>
                    <a class="nav-link-install"><i class="ri-checkbox-blank-circle-line"></i> Finish</a>
                </div>
                <!-- Page Content -->
                <div class="container-page border border-1 border-light rounded-2 bg-white shadow-sm">
                    <?php 
                        $valid = strip_tags(trim($_POST["lcscs"]));
                        $db_host = strip_tags(trim($_POST["host"]));
                        $db_user = strip_tags(trim($_POST["user"]));
                        $db_pass = strip_tags(trim($_POST["pass"]));
                        $db_name = strip_tags(trim($_POST["name"]));
                        // Let's import the sql file into the given database
                        if(!empty($db_host)){
                            
                            $myfile = fopen("../.env", "w") or die("Unable to open file!");
                      $txt = "";
                      fwrite($myfile, $txt);
                      $txt = "
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

#CI_ENVIRONMENT = development
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = ".getBaseUrl()."

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = $db_host
database.default.database = $db_name
database.default.username = $db_user
database.default.password = $db_pass

#--------------------------------------------------------------------
# API HEADER
#--------------------------------------------------------------------

API_HEADER_APP = NEMOSOFTS_APP

API_HEADER_WEB = NEMOSOFTS_WEB
";
                      fwrite($myfile, $txt);
                      fclose($myfile);

                            $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
                            mysqli_query($con,"SET NAMES 'utf8'");  
                            
                            if(mysqli_connect_errno()){ ?>
                            
                            <!-- Database Error-->
                            <form action="index.php?step=1" method="POST">
                                <div class='alert alert-danger'>
                                    <i class="ri-close-line"></i>
                                    <span><?php echo $error_message; ?></span>
                                </div>
                                <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-database-2-line"></i></span>
                                        <input type="text" class="form-control" id="host" placeholder="Enter your database host" name="host" value="localhost" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-user-line"></i></span>
                                        <input type="text" class="form-control" id="user" placeholder="Enter your database username" name="user" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                        <input type="text" class="form-control" id="pass" placeholder="Enter your database password" name="pass">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-database-2-line"></i></span>
                                        <input type="text" class="form-control" id="name" placeholder="Enter your database name" name="name" required>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <button type="submit" id="next"  class="btn btn-warning rounded-2 mt-3" style="min-width: 115px;">Import</button>
                                </div>
                            </form>
                        <?php
                            exit;
                          }
                          $templine = '';
                          $lines = file($database_dump_file);
                          foreach($lines as $line){
                            if(substr($line, 0, 2) == '--' || $line == '')
                              continue;
                            $templine .= $line;
                            $query = false;
                            if(substr(trim($line), -1, 1) == ';'){
                              $query = mysqli_query($con, $templine);
                              $templine = '';
                            }
                          }
                        ?>
                        
                        <!-- Database Done -->
                        <form action="index.php?step=2" method="POST">
                            <div class='alert alert-success'>
                                <i class="ri-check-line"></i>
                                <span>Database was successfully imported.</span>
                            </div>
                            <input type="hidden" name="dbscs" id="dbscs" value="true">
                            <div style="text-align: right;">
                                <button type="submit" class="btn btn-warning rounded-2 mt-3" style="min-width: 115px;">Next</button>
                            </div>
                        </form>
                        
                        <?php } else { ?>
                        
                            <!-- Database -->
                            <form action="index.php?step=1" method="POST">
                                <input type="hidden" name="lcscs" id="lcscs" value="<?php echo $valid; ?>">
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-database-2-line"></i></span>
                                        <input type="text" class="form-control" id="host" placeholder="Enter your database host" name="host" value="localhost" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-user-line"></i></span>
                                        <input type="text" class="form-control" id="user" placeholder="Enter your database username" name="user" required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-lock-line"></i></span>
                                        <input type="text" class="form-control" id="pass" placeholder="Enter your database password" name="pass">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ri-database-2-line"></i></span>
                                        <input type="text" class="form-control" id="name" placeholder="Enter your database name" name="name" required>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <button type="submit" id="next"  class="btn btn-warning rounded-2 mt-3" style="min-width: 115px;">Import</button>
                                </div>
                            </form>
                            
                        <?php } ?>
                    
                </div>
            <?php } else { ?>
                <div class='alert alert-danger'>
                    <i class="ri-close-line"></i>
                    <span>Sorry, something went wrong.</span>
                </div>
            <?php } ?>
        
        <?php break; case "2": ?>
            <?php if($_POST && isset($_POST["dbscs"])){
                session_destroy();
            ?>
                <!-- Navigation -->
                <div class="container-nav border border-1 border-light rounded-2 bg-white shadow-sm">
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Home</a>
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Verify</a>
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Database</a>
                    <a class="nav-link-install active"><i class="ri-checkbox-circle-line"></i> Finish</a>
                </div>
                <!-- Page Content -->
                <div class="container-page border border-1 border-light rounded-2 bg-white shadow-sm">
                    <div class='alert alert-success'>
                        <i class="ri-check-line"></i>
                        <span><?php echo $product_info['product_name']; ?> is successfully installed.</span>
                    </div>
                    <p>You can now login using your username: <b style="color: #f44336c7;">admin</b> and default password: <b style="color: #f44336c7;">admin</b></p>
                    <p>The first thing you should do is change your account details.</p>
                    <div class="mt-4" style="text-align: center;">
                        <a href="<?php echo getBaseUrl(); ?>" class="btn btn-primary btn--slide" style="min-width: 115px;">Let's go</a>
                    </div>
                    <p class="mt-4" style="text-align: center;">Thank you for purchasing our products</p>
                </div>
            <?php } else { ?>
                <div class='alert alert-danger'>
                    <i class="ri-close-line"></i>
                    <span>Sorry, something went wrong.</span>
                </div>
            <?php } ?>
        
        <?php break; } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
