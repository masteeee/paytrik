<?php 

require_once "koneksi.php";

if( !isset($_SESSION['user']) ){
	header("Location: index.php");
}

if( $_SESSION['level'] != "Admin" && $_SESSION['level'] != "Petugas" ){
	header("Location: index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> Paytrik - Validasi Pendaftaran </title>
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container bflex">
	<div class="head cflex">
		<a class="btn-menu">
			<div class="icon icon1"></div>
			<div class="icon icon2"></div>
			<div class="icon icon3"></div>
		</a>
	</div>
	<div class="lcontainer">
		<ul class="menu">
			<li class="title-menu"><a href="dashboard.php"><span> Dashboard </span></a></li>
			<?php if( $_SESSION['level'] == 'Admin' ){ ?>
				<li class="title-menu"><a href="inputtarif.php"><span> Input </span></a>
					<ul class="drop">
						<li><a href="inputtarif.php"> Tarif </a></li>
						<li><a href="inputpetugas.php"> Petugas </a></li>
						<li><a href="inputpelanggan.php"> Pelanggan </a></li>
					</ul>
				</li>
				<li class="title-menu"><a href="tampiltarif.php"><span> Tampil </span></a>
					<ul class="drop">
						<li><a href="tampiltarif.php"> Tarif </a></li>
						<li><a href="tampilpetugas.php"> Petugas </a></li>
						<li><a href="tampilpelanggan.php"> Pelanggan </a></li>
					</ul>
				</li>
			<?php } ?>
			<li class="title-menu"><a href="inputtagihan.php"><span> Tagihan </span></a></li>
			<li class="title-menu"><a href="inputpembayaran.php"><span> Pembayaran </span></a></li>
			<li><a class="btn-logout"><span> Logout </span></a></li>
		</ul>
	</div>
	<div class="rcontainer">
		<div class="header bflex">
			<a href="dashboard.php">
				<img src="image/logo.png">
			</a>
			<div class="title-user">
				<span> <?php echo $_SESSION['level'] ?> </span>
			</div>
		</div>
		<div class="wrapper">
			<div class="logout">
				<div class="screen-logout"></div>
				<div class="delimiter">
					<div class="clogout">
						<img src="export/logout.png">
						<h1> Apakah anda ingin Logout ? </h1>
						<p> Tekan tombol logout untuk keluar dari halaman atau tekan tombol batalkan untuk membaltakannya. </p>
						<div class="cflex">
							<a href="logout.php" class="btn-hapus"> Logout </a>
							<a class="close-logout"> Batalkan </a>
						</div>
					</div>
				</div>
			</div>
			<div class="delimiter">
				<div class="page">
					<div class="bflex move">
						<h1> Validasi Pendaftaran Pelanggan </h1>
					</div>
				</div>
				<p class="error"></p>
				<?php 
					$tbl = mysqli_query($connect, "SHOW TABLES LIKE 'tbpendaftaran'");
					if( mysqli_num_rows($tbl) == 0 ){
						// Jika tabel belum ada
						echo "<script>
							var error = document.getElementsByClassName('error')[0];
							error.style.display='block';
							error.innerHTML = 'Belum ada pendaftaran.';
						</script>";
					} else {
						$sql = mysqli_query($connect, "SELECT * FROM tbpendaftaran ORDER BY createdAt DESC");
						if( mysqli_num_rows($sql) > 0 ){
				?>
				<div class="wmax max">
					<table>
						<tr>
							<td><span> No </span></td>
							<td><span> No Pelanggan </span></td>
							<td><span> No Meter </span></td>
							<td><span> Nama </span></td>
							<td><span> Telepon </span></td>
							<td><span> Alamat </span></td>
							<td><span> Status </span></td>
							<td><span> Aksi </span></td>
						</tr>
						<?php $i=1; while($row = mysqli_fetch_assoc($sql)){ ?>
						<tr>
							<td><p class="number"> <?php echo $i++; ?> </p></td>
							<td><p> <?php echo $row['noPelanggan']; ?> </p></td>
							<td><p> <?php echo $row['noMeter']; ?> </p></td>
							<td><p> <?php echo $row['namaLengkap']; ?> </p></td>
							<td><p> <?php echo $row['telp']; ?> </p></td>
							<td><p> <?php echo $row['alamat']; ?> </p></td>
							<td><p> <?php echo $row['status']; ?> </p></td>
							<td>
								<form method="POST" class="cflex" style="gap:8px;">
									<input type="hidden" name="id" value="<?php echo $row['id']; ?>" />
									<button type="submit" name="approve" class="btn-edit"> Setujui </button>
									<button type="submit" name="reject" class="btn-hapus"> Tolak </button>
								</form>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
				<?php 
						} else {
							echo "<script>
								var error = document.getElementsByClassName('error')[0];
								error.style.display='block';
								error.innerHTML = 'Tidak ada data pendaftaran.';
							</script>";
						}
					}
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="script.js"></script>
</body>
<?php include 'footer.php'; ?>
</html>
<?php 

if(isset($_POST['approve'])){
	$id = (int)$_POST['id'];
	$find = mysqli_query($connect, "SELECT * FROM tbpendaftaran WHERE id=$id");
	if(mysqli_num_rows($find) > 0){
		$d = mysqli_fetch_assoc($find);
		// Cek apakah user sudah ada
		$cek = mysqli_query($connect, "SELECT username FROM tblogin WHERE username='".$d['noPelanggan']."'");
		if(mysqli_num_rows($cek) == 0){
			// Insert ke tblogin dan tbpelanggan
			$ins1 = mysqli_query($connect, "INSERT INTO tblogin VALUES('', '".$d['noPelanggan']."', '".$d['password']."', '".$d['namaLengkap']."', 'Pelanggan')");
			if($ins1){
				$ins2 = mysqli_query($connect, "INSERT INTO tbpelanggan VALUES('', '".$d['noPelanggan']."', '".$d['noMeter']."', '".$d['kodeTarif']."', '".$d['namaLengkap']."', '".$d['telp']."', '".$d['alamat']."')");
				if($ins2){
					mysqli_query($connect, "UPDATE tbpendaftaran SET status='Disetujui' WHERE id=$id");
					echo "<script>alert('Pendaftaran disetujui. Akun sudah aktif.');location.href='validasipelanggan.php';</script>";
				} else {
					echo "<script>alert('Gagal membuat data pelanggan.');location.href='validasipelanggan.php';</script>";
				}
			} else {
				echo "<script>alert('Gagal membuat akun login.');location.href='validasipelanggan.php';</script>";
			}
		} else {
			mysqli_query($connect, "UPDATE tbpendaftaran SET status='Disetujui' WHERE id=$id");
			echo "<script>alert('Pendaftaran sudah pernah disetujui sebelumnya.');location.href='validasipelanggan.php';</script>";
		}
	}
}

if(isset($_POST['reject'])){
	$id = (int)$_POST['id'];
	mysqli_query($connect, "UPDATE tbpendaftaran SET status='Ditolak' WHERE id=$id");
	echo "<script>alert('Pendaftaran ditolak.');location.href='validasipelanggan.php';</script>";
}

?>

