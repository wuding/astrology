
<body>

<?php
include __DIR__ . '/../_helper/tongji.php';
?>

<script>
<?php if ($redirect || !$location) { echo '//'; } ?> setTimeout("location.href='<?=$location?>'", 1000);
</script>
</body>
