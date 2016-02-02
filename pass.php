<?header('Content-Type: text/html; charset=utf-8');
//вход под дмином
session_start();
?>

<?
if(isset($_POST['pass'])){
	if($_POST['pass'] == 'Bumerang1982'){
		$_SESSION['Guest_id']['id_user']='557';
		//eader('location:index.php');
		echo '<script>window.location.href = "index.php";</script>';
		//echo "ок";
	}
}



?>

	
<form id='AvtorForm' method='post'  >
	<input type='password' name='pass'  />
	<input type='submit' value='ок' />
</form>

