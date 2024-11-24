<header> 
    <div class="logo">
        <img src="../images/logo.png" width="90px">
    </div>
</header>
<div class="sidebar-container">
    <div class="sidebar">
        <?php 
            $select_profile = $conn->prepare("SELECT * FROM `user` WHERE user_id = ?");
            $select_profile->execute([$user_id]);

            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        <div class="profile">
            <p><?= $fetch_profile['name']; ?></p>
            <p>(Kho)</p>
        </div>
        <?php ?>
        <!-- <h5>Menu</h5> -->
        <div class="navbar">
            <ul>
                <li onclick="window.location.href='task.php'"><i class="bi bi-card-checklist" style="margin-right: .7rem;"></i>Nhiệm vụ</li>
            </ul> 
        </div>
        <div class="navbar">
            <ul>
                <li onclick="window.location.href='all_stock.php'"><i class="bi bi-bag-plus-fill" style="margin-right: .7rem;"></i>Sản phẩm</li>
            </ul> 
        </div>
    </div>
</div>