<!DOCTYPE html>
<html>
<head>
	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<script src="{{ asset('js/app.js') }}" defer></script>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.3/semantic.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.3/semantic.js" defer></script>
	<title>Messenger</title>
</head>
<body>
	<div id="app">
		<div style="max-width: 1200px; margin: 0 auto;">
			<messenger :owner="{{ auth()->user() }}"></messenger>
		</div>
	</div>
</body>
</html>