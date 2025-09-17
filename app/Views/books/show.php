<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0"><?= esc($book['title']) ?></h2>
        <a class="btn btn-secondary" href="<?= site_url('books') ?>">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-muted mb-2">Author: <strong><?= esc($book['author']) ?></strong></p>
            <p><?= nl2br(esc($book['description'] ?? '')) ?></p>
            <div class="mt-3">
                <a class="btn btn-primary" href="<?= site_url('books/' . $book['id'] . '/edit') ?>">Edit</a>
                <form action="<?= site_url('books/' . $book['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this book?');">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

