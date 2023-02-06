<?php
    if(!empty($model->errors)){
        foreach($model->errors as $key => $error){
            echo "<ul>";
            if(!empty($error)){
                foreach($error as $value){
                    echo "<li>$key - $value</li>";
                }
            }
            echo "</ul>";
        }
    }
?>
<form method="post" action="/register" enctype="multipart/form-data">
    <input type="text" name="firstname">
    <input type="text" name="lastname">
    <input type="email" name="email">
    <select name="role">
        <option value="1">Admin</option>
        <option value="2">Editor</option>
    </select>
    <input type="password" name="password">
    <input type="password" name="passwordConfirm">
    <input type="file" name="image">
    <button type="submit">Register</button>
</form>