<?php $isAdminFooter = ($_SESSION["role"] ?? "") === "admin"; ?>
<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <div class="footer-brand">Hearth</div>
            <p>A little care. A lasting connection.<br>Helping pets and people find their way home.</p>
        </div>
    </div>
    <div class="footer-base">&copy; <?php echo date("Y"); ?> Hearth. Every home starts with a hello.</div>
</footer>