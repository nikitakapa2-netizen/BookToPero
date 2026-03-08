<a href="book.php?id=<?= $book['id'] ?>" class="text-decoration-none text-dark d-block">
    <div class="d-flex gap-2 align-items-center bg-light rounded p-2">
        <img src="<?= e($book['image']) ?>" width="42" height="60" style="object-fit:cover" class="rounded" alt="">
        <div class="small fw-semibold"><?= e($book['title']) ?></div>
    </div>
</a>
