<!DOCTYPE html>
<html lang="en">
<head>
 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <title><?php 
                public $page_title = "~Hello World~";
                echo $page_title; 
            ?>
    </title>
  
</head>
<body>

    <div class="container">
        <?php
        echo "<h1>{$page_title}</h1>";
        ?>
    </div>
</body>