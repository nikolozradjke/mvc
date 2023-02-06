<h1>Index</h1>
<p>welcome <?php echo $name ?></p>

<?php
for($i = 1; $i <= $pages; $i ++){
    echo "
        <a href='?page=$i'>$i</a>
    ";
}

foreach($posts as $post){
    echo "
        <img src='". $post['image'] ."' width='20%'>
    ";
}
?>