# QA Testing with MySQL Data Verification

## Project Overview

This project is a PHP web application titled **Kara David's Wish Note**. It collects target information through a web form and saves selected information into a MySQL database.

The project demonstrates web application testing, database verification, and the identification of a data-saving flaw.

## Technologies Used

- PHP
- MySQL / MariaDB
- HTML
- CSS
- JavaScript
- Apache Web Server
- Ubuntu through Termux

## Web Application Features

The application includes the following fields:

1. **First Name** – Required, maximum 30 characters.
2. **Last Name** – Required, maximum 30 characters.
3. **Government Position** – Required, maximum 20 characters.
4. **Location** – Optional, maximum 40 characters.

The application displays a **Verify Target Data** confirmation modal before submitting the form. Users can select **Cancel** or **Confirm & Send**.

## Database Information

**Database Name:** `birthday_wishnote`

**Table Name:** `main_corrupt_politicians`

| Column | Data Type | Description |
|---|---|---|
| `id` | INT | Primary key with auto-increment |
| `FirstName` | VARCHAR(30) | Target's first name |
| `LastName` | VARCHAR(30) | Target's last name |
| `GovPosition` | VARCHAR(20) | Target's government position |
| `Location` | VARCHAR(40) | Target's location; allows NULL |

## Testing Procedure

1. Open the web application in a browser.
2. Enter sample data in the form.
3. Click **Send Wish Note**.
4. Verify that the confirmation modal displays the entered information.
5. Click **Confirm & Send**.
6. Check the database to confirm whether the submitted information was saved.

## Test Data

| Field | Test Value |
|---|---|
| First Name | Maria |
| Last Name | Santos |
| Government Position | Mayor |
| Location | Bacolod City |

## Database Verification

The following SQL command was used to verify the submitted data:

```sql
SELECT * FROM main_corrupt_politicians;
```

**Observed database result:**

| id | FirstName | LastName | GovPosition | Location |
|---:|---|---|---|---|
| 1 | Maria | Santos | Mayor | NULL |

## Test Results

| Test Case | Expected Result | Actual Result | Status |
|---|---|---|---|
| Open the web application | Form loads successfully | Form loaded successfully | PASS |
| Validate required fields | Required fields cannot be empty | Required fields are marked required | PASS |
| Display confirmation modal | Entered information is displayed | Confirmation modal displayed the information | PASS |
| Submit the form | Data is inserted into the database | Maria Santos and Mayor were saved | PASS |
| Save the Location field | Location is stored in the database | Location was stored as NULL | FAIL — BUG IDENTIFIED |

## Identified Data-Saving Flaw

The application collects and displays the Location field, but the PHP `INSERT` statement does not include the `Location` column. The `bind_param()` function also does not bind the Location value.

As a result, the application reports a successful submission even though the Location value is not saved in the database. The database stores `NULL` for Location.

This is the intentional flaw included for the assignment. It was identified by comparing the submitted test data with the actual database record.

## Conclusion

The web application was successfully opened and tested locally. The confirmation modal worked, and the database stored the First Name, Last Name, and Government Position fields. Database verification identified the missing Location value, demonstrating how QA testing can detect data integrity problems that may not be visible to users.

## Project Files

- `readme.md` – Project documentation and test results.
- `webapp-generation-prompt.txt` – Prompt used to generate the web application.
- `test-webapp.php` – PHP source code for the web application.
