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
                    <a href="<?= base_url('?route=profile&username=' . urlencode($user['username']) . '&tab=over') ?>" 
   						class="tab-item <?= $active_tab === 'over' ? 'active' : '' ?>">
    					Over
					</a>
                    <a href="<?= base_url('?route=profile&username=' . urlencode($user['username']) . '&tab=krabbels') ?>" 
   						class="tab-item <?= $active_tab === 'over' ? 'active' : '' ?>">
    					Krabbels
					</a>
                    <a href="<?= base_url('?route=profile&username=' . urlencode($user['username']) . '&tab=vrienden') ?>" 
   						class="tab-item <?= $active_tab === 'over' ? 'active' : '' ?>">
    					Vrienden (<?= count($friends) ?>)
                    </a>
                    <a href="<?= base_url('?route=profile&username=' . urlencode($user['username']) . '&tab=fotos') ?>" 
   						class="tab-item <?= $active_tab === 'over' ? 'active' : '' ?>">
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
                    <?php elseif ($active_tab == 'krabbels'): ?>
                <!-- Krabbels tab content -->
                <div class="krabbels-container mt-4">
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                        <form action="<?= base_url('?route=profile/post-krabbel') ?>" method="post" class="bg-blue-50 p-4 rounded-lg mb-4">
                            <input type="hidden" name="receiver_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="receiver_username" value="<?= $user['username'] ?>">
                            <textarea name="message" placeholder="Schrijf een krabbel..." class="w-full p-2 border rounded mb-2" required></textarea>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Plaatsen</button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if (!empty($krabbels)): ?>
                        <?php foreach ($krabbels as $krabbel): ?>
                            <div class="krabbel-item bg-white p-4 rounded-lg shadow mb-4">
                                <div class="flex items-center mb-2">
                                    <img src="<?= base_url($krabbel['sender_avatar']) ?>" alt="<?= htmlspecialchars($krabbel['sender_name']) ?>" class="w-10 h-10 rounded-full mr-2">
                                    <div>
                                        <a href="<?= base_url('profile/' . $krabbel['sender_username']) ?>" class="font-bold text-blue-600"><?= htmlspecialchars($krabbel['sender_name']) ?></a>
                                        <div class="text-gray-500 text-sm"><?= date('d-m-Y H:i', strtotime($krabbel['created_at'])) ?></div>
                                    </div>
                                </div>
                                <p class="text-gray-700"><?= nl2br(htmlspecialchars($krabbel['message'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 italic text-center p-4">Er zijn nog geen krabbels geplaatst.</p>
                    <?php endif; ?>
                </div>
                    
                    <!-- Vrienden tab -->
                    <?php elseif ($active_tab == 'vrienden'): ?>
                    <!-- Vrienden tab content -->
                    <div class="friends-container mt-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <?php if (!empty($friends)): ?>
                                <?php foreach ($friends as $friend): ?>
                                    <div class="friend-card text-center">
                                        <a href="<?= base_url('profile/' . $friend['username']) ?>" class="block">
                                            <img src="<?= base_url($friend['avatar']) ?>" alt="<?= htmlspecialchars($friend['name']) ?>" class="w-20 h-20 rounded-full mx-auto mb-2 object-cover">
                                            <div class="font-medium"><?= htmlspecialchars($friend['name']) ?></div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 italic text-center p-4 col-span-full">Deze gebruiker heeft nog geen vrienden.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Foto's tab -->
                    <?php elseif ($active_tab == 'fotos'): ?>
                        <!-- Foto's tab content -->
                        <div class="photos-container mt-4">
                            <?php if ($viewer_is_owner): ?>
                                <form action="<?= base_url('profile/upload-foto') ?>" method="post" enctype="multipart/form-data" class="bg-blue-50 p-4 rounded-lg mb-4">
                                    <div class="mb-3">
                                        <label for="photo" class="block mb-1 font-medium">Foto uploaden:</label>
                                        <input type="file" name="photo" id="photo" accept="image/*" required class="block w-full">
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="block mb-1 font-medium">Beschrijving:</label>
                                        <input type="text" name="description" id="description" class="w-full p-2 border rounded">
                                    </div>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Uploaden</button>
                                </form>
                            <?php endif; ?>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                <?php if (!empty($fotos)): ?>
                                    <?php foreach ($fotos as $foto): ?>
                                        <div class="photo-card overflow-hidden rounded-lg shadow">
                                            <div class="h-32 overflow-hidden">
                                                <img src="<?= base_url('public/assets/images/' . $foto['filename']) ?>" alt="<?= htmlspecialchars($foto['description']) ?>" class="w-full h-full object-cover">
                                            </div>
                                            <div class="p-2 bg-white">
                                                <p class="text-sm"><?= htmlspecialchars($foto['description']) ?></p>
                                                <span class="text-xs text-gray-500"><?= date('d-m-Y', strtotime($foto['uploaded_at'])) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-gray-500 italic text-center p-4 col-span-full">Deze gebruiker heeft nog geen foto's geüpload.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div> <!-- Sluit .tab-content -->
            </div> <!-- Sluit .w-full md:w-2/3 p-4 -->
        </div> <!-- Sluit .flex flex-col md:flex-row -->
    </div> <!-- Sluit .profile-content -->
</div> <!-- Sluit .profile-container -->