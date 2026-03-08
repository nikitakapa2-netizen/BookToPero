<form method="post" class="row g-3 bg-white p-3 rounded shadow-sm">
<div class="col-md-4"><select class="form-select" name="category_id" required><?php foreach($categories as $c): ?><option value="<?=$c['id']?>" <?=isset($book)&&$book['category_id']==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><input class="form-control" name="title" required placeholder="Название" value="<?=e($book['title']??'')?>"></div>
<div class="col-md-6"><input class="form-control" name="author" required placeholder="Автор" value="<?=e($book['author']??'')?>"></div>
<div class="col-md-3"><input class="form-control" type="number" step="0.01" min="0.01" name="price" required placeholder="Цена" value="<?=e($book['price']??'')?>"></div>
<div class="col-md-3"><input class="form-control" type="number" min="0" name="quantity" required placeholder="Остаток" value="<?=e($book['quantity']??'')?>"></div>
<div class="col-12"><input class="form-control" name="image" required placeholder="URL изображения" value="<?=e($book['image']??'')?>"></div>
<div class="col-12"><textarea class="form-control" name="short_description" required placeholder="Краткое описание"><?=e($book['short_description']??'')?></textarea></div>
<div class="col-12"><textarea class="form-control" name="full_description" rows="4" required placeholder="Полное описание"><?=e($book['full_description']??'')?></textarea></div>
<div class="col-12 d-flex gap-3"><label><input type="checkbox" name="is_new" <?=!empty($book['is_new'])?'checked':''?>> Новинка</label><label><input type="checkbox" name="is_popular" <?=!empty($book['is_popular'])?'checked':''?>> Популярное</label><label><input type="checkbox" name="is_recommended" <?=!empty($book['is_recommended'])?'checked':''?>> Рекомендуем</label></div>
<div class="col-12"><button class="btn btn-primary">Сохранить</button></div>
</form>
