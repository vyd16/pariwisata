<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= base_url() ?>" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-plane"></i>
                    </div>
                    <span>TravelMate</span>
                </a>
                <p>Jasa pariwisata terpercaya dengan berbagai pilihan paket wisata menarik untuk liburan tak terlupakan
                    bersama keluarga dan orang tersayang.</p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <div>
                <h4>Navigasi</h4>
                <ul class="footer-links">
                    <li><a href="<?= base_url() ?>">Beranda</a></li>
                    <li><a href="<?= base_url() ?>#paket">Paket Wisata</a></li>
                    <li><a href="<?= base_url() ?>#tentang">Tentang Kami</a></li>
                    <li><a href="<?= base_url() ?>#kontak">Kontak</a></li>
                </ul>
            </div>

            <div>
                <h4>Layanan</h4>
                <ul class="footer-links">
                    <li><a href="#">Paket Tour</a></li>
                    <li><a href="#">Rental Mobil</a></li>
                    <li><a href="#">Tiket Pesawat</a></li>
                    <li><a href="#">Hotel & Penginapan</a></li>
                </ul>
            </div>

            <div>
                <h4>Kontak</h4>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> Jl. Pariwisata No. 123, Jakarta</li>
                    <li><i class="fas fa-phone"></i> +62 812 3456 7890</li>
                    <li><i class="fas fa-envelope"></i> info@travelmate.id</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy;
                <?= date('Y') ?> TravelMate. All rights reserved.
            </p>
            <p>Made with <i class="fas fa-heart text-danger"></i> in Indonesia</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="<?= base_url('assets/js/script.js') ?>"></script>
</body>

</html>