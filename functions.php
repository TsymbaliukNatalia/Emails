<?php

// функція повертає вміст файлу у вигляді масиву
function getEmailList(string $file) : array {
    $file = "data/data.txt";
    $emails = file($file);
    foreach ($emails as $key => $email) {
        $emails[$key] = trim($email);
    }
    return $emails;
}

// функція записує нову електронну адресу у файл
function addNewEmail(string $email, string $file) : bool {
    if(!inFile($email, $file)){
        if(filesize($file) == 0){
            file_put_contents($file, $email, FILE_APPEND | LOCK_EX);
            return true;
        } else {
            file_put_contents($file, "\n".$email, FILE_APPEND | LOCK_EX);
            return true;
        } 
    }
    return false;   
}

// функція перевіряє чи є значення у файлі
function inFile(string $email, string $file) : bool{
    $emails = getEmailList($file);
    return in_array($email, $emails);
}

// функція заміняє стару електронну адресу на нову
function changeEmail(string $old_email, string $new_email, string $file) : bool {
    $email_list = getEmailList($file);
    $emails_str = "";
    if(!in_array($old_email, $email_list)){
        return false;
    } else {
        foreach($email_list as $key => $email){
            if ($key == 0) {
                if($email == $old_email){
                    $emails_str = $emails_str.$new_email;
                } else {
                    $emails_str = $emails_str.$email;
                }  
            } else {
                if($email == $old_email){
                    $emails_str = $emails_str."\n".$new_email;
                } else {
                    $emails_str = $emails_str."\n".$email;
                }  
            }
        }
        file_put_contents($file, $emails_str);
        return true; 
    }
}

// функція видаляє електронну адресу з файлу
function deleteEmail(string $delete_email, string $file) : bool {
    $email_list = getEmailList($file);
    $emails_str = "";
    if(!in_array($delete_email, $email_list)){
        return false;
    } else {
        foreach($email_list as $key => $email){
            if ($key == 0) {
                if($email != $delete_email){
                    $emails_str = $emails_str.$email;
                } 
            } else {
                if($email != $delete_email){
                    $emails_str = $emails_str."\n".$email;
                } 
            }
        }
        file_put_contents($file, $emails_str);
        return true; 
    }
}


