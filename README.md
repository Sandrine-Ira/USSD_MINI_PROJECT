# USSD and SMS Expense Tracker System

A comprehensive expense tracking system accessible via USSD and SMS, allowing users to manage their expenses and budgets through mobile devices.

## Features

### USSD FeaturesUSSD and SMS Expense Tracker System
A comprehensive expense tracking system that allows users to manage their finances via USSD and SMS, providing an easy-to-use interface for managing expenses and budgets.

Features

USSD Features

Add Expenses

Select from predefined categories.

Enter the amount and description.

Track payment methods.

View Expenses

View recent expenses.

See detailed expenses by category.

Track spending history.

Budget Management

Set monthly budgets by category.

View budget summaries.

Track remaining budget.

User-Friendly Menu

Simple navigation.

Clear prompts for easy interaction.

SMS Features

Quick Commands

Check current budget balance.

Get expense summary.

View remaining budget.

Instant Notifications

Budget alerts when thresholds are reached.

Expense confirmations after entries.

Monthly summary reports via SMS.

Technical Requirements

PHP: Version 7.4 or higher

MySQL: Version 5.7 or higher

Web server: Apache or Nginx

SMS Gateway integration

USSD Gateway integration

Installation

Step-by-Step Setup

Clone the Repository

bash
Copy
Edit
git clone [repository-url]
cd ussdandsms_expense-tracker-system
Database Setup

Create a MySQL database.

Import the expense_tracker.sql file located in the repository.

Configure database credentials in config/database.php.

Configuration

Set up SMS gateway credentials.

Set up USSD gateway parameters.

Update database settings in config/database.php.
- **Add Expenses**
  - Select from predefined categories
  - Enter amount and description
  - Track payment methods
- **View Expenses**
  - View recent expenses
  - See expense details by category
  - Track spending history
- **Budget Management**
  - Set monthly budgets by category
  - View budget summaries
  - Track remaining budget
- **User-Friendly Menu**
  - Simple navigation
  - Clear prompts
  - Easy-to-use interface

### SMS Features
- **Quick Commands**
  - Check balance
  - Get expense summary
  - View budget status
- **Instant Notifications**
  - Budget alerts
  - Expense confirmations
  - Monthly summaries

## Technical Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- SMS Gateway integration
- USSD Gateway integration

## Installation

1. **Clone the repository**
   ```bash
   git clone [repository-url]
   cd ussdandsms_expense-tracker-system
   ```

2. **Database Setup**
   - Create a MySQL database
   - Import the `expense_trackerss.sql` file
   - Configure database credentials in `config/database.php`

3. **Configuration**
   - Update database settings in `config/database.php`
   - Configure SMS gateway settings
   - Set up USSD gateway parameters

4. **Directory Structure**
   ```
   ├── config/
   │   └── database.php
   ├── ussd/
   │   ├── index.php
   │   └── menu.php
   ├── sms/
   │   └── index.php
   ├── register.php
   └── expense_trackerss.sql
   ```

## Usage

### USSD Access
1. Dial the USSD code (e.g., *123#)
2. Follow the menu prompts:
   ```
   Welcome to Expense Tracker
   1. Add Expense
   2. View Expenses
   3. Set Budget
   4. View Budget
   5. Exit
   ```

### SMS Commands
Send the following commands to the system number:
- `balance` - Check budget balance
- `summary` - Get expense summary
- `help` - Show available commands

## Database Structure

### Users Table
- user_id (Primary Key)
- phone_number
- full_name
- email
- password_hash
- status

### Categories Table
- category_id (Primary Key)
- name
- description

### Expenses Table
- expense_id (Primary Key)
- user_id (Foreign Key)
- category_id (Foreign Key)
- amount
- description
- expense_date
- payment_method

### Budgets Table
- budget_id (Primary Key)
- user_id (Foreign Key)
- category_id (Foreign Key)
- amount
- start_date
- end_date

### SMS Logs Table
- sms_id (Primary Key)
- user_id (Foreign Key)
- message
- status
- sent_at

### USSD Sessions Table
- session_id (Primary Key)
- user_id (Foreign Key)
- session_key
- current_menu

## Security Features

- Password hashing
- SQL injection prevention
- Input validation
- Session management
- Secure database queries

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact [support email/contact]

## Acknowledgments

- Thanks to all contributors
- Inspired by the need for accessible financial management tools 
