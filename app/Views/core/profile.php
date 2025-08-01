<?php include __DIR__ . '/../layout/header.php';


$posts = $posts ?? [];
$photos = $photos ?? [];
$currentUser = $currentUser ?? [];
$baseUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/');
?>

<div class="core-profile">

  <!-- 🔷 Profiel header -->
  <div class="profile-header">
    <div class="profile-avatar-wrapper">
      <img src="<?= $user['avatar_url'] ?? $baseUrl . '/public/assets/images/avatars/default-avatar.png' ?>"
           alt="<?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'Gebruiker') ?>"
           class="profile-avatar">
    </div>

    <div class="profile-details">
      <h1 class="profile-name"><?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'Gebruiker') ?></h1>
      <div class="profile-username">@<?= htmlspecialchars($user['username']) ?></div>

      <?php if (!empty($user['bio'])): ?>
        <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
      <?php endif; ?>

      <div class="profile-meta">
        <?php if (!empty($user['location'])): ?>
          <div class="meta-item">📍 <?= htmlspecialchars($user['location']) ?></div>
        <?php endif; ?>
        <?php if (!empty($user['website'])): ?>
          <div class="meta-item">🌐 
            <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank">
              <?= htmlspecialchars(str_replace(['http://', 'https://'], '', $user['website'])) ?>
            </a>
          </div>
        <?php endif; ?>
        <?php if (!empty($user['created_at'])): ?>
          <div class="meta-item">📅 Lid sinds <?= date('F Y', strtotime($user['created_at'])) ?></div>
        <?php endif; ?>
      </div>

      <?php if (isset($_SESSION['user_id']) && $user['id'] === $_SESSION['user_id']): ?>
        <div class="edit-profile-container">
          <a href="<?= base_url('?route=profile/edit') ?>" class="profile-edit-button">✏️ Profiel bewerken</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 🟡 Laatste berichten -->
  <div class="profile-section">
    <h2>Laatste berichten</h2>
    <?php if (!empty($posts)): ?>
      <?php foreach (array_slice($posts, 0, 5) as $post): ?>
        <div class="profile-post">
          <div class="post-content">
          <?= $post['content_formatted'] ?? nl2br(htmlspecialchars($post['content'] ?? '')) ?>
        </div>

        <!-- Link Preview Section -->
        <?php if (!empty($post['link_preview_id']) && !empty($post['preview_url'])): ?>
        <div class="link-preview-section">
          <a href="<?= htmlspecialchars($post['preview_url']) ?>" target="_blank" class="link-preview-card timeline-preview">
            <?php if (!empty($post['preview_image'])): ?>
            <img src="<?= htmlspecialchars($post['preview_image']) ?>" alt="Link preview" class="link-preview-img">
            <?php endif; ?>
            <div class="link-preview-content">
              <h4 class="link-preview-title"><?= htmlspecialchars($post['preview_title'] ?? $post['preview_url']) ?></h4>
              <?php if (!empty($post['preview_description'])): ?>
              <p class="link-preview-description"><?= htmlspecialchars($post['preview_description']) ?></p>
              <?php endif; ?>
              <span class="link-preview-domain">🔗 <?= htmlspecialchars($post['preview_domain'] ?? parse_url($post['preview_url'], PHP_URL_HOST)) ?></span>
            </div>
          </a>
        </div>
        <?php endif; ?>

<?php if (!empty($post['media_path'])): ?>
            <img src="<?= base_url('uploads/' . $post['media_path']) ?>" 
                 alt="Post afbeelding" 
                 class="post-image"
                 onclick="openImageModal('<?= base_url('uploads/' . $post['media_path']) ?>')">
          <?php endif; ?>
          <small><?= $post['created_at'] ?></small>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Geen berichten geplaatst.</p>
    <?php endif; ?>
  </div>

  <!-- 🖼️ Laatste foto's -->
  <div class="profile-section">
    <h2>Laatste foto's</h2>
    <div class="profile-photos">
      <?php if (!empty($photos)): ?>
        <?php foreach (array_slice($photos, 0, 5) as $photo): ?>
          <img src="<?= htmlspecialchars($photo['url']) ?>" alt="Foto" class="profile-photo">
        <?php endforeach; ?>
      <?php else: ?>
        <p>Geen foto's beschikbaar.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- 📷 Image Modal -->
<div id="image-modal" class="modal" style="display: none;" onclick="closeImageModal()">
  <div class="modal-content">
    <img id="modal-image" src="" alt="Volledige afbeelding">
    <button class="modal-close" onclick="closeImageModal()">×</button>
  </div>
</div>

<script>
  function openImageModal(imageSrc) {
    document.getElementById('modal-image').src = imageSrc;
    document.getElementById('image-modal').style.display = 'flex';
  }
  function closeImageModal() {
    document.getElementById('image-modal').style.display = 'none';
  }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
