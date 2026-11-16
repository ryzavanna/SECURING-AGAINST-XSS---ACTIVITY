<?php
session_start();

require_once 'dbConfig.php';
require_once 'models.php';

// =========================================
// XSS INPUT SANITIZATION HELPER
// =========================================
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// =========================================
// USER AUTHENTICATION HANDLERS
// =========================================

// User Registration
if (isset($_POST['registerUserBtn'])) {
    $userName  = sanitizeInput($_POST['userName'] ?? '');
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName  = sanitizeInput($_POST['lastName'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($userName) || empty($firstName) || empty($lastName) || empty($password)) {
        header("Location: ../register.php?error=emptyinput");
        exit();
    }

    // Password Policy: Min 8 chars, at least 1 uppercase, 1 lowercase, and 1 number
    $hasUppercase = preg_match('@[A-Z]@', $password);
    $hasLowercase = preg_match('@[a-z]@', $password);
    $hasNumber    = preg_match('@[0-9]@', $password);

    if (strlen($password) < 8 || !$hasUppercase || !$hasLowercase || !$hasNumber) {
        header("Location: ../register.php?error=weakpassword");
        exit();
    }

    $registerStatus = registerUser($pdo, $userName, $firstName, $lastName, $password);
    
    if ($registerStatus === true) {
        header("Location: ../login.php?registration=success");
        exit();
    } elseif ($registerStatus === 'taken') {
        header("Location: ../register.php?error=usernametaken");
        exit();
    } else {
        header("Location: ../register.php?error=stmtfailed");
        exit();
    }
}

// User Login
if (isset($_POST['loginUserBtn'])) {
    $userName = sanitizeInput($_POST['userName'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($userName) && !empty($password)) {
        if (loginUser($pdo, $userName, $password)) {
            session_regenerate_id(true);
            $_SESSION['user_name'] = $userName;
            header("Location: ../index.php");
            exit();
        } else {
            header("Location: ../login.php?error=invalidcredentials");
            exit();
        }
    } else {
        header("Location: ../login.php?error=emptyinput");
        exit();
    }
}

// User Logout
if (isset($_GET['logoutUser'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// =========================================
// DESIGNER (PARENT) HANDLERS
// =========================================

if (isset($_POST['insertDesignerBtn'])) {
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../login.php");
        exit();
    }

    $userName       = sanitizeInput($_POST['userName'] ?? '');
    $firstName      = sanitizeInput($_POST['firstName'] ?? '');
    $lastName       = sanitizeInput($_POST['lastName'] ?? '');
    $dateOfBirth    = sanitizeInput($_POST['dateOfBirth'] ?? '');
    $specialization = sanitizeInput($_POST['specialization'] ?? '');
    $addedBy        = $_SESSION['user_name'];

    if (!empty($userName) && !empty($firstName) && !empty($lastName)) {
        if (insertDesigner($pdo, $userName, $firstName, $lastName, $dateOfBirth, $specialization, $addedBy)) {
            header("Location: ../index.php");
            exit();
        }
    }
}

if (isset($_POST['editDesignerBtn'])) {
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../login.php");
        exit();
    }

    $designerId     = $_GET['designerId'];
    $userName       = sanitizeInput($_POST['userName'] ?? '');
    $firstName      = sanitizeInput($_POST['firstName'] ?? '');
    $lastName       = sanitizeInput($_POST['lastName'] ?? '');
    $dateOfBirth    = sanitizeInput($_POST['dateOfBirth'] ?? '');
    $specialization = sanitizeInput($_POST['specialization'] ?? '');
    $lastUpdatedBy  = $_SESSION['user_name'];

    if (updateDesigner($pdo, $designerId, $userName, $firstName, $lastName, $dateOfBirth, $specialization, $lastUpdatedBy)) {
        header("Location: ../index.php");
        exit();
    }
}

if (isset($_POST['deleteDesignerBtn'])) {
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../login.php");
        exit();
    }

    $designerId = $_GET['designerId'];
    if (deleteDesigner($pdo, $designerId)) {
        header("Location: ../index.php");
        exit();
    }
}

// =========================================
// PRODUCT (CHILD) HANDLERS
// =========================================

if (isset($_POST['insertProductBtn'])) {
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../login.php");
        exit();
    }

    $designerId  = $_GET['designerId'];
    $productName = sanitizeInput($_POST['productName'] ?? '');
    $productType = sanitizeInput($_POST['productType'] ?? '');
    $price       = $_POST['price'];
    $salePrice   = !empty($_POST['salePrice']) ? $_POST['salePrice'] : NULL;
    $addedBy     = $_SESSION['user_name'];

    if (!empty($productName) && !empty($productType) && !empty($price)) {
        if (insertProduct($pdo, $designerId, $productName, $productType, $price, $salePrice, $addedBy)) {
            header("Location: ../viewProducts.php?designerId=" . urlencode($designerId));
            exit();
        }
    }
}

if (isset($_POST['editProductBtn'])) {
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../login.php");
        exit();
    }

    $digitalProductId = $_GET['digitalProductId'];
    $designerId       = $_GET['designerId'];
    $productName      = sanitizeInput($_POST['productName'] ?? '');
    $productType      = sanitizeInput($_POST['productType'] ?? '');
    $price            = $_POST['price'];
    $salePrice        = !empty($_POST['salePrice']) ? $_POST['salePrice'] : NULL;
    $lastUpdatedBy    = $_SESSION['user_name'];

    if (updateProduct($pdo, $digitalProductId, $productName, $productType, $price, $salePrice, $lastUpdatedBy)) {
        header("Location: ../viewProducts.php?designerId=" . urlencode($designerId));
        exit();
    }
}

if (isset($_POST['deleteProductBtn'])) {
    if (!isset($_SESSION['user_name'])) {
        header("Location: ../login.php");
        exit();
    }

    $digitalProductId = $_GET['digitalProductId'];
    $designerId       = $_GET['designerId'];
    if (deleteProduct($pdo, $digitalProductId)) {
        header("Location: ../viewProducts.php?designerId=" . urlencode($designerId));
        exit();
    }
}
?>