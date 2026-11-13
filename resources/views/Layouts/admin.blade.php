<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gamification</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div>
        @yield('header')
    </div>
    <div>
        @yield('container')
    </div>
@livewireScripts
</body>
<script>
    function generateCode() {
        const result = Math.random().toString(36).substring(2, 7);
        document.getElementById('class_code').value = result;
    }

    function previewImage(input, previewId) {
        var preview = document.querySelector(`#${previewId}`);
        var file = input.files[0];
        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
        }
    }

    document.getElementById('question_image').addEventListener('change', function () {
        previewImage(this, 'previewQuestion');
    });
    
    document.getElementById('option_1_image').addEventListener('change', function () {
        previewImage(this, 'previewOption1');
    });
    
    document.getElementById('option_2_image').addEventListener('change', function () {
        previewImage(this, 'option_2_image');
    });
    
    document.getElementById('option_3_image').addEventListener('change', function () {
        previewImage(this, 'option_3_image');
    });
    
    document.getElementById('option_4_image').addEventListener('change', function () {
        previewImage(this, 'option_4_image');
    });

</script>
</html>