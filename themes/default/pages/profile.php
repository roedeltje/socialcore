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
                <img src="<?= $user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                    alt="<?= htmlspecialchars($user['name'] ?? 'Gebruiker') ?>" 
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
                        class="tab-item <?= $active_tab === 'krabbels' ? 'active' : '' ?>">
                        Krabbels
                    </a>
                    <a href="<?= base_url('?route=profile&username=' . urlencode($user['username']) . '&tab=vrienden') ?>" 
                        class="tab-item <?= $active_tab === 'vrienden' ? 'active' : '' ?>">
                        Vrienden (<?= count($friends) ?>)
                    </a>
                    <a href="<?= base_url('?route=profile&username=' . urlencode($user['username']) . '&tab=fotos') ?>" 
                        class="tab-item <?= $active_tab === 'fotos' ? 'active' : '' ?>">
                        Foto's
                    </a>
                </div>
                
                <!-- Tab inhoud -->
                <div class="tab-content">
                    <!-- Over tab -->
                <!-- Over tab -->
                    <?php if ($active_tab === 'over'): ?>
                        <div class="about-section">
                            <!-- Basisinformatie -->
                            <div class="about-block">
                                <h3>Basisinformatie</h3>
                                <div class="info-grid">
                                    <?php if (!empty($user['display_name'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Naam:</span>
                                            <span class="info-value"><?= htmlspecialchars($user['display_name']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user['location'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Locatie:</span>
                                            <span class="info-value"><?= htmlspecialchars($user['location']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user['website'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Website:</span>
                                            <span class="info-value">
                                                <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank" rel="noopener">
                                                    <?= htmlspecialchars($user['website']) ?>
                                                </a>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user['phone'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Telefoon:</span>
                                            <span class="info-value"><?= htmlspecialchars($user['phone']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user['date_of_birth'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Geboren:</span>
                                            <span class="info-value"><?= date('d-m-Y', strtotime($user['date_of_birth'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user['gender'])): ?>
                                        <div class="info-item">
                                            <span class="info-label">Geslacht:</span>
                                            <span class="info-value">
                                                <?php 
                                                $genders = ['male' => 'Man', 'female' => 'Vrouw', 'other' => 'Anders', 'prefer_not_to_say' => 'Zeg ik liever niet'];
                                                echo $genders[$user['gender']] ?? htmlspecialchars($user['gender']);
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="info-item">
                                        <span class="info-label">Lid sinds:</span>
                                        <span class="info-value"><?= htmlspecialchars($user['joined']) ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Over mij / Bio -->
                            <?php if (!empty($user['bio'])): ?>
                                <div class="about-block bio-section">
                                    <h3>Over mij</h3>
                                    <div class="bio-content">
                                        <?= nl2br(htmlspecialchars($user['bio'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Interesses -->
                            <?php if (!empty($user['interests'])): ?>
                                <div class="about-block interests-section">
                                    <h3>Interesses</h3>
                                    <div class="interests-tags">
                                        <?php foreach ($user['interests'] as $interest): ?>
                                            <span class="interest-tag">
                                                <?= htmlspecialchars($interest) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Als geen informatie beschikbaar is -->
                            <?php if (empty($user['bio']) && empty($user['location']) && empty($user['website']) && empty($user['phone'])): ?>
                                <div class="empty-profile-state">
                                    <div class="icon">👤</div>
                                    <h3>Profiel nog niet ingevuld</h3>
                                    <p>
                                        <?php if ($viewer_is_owner): ?>
                                            Voeg wat informatie toe aan je profiel om jezelf voor te stellen aan andere gebruikers.
                                        <?php else: ?>
                                            Deze gebruiker heeft nog geen profielinformatie toegevoegd.
                                        <?php endif; ?>
                                    </p>
                                    <?php if ($viewer_is_owner): ?>
                                        <a href="<?= base_url('profile/edit') ?>" class="hyves-button">
                                            Profiel bewerken
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    
                    <!-- Krabbels tab -->
                    <?php elseif ($active_tab == 'krabbels'): ?>
                        <!-- Krabbels tab content -->
                        <div class="krabbels-container mt-4">
                            <!-- Krabbel formulier (voor andere gebruikers) -->
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user['id']): ?>
                                <form action="<?= base_url('?route=profile/post-krabbel') ?>" method="post" class="bg-blue-50 p-4 rounded-lg mb-4">
                                    <h4 class="font-bold text-blue-700 mb-3">Schrijf een krabbel</h4>
                                    <input type="hidden" name="receiver_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="receiver_username" value="<?= $user['username'] ?>">
                                    <textarea name="message" placeholder="Schrijf een krabbel..." class="w-full p-2 border rounded mb-2" required></textarea>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Krabbel plaatsen</button>
                                </form>
                            <?php endif; ?>
                            
                            <!-- Post formulier (voor eigen profiel) -->
                            <?php if ($viewer_is_owner): ?>
                                <div class="post-composer bg-white rounded-lg shadow-md mb-6 overflow-hidden">
                                    <div class="bg-blue-100 p-3 border-b border-blue-200">
                                        <h3 class="font-bold text-blue-800">Plaats een bericht</h3>
                                    </div>
                                    <div class="p-4">
                                        <form action="<?= base_url('feed/create') ?>" method="post" enctype="multipart/form-data" id="profilePostForm">
                                            <div class="flex space-x-3">
                                                <img src="<?= $user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                                     alt="<?= htmlspecialchars($user['name']) ?>" 
                                                     class="w-10 h-10 rounded-full border-2 border-blue-200">
                                                <textarea name="content" rows="2" 
                                                          class="flex-1 p-3 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                                          placeholder="Wat is er aan de hand, <?= htmlspecialchars($user['name']) ?>?"
                                                          maxlength="1000"
                                                          id="profilePostContent"></textarea>
                                            </div>
                                            
                                            <!-- Afbeelding preview container -->
                                            <div id="profileImagePreview" class="mt-3 relative rounded-lg border border-blue-200 bg-blue-50 hidden">
                                                <img src="" alt="Preview" class="max-h-64 rounded-lg mx-auto">
                                                <button type="button" id="profileRemoveImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">×</button>
                                            </div>
                                            
                                            <!-- Karakterteller -->
                                            <div class="flex justify-between items-center mt-2 text-sm text-gray-500">
                                                <span></span>
                                                <span id="profileCharCounter">0/1000</span>
                                            </div>
                                            
                                            <div class="flex justify-between mt-3">
                                                <div class="flex space-x-2">
                                                    <!-- Afbeelding upload button -->
                                                    <label for="profileImageUpload" class="hyves-tool-button cursor-pointer" title="Voeg foto toe">
                                                        <span class="icon">📷</span>
                                                        <input type="file" id="profileImageUpload" name="image" accept="image/*" class="hidden">
                                                    </label>
                                                    <button type="button" class="hyves-tool-button" title="Voeg video toe">
                                                        <span class="icon">🎬</span>
                                                    </button>
                                                    <button type="button" class="hyves-tool-button" title="Voeg link toe">
                                                        <span class="icon">🔗</span>
                                                    </button>
                                                    <button type="button" class="hyves-tool-button" title="Voeg emoji toe">
                                                        <span class="icon">😊</span>
                                                    </button>
                                                </div>
                                                <button type="submit" class="hyves-button bg-blue-500 hover:bg-blue-600 text-sm px-4" id="profileSubmitBtn">
                                                    Plaatsen
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Echte posts uit database -->
                            <?php if (!empty($posts)): ?>
                                <div class="mb-6">
                                    <h4 class="font-bold text-blue-700 mb-3 border-b border-blue-200 pb-2">Berichten</h4>
                                    <?php foreach ($posts as $post): ?>
                                        <div class="bg-white p-4 rounded-lg shadow mb-4">
                                            <div class="flex items-center mb-3">
                                                <img src="<?= $post['avatar'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                                    alt="<?= htmlspecialchars($user['name']) ?>" 
                                                    class="w-10 h-10 rounded-full mr-3">
                                                <div class="flex-grow">
                                                    <div class="font-bold text-blue-600"><?= htmlspecialchars($user['name']) ?></div>
                                                    <div class="text-gray-500 text-sm"><?= $post['created_at'] ?></div>
                                                </div>
                                                
                                                <!-- Verwijder/opties menu -->
                                                <?php if (isset($_SESSION['user_id']) && (isset($post['user_id']) && $post['user_id'] == $_SESSION['user_id'] || isset($_SESSION['role']) && $_SESSION['role'] === 'admin')): ?>
                                                    <div class="relative post-menu">
                                                        <button type="button" class="post-menu-button text-gray-500 hover:text-gray-700 p-1 rounded-full">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                            </svg>
                                                        </button>
                                                        <div class="post-menu-dropdown absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                                                            <form action="<?= base_url('feed/delete') ?>" method="post" class="delete-post-form">
                                                                <input type="hidden" name="post_id" value="<?= $post['id'] ?? 0 ?>">
                                                                <button type="button" class="delete-post-button block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                                    Bericht verwijderen
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <?php if (!empty($post['content'])): ?>
                                                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                                                <?php endif; ?>
                                                
                                                <!-- Afbeelding weergave -->
                                                <?php if (!empty($post['media_path'])): ?>
                                                    <div class="mt-3 post-media rounded-lg overflow-hidden border border-blue-200">
                                                        <img src="<?= base_url('uploads/' . $post['media_path']) ?>" 
                                                            alt="Post afbeelding" 
                                                            class="w-full h-auto max-h-96 object-contain">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Post actions -->
                                            <div class="flex justify-between text-sm border-t border-gray-200 pt-3">
                                                <button class="hyves-action-button like-button <?= isset($post['is_liked']) && $post['is_liked'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>">
                                                    <span class="like-icon">👍</span>
                                                    <span class="text"><span class="like-count"><?= $post['likes'] ?? 0 ?></span> Respect!</span>
                                                </button>
                                                <button class="flex items-center space-x-1 text-blue-600 hover:text-blue-800">
                                                    <span>💬</span>
                                                    <span><?= $post['comments'] ?? 0 ?> Reacties</span>
                                                </button>
                                            </div>

                                            <!-- Comments container -->
                                            <div class="comments-section border-t border-gray-200 pt-3 mt-3">
                                                <!-- Bestaande comments -->
                                                <div class="existing-comments space-y-3 mb-4">
                                                    <?php if (!empty($post['comments_list'])): ?>
                                                        <?php foreach ($post['comments_list'] as $comment): ?>
                                                            <div class="comment-item flex space-x-3 p-2 bg-blue-50 rounded-lg">
                                                                <img src="<?= htmlspecialchars($comment['avatar']) ?>" 
                                                                    alt="<?= htmlspecialchars($comment['user_name']) ?>" 
                                                                    class="w-8 h-8 rounded-full border border-blue-200 flex-shrink-0">
                                                                <div class="flex-grow">
                                                                    <div class="comment-header flex items-center space-x-2 mb-1">
                                                                        <a href="<?= base_url('profile/' . $comment['username']) ?>" class="font-medium text-blue-800 hover:underline text-sm">
                                                                            <?= htmlspecialchars($comment['user_name']) ?>
                                                                        </a>
                                                                        <span class="text-xs text-gray-500"><?= htmlspecialchars($comment['time_ago']) ?></span>
                                                                    </div>
                                                                    <p class="text-gray-700 text-sm"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <!-- Toon alleen als er geen comments zijn -->
                                                        <div class="no-comments text-center text-gray-500 text-sm py-2">
                                                            Nog geen reacties. Wees de eerste!
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Comment formulier -->
                                                <form class="add-comment-form flex space-x-3" data-post-id="<?= $post['id'] ?>">
                                                    <img src="<?= $user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                                         alt="<?= htmlspecialchars($user['name']) ?>" 
                                                         class="w-8 h-8 rounded-full border border-blue-200 flex-shrink-0">
                                                    <div class="flex-grow">
                                                        <textarea name="comment_content" 
                                                                  class="w-full p-2 text-sm border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" 
                                                                  rows="2" 
                                                                  placeholder="Schrijf een reactie..."
                                                                  maxlength="500"></textarea>
                                                        <div class="flex justify-between items-center mt-2">
                                                            <span class="text-xs text-gray-500 comment-char-counter">0/500</span>
                                                            <button type="submit" 
                                                                    class="hyves-button bg-blue-500 hover:bg-blue-600 text-xs px-3 py-1">
                                                                Reageren
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Krabbels van vrienden -->
                            <?php if (!empty($krabbels)): ?>
                                <div class="mb-6">
                                    <h4 class="font-bold text-blue-700 mb-3 border-b border-blue-200 pb-2">Krabbels van vrienden</h4>
                                    <?php foreach ($krabbels as $krabbel): ?>
                                        <div class="krabbel-item bg-white p-4 rounded-lg shadow mb-4">
                                            <div class="flex items-center mb-2">
                                                <img src="<?= base_url('theme-assets/default/images/' . 
                                                          (strpos($krabbel['sender_name'], 'Jan') !== false || 
                                                           strpos($krabbel['sender_name'], 'Thomas') !== false || 
                                                           strpos($krabbel['sender_name'], 'Lucas') !== false || 
                                                           strpos($krabbel['sender_name'], 'Tim') !== false || 
                                                           strpos($krabbel['sender_name'], 'Robin') !== false ? 
                                                           'default-avatar-male.png' : 
                                                           (strpos($krabbel['sender_name'], 'Petra') !== false || 
                                                            strpos($krabbel['sender_name'], 'Emma') !== false || 
                                                            strpos($krabbel['sender_name'], 'Sophie') !== false || 
                                                            strpos($krabbel['sender_name'], 'Nina') !== false || 
                                                            strpos($krabbel['sender_name'], 'Laura') !== false ? 
                                                            'default-avatar-female.png' : 'default-avatar.png'))) ?>" 
                                                       alt="<?= htmlspecialchars($krabbel['sender_name']) ?>" class="w-10 h-10 rounded-full mr-2">
                                                <div class="flex-grow">
                                                    <a href="<?= base_url('profile/' . $krabbel['sender_username']) ?>" class="font-bold text-blue-600"><?= htmlspecialchars($krabbel['sender_name']) ?></a>
                                                    <div class="text-gray-500 text-sm"><?= date('d-m-Y H:i', strtotime($krabbel['created_at'])) ?></div>
                                                </div>
                                            </div>
                                            <p class="text-gray-700"><?= nl2br(htmlspecialchars($krabbel['message'])) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Als er geen content is -->
                            <?php if (empty($posts) && empty($krabbels)): ?>
                                <div class="bg-white p-6 rounded-lg shadow text-center">
                                    <div class="text-6xl mb-4">📭</div>
                                    <h3 class="text-xl font-bold text-blue-800 mb-2">Nog geen krabbels</h3>
                                    <p class="text-gray-600">
                                        <?php if ($viewer_is_owner): ?>
                                            Plaats je eerste bericht of vraag vrienden om een krabbel achter te laten!
                                        <?php else: ?>
                                            Wees de eerste om een krabbel achter te laten!
                                        <?php endif; ?>
                                    </p>
                                </div>
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
                                                <img src="<?= base_url('theme-assets/default/images/' . 
                                                          (strpos($friend['name'], 'Jan') !== false || 
                                                           strpos($friend['name'], 'Thomas') !== false || 
                                                           strpos($friend['name'], 'Lucas') !== false || 
                                                           strpos($friend['name'], 'Tim') !== false || 
                                                           strpos($friend['name'], 'Robin') !== false ? 
                                                           'default-avatar-male.png' : 
                                                           (strpos($friend['name'], 'Petra') !== false || 
                                                            strpos($friend['name'], 'Emma') !== false || 
                                                            strpos($friend['name'], 'Sophie') !== false || 
                                                            strpos($friend['name'], 'Nina') !== false || 
                                                            strpos($friend['name'], 'Laura') !== false ? 
                                                            'default-avatar-female.png' : 'default-avatar.png'))) ?>" 
                                                   alt="<?= htmlspecialchars($friend['name']) ?>" class="w-20 h-20 rounded-full mx-auto mb-2 object-cover">
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