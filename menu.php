<?php
include 'util.php';
include 'db.php';
include 'sms.php';

class Menu {
    protected $phoneNumber;
    protected $conn;
    //protected $sms;

    function __construct($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
        $db = new DB();
        $this->conn = $db->connect();
        //$this->sms = new Sms($this->phoneNumber);
    }

    public function isUserRegistered() {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE phone_number = ?");
        $stmt->execute([$this->phoneNumber]);
        return $stmt->rowCount() > 0;
    }

    public function mainMenuUnregistered() {
        echo "CON Welcome to Expense Tracker\n";
        echo "1. Register\n";
    }

    public function mainMenuRegistered() {
        echo "CON Expense Tracker Main Menu\n";
        echo "1. Add Category & Budget\n";
        echo "2. Add Expense\n";
        echo "3. View Expenses\n";
        echo "4. View Remaining Budget\n";
    }

    public function menuRegister($textArray) {
        $level = count($textArray);
        if ($level == 1) {
            echo "CON Enter your full name:\n";
        } elseif ($level == 2) {
            echo "CON Set your PIN:\n" . Util::$GO_TO_MAIN_MENU . ". Main Menu\n" . Util::$GO_BACK . ". Back";
        } elseif ($level == 3) {
            $name = $textArray[1];
            $pin = $textArray[2];
            $stmt = $this->conn->prepare("INSERT INTO users (fullname, pin, phone_number) VALUES (?, ?, ?)");
            $stmt->execute([$name, $pin, $this->phoneNumber]);

            // SMS after successful registration
            $msg = "Welcome to Expense Tracker, $name! Your account has been created successfully.";
            $sms = new Sms($this->phoneNumber);
            $sent = $sms->sendSMS($msg, $this->phoneNumber);

            $status = strtolower($sent['status'] ?? ($sent['Status'] ?? ''));
            if ($status == 'success') {
                echo "END $name, you are now registered. You will receive an SMS shortly.";
            } else {
                echo "END $name, you are now registered. However, SMS could not be sent.";
            }
        }
    }

    public function menuAddCategory($textArray) {
        $level = count($textArray);
        if ($level == 1) {
            echo "CON Enter category name:\n" . Util::$GO_TO_MAIN_MENU . ". Main Menu\n" . Util::$GO_BACK . ". Back";
        } elseif ($level == 2) {
            echo "CON Enter budget for {$textArray[1]}:\n" . Util::$GO_TO_MAIN_MENU . ". Main Menu\n" . Util::$GO_BACK . ". Back";
        } elseif ($level == 3) {
            $category = $textArray[1];
            $budget = $textArray[2];
            $stmt = $this->conn->prepare("INSERT INTO categories (phone_number, category_name, budget) VALUES (?, ?, ?)");
            $stmt->execute([$this->phoneNumber, $category, $budget]);

            // SMS after adding category
            $msg = "Hi! Your new category '$category' with a budget of $budget RWF has been created successfully.";
            $sms = new Sms($this->phoneNumber);
            $sent = $sms->sendSMS($msg, $this->phoneNumber);

            $status = strtolower($sent['status'] ?? ($sent['Status'] ?? ''));
            if ($status == 'success') {
                echo "END Category '$category' with budget $budget RWF saved. You will receive an SMS shortly.";
            } else {
                echo "END Category '$category' with budget $budget RWF saved. However, SMS could not be sent.";
            }
        }
    }

    public function menuAddExpense($textArray) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE phone_number = ?");
        $stmt->execute([$this->phoneNumber]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $level = count($textArray);
        if ($level == 1) {
            echo "CON Select Category:\n";
            foreach ($categories as $index => $cat) {
                echo ($index + 1) . ". " . $cat['category_name'] . "\n";
            }
            echo Util::$GO_TO_MAIN_MENU . ". Main Menu\n" . Util::$GO_BACK . ". Back";
        } elseif ($level == 2) {
            echo "CON Enter amount:\n" . Util::$GO_TO_MAIN_MENU . ". Main Menu\n" . Util::$GO_BACK . ". Back";
        } elseif ($level == 3) {
            echo "CON Enter description:\n" . Util::$GO_TO_MAIN_MENU . ". Main Menu\n" . Util::$GO_BACK . ". Back";
        } elseif ($level == 4) {
            $categoryIndex = (int)$textArray[1] - 1;

            if (!isset($categories[$categoryIndex])) {
                echo "END Invalid category selected.";
                return;
            }

            $amount = $textArray[2];
            $desc = $textArray[3];

            $category = $categories[$categoryIndex];
            $categoryId = $category['id'];

            $stmt = $this->conn->prepare("INSERT INTO expenses (phone_number, category_id, amount, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->phoneNumber, $categoryId, $amount, $desc]);

            $stmt = $this->conn->prepare("SELECT SUM(amount) AS total FROM expenses WHERE category_id = ?");
            $stmt->execute([$categoryId]);
            $totalSpent = $stmt->fetch()['total'];
            $budget = $category['budget'];

            $smsMessage = "Expense Alert: $amount RWF spent on " . $category['category_name'] . " - $desc";
            if ((float)$totalSpent > (float)$budget) {
                $smsMessage .= "\nWARNING: You've exceeded your budget for " . $category['category_name'] . "!";
            }

            $sms = new Sms($this->phoneNumber);
            $sent = $sms->sendSMS($smsMessage, $this->phoneNumber);
            $status = strtolower($sent['status'] ?? ($sent['Status'] ?? ''));

            if ($status == 'success') {
                if ((float)$totalSpent > (float)$budget) {
                    echo "END Alert: You've exceeded your budget for " . $category['category_name'] . "!";
                } else {
                    echo "END Expense saved under " . $category['category_name'] . ".";
                }
            } else {
                echo "END Expense saved but there was an error sending the SMS notification.";
            }
        }
    }

    public function menuViewExpenses() {
    $stmt = $this->conn->prepare("SELECT c.category_name, SUM(e.amount) AS total FROM expenses e JOIN categories c ON e.category_id = c.id WHERE e.phone_number = ? GROUP BY c.category_name");
    $stmt->execute([$this->phoneNumber]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = "END Expenses:\n";
    foreach ($expenses as $row) {
        $response .= $row['category_name'] . ": " . $row['total'] . "\n";
    }

    // SMS after viewing expenses
    $smsMessage = "Here is the summary of your expenses:\n" . $response;
    $sms = new Sms($this->phoneNumber);
    $sent = $sms->sendSMS($smsMessage, $this->phoneNumber);

    $status = strtolower($sent['status'] ?? ($sent['Status'] ?? ''));

    if ($status == 'success') {
        echo $response . "You will receive an SMS shortly.";
    } else {
        echo $response . "However, SMS could not be sent.";
    }
}


    public function menuRemainingBudget() {
    $stmt = $this->conn->prepare("SELECT c.category_name, c.budget, IFNULL(SUM(e.amount), 0) AS spent FROM categories c LEFT JOIN expenses e ON c.id = e.category_id WHERE c.phone_number = ? GROUP BY c.id");
    $stmt->execute([$this->phoneNumber]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = "END Remaining Budgets:\n";
    foreach ($categories as $row) {
        $remaining = (float)$row['budget'] - (float)$row['spent'];
        $response .= $row['category_name'] . ": " . $remaining . " left\n";
    }

    // SMS after viewing remaining budget
    $smsMessage = "Here is your remaining budget summary:\n" . $response;
    $sms = new Sms($this->phoneNumber);
    $sent = $sms->sendSMS($smsMessage, $this->phoneNumber);

    $status = strtolower($sent['status'] ?? ($sent['Status'] ?? ''));

    if ($status == 'success') {
        echo $response . "You will receive an SMS shortly.";
    } else {
        echo $response . "However, SMS could not be sent.";
    }
}


    public function goBack($text) {
        $exploded = explode("*", $text);
        while (($i = array_search(Util::$GO_BACK, $exploded)) !== false) {
            array_splice($exploded, $i - 1, 2);
        }
        return join("*", $exploded);
    }

    public function goToMainMenu($text) {
        $exploded = explode("*", $text);
        while (($i = array_search(Util::$GO_TO_MAIN_MENU, $exploded)) !== false) {
            $exploded = array_slice($exploded, $i + 1);
        }
        return join("*", $exploded);
    }

    public function middleware($text) {
        return $this->goBack($this->goToMainMenu($text));
    }
}
?>
