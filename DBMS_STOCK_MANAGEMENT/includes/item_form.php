<?php $isEdit = !empty($i['id']); ?>
<div class="modal-header"><h5 class="modal-title"><?= $isEdit ? 'Edit Item' : 'Add Item' ?></h5><button class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
<div class="modal-body">
  <div class="row g-3">
    <div class="col-md-4"><label class="form-label">Item code</label><input name="item_code" class="form-control" required value="<?= e($i['item_code'] ?? '') ?>"></div>
    <div class="col-md-8"><label class="form-label">Item name</label><input name="item_name" class="form-control" required value="<?= e($i['item_name'] ?? '') ?>"></div>
    <div class="col-md-4"><label class="form-label">Category</label><select name="category_id" class="form-select" required><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (($i['category_id'] ?? '')==$c['id'])?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><label class="form-label">Quantity</label><input name="quantity" type="number" step="0.01" min="0" class="form-control" <?= $isEdit?'disabled':'' ?> required value="<?= e($i['quantity'] ?? '0') ?>"></div>
    <div class="col-md-2"><label class="form-label">Unit</label><input name="unit" class="form-control" required value="<?= e($i['unit'] ?? 'Nos') ?>"></div>
    <div class="col-md-2"><label class="form-label">Unit price</label><input name="unit_price" type="number" step="0.01" min="0" class="form-control" value="<?= e($i['unit_price'] ?? '0') ?>"></div>
    <div class="col-md-2"><label class="form-label">Min stock</label><input name="minimum_stock" type="number" step="0.01" min="0" class="form-control" value="<?= e($i['minimum_stock'] ?? '5') ?>"></div>
    <div class="col-md-6"><label class="form-label">Storage location</label><input name="storage_location" class="form-control" value="<?= e($i['storage_location'] ?? '') ?>"></div>
    <?php if (!$isEdit): ?><div class="col-md-6"><label class="form-label">Invoice PDF/JPG/PNG</label><input name="invoice" type="file" accept=".pdf,.jpg,.jpeg,.png" class="form-control"></div><?php endif; ?>
    <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?= e($i['description'] ?? '') ?></textarea></div>
  </div>
</div>
<div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary">Save</button></div>

