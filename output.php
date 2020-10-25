<?php
include "functions.php";

// записуємо шлях до файлу з яким будемо працювати
$file = "data/data.txt";

// встановлюємо для всіх форм прапорець який має перемикатись при наявності помилок
$error_flags = array();
$error_flags = [false, false, false];

// оголошуємо масив з варіантами повідомлень про помилку
$error_messages = array();
$error_messages = [
    "Введена не валідна електронна адреса!!!!",
    "Поле не заповнене !!!",
    "Електронна адреса вже існує у списку!!!",
    "Неможливо змінити адресу якої немає в списку!!!",
    "Неможливо видалити адресу якої немає в списку!!!"
];

// оголошуємо змінну, що прийматиме значення конкретного повідомлення про помилку
$error_message = "";

// встановлюємо для всіх форм прапорець який має перемикатись при успішності операції
$success_flags = array();
$success_flags = [false, false, false];

// оголошуємо змінну, що прийматиме значення конкретного повідомлення про успіх
$success_message = "";

// оголошуємо масив з варіантами повідомлень про успіх операції
$success_messages = array();
$success_messages = [
    "Адресу успішно додано!",
    "Адресу успішно змінено!",
    "Адресу успішно видалено!"
];

// оголошуємо змінні полів форм, щоб зберігати їх значення після відправки форми
$new_email = "";
$old_email = "";
$changed_email = "";
$delete_email = "";
// перевіряємо чи не пустий масив GET
if (!empty($_GET)) {
    // перевіряємо чи відправлена конкретна форма
    if (array_key_exists('new_email_submit', $_GET)) {
        $new_email = trim($_GET["new_email"]);
        // перевіряємо чи пусте введене поле
        // якщо так, виконується код в середині функції isEmptyField
        // якщо ні, продовжуємо перевірку
        if (!isEmptyField($new_email, 0)) {
            // перевіряємо валідність введеної адреси
            // якщо адреса не валідна виводимо повідомлення про помилку
            if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                $error_flags[0] = true;
                $error_message = $error_messages[0];
            } else {
                // перевіряємо чи додалась адреса у файл
                // якщо ні виводимо повідомлення, що дана адреса вже існує у файлі
                // інакше виводимо повідомлення про успіх операції
                if (!addNewEmail($new_email, $file)) {
                    $error_flags[0] = true;
                    $error_message = $error_messages[2];
                } else {
                    $success_flags[0] = true;
                    $success_message = $success_messages[0];
                }
            }
        }
    }
    if (array_key_exists('changed_email_submit', $_GET)) {
        $old_email = trim($_GET["old_email"]);
        $changed_email = trim($_GET["changed_email"]);
        isEmptyField($old_email, 1);
        if (!isEmptyField($changed_email, 1)) {
            if (!filter_var($changed_email, FILTER_VALIDATE_EMAIL)) {
                $error_flags[1] = true;
                $error_message = $error_messages[0];
            } else {
                // якщо адреси яку хочуть змінити немає у файлі видаємо повідомлення про помилку
                if (!inFile($old_email, $file)) {
                    $error_flags[1] = true;
                    $error_message = $error_messages[3];
                } else {
                    if (changeEmail($old_email, $changed_email, $file)) {
                        $success_flags[1] = true;
                        $success_message = $success_messages[1];
                    }
                }
            }
        }
    }
    if (array_key_exists('delete_email_submit', $_GET)) {
        $delete_email = trim($_GET["delete_email"]);
        isEmptyField($delete_email, 2);
        if (!deleteEmail($delete_email, $file)) {
            $error_flags[2] = true;
            $error_message = $error_messages[4];
        } else {
            $success_flags[2] = true;
            $success_message = $success_messages[2];
        }
    }
}

// перевіряємо чи заповнене поле, якщо поле не заповнено виводимо повідомлення про помилку
function isEmptyField(string $field_name, int $form_number): bool
{
    global $error_flags;
    global $error_message;
    global $error_messages;
    if (strlen($field_name) == 0) {
        $error_flags[$form_number] = true;
        $error_message = $error_messages[1];
        return true;
    }
    return false;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email list</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <section>
        <div>
            <form action="output.php" method="get">
                <label for="add">
                    Додати нову електронну адресу:
                </label>
                <input type="text" name="new_email" id="add" value="<?= $new_email ?>">
                <input type="submit" name="new_email_submit" value="Додати">
                <? if($error_flags[0] == true):?>
                <p class="error"><?= $error_message ?></p>
                <? endif;?>
                <? if($success_flags[0] == true):?>
                <p class="success"><?= $success_message ?></p>
                <? endif;?>
            </form>
            <form action="output.php" method="get">
                <p>Змінити електронну адресу:</p>
                <label for="old">
                    Стара адреса:
                </label>
                <input type="text" name="old_email" id="old" value="<?= $old_email ?>">
                <label for="changed">
                    Нова адреса:
                </label>
                <input type="text" name="changed_email" id="changed" value="<?= $changed_email ?>">
                <input type="submit" name="changed_email_submit" value="Змінити">
                <? if($error_flags[1] == true):?>
                <p class="error"><?= $error_message ?></p>
                <? endif;?>
                <? if($success_flags[1] == true):?>
                <p class="success"><?= $success_message ?></p>
                <? endif;?>
            </form>
            <form action="output.php" method="get">
                <label for="delete">
                    Видалити електронну адресу:
                </label>
                <input type="text" name="delete_email" id="delete" value="<?= $delete_email ?>">
                <input type="submit" name="delete_email_submit" value="Видалити">
                <? if($error_flags[2] == true):?>
                <p class="error"><?= $error_message ?></p>
                <? endif;?>
                <? if($success_flags[2] == true):?>
                <p class="success"><?= $success_message ?></p>
                <? endif;?>
            </form>
        </div>
        <div>
            <table>
                <caption>Список електронних адрес</caption>
                <tr>
                    <th>№</th>
                    <th>Електронна адреса</th>
                </tr>
                <?php
                $i = 1; // іттератор нумерації адрес
                $printList = getEmailList($file);
                foreach ($printList as $email) {
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $email ?></td>
                    </tr>
                <?php }; ?>
            </table>
        </div>
    </section>
</body>

</html>