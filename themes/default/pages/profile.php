<?php /* SocialCore profielpagina in Hyves-stijl */ ?>

<?php include THEME_PATH . '/partials/messages.php'; ?>

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
                        <!-- Eigen profiel - toon bewerk knop -->
                        <a href="<?= base_url('profile/edit') ?>" class="hyves-button bg-blue-500 hover:bg-blue-600">
                            Profiel bewerken
                        </a>
                    <?php else: ?>
                        <!-- Andermans profiel - toon slimme vriendschapsknop -->
                        <?php 
                        // Bepaal welke knop en actie te tonen op basis van vriendschapsstatus
                        $buttonText = '';
                        $buttonClass = '';
                        $buttonAction = '';
                        $buttonDisabled = false;
                        
                        switch($friendship_status) {
                            case 'none':
                                // Geen vriendschap - toon voeg vriend toe knop
                                $buttonText = '+ Vriend toevoegen';
                                $buttonClass = 'bg-green-500 hover:bg-green-600';
                                $buttonAction = base_url('?route=friends/add&user=' . $user['username']);
                                break;
                                
                            case 'pending':
                                if ($friendship_direction === 'sent') {
                                    // Ik heb verzoek verstuurd - toon verzoek verzonden (disabled)
                                    $buttonText = 'Verzoek verzonden ⏳';
                                    $buttonClass = 'bg-yellow-500 cursor-not-allowed';
                                    $buttonDisabled = true;
                                } else {
                                    // Ik heb verzoek ontvangen - toon accepteer knop
                                    $buttonText = 'Verzoek accepteren ✓';
                                    $buttonClass = 'bg-green-500 hover:bg-green-600';
                                    $buttonAction = '#'; // We maken later een form voor accepteren
                                }
                                break;
                                
                            case 'accepted':
                                // We zijn vrienden - toon vrienden status
                                $buttonText = 'Vrienden ✓';
                                $buttonClass = 'bg-blue-500 hover:bg-blue-600';
                                $buttonDisabled = true;
                                break;
                                
                            default:
                                // Fallback
                                $buttonText = '+ Vriend toevoegen';
                                $buttonClass = 'bg-green-500 hover:bg-green-600';
                                $buttonAction = base_url('?route=friends/add&user=' . $user['username']);
                                break;
                        }
                        ?>
                        
                        <!-- Slimme vriendschapsknop -->
                        <?php if ($friendship_status === 'pending' && $friendship_direction === 'received'): ?>
                            <!-- Speciale knoppen voor ontvangen verzoek -->
                            <div class="flex space-x-2">
                                <form action="<?= base_url('friends/accept') ?>" method="post" class="flex-1">
                                    <input type="hidden" name="friendship_id" value="<?= $friendship_data['id'] ?? '' ?>">
                                    <button type="submit" class="hyves-button bg-green-500 hover:bg-green-600 w-full">
                                        ✓ Accepteren
                                    </button>
                                </form>
                                <form action="<?= base_url('friends/decline') ?>" method="post" class="flex-1">
                                    <input type="hidden" name="friendship_id" value="<?= $friendship_data['id'] ?? '' ?>">
                                    <button type="submit" class="hyves-button bg-red-500 hover:bg-red-600 w-full">
                                        ✗ Weigeren
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <!-- Standaard vriendschapsknop -->
                            <?php if ($buttonDisabled): ?>
                                <button class="hyves-button <?= $buttonClass ?>" disabled>
                                    <?= $buttonText ?>
                                </button>
                            <?php else: ?>
                                <a href="<?= $buttonAction ?>" class="hyves-button <?= $buttonClass ?>">
                                    <?= $buttonText ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Bericht sturen knop (altijd beschikbaar voor andere gebruikers) -->
                        <a href="<?= base_url('messages/new/' . $user['username']) ?>" class="hyves-button bg-blue-500 hover:bg-blue-600">
                            Bericht sturen
                        </a>
                    <?php endif; ?>
                    
                    <!-- Respect tonen knop (voor iedereen beschikbaar) -->
                    <a href="<?= base_url('profile/' . $user['username'] . '/respect') ?>" class="hyves-button bg-yellow-500 hover:bg-yellow-600">
                        Respect tonen! ✓
                    </a>
                </div>
                <!-- Debug informatie (kun je later weghalen) -->
                <?php if (isset($_GET['debug'])): ?>
                    <div class="mt-4 p-3 bg-gray-100 rounded text-sm">
                        <strong>Debug info:</strong><br>
                        Friendship status: <?= $friendship_status ?? 'undefined' ?><br>
                        Friendship direction: <?= $friendship_direction ?? 'undefined' ?><br>
                        Viewer is owner: <?= $viewer_is_owner ? 'yes' : 'no' ?><br>
                        User ID: <?= $user['id'] ?><br>
                        Current user ID: <?= $_SESSION['user_id'] ?? 'not set' ?>
                    </div>
                <?php endif; ?>
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
                                        <?php 
                                        $form_id = 'profilePostForm';
                                        $context = 'profile';
                                        // $user = $user; (al beschikbaar)
                                        include THEME_PATH . '/partials/post-form.php'; 
                                        ?>
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
                                                <img src="<?= get_avatar_url($post['avatar']) ?>" 
                                                    alt="<?= htmlspecialchars($post['user_name']) ?>" 
                                                    class="w-10 h-10 rounded-full mr-3">
                                                <div class="flex-grow">
                                                    <?php if ($post['is_wall_message'] && !empty($post['wall_message_header'])): ?>
                                                        <!-- Wall message header: Sender → Receiver -->
                                                        <div class="font-bold text-blue-600"><?= htmlspecialchars($post['wall_message_header']) ?></div>
                                                        <div class="text-sm text-gray-600">plaatste een krabbel</div>
                                                    <?php else: ?>
                                                        <!-- Regular timeline post -->
                                                        <div class="font-bold text-blue-600"><?= htmlspecialchars($post['user_name']) ?></div>
                                                    <?php endif; ?>
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
                                                    <p class="text-gray-700"><?= $post['content_formatted'] ?? nl2br(htmlspecialchars($post['content'])) ?></p>
                                                <?php endif; ?>
                                                
                                                <!-- Afbeelding weergave -->
                                                <?php if (!empty($post['media_path'])): ?>
                                                    <div class="mt-3 post-media rounded-lg overflow-hidden border border-blue-200">
                                                        <img src="<?= base_url('uploads/' . $post['media_path']) ?>" 
                                                            alt="Post afbeelding" 
                                                            class="w-full h-auto max-h-96 object-contain">
                                                    </div>
                                                <?php endif; ?>
                                                
                                                    <?php if ($post['type'] === 'link' && !empty($post['preview_url'])): ?>
                                                    <?php get_theme_component('link-preview', ['post' => $post]); ?>
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
                                            
                                            <?php 
                                            // Bereid variabelen voor comments sectie
                                            $comments_data = [
                                                'post_id' => $post['id'],
                                                'comments_list' => $post['comments_list'] ?? [],
                                                'current_user' => $user, // Op profielpagina gebruiken we $user in plaats van $current_user
                                                'show_comment_form' => true,
                                                'show_likes' => true
                                            ];
                                            
                                            // Include de comments sectie partial
                                            extract($comments_data);
                                            include THEME_PATH . '/partials/comments-section.php';
                                            ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- DEBUG: Check krabbels data -->
                            <?php if (isset($_GET['debug'])): ?>
                                <div class="bg-yellow-100 p-3 mb-4 rounded">
                                    <strong>Debug Krabbels:</strong><br>
                                    Aantal krabbels: <?= count($krabbels ?? []) ?><br>
                                    <?php if (!empty($krabbels)): ?>
                                        Eerste krabbel: <?= htmlspecialchars($krabbels[0]['message'] ?? 'geen message') ?><br>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Krabbels van vrienden -->
                            

                            <!-- Als er geen content is - ALLEEN ALS ER ECHT NIKS IS -->
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
                    <div class="section-header mb-4">
                        <h3 class="text-lg font-bold text-blue-800">Vrienden (<?= count($friends) ?>)</h3>
                    </div>
                    
                    <?php if (!empty($friends)): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <?php foreach ($friends as $friend): ?>
                                <div class="friend-card bg-white rounded-lg p-4 shadow-sm border hover:shadow-md transition-shadow text-center">
                                    <a href="<?= base_url('?route=profile&username=' . $friend['username']) ?>" class="block">
                                        <img src="<?= $friend['avatar_url'] ?>" 
                                            alt="<?= htmlspecialchars($friend['display_name']) ?>" 
                                            class="w-16 h-16 rounded-full mx-auto mb-2 object-cover border-2 border-blue-200">
                                        <div class="font-medium text-gray-900 text-sm">
                                            <?= htmlspecialchars($friend['display_name']) ?>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            @<?= htmlspecialchars($friend['username']) ?>
                                        </div>
                                    </a>
                                    
                                    <!-- Actie knoppen -->
                                    <div class="mt-3 flex space-x-2">
                                        <a href="<?= base_url('?route=messages/compose&user=' . $friend['username']) ?>" 
                                        class="flex-1 bg-blue-500 text-white text-xs px-2 py-1 rounded hover:bg-blue-600 text-center transition-colors">
                                            💬 Bericht
                                        </a>
                                        <a href="<?= base_url('?route=profile&username=' . $friend['username']) ?>" 
                                        class="flex-1 bg-gray-500 text-white text-xs px-2 py-1 rounded hover:bg-gray-600 text-center transition-colors">
                                            👤 Profiel
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-friends text-center py-12">
                            <div class="text-6xl mb-4">👥</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Nog geen vrienden</h3>
                            <p class="text-gray-600">
                                <?php if ($viewer_is_owner): ?>
                                    Zoek mensen op om vrienden mee te worden!
                                <?php else: ?>
                                    <?= htmlspecialchars($user['display_name']) ?> heeft nog geen vrienden.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                                
                                <?php elseif ($active_tab == 'fotos'): ?>
                                    <!-- Foto's tab content -->
                                    <div class="photos-container mt-4">
                                        <?php if ($viewer_is_owner): ?>
                                            <form action="<?= base_url('?route=profile/upload-foto') ?>" method="post" enctype="multipart/form-data" class="bg-blue-50 p-4 rounded-lg mb-4">
                                                <h4 class="font-bold text-blue-700 mb-3">Foto uploaden</h4>
                                                <div class="mb-3">
                                                    <label for="photo" class="block mb-1 font-medium">Foto uploaden:</label>
                                                    <input type="file" name="photo" id="photo" accept="image/*" required class="block w-full border rounded p-2">
                                                    <small class="text-gray-500">Toegestane formaten: JPG, PNG, GIF, WebP. Max 5MB.</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="description" class="block mb-1 font-medium">Beschrijving:</label>
                                                    <input type="text" name="description" id="description" placeholder="Optionele beschrijving..." class="w-full p-2 border rounded">
                                                </div>
                                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                    📸 Uploaden
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <!-- Foto grid -->
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                            <?php if (!empty($fotos)): ?>
                                                <?php foreach ($fotos as $foto): ?>
                                                    <div class="photo-card overflow-hidden rounded-lg shadow-md bg-white">
                                                        <div class="h-32 overflow-hidden">
                                                            <img src="<?= htmlspecialchars($foto['full_url']) ?>" 
                                                                alt="<?= htmlspecialchars($foto['description']) ?>" 
                                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-200 cursor-pointer"
                                                                onclick="openPhotoModal('<?= htmlspecialchars($foto['full_url']) ?>', '<?= htmlspecialchars($foto['description']) ?>')">
                                                        </div>
                                                        <div class="p-2">
                                                            <p class="text-sm text-gray-700"><?= htmlspecialchars($foto['description']) ?></p>
                                                            <div class="flex justify-between items-center mt-2">
                                                                <span class="text-xs text-gray-500"><?= date('d-m-Y', strtotime($foto['uploaded_at'])) ?></span>
                                                                
                                                                <!-- Verwijder knop (alleen voor eigenaar) -->
                                                                <?php if ($viewer_is_owner): ?>
                                                                    <button onclick="deletePhoto(<?= intval($foto['post_id']) ?>)" 
                                                                            class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded">
                                                                        🗑️
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-span-full text-center py-12">
                                                    <div class="text-6xl mb-4">📷</div>
                                                    <h3 class="text-xl font-bold text-blue-800 mb-2">Nog geen foto's</h3>
                                                    <p class="text-gray-600">
                                                        <?php if ($viewer_is_owner): ?>
                                                            Upload je eerste foto om je herinneringen te delen!
                                                        <?php else: ?>
                                                            Deze gebruiker heeft nog geen foto's geüpload.
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                        <!-- Foto modal -->
                        <div id="photoModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center" onclick="closePhotoModal()">
                            <div class="max-w-4xl max-h-full p-4 relative">
                                <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
                                <div id="modalDescription" class="text-white text-center mt-4"></div>
                                <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">×</button>
                            </div>
                        </div>

                        <script>
                        function openPhotoModal(imageUrl, description) {
                            document.getElementById('modalImage').src = imageUrl;
                            document.getElementById('modalDescription').textContent = description;
                            document.getElementById('photoModal').classList.remove('hidden');
                        }

                        function closePhotoModal() {
                            document.getElementById('photoModal').classList.add('hidden');
                        }

                        function deletePhoto(postId) {
                            if (confirm('Weet je zeker dat je deze foto wilt verwijderen?')) {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = '<?= base_url("?route=feed/delete") ?>';
                                
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'post_id';
                                input.value = postId;
                                
                                form.appendChild(input);
                                document.body.appendChild(form);
                                form.submit();
                            }
                        }

                        document.addEventListener('keydown', function(e) {
                            if (e.key === 'Escape') {
                                closePhotoModal();
                            }
                        });
                        </script>
                    <?php endif; ?>
                </div> <!-- Sluit .tab-content -->
            </div> <!-- Sluit .w-full md:w-2/3 p-4 -->
        </div> <!-- Sluit .flex flex-col md:flex-row -->
    </div> <!-- Sluit .profile-content -->
</div> <!-- Sluit .profile-container -->