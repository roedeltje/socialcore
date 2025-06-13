<?php 
// themes/default/partials/messages.php
// Herbruikbare berichten weergave voor alle thema pagina's

// Success berichten
if (isset($_SESSION['success'])): 
?>
    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php 
if (isset($_SESSION['success_message'])): 
?>
    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg">
        <?= htmlspecialchars($_SESSION['success_message']) ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php 
// Error berichten
if (isset($_SESSION['error'])): 
?>
    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php 
if (isset($_SESSION['error_message'])): 
?>
    <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
        <?= htmlspecialchars($_SESSION['error_message']) ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>