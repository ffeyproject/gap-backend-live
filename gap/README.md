<h1 align="center">
    APLIKASI OPERASIONAL PRODUKSI PT. GAJAH ANGKASA PERKASA BANDUNG
</h1>

<p>
Aplikasi pencatatan produksi.
</p>

Kebutuhan Perangkat Lunak
------------
> **Penting: Pastikan server sudah memenuhi kriteria berikut ini.**
- Webserver Nginx, here the doc: https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04
- Database Postgres, link of doc: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-postgresql-on-ubuntu-18-04
- Composer, link of doc: https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04
- Git, link of doc: https://www.digitalocean.com/community/tutorials/how-to-install-git-on-ubuntu-18-04
- Gmail account for mailer service, doc: https://hotter.io/docs/email-accounts/secure-app-gmail/

Persiapan
------------
### Buat database untuk aplikasi ini, gunakan nama database default
```
sudo -u postgres createdb gap_software
```

Instalasi
------------
### Clone repository, sebaiknya menggunakan ssh. Untuk penjelasan mengenai koneksi git menggunakan ssh, silahkan baca link dibawah ini
```
https://help.github.com/en/github/authenticating-to-github/connecting-to-github-with-ssh
```
Setelah ssh selesai di setting, minta kepada pemilik repo ini untuk mendaftarka ssh server anda, agar bisa melakukan clone.
Selanjutnya, masuk ke nginx web directory dengan mengetik perintah berikut:
```
cd /var/www/html
```

Lalu clone repo ini menggunakan ssh
```
git clone git@github.com:cooljar/gap.git
```
Jika muncul pesan kesalahan, kemungkinan ssh server anda belum didaftarkan oleh pemilik repo ini.

Inisialisasi Project
---
Move to root project directory by type following command
```
cd gap
```
Update project dependencies using composer
```
composer update
```
Give writetable by all permission to /backend/backups directory, direktori ini digunakan untuk menyimpan file dump backup database.
```
chmod -R 777 backend/backups
chmod -R 777 backend/web/uploads
chmod -R 777 frontend/web/uploads
```

Chose prefer environment by run following command
```
php init
```
will return output:
```
[0] Development
[1] Production
   
Your choice [0-1, or "q" to quit]
```
select env option by typing the number and press enter

Setup database  and mailer connection, update file common/config/main-local.php then write database connection and mailer setting.
```
nano common/config/main-local.php
```
lalu ubah isinya
```
<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=localhost;dbname=gap_software',
            'username' => 'cooljar',
            'password' => 'ikhjwe728gs2432j2s',
            'charset' => 'utf8',
        ],
    ],
];
```
Sesuaikan isinya dengan data server dan akun email anda.

Run initial migration by typing following command
```
php yii migrate/to m190124_110200_add_verification_token_column_to_user_table
```

Run RBAC migration by typing following command
```
php yii migrate --migrationPath=@yii/rbac/migrations
```

Run all migration by typing following command
```
php yii migrate
```
Nantinya akan terlihat tabel apa saja yang berhasil dibuat pada console window. Migrasi terakhir dengan nama "m191230_999999_0_insert_default_data" merupakan migrasi untuk mengisi data sample kedalann database, sehingga aplikasi mempunyai data contoh sebagai alat bantu.

### Setup ubuntu nginx server for backend app
first create new conf file for backend app under /etc/nginx/sites-available
example file name: gapSoftwareBackend
```
sudo nano /etc/nginx/sites-available/gapSoftwareBackend
```
insert following content
```
server {
	listen 80;
	#listen [::]:80 default_server;

	root /var/www/html/gap/backend/web;

	# Add index.php to the list if you are using PHP
	index index.php index.html index.htm index.nginx-debian.html;

	server_name gap2.cooljarsoft.com;

	location / {
		# First attempt to serve request as file, then
		# as directory, then fall back to displaying a 404.
		try_files $uri $uri/ =404;
	}

	location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
silahkan rubah isinya sesuai dengan konfigurasi server dan domain anda.

symbolic link from your new server block configuration file (in the /etc/nginx/sites-available/ directory) to the /etc/nginx/sites-enabled/ directory:
```
sudo ln -s /etc/nginx/sites-available/gapSoftwareBackend /etc/nginx/sites-enabled/
```
Test your new configuration file for syntax errors by typing:
```
sudo nginx -t
```
When you are ready, reload Nginx to make the necessary changes:
```
sudo systemctl reload nginx
```

CATATAN AKUN-AKUN
-------------------
Semua catatan tentang akun-akun yang terkait dengan aplikasi ini, ada pada file note.txt.

INFORMASI SERVER
-------------------
```
ssh cooljar@68.183.234.145
pass ssh git pull: mautauaja

cd /var/www/html/gap
cd /var/www/html/test/gap

alter table trn_gudang_jadi alter column qty type decimal using qty::decimal;
```

RESTORE DATABASE
------------------------
```
dropdb -U cooljar gap_software
createdb -U cooljar gap_software
psql -U cooljar gap_software < backend/backups/gap_software_test_full_default_2022-06-24_14-08-57.sql
```

PR revisi
---------------------------
Pembuatan Menu Rekap Stock Greige

1. Halaman Index Rekap Stock Greige seperti yang ada di table perancangan, kolom yg bisa di search: Tanggal, nomer Lot, Motif, Keterangan, Kondigi Greige. Data yang muncul yang di search saja.
2. Untuk Isi data dari Export seperti view di excel semua motif yang muncul.

Query yang perlu dieksekusi di server
------------------------
alter table mst_handling alter column ket_washing drop not null;
alter table mst_handling alter column ket_washing drop default;
alter table mst_handling alter column ket_wr drop not null;
alter table mst_handling alter column ket_wr drop default;