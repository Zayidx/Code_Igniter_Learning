<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Edit Book</h2>
        <a class="btn btn-secondary" href="<?= site_url('books') ?>">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($validation) && $validation->getErrors()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('books/' . $book['id'] . '/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" value="<?= esc(old('title', $book['title'])) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" name="author" id="author" value="<?= esc(old('author', $book['author'])) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="6" class="form-control"><?= esc(old('description', $book['description'] ?? '')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

