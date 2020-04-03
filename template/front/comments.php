<div class="owl-carousel comments-plugin-carousel owl-theme">
    <?php
    $data = (isset($data)) ? $data : [];
    foreach ($data as $datum) {
        ?>
        <div class="item">
            <div>
                <img class="avatar" src="https://i.pravatar.cc/150?img=3" alt="">
            </div>
            <div class="content-text">
                <?php echo $datum->content ?>
            </div>
            <div class="name"><?php
                $user =  json_decode($datum->extra,1);
                echo $user['name']['first'].' '.$user['name']['last']
                ?></div>
        </div>
        <?php
    }
    ?>
</div>

