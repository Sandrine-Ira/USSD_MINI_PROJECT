<?php
include 'menu.php';

$sessionId = $_POST['sessionId'];
$phoneNumber = $_POST['phoneNumber'];
$serviceCode = $_POST['serviceCode'];
$text = $_POST['text'];

$menu = new Menu($phoneNumber);
$text = $menu->middleware($text);

if ($text == "" && !$menu->isUserRegistered()) {
    $menu->mainMenuUnregistered();
} elseif ($text == "" && $menu->isUserRegistered()) {
    $menu->mainMenuRegistered();
} elseif (!$menu->isUserRegistered()) {
    $textArray = explode("*", $text);
    switch ($textArray[0]) {
        case 1:
            $menu->menuRegister($textArray);
            break;
        default:
            echo "END Invalid option. Retry.";
    }
} else {
    $textArray = explode("*", $text);
    switch ($textArray[0]) {
        case 1:
            $menu->menuAddCategory($textArray);
            break;
        case 2:
            $menu->menuAddExpense($textArray);
            break;
        case 3:
            $menu->menuViewExpenses();
            break;
        case 4:
            $menu->menuRemainingBudget();
            break;
        default:
            echo "END Invalid choice";
    }
}
?>
