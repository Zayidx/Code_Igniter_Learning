<!-- Container utama daftar buku -->
<div class="container my-4">
    <?php // Tampilkan flash message sukses bila ada ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success" role="alert">
            <?= esc(session()->getFlashdata('message')) ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">All Books</h2> <!-- Judul section -->
        <a class="btn btn-primary" href="<?= site_url('books/new') ?>">Add Book</a> <!-- Tombol ke form create -->
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0"> <!-- Tabel daftar buku -->
                    <thead class="table-light"> <!-- Header tabel -->
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th style="width: 220px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody> <!-- Body tabel -->
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No books yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($books as $item): ?> <!-- Looping data buku -->
                            <tr>
                                <td><?= $no++ ?></td> <!-- Nomor urut tampilan -->
                                <td><?= esc($item['title']) ?></td> <!-- Judul buku -->
                                <td><?= esc($item['author']) ?></td> <!-- Penulis -->
                                <td><?= esc($item['created_at'] ?? '-') ?></td> <!-- Tanggal dibuat -->
                                <td>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('books/' . $item['id']) ?>">View</a> <!-- Link detail -->
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('books/' . $item['id'] . '/edit') ?>">Edit</a> <!-- Link edit -->
                                    <form action="<?= site_url('books/' . $item['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this book?');"> <!-- Form hapus -->
                                        <?= csrf_field() ?> <!-- Token CSRF untuk keamanan -->
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button> <!-- Tombol hapus -->
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
