<?php /* SocialCore profielpagina in Hyves-stijl */ ?>

<div class="profile-container">
    <!-- Profiel header -->
    <div class="profile-header bg-blue-100 border-b-4 border-blue-400 rounded-t-lg p-4">
        <h1 class="text-2xl font-bold text-blue-800"><?= htmlspecialchars($user['name']) ?></h1>
        <div class="text-sm text-blue-600">
            <span class="inline-block mr-4">Lid sinds: <?= htmlspecialchars($user['joined']) ?></span>
            <span class="inline-block">Locatie: <?= htmlspecialchars($user['location']) ?></span>
        </div>
    </div>
    
    <!-- Profiel inhoud -->
    <div class="profile-content bg-white rounded-b-lg shadow-md">
        <div class="flex flex-col md:flex-row">
            <!-- Linker kolom - Profielfoto en acties -->
            <div class="w-full md:w-1/3 p-4 border-r border-blue-100">
                <div class="profile-photo mb-4">
                    <img src="<?= base_url('public/uploads/' . $user['avatar']) ?>" 
                         alt="<?= htmlspecialchars($user['name']) ?>" 
                         class="w-full max-w-xs mx-auto border-4 border-blue-200 rounded-lg">
                </div>
                
                <!-- Actieknoppen -->
                <div class="profile-actions flex flex-col space-y-2 mt-4">
                    <?php if ($viewer_is_owner): ?>
                        <a href="<?= base_url('profile/edit') ?>" class="hyves-button bg-blue-500 hover:bg-blue-600">
                            Profiel bewerken
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('friends/add/' . $user['username']) ?>" class="hyves-button bg-green-500 hover:bg-green-600">
                            + Vriend toevoegen
                        </a>
                        <a href="<?= base_url('messages/new/' . $user['username']) ?>" class="hyves-button bg-blue-500 hover:bg-blue-600">
                            Bericht sturen
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('profile/' . $user['username'] . '/respect') ?>" class="hyves-button bg-yellow-500 hover:bg-yellow-600">
                        Respect tonen! ✓
                    </a>
                </div>
            </div>
            
            <!-- Rechter kolom - Profiel content tabs -->
            <div class="w-full md:w-2/3 p-4">
                <!-- Profiel navigatietabs -->
                <div class="profile-tabs flex border-b-2 border-blue-200 mb-4">
                    <a href="<?= base_url('profile/' . $user['username'] . '?tab=over') ?>" 
                       class="tab-item <?= $active_tab === 'over' ? 'active' : '' ?>">
                        Over
                    </a>
                    <a href="<?= base_url('profile/' . $user['username'] . '?tab=krabbels') ?>" 
                       class="tab-item <?= $active_tab === 'krabbels' ? 'active' : '' ?>">
                        Krabbels
                    </a>
                    <a href="<?= base_url('profile/' . $user['username'] . '?tab=vrienden') ?>" 
                       class="tab-item <?= $active_tab === 'vrienden' ? 'active' : '' ?>">
                        Vrienden (<?= count($friends) ?>)
                    </a>
                    <a href="<?= base_url('profile/' . $user['username'] . '?tab=fotos') ?>" 
                       class="tab-item <?= $active_tab === 'fotos' ? 'active' : '' ?>">
                        Foto's
                    </a>
                </div>
                
                <!-- Tab inhoud -->
                <div class="tab-content">
                    <!-- Over tab -->
                    <?php if ($active_tab === 'over'): ?>
                        <div class="about-section">
                            <div class="about-block mb-6">
                                <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Over mij</h3>
                                <p class="mb-4"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                            </div>
                            
                            <div class="about-block mb-6">
                                <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Interesses</h3>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($user['interests'] as $interest): ?>
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm">
                                            <?= htmlspecialchars($interest) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($user['favorite_quote'])): ?>
                                <div class="about-block">
                                    <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Favoriete quote</h3>
                                    <blockquote class="pl-4 border-l-4 border-blue-300 italic">
                                        "<?= htmlspecialchars($user['favorite_quote']) ?>"
                                    </blockquote>
                                </div>
                            <?php endif; ?>
                        </div>
                    
                    <!-- Krabbels tab -->
                    <?php elseif ($active_tab === 'krabbels'): ?>
                        <div class="guestbook-section">
                            <!-- Krabbel toevoegen -->
                            <?php if (!$viewer_is_owner): ?>
                                <div class="add-krabbel bg-blue-50 p-4 rounded-lg mb-6">
                                    <h3 class="text-lg font-bold text-blue-700 mb-3">Plaats een krabbel</h3>
                                    <form action="<?= base_url('profile/krabbel/add') ?>" method="post">
                                        <input type="hidden" name="recipient_id" value="<?= $user['id'] ?>">
                                        <textarea name="content" rows="3" class="w-full p-2 border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                  placeholder="Laat een berichtje achter..."></textarea>
                                        <button type="submit" class="hyves-button bg-blue-500 hover:bg-blue-600 mt-2">
                                            Plaatsen
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Bestaande krabbels -->
                            <div class="krabbels-list">
                                <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">
                                    Krabbels (<?= count($posts) ?>)
                                </h3>
                                
                                <?php foreach ($posts as $post): ?>
                                    <div class="krabbel bg-blue-50 rounded-lg p-4 mb-4 border-l-4 border-blue-300">
                                        <div class="krabbel-header flex justify-between mb-2">
                                            <div class="text-sm text-blue-700">Door: <a href="#" class="font-bold hover:underline">Gebruiker</a></div>
                                            <div class="text-xs text-gray-500"><?= $post['created_at'] ?></div>
                                        </div>
                                        <div class="krabbel-content mb-2">
                                            <?= nl2br(htmlspecialchars($post['content'])) ?>
                                        </div>
                                        <div class="krabbel-footer flex justify-between text-xs text-gray-500">
                                            <div>
                                                <span class="mr-2">❤️ <?= $post['likes'] ?></span>
                                                <span>💬 <?= $post['comments'] ?></span>
                                            </div>
                                            <div>
                                                <a href="#" class="text-blue-600 hover:underline">Reageren</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if (empty($posts)): ?>
                                    <div class="empty-state text-center py-6 text-gray-500">
                                        Nog geen krabbels. Wees de eerste die een bericht achterlaat!
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    
                    <!-- Vrienden tab -->
                    <?php elseif ($active_tab === 'vrienden'): ?>
                        <div class="friends-section">
                            <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">
                                Vrienden (<?= count($friends) ?>)
                            </h3>
                            
                            <div class="friends-grid grid grid-cols-2 md:grid-cols-3 gap-4">
                                <?php foreach ($friends as $friend): ?>
                                    <div class="friend-card bg-blue-50 rounded-lg p-3 border border-blue-200 hover:shadow-md transition-shadow">
                                        <a href="<?= base_url('profile/' . $friend['username']) ?>" class="flex flex-col items-center">
                                            <img src="<?= base_url('public/uploads/' . $friend['avatar']) ?>" 
                                                 alt="<?= htmlspecialchars($friend['name']) ?>" 
                                                 class="w-16 h-16 rounded-full border-2 border-blue-300">
                                            <div class="font-medium text-blue-800 mt-2 text-center">
                                                <?= htmlspecialchars($friend['name']) ?>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (empty($friends)): ?>
                                <div class="empty-state text-center py-6 text-gray-500">
                                    Nog geen vrienden toegevoegd.
                                </div>
                            <?php endif; ?>
                        </div>
                    
                    <!-- Foto's tab -->
                    <?php elseif ($active_tab === 'fotos'): ?>
                        <div class="photos-section">
                            <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">
                                Foto's
                            </h3>
                            
                            <div class="empty-state text-center py-6 text-gray-500">
                                Nog geen foto's toegevoegd.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>