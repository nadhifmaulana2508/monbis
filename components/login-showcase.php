<?php
/**
 * Showcase edukasi login Monbis.
 * Dipisahkan dari halaman login agar konten carousel dapat dirawat dan dipakai ulang.
 */
if (!function_exists('mb_render_login_showcase')) {
    function mb_render_login_showcase(): void
    {
        ?>
        <aside class="mb-login-showcase" aria-label="Informasi pembelajaran Monbis">
            <div class="mb-login-showcase__glow mb-login-showcase__glow--one" aria-hidden="true"></div>
            <div class="mb-login-showcase__glow mb-login-showcase__glow--two" aria-hidden="true"></div>

            <div class="mb-login-showcase__content">
                <div class="mb-login-showcase__eyebrow">
                    <span class="mb-login-showcase__eyebrow-icon" aria-hidden="true">
                        <img src="./img/monbis-icon.webp?v=1" alt="">
                    </span>
                    <span>MONBIS INSIGHT</span>
                </div>

                <div class="mb-login-showcase__intro">
                    <span class="mb-login-showcase__overline">Budaya risiko &amp; pembelajaran</span>
                    <h2>Tetap waspada,<br><span>tetap bertumbuh.</span></h2>
                    <p>Waspada, monitor, dan belajar dari data untuk mengambil keputusan lebih awal.</p>
                </div>

                <div class="mb-login-carousel" data-login-carousel>
                    <article class="mb-login-slide is-active" data-login-slide>
                        <div class="mb-login-slide__topline" aria-hidden="true"><span></span></div>
                        <span class="mb-login-slide__tag">PENCEGAHAN FRAUD</span>
                        <h3>Kenali tanda-tanda tidak wajar sejak awal.</h3>
                        <p>Verifikasi data, jaga pemisahan tugas, dan laporkan penyimpangan kecil yang berulang.</p>
                    </article>

                    <article class="mb-login-slide" data-login-slide>
                        <div class="mb-login-slide__topline" aria-hidden="true"><span></span></div>
                        <span class="mb-login-slide__tag">MONITORING DINI</span>
                        <h3>Risiko yang terlihat lebih cepat, lebih mudah dikendalikan.</h3>
                        <p>Pantau tren kredit, tunggakan, dan realisasi sebelum berkembang menjadi masalah besar.</p>
                    </article>

                    <article class="mb-login-slide" data-login-slide>
                        <div class="mb-login-slide__topline" aria-hidden="true"><span></span></div>
                        <span class="mb-login-slide__tag">BELAJAR BERSAMA</span>
                        <h3>Data yang rapi membantu keputusan yang lebih baik.</h3>
                        <p>Gunakan Monbis untuk mengevaluasi, memperbaiki, dan bergerak lebih cepat.</p>
                    </article>
                </div>

                <div class="mb-login-showcase__controls">
                    <div class="mb-login-showcase__dots" role="tablist" aria-label="Pilih informasi">
                        <button type="button" class="is-active" data-login-dot="0" role="tab" aria-label="Informasi 1" aria-selected="true"></button>
                        <button type="button" data-login-dot="1" role="tab" aria-label="Informasi 2" aria-selected="false"></button>
                        <button type="button" data-login-dot="2" role="tab" aria-label="Informasi 3" aria-selected="false"></button>
                    </div>
                    <div class="mb-login-showcase__arrows">
                        <button type="button" data-login-prev aria-label="Informasi sebelumnya">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <button type="button" data-login-next aria-label="Informasi berikutnya">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>

                <div class="mb-login-showcase__footer">
                    <span class="mb-login-showcase__footer-line"></span>
                    <span>Mulai dari hal kecil. Konsisten setiap hari.</span>
                </div>
            </div>
        </aside>
        <?php
    }
}
