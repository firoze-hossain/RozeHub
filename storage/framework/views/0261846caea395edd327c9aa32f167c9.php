<?php $__env->startSection('content'); ?>
<div class="admin-page-head"><div><span>ADMINISTRATOR</span><h2>Account & security</h2><p>Update the account used to access the RozeHub control center.</p></div></div>
<form class="admin-form-card" method="POST" action="<?php echo e(route('admin.account.update')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<div class="form-grid two"><label>Name<input name="name" value="<?php echo e(old('name',auth()->user()->name)); ?>" required></label><label>Email<input type="email" name="email" value="<?php echo e(old('email',auth()->user()->email)); ?>" required></label></div>
<label>Current password<input type="password" name="current_password" required autocomplete="current-password"></label>
<div class="form-grid two"><label>New password <span class="hint">Minimum 12 characters</span><input type="password" name="password" autocomplete="new-password"></label><label>Confirm new password<input type="password" name="password_confirmation" autocomplete="new-password"></label></div>
<div class="form-actions"><button class="admin-primary" type="submit">Save security settings</button></div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/firoze/Downloads/RozeHub-Ecosystem-Extensions-Full-Updated/RozeHub-Ecosystem-Extensions-Updated/resources/views/admin/account.blade.php ENDPATH**/ ?>