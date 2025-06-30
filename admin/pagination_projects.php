<!-- Pagination Projects -->
<nav>
    <ul class="pagination justify-content-center">
        <!-- Tombol Prev -->
        <li class="page-item <?= ($page_proj <= 1) ? 'disabled' : '' ?>">
            <a class="page-link"
                href="?page_proj=<?= max(1, $page_proj - 1) ?>&page=<?= $page ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>">&laquo;
                Prev</a>
        </li>

        <!-- Nomor Halaman -->
        <?php
        $start = max(1, $page_proj - 2);
        $end = min($total_pages_projects, $page_proj + 2);
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= ($i == $page_proj) ? 'active' : '' ?>">
                <a class="page-link"
                    href="?page_proj=<?= $i ?>&page=<?= $page ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= ($page_proj >= $total_pages_projects) ? 'disabled' : '' ?>">
            <a class="page-link"
                href="?page_proj=<?= min($total_pages_projects, $page_proj + 1) ?>&page=<?= $page ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>">Next
                &raquo;</a>
        </li>
    </ul>

    <div class="text-center mt-2">
        Page <?= $page_proj ?> of <?= $total_pages_projects ?>
    </div>
</nav>