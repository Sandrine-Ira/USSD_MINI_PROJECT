# **USSD and SMS Expense Tracker System**  
A comprehensive expense tracking system that allows users to manage their finances via USSD and SMS, providing an easy-to-use interface for managing expenses and budgets.

---

## **FEATURES**

### **USSD Features**

- **Add Expenses**  
  - Select from predefined categories  
  - Enter the amount and description  
  - Track payment methods  

- **View Expenses**  
  - View recent expenses  
  - See detailed expenses by category  
  - Track spending history  

- **Budget Management**  
  - Set monthly budgets by category  
  - View budget summaries  
  - Track remaining budget  

- **User-Friendly Menu**  
  - Simple navigation  
  - Clear prompts for easy interaction  

---

### **SMS Features**

- **Quick Commands**  
  - Check current budget balance  
  - Get expense summary  
  - View remaining budget  

- **Instant Notifications**  
  - Budget alerts when thresholds are reached  
  - Expense confirmations after entries  
  - Monthly summary reports via SMS  

---

## **TECHNICAL REQUIREMENTS**

- PHP: Version 7.4 or higher  
- MySQL: Version 5.7 or higher  
- Web server: Apache or Nginx  
- SMS Gateway integration  
- USSD Gateway integration  

---

## **INSTALLATION**

### **1. Clone the Repository**
```bash
git clone [repository-url]
cd ussdandsms_expense-tracker-system
```

---

### **2. Database Setup**
- Create a MySQL database  
- Import the `expense_tracker.sql` file located in the repository  
- Configure database credentials in `config/database.php`  

---

### **3. Configuration**
- Set up SMS gateway credentials  
- Set up USSD gateway parameters  
- Update database settings in `config/database.php`
