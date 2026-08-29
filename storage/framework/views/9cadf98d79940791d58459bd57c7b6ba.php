<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo e($title ?? config('app.name', 'RozeHub')); ?></title>
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('images/rozehub-icon.png')); ?>">
<link rel="apple-touch-icon" href="<?php echo e(asset('images/rozehub-icon.png')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/rozehub.css')); ?>?v=20260828-novaos-selection-1">
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body><?php echo e($slot); ?><?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?></body>
</html>
<?php /**PATH /home/firoze/Downloads/RozeHub-Ecosystem-Extensions-Full-Updated/RozeHub-Ecosystem-Extensions-Updated/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>