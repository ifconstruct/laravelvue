<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel Vue</title>
        <!-- Styles -->
		<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css','resources/js/app.js']); ?>
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>

<?php /**PATH C:\OSPanel\domains\laravelvue\resources\views/app.blade.php ENDPATH**/ ?>