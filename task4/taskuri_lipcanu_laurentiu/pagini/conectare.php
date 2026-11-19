<?php
require_once dirname(__DIR__) . '/config.php';
if (user()) redirect('pagini/solicitari.php');
$titluPagina = 'Conectare';
$paginaActiva = 'conectare';
$email = '';
$errors = [];
if (isPost()) {
    protectPost();
    $email = field($_POST, 'email');
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors['email'] = 'Introdu o adresă de e-mail validă.';
    if (!is_string($password) || $password === '' || strlen($password) > 72 || str_contains($password, "\0")) $errors['password'] = 'Introdu o parolă validă.';
    if (!$errors) {
        $account = query('SELECT id, password_hash FROM users WHERE email = ?', [$email])->fetch();
        if ($account && password_verify($password, $account['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION = ['user_id' => (int) $account['id']];
            flash('Te-ai conectat cu succes.');
            redirect('pagini/solicitari.php');
        }
        $errors['password'] = 'E-mailul sau parola nu sunt corecte.';
    }
    formInvalid($errors);
}
require dirname(__DIR__) . '/header.php';
?>
<div class="page-intro"><p class="eyebrow">Contul meu</p><h1>Conectare</h1><p>Intră în cont pentru a trimite și urmări solicitările tale.</p></div>
<section class="content-panel compact-panel">
    <form method="post" class="field-form" novalidate>
        <?php csrfField(); ?>
        <div class="field"><label for="email">E-mail</label><input id="email" name="email" type="email" maxlength="190" value="<?= e($email) ?>" autocomplete="username" required><?php formError($errors, 'email'); ?></div>
        <div class="field"><label for="password">Parolă</label><input id="password" name="password" type="password" autocomplete="current-password" required><?php formError($errors, 'password'); ?></div>
        <button class="button primary" type="submit">Conectează-te</button>
    </form>
</section>
<?php require dirname(__DIR__) . '/footer.php'; ?>
