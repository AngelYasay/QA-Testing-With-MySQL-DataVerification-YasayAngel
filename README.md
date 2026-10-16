# QA Testing with MySQL Data Verification

## 1. Project Overview

This project is a PHP web application titled **Kara David's Wish Note**. It collects information through a web form and saves the submitted data into a MySQL/MariaDB database.

The purpose of this project is to test the web application's functionality, verify database records, and identify data-saving errors.

## 2. Technologies Used

- PHP
- MySQL / MariaDB
- HTML
- CSS
- JavaScript
- Apache Web Server
- Ubuntu using Termux

## 3. Web Application Features

The application includes four input fields:

1. **First Name** – Required, maximum 30 characters.
2. **Last Name** – Required, maximum 30 characters.
3. **Government Position** – Required, maximum 20 characters.
4. **Location** – Optional, maximum 40 characters.

The application includes a confirmation modal titled **Verify Target Data**. Users can review the entered information before submitting it by selecting **Confirm & Send** or cancel the submission.

## 4. Database Information

**Database Name:** `birthday_wishnote`

**Table Name:** `main_corrupt_politicians`

| Column | Data Type | Description |
|---|---|---|
| `id` | INT | Primary key with auto-increment |
| `FirstName` | VARCHAR(30) | Target's first name |
| `LastName` | VARCHAR(30) | Target's last name |
| `GovPosition` | VARCHAR(20) | Target's government position |
| `Location` | VARCHAR(40) | Target's location; allows NULL |

## 5. Test Data

Five sample records were submitted through the web application.

| ID | First Name | Last Name | Government Position | Entered Location |
|---:|---|---|---|---|
| 1 | Maria | Santos | Mayor | Bacolod City |
| 2 | Juan | Dela Cruz | Governor | Talisay City |
| 3 | Ana | Reyes | Senator | Manila |
| 4 | Pedro | Garcia | Councilor | Silay City |
| 5 | Sofia | Mendoza | Secretary | Cebu City |

These names and entries are fictional sample data used for testing.

## 6. Testing Procedure

1. Open the PHP web application in the browser.
2. Enter the test data into the form.
3. Click **Send Wish Note**.
4. Verify that the confirmation modal displays the entered information.
5. Click **Confirm & Send**.
6. Check the database to verify whether the records were saved correctly.
7. Compare the entered values with the values stored in the database.

## 7. Database Verification

The following SQL command was used to verify the saved records:

```sql
SELECT * FROM main_corrupt_politicians;
```

### Actual Database Results

| ID | First Name | Last Name | Government Position | Stored Location |
|---:|---|---|---|---|
| 1 | Maria | Santos | Mayor | NULL |
| 2 | Juan | Dela Cruz | Governor | NULL |
| 3 | Ana | Reyes | Senator | NULL |
| 4 | Pedro | Garcia | Councilor | NULL |
| 5 | Sofia | Mendoza | Secretary | NULL |

**Result:** All five records were successfully inserted into the database. However, the Location field was stored as `NULL` for every record.

## 8. Test Results

| Test Case | Expected Result | Actual Result | Status |
|---|---|---|---|
| Open the web application | The form loads successfully | The form opened successfully | PASS |
| Required field validation | Required fields cannot be empty | Required fields are marked as required | PASS |
| Confirmation modal | Entered information appears in the modal | The modal displayed the entered information | PASS |
| Database insertion | Submitted records are saved | Five records were saved | PASS |
| First Name storage | First Name is saved correctly | All five names were saved | PASS |
| Last Name storage | Last Name is saved correctly | All five names were saved | PASS |
| Government Position storage | Government Position is saved correctly | All five positions were saved | PASS |
| Location storage | Entered Location is saved | All five Location values were `NULL` | FAIL — BUG IDENTIFIED |

## 9. Identified Data-Saving Flaw

The application collects and displays the Location field, but the PHP database `INSERT` statement does not include the `Location` column. The `bind_param()` function also does not include the Location value.

Because of this omission, the application displays a success message even though the Location data is not saved.

This flaw was identified by comparing the five submitted test records with the actual database results.

## 10. Conclusion

The web application was successfully tested in a local environment. The form loaded correctly, the confirmation modal displayed the submitted information, and all five records were saved to the database.

However, database verification revealed a data integrity issue: the Location field was not saved and appeared as `NULL` in every record.

This testing process demonstrates the importance of comparing user-submitted data with database records to identify errors that may not be obvious from the application's success message.

## 11. Project Files

The GitHub repository contains the following required files:

- `readme.md` – Project overview, database information, test procedure, and test results.
- `webapp-generation-prompt.txt` – The prompt used to generate the web application.
- `test-webapp.php` – The PHP source code for the web application.
