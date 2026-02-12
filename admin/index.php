<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // atur nilai menjadi 1 jika di publish ke real public server
ini_set('session.use_strict_mode', 1);

session_start();

require_once '../lib/auth.php';
require_once '../lib/functions.php';

requireAuth();
if (getUserRole() !== 'admin') {
    redirect('../login.php');
}
?>

<?php include '../views/'.$THEME.'/header.php'; ?>
<?php include '../views/'.$THEME.'/sidebar.php'; ?>
<!-- <?php include '../views/'.$THEME.'/topnav.php'; ?> -->
<?php include '../views/'.$THEME.'/upper_block.php'; ?>
<?php include '../views/'.$THEME.'/admin_content.php'; ?>

<div class="j">
    <h2>Welcome, <?php echo ucfirst($_SESSION['role']); ?>!</h2>
    <p>You are logged in as: <strong><?php echo $_SESSION['username']; ?></strong></p>
    
    <a href="../logout.php" class="btn btn-outline-danger">Logout</a>
</div>
<script>
    setTimeout(() => {
        window.location.href = "../dashboard/index.php";
    }, 2000);
</script>

<style>
    .j{
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 50%;
        margin-left: 20px;
    }
</style>
<?php include '../views/'.$THEME.'/lower_block.php'; ?>
<?php include '../views/'.$THEME.'/footer.php'; ?>
