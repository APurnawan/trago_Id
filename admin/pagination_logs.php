<!-- Pagination Employee Logs -->
<nav>
    <ul class="pagination justify-content-center">
        <!-- Tombol Prev -->
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>">&laquo;
                Prev</a>
        </li>

        <!-- Tombol halaman -->
        <?php
        $start = max(1, $page - 2);
        $end = min($total_pages_logs, $page + 2);
        for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= ($page >= $total_pages_logs) ? 'disabled' : '' ?>">
            <a class="page-link"
                href="?page=<?= min($total_pages_logs, $page + 1) ?>&tahun=<?= $tahun ?>&bulan=<?= $bulan ?>">Next
                &raquo;</a>
        </li>
    </ul>

    <div class="text-center mt-2">
        Page <?= $page ?> of <?= $total_pages_logs ?>
    </div>
</nav>