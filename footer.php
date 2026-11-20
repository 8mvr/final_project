<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <div class="brand light">hearth</div>
            <p>A little care. A lasting connection.<br>Helping pets and people find their way home.</p>
        </div>
        <div>
            <h4>Find your way</h4>
            <a href="dashboard.php">Find a pet</a>
            <?php if (($_SESSION["role"] ?? "") === "admin") { ?><a href="addpet.php">Add a pet</a><?php } ?>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="footer-base">© <?= date("Y") ?> Hearth. Every home starts with a hello.</div>
</footer>