<html>
<head>
    <script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<title>
    hash
</title>
<body>
{!! isset($message) ? $message : ''!!}

<form action="/" method="POST" id="form_hash">
    <input type="text" name="id_user" value="">
    <input type="hidden" name="hash" id="hash" value="">
    <button type="button" onclick="enviar_formulario()">Crear hash</button>
</form>

<script>

    function enviar_formulario() {
        $.ajax({
            type    : 'POST',
            url     : '{{url('hash')}}',
            success : function (response) {
                $("#hash").val(response);
                $( "#form_hash" ).submit();
            }
        });
    }
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });


</script>
</body>
</html>
