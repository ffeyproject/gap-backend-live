<?php
use yii\helpers\Html;
?>

<div class="modal fade" id="modalPetunjukRetur" tabindex="-1" role="dialog" aria-labelledby="modalPetunjukReturLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 6px;">
            <div class="modal-header bg-primary" style="border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 12px 20px;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9; color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalPetunjukReturLabel" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-info-circle"></i> Petunjuk &amp; Instruksi Penggunaan Menu Retur Buyer
                </h4>
            </div>
            <div class="modal-body" style="padding: 20px 25px; max-height: 75vh; overflow-y: auto;">
                
                <!-- Gambaran Umum -->
                <div class="callout callout-info" style="margin-bottom: 20px; border-left-width: 5px;">
                    <h5 style="font-weight: bold; margin-top: 0;"><i class="fa fa-lightbulb-o"></i> Gambaran Umum Menu</h5>
                    <p style="margin-bottom: 0;">
                        Menu ini digunakan untuk mencatat pengembalian (retur) kain dari buyer/customer ke dalam sistem gudang. 
                        Sistem mendukung penginputan dari <strong>satu atau lebih nomor WO sekaligus</strong> dalam satu proses pendaftaran dokumen retur.
                    </p>
                </div>

                <!-- Langkah 1 -->
                <div class="panel panel-default" style="border-left: 4px solid #3c8dbc; margin-bottom: 18px;">
                    <div class="panel-heading" style="background: #f8f9fa;">
                        <h4 class="panel-title" style="font-weight: bold; color: #333;">
                            <span class="badge bg-blue" style="font-size: 13px; margin-right: 6px;">Langkah 1</span> 
                            Pengisian Data Dokumen Header
                        </h4>
                    </div>
                    <div class="panel-body">
                        <table class="table table-condensed table-striped" style="margin-bottom: 0; font-size: 13px;">
                            <tbody>
                                <tr>
                                    <td style="width: 28%; font-weight: bold;">Unit Satuan</td>
                                    <td>Pilih satuan panjang kain (misal: <em>Yard</em> atau <em>Meter</em>). Pada input baru, kolom ini dapat dikosongkan agar otomatis mengikuti satuan pada surat jalan pengiriman asal.</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Jenis Gudang</td>
                                    <td>Pilih jenis gudang tujuan penyimpanan kain yang diretur (misal: <em>Gudang Fresh</em>, <em>Gudang Greige</em>, dsb).</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Tanggal Retur &amp; Dokumen</td>
                                    <td>Isi tanggal fisik barang diterima di pabrik dan tanggal surat jalan/dokumen retur dari buyer.</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">No. Dokumen</td>
                                    <td>Masukkan nomor surat jalan retur atau nomor dokumen referensi resmi dari pihak buyer.</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: bold;">Penanggung Jawab &amp; QC</td>
                                    <td>Ketikkan nama staf penerima/penanggung jawab dan nama personil QC yang memeriksa fisik kain.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Langkah 2 -->
                <div class="panel panel-default" style="border-left: 4px solid #00a65a; margin-bottom: 18px;">
                    <div class="panel-heading" style="background: #f8f9fa;">
                        <h4 class="panel-title" style="font-weight: bold; color: #333;">
                            <span class="badge bg-green" style="font-size: 13px; margin-right: 6px;">Langkah 2</span> 
                            Metode Input Item Retur (Pilih Salah Satu yang Paling Memudahkan)
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="media" style="margin-bottom: 15px;">
                            <div class="media-left">
                                <span class="label label-success" style="font-size: 13px; padding: 4px 8px;">Metode A</span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading" style="font-weight: bold; color: #00a65a;">Scan Barcode / QR Code (Paling Cepat &amp; Otomatis)</h5>
                                <ul style="padding-left: 18px; margin-bottom: 0; font-size: 13px;">
                                    <li>Letakkan kursor pada kolom <strong>Scan QR / ID Roll</strong> di tabel item.</li>
                                    <li>Gunakan scanner barcode/QR atau ketik nomor <strong>ID Roll</strong> kain lalu tekan <strong>Enter</strong>.</li>
                                    <li>Sistem secara otomatis mengisi nomor WO, Surat Jalan asal, nomor roll asal, Qty, dan Grade tanpa perlu memilih manual satu per satu.</li>
                                </ul>
                            </div>
                        </div>

                        <hr style="margin: 12px 0;">

                        <div class="media" style="margin-bottom: 15px;">
                            <div class="media-left">
                                <span class="label label-primary" style="font-size: 13px; padding: 4px 8px;">Metode B</span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading" style="font-weight: bold; color: #3c8dbc;">Tambah Banyak Roll / Qty Sekaligus (Fitur Bulk)</h5>
                                <ul style="padding-left: 18px; margin-bottom: 0; font-size: 13px;">
                                    <li>Gunakan kotak abu-abu di atas tabel: <em>"Tambah Banyak Roll / Qty untuk 1 WO"</em>.</li>
                                    <li>Pilih <strong>Nomor WO</strong> dan <strong>Surat Jalan</strong> asal pengiriman.</li>
                                    <li><strong>Roll berlabel:</strong> Centang roll yang sesuai pada daftar roll yang muncul.</li>
                                    <li><strong>Roll tanpa label/hilang:</strong> Ketikkan angka qty roll pada kotak <em>"Qty roll tidak diketahui"</em> dipisahkan oleh spasi, koma, atau enter (contoh: <code>50 48.5 52 47</code>).</li>
                                    <li>Klik tombol <strong>Tambahkan ke Item</strong>. Seluruh roll otomatis masuk sebagai baris-baris item pada tabel.</li>
                                </ul>
                            </div>
                        </div>

                        <hr style="margin: 12px 0;">

                        <div class="media">
                            <div class="media-left">
                                <span class="label label-default" style="font-size: 13px; padding: 4px 8px;">Metode C</span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading" style="font-weight: bold; color: #555;">Input Manual Baris demi Baris</h5>
                                <ul style="padding-left: 18px; margin-bottom: 0; font-size: 13px;">
                                    <li>Klik tombol hijau <strong><i class="fa fa-plus"></i></strong> di pojok kanan tabel untuk menambah baris kosong.</li>
                                    <li>Pilih WO, Surat Jalan, Roll Asal, masukkan Qty, dan tentukan Grade secara manual.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aturan & Catatan Penting -->
                <div class="panel panel-default" style="border-left: 4px solid #f39c12; margin-bottom: 10px;">
                    <div class="panel-heading" style="background: #f8f9fa;">
                        <h4 class="panel-title" style="font-weight: bold; color: #333;">
                            <span class="badge bg-yellow" style="font-size: 13px; margin-right: 6px;">Catatan Penting</span> 
                            Ketentuan Penyimpanan &amp; Validasi
                        </h4>
                    </div>
                    <div class="panel-body" style="font-size: 13px;">
                        <ul style="padding-left: 20px; margin-bottom: 0;">
                            <li style="margin-bottom: 6px;">
                                <strong>Multi-WO Otomatis:</strong> Anda dapat menginput item dari beberapa WO berbeda (selama masih dari customer yang sama). Saat form disimpan, sistem secara otomatis akan memisahkan dan membuat 1 dokumen retur per nomor WO dengan nomor dokumen yang sama.
                            </li>
                            <li style="margin-bottom: 6px;">
                                <strong>Dukungan Surat Jalan Ex-Finish:</strong> Sistem juga mendeteksi pengiriman dari modul <em>Surat Jalan Ex-Finish</em> (ditandai dengan prefix <code>[Ex-Finish]</code>). Item atau roll dari surat jalan ex-finish dapat dipilih dan di-retur seperti pengiriman reguler.
                            </li>
                            <li style="margin-bottom: 6px;">
                                <strong>Label Roll Hilang:</strong> Kolom Surat Jalan dan Roll Asal diperbolehkan dibiarkan <em>"-- Tidak diketahui --"</em> jika label pada roll fisik sudah rusak/hilang. Sistem tetap membatasi total Qty retur tidak boleh melebihi sisa riwayat pengiriman WO tersebut.
                            </li>
                            <li>
                                <strong>Penyelesaian Retur:</strong> Setelah menekan tombol <strong>Save</strong>, dokumen berstatus <em>Draft</em>. Buka halaman detail dokumen tersebut lalu klik tombol <strong>Posting</strong> untuk memvalidasi dan memasukkan kembali barang retur ke <strong>Gudang Jadi sebagai Stok Aktif</strong>.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="background: #f9f9f9; border-bottom-left-radius: 6px; border-bottom-right-radius: 6px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
