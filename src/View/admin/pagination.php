<li class="page-item <?= ($start - $size) >= 0 ?: 'disabled'?>">
    <a class="page-link" href="<?= $url?>?start=<?= $start - $size?>">Previous</a>
</li>
<?php for($j=0, $i= 0, $count = 0; $i<$total; $i = $i + $size, $j++): ?>

    <?php if ($i >= $start - ($numItems - 2)  * $size && $count < $numItems): ?>
    <li class="page-item <?= $i === $start ? 'active' : ''?>"><a class="page-link" href="<?= $url?>?start=<?= $i?>"><?= $j+1 ?></a></li>
    <?php $count++ ?>
    <?php endif; ?>
<?php endfor; ?>
<?php if ($i <= $total): ?>
    <li class="page-item disabled"><a class="page-link">...</a></li>
<?php endif; ?>  
<li class="page-item <?= $total > ($start + $size) ?: 'disabled'?>">
    <a class="page-link" href="<?= $url?>?start=<?= $start + $size?>">Next</a>
</li>