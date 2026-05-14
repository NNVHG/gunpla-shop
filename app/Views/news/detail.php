<?php
/**
 * @var array $newsItem
 * @var array $latestNews
 * @var array $categories
 */
?>
<div class="container my-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/news">Tin tức</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($newsItem['title']) ?></li>
                </ol>
            </nav>

            <article class="bg-white p-4 p-md-5 rounded shadow-sm">
                <h1 class="mb-3"><?= htmlspecialchars($newsItem['title']) ?></h1>
                
                <div class="text-muted mb-4 pb-3 border-bottom">
                    <i class="far fa-calendar-alt me-2"></i> <?= date('d/m/Y H:i', strtotime($newsItem['created_at'])) ?>
                </div>

                <?php if (!empty($newsItem['image_path'])): ?>
                    <img src="<?= BASE_URL . htmlspecialchars($newsItem['image_path']) ?>" class="img-fluid rounded mb-4 w-100" alt="<?= htmlspecialchars($newsItem['title']) ?>" style="max-height: 500px; object-fit: cover;">
                <?php endif; ?>

                <?php if (!empty($newsItem['summary'])): ?>
                    <div class="lead mb-4 fw-bold">
                        <?= nl2br(htmlspecialchars($newsItem['summary'])) ?>
                    </div>
                <?php endif; ?>

                <div class="news-content">
                    <?= $newsItem['content'] // Raw HTML allowed for content ?>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tin tức mới nhất</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($latestNews as $item): ?>
                            <?php if ($item['id'] !== $newsItem['id']): ?>
                                <li class="list-group-item p-3">
                                    <a href="<?= BASE_URL ?>/news/detail/<?= htmlspecialchars($item['slug'] ?: $item['id']) ?>" class="text-decoration-none d-flex align-items-center">
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="<?= BASE_URL . htmlspecialchars($item['image_path']) ?>" alt="" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1 text-dark text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </h6>
                                            <small class="text-muted"><?= date('d/m/Y', strtotime($item['created_at'])) ?></small>
                                        </div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Danh mục sản phẩm</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= BASE_URL ?>/products?category_id=<?= $cat['id'] ?>" class="list-group-item list-group-item-action">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
