
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "birthday_wishnote";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed. Check your local MariaDB configuration.");
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST["FirstName"] ?? "");
    $lastName = trim($_POST["LastName"] ?? "");
    $govPosition = trim($_POST["GovPosition"] ?? "");
    $location = trim($_POST["Location"] ?? "");

    // Validate required fields and maximum lengths.
    if (
        $firstName === "" ||
        $lastName === "" ||
        $govPosition === "" ||
        strlen($firstName) > 30 ||
        strlen($lastName) > 30 ||
        strlen($govPosition) > 20 ||
        strlen($location) > 40
    ) {
        $error = "Please check the required fields and character limits.";
    } else {
        // INTENTIONAL ASSIGNMENT FLAW:
        // Location is collected but omitted from the INSERT statement
        // and bind_param(), so it will not be saved in the database.
        $stmt = $conn->prepare(
            "INSERT INTO main_corrupt_politicians
            (FirstName, LastName, GovPosition)
            VALUES (?, ?, ?)"
        );

        if ($stmt) {
            $stmt->bind_param(
                "sss",
                $firstName,
                $lastName,
                $govPosition
            );

            if ($stmt->execute()) {
                $success = "Success! The wish note has been sent.";
            } else {
                $error = "The wish note could not be saved.";
            }

            $stmt->close();
        } else {
            $error = "Unable to prepare the database query.";
        }
    }
}

function h($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kara David's Wish Note</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1f2937;
        }

        .card {
            max-width: 520px;
            margin: 24px auto;
            background: white;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 8px 28px #00000012;
        }

        h2 {
            text-align: center;
            color: #2563eb;
        }

        label {
            display: block;
            margin: 16px 0 7px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 16px;
        }

        button {
            border: 0;
            border-radius: 7px;
            padding: 12px 16px;
            cursor: pointer;
            font-size: 15px;
        }

        .primary {
            background: #2563eb;
            color: white;
            width: 100%;
            margin-top: 22px;
        }

        .primary:hover {
            background: #1d4ed8;
        }

        .cancel {
            background: #e5e7eb;
            color: #111827;
        }

        .confirm {
            background: #2563eb;
            color: white;
        }

        .message {
            padding: 12px;
            border-radius: 7px;
            margin: 12px 0;
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: #0009;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal {
            background: white;
            border-radius: 12px;
            padding: 24px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 35px #0004;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .value {
            overflow-wrap: anywhere;
        }
    </style>
</head>

<body>
    <main class="card">
        <h2>Kara David's Wish Note</h2>

        <?php if ($success !== ""): ?>
            <p class="message"><?= h($success) ?></p>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <p class="message error"><?= h($error) ?></p>
        <?php endif; ?>

        <form id="wishForm" method="POST">
            <label for="FirstName">Target's First Name</label>
            <input
                id="FirstName"
                name="FirstName"
                maxlength="30"
                required
                value="<?= h($_POST["FirstName"] ?? "") ?>"
            >

            <label for="LastName">Target's Last Name</label>
            <input
                id="LastName"
                name="LastName"
                maxlength="30"
                required
                value="<?= h($_POST["LastName"] ?? "") ?>"
            >

            <label for="GovPosition">Target's Government Position</label>
            <input
                id="GovPosition"
                name="GovPosition"
                maxlength="20"
                required
                value="<?= h($_POST["GovPosition"] ?? "") ?>"
            >

            <label for="Location">Target's Location</label>
            <input
                id="Location"
                name="Location"
                maxlength="40"
                value="<?= h($_POST["Location"] ?? "") ?>"
            >

            <button class="primary" type="submit">
                Send Wish Note
            </button>
        </form>
    </main>

    <div
        class="overlay"
        id="overlay"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalTitle"
    >
        <div class="modal">
            <h3 id="modalTitle">Verify Target Data</h3>

            <p>
                <strong>First Name:</strong>
                <span class="value" id="showFirst"></span>
            </p>

            <p>
                <strong>Last Name:</strong>
                <span class="value" id="showLast"></span>
            </p>

            <p>
                <strong>Government Position:</strong>
                <span class="value" id="showPosition"></span>
            </p>

            <p>
                <strong>Location:</strong>
                <span class="value" id="showLocation"></span>
            </p>

            <div class="actions">
                <button type="button" class="cancel" id="cancelBtn">
                    Cancel
                </button>

                <button type="button" class="confirm" id="confirmBtn">
                    Confirm &amp; Send
                </button>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById("wishForm");
        const overlay = document.getElementById("overlay");

        form.addEventListener("submit", function (event) {
            event.preventDefault();

            document.getElementById("showFirst").textContent =
                form.elements["FirstName"].value;

            document.getElementById("showLast").textContent =
                form.elements["LastName"].value;

            document.getElementById("showPosition").textContent =
                form.elements["GovPosition"].value;

            document.getElementById("showLocation").textContent =
                form.elements["Location"].value || "(not provided)";

            overlay.style.display = "flex";
        });

        document.getElementById("cancelBtn").addEventListener("click", function () {
            overlay.style.display = "none";
        });

        document.getElementById("confirmBtn").addEventListener("click", function () {
            HTMLFormElement.prototype.submit.call(form);
        });
    </script>
</body>
</html>
