<?php
// Add the Sms class at the beginning of your file
include 'db.php';
include 'sms.php';

class Menu {
    protected $phoneNumber;
    protected $conn;
    protected $sms;

    function __construct($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
        $db = new DB();
        $this->conn = $db->connect();
        $this->sms = new Sms($phoneNumber); // Instantiate the Sms class
    }

    // After user registration
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
            echo "END $name, you are now registered.";

            // Send SMS to the user after registration
            $message = "Hello $name, you have successfully registered for the Expense Tracker.";
            $this->sms->sendSms($message, $this->phoneNumber);
        }
    }

    // After adding a category
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
            echo "END Category '$category' with budget $budget RWF saved.";

            // Send SMS to the user after adding the category
            $message = "You have successfully added a category '$category' with a budget of $budget RWF.";
            $this->sms->sendSms($message, $this->phoneNumber);
        }
    }

    // After adding an expense
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
            $categoryIndex = $textArray[1] - 1;
            $amount = $textArray[2];
            $desc = $textArray[3];

            if (!isset($categories[$categoryIndex])) {
                echo "END Invalid category selected.";
                return;
            }

            $category = $categories[$categoryIndex];
            $categoryId = $category['id'];

            $stmt = $this->conn->prepare("INSERT INTO expenses (phone_number, category_id, amount, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$this->phoneNumber, $categoryId, $amount, $desc]);

            $stmt = $this->conn->prepare("SELECT SUM(amount) AS total FROM expenses WHERE category_id = ?");
            $stmt->execute([$categoryId]);
            $totalSpent = $stmt->fetch()['total'];
            $budget = $category['budget'];

            if ((float)$totalSpent > (float)$budget) {
                echo "END Alert: You’ve exceeded your budget for " . $category['category_name'] . "!";

                // Send SMS about exceeding the budget
                $message = "Alert: You've exceeded your budget for the category '$category[category_name]'.";
                $this->sms->sendSms($message, $this->phoneNumber);
            } else {
                echo "END Expense saved under " . $category['category_name'] . ".";

                // Send SMS confirming the expense
                $message = "Your expense under the category '$category[category_name]' has been recorded. Amount: $amount RWF.";
                $this->sms->sendSms($message, $this->phoneNumber);
            }
        }
    }

    // After viewing expenses
    public function menuViewExpenses() {
        $stmt = $this->conn->prepare("SELECT c.category_name, SUM(e.amount) AS total FROM expenses e JOIN categories c ON e.category_id = c.id WHERE e.phone_number = ? GROUP BY c.category_name");
        $stmt->execute([$this->phoneNumber]);
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = "END Expenses:\n";
        foreach ($expenses as $row) {
            $response .= $row['category_name'] . ": " . $row['total'] . "\n";
        }
        echo $response;

        // Send SMS after viewing expenses
        $message = "You have viewed your expenses. Details:\n" . $response;
        $this->sms->sendSms($message, $this->phoneNumber);
    }

    // After viewing remaining budget
    public function menuRemainingBudget() {
        $stmt = $this->conn->prepare("SELECT c.category_name, c.budget, IFNULL(SUM(e.amount), 0) AS spent FROM categories c LEFT JOIN expenses e ON c.id = e.category_id WHERE c.phone_number = ? GROUP BY c.id");
        $stmt->execute([$this->phoneNumber]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = "END Remaining Budgets:\n";
        foreach ($categories as $row) {
            $remaining = (float)$row['budget'] - (float)$row['spent'];
            $response .= $row['category_name'] . ": " . $remaining . " left\n";
        }
        echo $response;

        // Send SMS after viewing remaining budget
        $message = "You have viewed your remaining budgets. Details:\n" . $response;
        $this->sms->sendSms($message, $this->phoneNumber);
    }
}
