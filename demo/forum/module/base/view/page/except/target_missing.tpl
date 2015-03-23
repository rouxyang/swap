<body>
    <?php if ($msg === ''): ?>
        <p>404 not found</p>
    <?php else: ?>
        <p><?php echo $msg; ?></p>
    <?php endif; ?>
</body>
