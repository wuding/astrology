<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<style>
textarea {
	width: 100%;
}
	</style>
</head>

<body>
<main class="main-page">
	<form method="post">
		<textarea name="cookie" rows="10"><?=htmlspecialchars($cookie)?></textarea>
		<button type="submit">submit</button>
	</form>
</main>
</body>
</html>
