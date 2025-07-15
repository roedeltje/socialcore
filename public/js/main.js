/**
 * SocialCore Main Chat JavaScript - Vereenvoudigd & Universeel
 * /public/js/main.js
 * 
 * Unified chat functionality voor BEIDE core en theme chat views
 * Automatische detectie van pagina type en beschikbare elementen
 */

console.log("🚀 SocialCore Main Chat JavaScript Loading...");

// Global chat state
let chatState = {
    initialized: false,
    config: null,
    elements: {},
    emojiPicker: null,
    lastMessageId: 0,
    pollingInterval: null,
    currentInterface: null, // 'core' of 'theme'
    pageType: null // 'index' of 'conversation'
};

/**
 * Smart initialization - detecteert automatisch wat voor pagina we hebben
 */
function initUniversalChat() {
    // Veilige check voordat we features benaderen
    if (!chatState || !chatState.config || !chatState.config.features) {
        console.warn('ChatState config features not initialized, creating default...');
        chatState = {
            config: {
                features: {
                    real_time: false,
                    search: true,
                    emoji_picker: true,
                    file_upload: true
                }
            },
            interface: 'unknown',
            pageType: 'unknown'
        };
    }

    // ✅ NIEUWE: Laad config van conversation.php
    if (typeof window.SOCIALCORE_CHAT_CONFIG !== 'undefined') {
        console.log("✅ Loading chat config from conversation.php...");
        
        // Merge de configuratie
        chatState.config = {
            ...chatState.config,
            ...window.SOCIALCORE_CHAT_CONFIG,
            // Zorg ervoor dat features behouden blijft
            features: {
                ...chatState.config.features,
                ...window.SOCIALCORE_CHAT_CONFIG.features
            }
        };
        
        console.log("✅ Updated chatState.config:", chatState.config);
    }

    // Nu veilig verder gaan met initialisatie
    detectPageTypeAndInterface();
    
    // Alleen index page features als we op een index page zijn
    if (chatState.pageType === 'index') {
        initIndexPageFeatures();
    } else if (chatState.pageType === 'conversation') {
        initConversationPageFeatures();
    } else if (chatState.pageType === 'compose') {
        initComposePageFeatures();
    }
}

// Helper functie om safe property access te garanteren
function safeGetFeature(featureName, defaultValue = false) {
    return chatState?.config?.features?.[featureName] ?? defaultValue;
}

// Gebruik deze helper functie in plaats van directe toegang:
// Voorbeeld:
function someFunction() {
    if (safeGetFeature('real_time')) {
        // Real-time feature code
    }
    
    if (safeGetFeature('search', true)) { // default true voor search
        // Search feature code
    }
}

// Initialisatie met error handling
function initializeChatSystem() {
    try {
        // Probeer chat systeem te initialiseren
        initUniversalChat();
    } catch (error) {
        console.error('Error initializing chat system:', error);
        
        // Fallback: minimale initialisatie
        if (!window.chatState) {
            window.chatState = {
                config: {
                    features: {
                        real_time: false,
                        search: true,
                        emoji_picker: true,
                        file_upload: true
                    }
                },
                interface: 'core', // of 'theme'
                pageType: 'index'  // of 'conversation'
            };
        }
    }
}

// DOM ready event met timeout als backup
// document.addEventListener('DOMContentLoaded', function() {
//     initializeChatSystem();
// });

// // Backup initialisatie met timeout (zoals je al hebt op regel 1011)
// setTimeout(function() {
//     if (!window.chatState || !window.chatState.config) {
//         console.log('🔄 Backup chat initialization triggered');
//         initializeChatSystem();
//     } else {
//         console.log('✅ Chat system already initialized, skipping backup');
//     }
// }, 200);

let chatInitialized = false;

function initChatOnce() {
    if (chatInitialized) {
        console.log('✅ Chat already initialized, skipping...');
        return;
    }
    
    chatInitialized = true;
    console.log('🚀 Initializing chat system (single time)...');
    initializeChatSystem();
}

// DOM ready event
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM ready, initializing chat...');
    initChatOnce();
});

// Backup met timeout (alleen als nog niet geïnitialiseerd)
setTimeout(function() {
    if (!chatInitialized) {
        console.log('🔄 Backup chat initialization triggered');
        initChatOnce();
    }
}, 500);

/**
 * Smart detectie van pagina type en interface
 */
function detectPageTypeAndInterface() {
    console.log("🔍 Starting detection...");
    
    // === INTERFACE DETECTIE ===
    // Check voor Core Chat elementen (meer specifieke selectors)
    const coreElements = [
        '.chat-app',
        '.core-chat-container', 
        '.core-chat-header',
        '#messageForm',
        '#messageInput',
        '.chat-compose-container'
    ];
    
    // Check voor Theme Chat elementen  
    const themeElements = [
        '.hyves-chat-container',
        '.theme-chat-container',
        '.theme-chat-header',
        '#hyves-message-form',
        '.hyves-chat-header'
    ];
    
    let interfaceFound = false;
    
    // Test Core elements
    for (const selector of coreElements) {
        if (document.querySelector(selector)) {
            chatState.currentInterface = 'core';
            interfaceFound = true;
            console.log(`✅ Core interface detected via: ${selector}`);
            break;
        }
    }
    
    // Test Theme elements (als Core niet gevonden)
    if (!interfaceFound) {
        for (const selector of themeElements) {
            if (document.querySelector(selector)) {
                chatState.currentInterface = 'theme';
                interfaceFound = true;
                console.log(`✅ Theme interface detected via: ${selector}`);
                break;
            }
        }
    }
    
    // Fallback: Check URL en default naar Core
    if (!interfaceFound) {
        const url = window.location.href;
        if (url.includes('chat/')) {
            chatState.currentInterface = 'core'; // Default naar core
            console.log('⚠️ Chat route detected, defaulting to Core interface');
        } else {
            chatState.currentInterface = 'unknown';
            console.log('❌ No chat interface detected');
        }
    }

    // === PAGINA TYPE DETECTIE ===
    const url = window.location.href;
    const route = new URLSearchParams(window.location.search).get('route') || '';
    
    console.log(`🔍 URL: ${url}`);
    console.log(`🔍 Route parameter: ${route}`);
    
    // Specifieke route checks
    if (route === 'chat/compose' || url.includes('chat/compose')) {
        chatState.pageType = 'compose';
        console.log('✅ Chat Compose page detected');
        
    } else if (route === 'chat/index' || url.includes('chat/index')) {
        chatState.pageType = 'index';
        console.log('✅ Chat Index page detected');
        
    } else if (route && route.startsWith('chat/conversation') || url.includes('chat/conversation')) {
        chatState.pageType = 'conversation';
        console.log('✅ Chat Conversation page detected');
        
    } else if (route && route.startsWith('chat/') || url.includes('chat/')) {
        // Fallback voor andere chat pagina's - detecteer op basis van elementen
        const hasMessageForm = !!(
            document.getElementById('messageForm') || 
            document.getElementById('hyves-message-form') ||
            document.querySelector('.message-form')
        );
        
        const hasConversationList = !!(
            document.querySelector('.conversation-item') ||
            document.querySelector('.hyves-conversation-card') ||
            document.querySelector('.chat-item')
        );
        
        const hasFriendsList = !!(
            document.querySelector('.friend-item') ||
            document.querySelector('.user-item') ||
            document.querySelector('.friends-list')
        );

        if (hasMessageForm) {
            chatState.pageType = 'conversation';
            console.log('✅ Conversation page detected via message form');
        } else if (hasFriendsList) {
            chatState.pageType = 'compose';
            console.log('✅ Compose page detected via friends list');
        } else if (hasConversationList) {
            chatState.pageType = 'index';
            console.log('✅ Index page detected via conversation list');
        } else {
            chatState.pageType = 'chat';
            console.log('✅ Generic Chat page detected');
        }
        
    } else {
        chatState.pageType = 'other';
        console.log('📄 Non-chat page detected');
    }

    // === ELEMENT DETECTIE ===
    if (chatState.pageType === 'conversation') {
        getConversationElements();
    } else if (chatState.pageType === 'index') {
        getIndexElements();
    } else if (chatState.pageType === 'compose') {
        getComposeElements(); // Nieuwe functie voor compose page
    }

    console.log("🎯 Final Detection Result:", {
        interface: chatState.currentInterface,
        pageType: chatState.pageType,
        url: url,
        route: route
    });
}

function getComposeElements() {
    console.log("👥 Getting compose page elements...");
    
    chatState.elements = {
        // Search elementen voor vrienden zoeken
        searchInput: document.getElementById('friend-search') || 
                    document.querySelector('.friend-search') ||
                    document.querySelector('[data-search="friends"]') ||
                    document.querySelector('.search-input'),
        
        // Vrienden lijst
        friendsList: document.querySelector('.friends-list') ||
                    document.querySelector('.users-list') ||
                    document.querySelector('.friend-items-container'),
                    
        // Friend items
        friendItems: document.querySelectorAll('.friend-item') ||
                    document.querySelectorAll('.user-item')
    };
    
    console.log("👥 Compose elements found:", {
        hasSearchInput: !!chatState.elements.searchInput,
        hasFriendsList: !!chatState.elements.friendsList,
        friendItemsCount: chatState.elements.friendItems.length
    });
}

/**
 * VOEG DEZE NIEUWE FUNCTIE TOE voor compose page features:
 */
function initComposePageFeatures() {
    console.log("👥 Initializing compose page features for", chatState.currentInterface);
    
    // Friend search functionaliteit
    if (chatState.config.features.search) {
        initFriendSearch();
    }
    
    console.log('✅ Compose page features initialized');
}

/**
 * VOEG DEZE NIEUWE FUNCTIE TOE voor friend search:
 */
function initFriendSearch() {
    console.log('👥 Initializing friend search...');
    
    const searchInput = chatState.elements.searchInput;
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            filterFriends(query);
        });
        console.log('✅ Friend search initialized');
    } else {
        console.log('⚠️ Friend search input not found');
    }
}

/**
 * VOEG DEZE NIEUWE FUNCTIE TOE voor filtering friends:
 */
function filterFriends(query) {
    const friendItems = chatState.elements.friendItems || 
                       document.querySelectorAll('.friend-item, .user-item');
    
    friendItems.forEach(item => {
        const nameElement = item.querySelector('.friend-name') || 
                           item.querySelector('.user-name') ||
                           item.querySelector('[data-friend-name]');
        
        if (nameElement) {
            const name = nameElement.textContent.toLowerCase();
            const matches = name.includes(query);
            item.style.display = matches ? '' : 'none';
        }
    });
    
    console.log(`🔍 Filtered friends with query: "${query}"`);
}

/**
 * Haal conversation page elementen op (gestandaardiseerde IDs)
 */
function getConversationElements() {
    console.log("🔍 Getting conversation elements...");
    
    chatState.elements = {
        messageForm: document.getElementById('messageForm'),
        messageInput: document.getElementById('messageInput'),
        sendButton: document.getElementById('sendButton'),
        charCounter: document.getElementById('charCounter'),
        attachmentButton: document.getElementById('attachmentButton'),
        fileInput: document.getElementById('fileInput'),
        emojiButton: document.getElementById('emojiButton'),
        messagesContainer: document.getElementById('messagesContainer'),
        messagesList: document.getElementById('messagesList'),
        typingIndicator: document.getElementById('typingIndicator')
    };
    
    console.log("🔍 Elements found:", {
        messageForm: !!chatState.elements.messageForm,
        messageInput: !!chatState.elements.messageInput,
        sendButton: !!chatState.elements.sendButton,
        attachmentButton: !!chatState.elements.attachmentButton,
        fileInput: !!chatState.elements.fileInput,
        emojiButton: !!chatState.elements.emojiButton
    });
}

/**
 * Haal index page elementen op (gestandaardiseerde IDs)
 */
function getIndexElements() {
    chatState.elements = {
        searchToggle: document.getElementById('searchToggle'),
        searchInput: document.getElementById('searchInput'),
        searchContainer: document.getElementById('searchContainer')
    };
}

/**
 * Initialiseer features voor index pagina's (conversation overview)
 */
function initIndexPageFeatures() {
    console.log("📋 Initializing index page features for", chatState.currentInterface);
    
    // Veilige check voordat we features benaderen
    if (!chatState || !chatState.config || !chatState.config.features) {
        console.warn('❌ ChatState config features not initialized, skipping index page features');
        return;
    }

    // Search functionaliteit  
    if (chatState.config.features.search) {
        initUniversalSearch();
    }

    // Real-time features (als geactiveerd)
    if (chatState.config.features.real_time) {
        console.log('🔄 Real-time features enabled');
        initIndexRealTime();
    }

    console.log('✅ Index page features initialized');
}

/**
 * Initialiseer features voor conversation pagina's (actual chat)
 */
function initConversationPageFeatures() {
    console.log("💬 DEBUG: Initializing conversation page features");
    
    // ✅ DEBUG: Log chatState voor troubleshooting
    console.log("💬 DEBUG: chatState.config:", chatState.config);
    console.log("💬 DEBUG: chatState.config.features:", chatState.config.features);
    
    // Alle chat functionaliteiten
    console.log("💬 DEBUG: Starting initMessageInput...");
    initMessageInput();
    
    console.log("💬 DEBUG: Starting initFormHandling...");
    initFormHandling();
    
    console.log("💬 DEBUG: Starting initFileUpload...");
    initFileUpload();        // ✅ Deze MOET worden aangeroepen
    
    console.log("💬 DEBUG: Starting initChatEmojiPicker...");
    initChatEmojiPicker();
    
    // ❌ UITGECOMMENT omdat je die hebt uitgeschakeld:
    initImageModal();     
    
    console.log("💬 DEBUG: All init functions called");
    
    // Real-time messaging (als enabled)
    if (chatState.config.features && chatState.config.features.real_time) {
        console.log("💬 DEBUG: Starting real-time features...");
        initConversationRealTime();
    }
    
    // Scroll to bottom
    console.log("💬 DEBUG: Scrolling to bottom...");
    scrollToBottom();
    
    console.log("✅ DEBUG: Conversation page features initialization complete");
}

/**
 * Universele search functionaliteit voor index pagina's
 */
function initUniversalSearch() {
    const { searchToggle, searchInput, searchContainer } = chatState.elements;
    
    if (!searchToggle || !searchContainer || !searchInput) {
        console.log("⚠️ Search elements not available");
        return;
    }

    searchToggle.addEventListener('click', function() {
        const isVisible = searchContainer.style.display !== 'none';
        searchContainer.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible) {
            searchInput.focus();
        } else {
            searchInput.value = '';
            filterConversations('');
        }
    });

    searchInput.addEventListener('input', function(e) {
        filterConversations(e.target.value);
    });

    console.log("✅ Universal search initialized for", chatState.currentInterface);
}

/**
 * Filter conversations op index pagina (universele classes)
 */
function filterConversations(query) {
    const conversations = document.querySelectorAll('.conversation-item');
    const lowerQuery = query.toLowerCase();
    
    conversations.forEach(item => {
        const friendName = item.querySelector('.friend-name')?.textContent.toLowerCase() || '';
        const lastMessage = item.querySelector('.last-message')?.textContent.toLowerCase() || '';
        
        const matches = friendName.includes(lowerQuery) || lastMessage.includes(lowerQuery);
        
        // Pas display style aan op basis van interface
        if (chatState.currentInterface === 'theme') {
            item.style.display = matches ? 'block' : 'none';
        } else {
            item.style.display = matches ? 'flex' : 'none';
        }
    });
}

/**
 * Initialize message input functionality (conversation pages)
 */
function initMessageInput() {
    const { messageInput, charCounter, sendButton } = chatState.elements;
    
    if (!messageInput || !sendButton) {
        console.log("❌ Message input elements not found");
        return;
    }
    
    // Character counter and auto-resize
    messageInput.addEventListener('input', function() {
        // Auto-resize
        autoResizeTextarea(this);
        
        // Character counter
        updateCharacterCounter();
        
        // Update send button state
        updateSendButtonState();
    });

    // Enter key to send (Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendButton.disabled && !messageInput.disabled) {
                sendMessage();
            }
        }
    });

    console.log("✅ Message input initialized for", chatState.currentInterface);
}

/**
 * Initialize form handling (conversation pages)
 */
function initFormHandling() {
    const { messageForm } = chatState.elements;
    
    if (!messageForm) {
        console.log("❌ Message form not found");
        return;
    }
    
    messageForm.addEventListener('submit', function(e) {
        console.log("📤 Form submit triggered");
        e.preventDefault();
        
        if (!chatState.elements.sendButton.disabled && !chatState.elements.messageInput.disabled) {
            console.log("📤 Calling sendMessage()");
            sendMessage();
        } else {
            console.log("📤 Blocked - button or input disabled");
        }
    });

    console.log("✅ Form handling initialized for", chatState.currentInterface);
}

/**
 * Initialize file upload functionality (conversation pages)
 */
function initFileUpload() {
    console.log("🔧 DEBUG: initFileUpload() called");
    
    const { attachmentButton, fileInput } = chatState.elements;
    const fileUploadEnabled = chatState.config?.features?.file_upload;
    
    if (!fileUploadEnabled || !attachmentButton || !fileInput) {
        console.log("⚠️ File upload not available or disabled");
        return;
    }

    // ✅ FIX: Verwijder bestaande listeners eerst
    const newAttachmentButton = attachmentButton.cloneNode(true);
    attachmentButton.parentNode.replaceChild(newAttachmentButton, attachmentButton);
    
    const newFileInput = fileInput.cloneNode(true);
    fileInput.parentNode.replaceChild(newFileInput, fileInput);
    
    // Update chatState elements met nieuwe nodes
    chatState.elements.attachmentButton = newAttachmentButton;
    chatState.elements.fileInput = newFileInput;

    console.log("✅ DEBUG: Cleaned old listeners, setting up new ones...");

    // ✅ NIEUWE EVENT LISTENERS (geen duplicates mogelijk)
    newAttachmentButton.addEventListener('click', function() {
        console.log("📎 Attachment button clicked!");
        newFileInput.click();
    });

    newFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            console.log("📷 File selected:", file.name);
            showInlineImagePreview(file);
        }
    });

    console.log("✅ File upload setup complete (single listeners)!");
}

/**
 * ✅ NIEUWE FUNCTIE: Show inline image preview (zoals tijdlijn)
 */
function showInlineImagePreview(file) {
    console.log("🖼️ Showing INLINE image preview for:", file.name);
    
    // Validation
    const maxSize = (chatState.config.max_file_size || 2048) * 1024;
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (file.size > maxSize) {
        const maxMB = Math.round(maxSize / (1024 * 1024) * 10) / 10;
        alert(`Bestand is te groot (max ${maxMB}MB)`);
        clearFileInput();
        return;
    }
    
    if (!allowedTypes.includes(file.type)) {
        alert('Alleen JPG, PNG, GIF en WebP bestanden toegestaan');
        clearFileInput();
        return;
    }
    
    // Get elements
    const previewContainer = document.getElementById('chatImagePreview');
    const previewImage = document.getElementById('chatPreviewImage');
    const captionTextarea = document.getElementById('chatImageCaption');
    const captionCounter = document.getElementById('chatCaptionCounter');
    const removeButton = document.getElementById('chatRemoveImage');
    
    if (!previewContainer || !previewImage) {
        console.error("❌ Inline preview elements not found!");
        return;
    }
    
    // ✅ FIX: Verwijder bestaande caption listeners
    if (captionTextarea) {
        const newCaptionTextarea = captionTextarea.cloneNode(true);
        captionTextarea.parentNode.replaceChild(newCaptionTextarea, captionTextarea);
        
        // Setup nieuwe caption listener
        newCaptionTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const counter = document.getElementById('chatCaptionCounter');
            if (counter) {
                counter.textContent = length;
                
                if (length > 200) {
                    counter.style.color = '#dc3545';
                } else if (length > 160) {
                    counter.style.color = '#ffc107';
                } else {
                    counter.style.color = '#667781';
                }
            }
        });
        
        // Reset
        newCaptionTextarea.value = '';
        if (captionCounter) {
            captionCounter.textContent = '0';
            captionCounter.style.color = '#667781';
        }
    }
    
    // ✅ FIX: Verwijder bestaande remove button listeners  
    if (removeButton) {
        const newRemoveButton = removeButton.cloneNode(true);
        removeButton.parentNode.replaceChild(newRemoveButton, removeButton);
        
        newRemoveButton.addEventListener('click', function() {
            console.log("🗑️ Remove button clicked");
            hideInlineImagePreview();
            clearFileInput();
        });
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImage.src = e.target.result;
        previewContainer.style.display = 'block';
        console.log("✅ Inline preview visible (single instance)!");
    };
    reader.readAsDataURL(file);
}

/**
 * ✅ NIEUWE FUNCTIE: Hide inline image preview
 */
function hideInlineImagePreview() {
    const previewContainer = document.getElementById('chatImagePreview');
    const captionTextarea = document.getElementById('chatImageCaption');
    
    if (previewContainer) {
        previewContainer.style.display = 'none';
    }
    
    if (captionTextarea) {
        captionTextarea.value = '';
    }
    
    console.log("✅ Inline preview hidden");
}

/**
 * ✅ NIEUWE FUNCTIE: Clear file input
 */
function clearFileInput() {
    const { fileInput } = chatState.elements;
    if (fileInput) {
        fileInput.value = '';
    }
}

/**
 * ✅ AANGEPASTE FUNCTIE: Send message with inline preview support
 */
function sendMessage() {
    console.log("📤 sendMessage() called for", chatState.currentInterface);
    
    // ✅ NIEUWE: Zorg ervoor dat config compleet is
    ensureValidConfig();
    
    const { messageInput, sendButton, fileInput } = chatState.elements;
    const content = messageInput.value.trim();
    
    console.log("📝 Content:", content);
    console.log("🔒 Input disabled:", messageInput.disabled);

    // Check for content OR image OR caption
    const hasContent = !empty(content);
    const hasPhoto = fileInput && fileInput.files && fileInput.files[0];
    
    // Check caption from inline preview
    const captionTextarea = document.getElementById('chatImageCaption');
    const hasCaption = captionTextarea && captionTextarea.value.trim();
    
    if (!hasContent && !hasPhoto && !hasCaption) {
        console.log("❌ Validation failed - no content, photo, or caption");
        return;
    }
    
    const maxLength = chatState.config.max_message_length || 1000;
    if (content.length > maxLength) {
        alert(`Bericht is te lang (max ${maxLength} karakters)`);
        return;
    }
    
    console.log("✅ Validation passed");
    
    // ✅ CHECK: Zorg ervoor dat URLs beschikbaar zijn
    if (!chatState.config.urls || !chatState.config.urls.send) {
        console.error("❌ Send URL not configured!");
        console.log("Config:", chatState.config);
        alert("Configuratiefout: Send URL niet gevonden");
        return;
    }
    
    // Disable form to prevent double submission
    setFormDisabled(true);
    
    // Create FormData
    const formData = new FormData();
    formData.append('friend_id', chatState.config.friend_id);
    
    // Use caption as content if no text content
    let finalContent = content;
    if (!finalContent && hasCaption) {
        finalContent = captionTextarea.value.trim();
    }
    
    formData.append('content', finalContent);
    
    // Add file if present
    if (hasPhoto) {
        console.log("📷 Adding file:", fileInput.files[0].name);
        formData.append('message_photo', fileInput.files[0]);
    }
    
    console.log("🌐 Sending to:", chatState.config.urls.send);
    
    // Send request
    fetch(chatState.config.urls.send, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log("📨 Response received:", response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("📊 Response data:", data);
        if (data.success) {
            console.log("✅ Message sent successfully");
            
            // Clear form including inline preview
            resetFormWithPreview();
            
            // ✅ NIEUWE LOGICA: Core vs Theme handling
            if (chatState.currentInterface === 'core' && data.message) {
                // Core: Add message to DOM without reload
                addMessageToCore(data.message);
            } else if (chatState.config.features && chatState.config.features.real_time) {
                // Theme real-time: Message will appear via polling
                setTimeout(() => scrollToBottom(), 100);
            } else {
                // Theme non-real-time: Reload page
                window.location.reload();
            }
        } else {
            console.log("❌ Server error:", data.message);
            alert('Fout: ' + (data.message || 'Onbekende fout'));
            setFormDisabled(false);
        }
    })
    .catch(error => {
        console.error("💥 Network error:", error);
        alert('Fout bij verzenden van bericht: ' + error.message);
        setFormDisabled(false);
    });
}

/**
 * Core AJAX message handler - voegt bericht toe aan DOM zonder page reload
 */
function addMessageToCore(messageData) {
    console.log('➕ Core Chat: Adding message to DOM:', messageData);
    
    const messagesList = document.getElementById('messagesList');
    if (!messagesList) {
        console.error('❌ Core Chat: Messages list not found');
        return;
    }
    
    // Remove "no messages" state if present
    const noMessages = messagesList.querySelector('.no-messages');
    if (noMessages) {
        noMessages.remove();
    }
    
    // Create message HTML
    const isOwn = messageData.sender_id == chatState.config.current_user_id;
    const messageClass = isOwn ? 'message message-own' : 'message message-other';
    
    let mediaHtml = '';
    if (messageData.message_type === 'image' && messageData.media_info) {
        mediaHtml = `
            <div class="message-media">
                <img src="${messageData.media_info.media_url}" 
                     alt="Afbeelding"
                     class="message-image"
                     onclick="openChatImageModal('${messageData.media_info.media_url}')">
            </div>
        `;
    }
    
    let textHtml = '';
    if (messageData.message_text && messageData.message_text.trim()) {
        textHtml = `<p class="message-text">${messageData.message_text.replace(/\n/g, '<br>')}</p>`;
    }
    
    const messageHtml = `
        <div class="${messageClass}" data-message-id="${messageData.id}">
            <!-- ✅ ALTIJD avatar tonen -->
            <div class="message-avatar-container">
                <img src="${messageData.sender_avatar_url}" 
                     alt="Avatar" 
                     class="message-avatar">
            </div>
            
            <div class="message-bubble">
                <div class="message-content">
                    ${mediaHtml}
                    ${textHtml}
                </div>
                
                <div class="message-footer">
                    <span class="message-time">Nu</span>
                    ${isOwn ? '<span class="message-status">✓</span>' : ''}
                </div>
            </div>
        </div>
    `;
    
    // Add to container
    messagesList.insertAdjacentHTML('beforeend', messageHtml);
    
    // Scroll to bottom
    scrollToBottom();
    
    console.log('✅ Core Chat: Message added successfully');
}

/**
 * ✅ AANGEPASTE FUNCTIE: Reset form including inline preview
 */
function resetFormWithPreview() {
    const { messageInput, sendButton, fileInput, charCounter } = chatState.elements;
    
    // Reset text input
    messageInput.value = '';
    messageInput.style.height = 'auto';
    messageInput.disabled = false;
    
    // Reset send button
    sendButton.disabled = true;
    
    if (chatState.currentInterface === 'theme') {
        sendButton.innerHTML = '📩 Versturen';
    } else {
        sendButton.textContent = '➤';
    }
    
    // Reset file input
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Reset character counter
    updateCharacterCounter();
    
    // Hide inline preview
    hideInlineImagePreview();
    
    // Reset caption
    const captionTextarea = document.getElementById('chatImageCaption');
    const captionCounter = document.getElementById('chatCaptionCounter');
    if (captionTextarea) {
        captionTextarea.value = '';
    }
    if (captionCounter) {
        captionCounter.textContent = '0';
        captionCounter.style.color = '#667781';
    }
    
    messageInput.focus();
}

/**
 * Initialize chat-specific emoji picker (conversation pages)
 */
function initChatEmojiPicker() {
    const { emojiButton: elementEmojiButton, messageInput } = chatState.elements;
    
    console.log("😊 Initializing chat emoji picker...", {
        emojiButton: !!elementEmojiButton,
        messageInput: !!messageInput,
        features: chatState.config.features
    });
    
    // ✅ Verbeterde feature check
    const emojiEnabled = chatState.config.features && 
                        (chatState.config.features.emoji_picker || 
                         chatState.config.features.emoji || 
                         true); // Default naar true als niet gespecificeerd
    
    if (!emojiEnabled) {
        console.log("⚠️ Emoji picker disabled in config");
        return;
    }
    
    let finalEmojiButton = elementEmojiButton;
    
    if (!finalEmojiButton) {
        console.log("⚠️ Emoji button not found - trying alternative selectors");
        
        // Probeer alternatieve selectors
        finalEmojiButton = document.querySelector('.emoji-btn') ||
                          document.querySelector('.emoji-picker-btn') ||
                          document.querySelector('[data-emoji-trigger]') ||
                          document.querySelector('#emojiButton');
        
        if (finalEmojiButton) {
            console.log("✅ Found emoji button with alternative selector");
            chatState.elements.emojiButton = finalEmojiButton;
        } else {
            console.log("❌ No emoji button found anywhere");
            return;
        }
    }

    const emojis = [
        '😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣',
        '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰',
        '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜',
        '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙',
        '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍',
        '🎉', '🎈', '🎁', '🌟', '⭐', '💫', '✨', '🔥'
    ];
    
    finalEmojiButton.addEventListener('click', function(e) {
        e.preventDefault();
        console.log("😊 Chat emoji button clicked");
        
        if (chatState.emojiPicker) {
            chatState.emojiPicker.remove();
            chatState.emojiPicker = null;
            return;
        }
        
        // Create emoji picker
        chatState.emojiPicker = createChatEmojiPicker(emojis, finalEmojiButton, messageInput);
        document.body.appendChild(chatState.emojiPicker);
    });
    
    // Close picker on outside click
    document.addEventListener('click', function(e) {
        if (chatState.emojiPicker && 
            !chatState.emojiPicker.contains(e.target) && 
            e.target !== finalEmojiButton) {
            chatState.emojiPicker.remove();
            chatState.emojiPicker = null;
        }
    });

    console.log("✅ Chat emoji picker initialized for", chatState.currentInterface);
}


/**
 * Create chat-specific emoji picker element
 */
function createChatEmojiPicker(emojis, button, input) {
    const picker = document.createElement('div');
    picker.className = 'socialcore-chat-emoji-picker';
    
    // Base styling that works for both interfaces
    const baseStyle = {
        position: 'fixed',
        background: 'white',
        border: '1px solid #e1e5e9',
        borderRadius: '12px',
        padding: '12px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        display: 'grid',
        gridTemplateColumns: 'repeat(8, 1fr)',
        gap: '8px',
        width: '280px',
        maxHeight: '200px',
        overflowY: 'auto',
        zIndex: '10000'
    };
    
    // Apply theme-specific styling
    if (chatState.currentInterface === 'theme') {
        // Theme-style theming
        baseStyle.border = '2px solid #ff6600';
        baseStyle.background = 'linear-gradient(135deg, #fff9f0 0%, #ffffff 100%)';
        baseStyle.boxShadow = '0 8px 25px rgba(255, 102, 0, 0.15)';
    }
    
    // Apply all styles
    Object.assign(picker.style, baseStyle);
    
    // Position above button
    const rect = button.getBoundingClientRect();
    picker.style.bottom = (window.innerHeight - rect.top + 10) + 'px';
    picker.style.left = rect.left + 'px';
    
    // Add emojis
    emojis.forEach(emoji => {
        const span = document.createElement('span');
        span.textContent = emoji;
        span.className = 'chat-emoji-item';
        
        const spanStyle = {
            fontSize: '20px',
            cursor: 'pointer',
            padding: '4px',
            borderRadius: '6px',
            textAlign: 'center',
            transition: 'background-color 0.2s'
        };
        
        Object.assign(span.style, spanStyle);
        
        // Theme-specific hover colors
        span.addEventListener('mouseenter', () => {
            if (chatState.currentInterface === 'theme') {
                span.style.background = '#fff4e6'; // Theme orange tint
            } else {
                span.style.background = '#f0f2f5'; // Core neutral
            }
        });
        
        span.addEventListener('mouseleave', () => {
            span.style.background = 'transparent';
        });
        
        span.addEventListener('click', function() {
            insertChatEmojiAtCursor(input, emoji);
            picker.remove();
            chatState.emojiPicker = null;
        });
        
        picker.appendChild(span);
    });
    
    return picker;
}

/**
 * Insert emoji at cursor position in chat input
 */
function insertChatEmojiAtCursor(input, emoji) {
    const cursorPos = input.selectionStart;
    const textBefore = input.value.substring(0, cursorPos);
    const textAfter = input.value.substring(cursorPos);
    
    input.value = textBefore + emoji + textAfter;
    input.selectionStart = input.selectionEnd = cursorPos + emoji.length;
    input.focus();
    
    // Trigger input event to update counter and button state
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

/**
 * Initialize image modal functionality
 */
// function initImageModal() {
//     // Make sure modal functions are available globally
//     window.openChatImageModal = function(imageSrc) {
//         const modalIds = ['imageModal', 'hyves-image-modal'];
//         const imageIds = ['modalImage', 'hyves-modal-image'];
        
//         let modal = null;
//         let modalImage = null;
        
//         // Find modal elements
//         for (let i = 0; i < modalIds.length; i++) {
//             modal = document.getElementById(modalIds[i]);
//             if (modal) {
//                 modalImage = document.getElementById(imageIds[i]);
//                 break;
//             }
//         }
        
//         if (modal && modalImage) {
//             modalImage.src = imageSrc;
//             modal.style.display = 'flex';
//         }
//     };

//     window.closeChatImageModal = function() {
//         const modalIds = ['imageModal', 'hyves-image-modal'];
        
//         modalIds.forEach(id => {
//             const modal = document.getElementById(id);
//             if (modal) {
//                 modal.style.display = 'none';
//             }
//         });
//     };

//     // ESC key to close modal
//     document.addEventListener('keydown', function(e) {
//         if (e.key === 'Escape') {
//             window.closeChatImageModal();
//         }
//     });

//     console.log("✅ Image modal initialized for", chatState.currentInterface);
// }

/**
 * Initialize real-time features voor index pages
 */
function initIndexRealTime() {
    console.log("🔄 Index real-time features initialized");
    // TODO: Poll for new conversations, unread counts, etc.
}

/**
 * Initialize real-time features voor conversation pages
 */
function initConversationRealTime() {
    if (!chatState.config.features || !chatState.config.features.real_time) {
        return;
    }

    // Start polling for new messages
    startMessagePolling();
    
    console.log("✅ Conversation real-time features initialized");
}

/**
 * Main send message function (conversation pages only)
 */
function sendMessage() {
    console.log("📤 sendMessage() called for", chatState.currentInterface);
    
    // Zorg ervoor dat config compleet is
    ensureValidConfig();
    
    const { messageInput, sendButton, fileInput } = chatState.elements;
    const content = messageInput.value.trim();
    
    console.log("📝 Content:", content);
    console.log("🔒 Input disabled:", messageInput.disabled);

    // Check for content OR image OR caption
    const hasContent = !empty(content);
    const hasPhoto = fileInput && fileInput.files && fileInput.files[0];
    
    // Check caption from inline preview
    const captionTextarea = document.getElementById('chatImageCaption');
    const hasCaption = captionTextarea && captionTextarea.value.trim();
    
    if (!hasContent && !hasPhoto && !hasCaption) {
        console.log("❌ Validation failed - no content, photo, or caption");
        return;
    }
    
    const maxLength = chatState.config.max_message_length || 1000;
    if (content.length > maxLength) {
        showNotification(`Bericht is te lang (max ${maxLength} karakters)`, 'error');
        return;
    }
    
    console.log("✅ Validation passed");
    
    // Check URLs beschikbaar zijn
    if (!chatState.config.urls || !chatState.config.urls.send) {
        console.error("❌ Send URL not configured!");
        console.log("Config:", chatState.config);
        showNotification("Configuratiefout: Send URL niet gevonden", 'error');
        return;
    }
    
    // Disable form to prevent double submission
    setFormDisabled(true);
    
    // Create FormData
    const formData = new FormData();
    formData.append('friend_id', chatState.config.friend_id);
    
    // Use caption as content if no text content
    let finalContent = content;
    if (!finalContent && hasCaption) {
        finalContent = captionTextarea.value.trim();
    }
    
    formData.append('content', finalContent);
    
    // Add file if present
    if (hasPhoto) {
        console.log("📷 Adding file:", fileInput.files[0].name);
        formData.append('message_photo', fileInput.files[0]);
    }
    
    console.log("🌐 Sending to:", chatState.config.urls.send);
    
    // Send request
    fetch(chatState.config.urls.send, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log("📨 Response received:", response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("📊 Response data:", data);
        if (data.success) {
            console.log("✅ Message sent successfully");
            
            // Clear form including inline preview
            resetFormWithPreview();
            
            // NIEUWE LOGICA: Core vs Theme handling
            if (chatState.currentInterface === 'core' && data.message) {
                // Core: Add message to DOM without reload
                addMessageToCore(data.message);
                showNotification('Bericht verzonden!', 'success');
            } else if (chatState.config.features && chatState.config.features.real_time) {
                // Theme real-time: Message will appear via polling
                setTimeout(() => scrollToBottom(), 100);
            } else {
                // Theme non-real-time: Reload page
                window.location.reload();
            }
        } else {
            console.log("❌ Server error:", data.message);
            showNotification('Fout: ' + (data.message || 'Onbekende fout'), 'error');
            setFormDisabled(false);
        }
    })
    .catch(error => {
        console.error("💥 Network error:", error);
        showNotification('Netwerkfout bij versturen', 'error');
        setFormDisabled(false);
    });
}

// ===== 2. NIEUWE FUNCTIE: addMessageToCore =====
function addMessageToCore(messageData) {
    console.log('➕ Core Chat: Adding message to DOM:', messageData);
    
    const messagesList = document.getElementById('messagesList');
    if (!messagesList) {
        console.error('❌ Core Chat: Messages list not found');
        return;
    }
    
    // Remove "no messages" state if present
    const noMessages = messagesList.querySelector('.no-messages');
    if (noMessages) {
        noMessages.remove();
    }
    
    // Create message HTML
    const isOwn = messageData.sender_id == chatState.config.current_user_id;
    const messageClass = isOwn ? 'message message-own' : 'message message-other';
    
    let mediaHtml = '';
    if (messageData.message_type === 'image' && messageData.media_info) {
        mediaHtml = `
            <div class="message-media">
                <img src="${messageData.media_info.media_url}" 
                     alt="Afbeelding"
                     class="message-image"
                     onclick="openChatImageModal('${messageData.media_info.media_url}')">
            </div>
        `;
    }
    
    let textHtml = '';
    if (messageData.message_text && messageData.message_text.trim()) {
        textHtml = `<p class="message-text">${messageData.message_text.replace(/\n/g, '<br>')}</p>`;
    }
    
    const messageHtml = `
        <div class="${messageClass}" data-message-id="${messageData.id}">
            <div class="message-avatar-container">
                <img src="${messageData.sender_avatar_url}" 
                     alt="Avatar" 
                     class="message-avatar">
            </div>
            
            <div class="message-bubble">
                <div class="message-content">
                    ${mediaHtml}
                    ${textHtml}
                </div>
                
                <div class="message-footer">
                    <span class="message-time">Nu</span>
                    ${isOwn ? '<span class="message-status">✓</span>' : ''}
                </div>
            </div>
        </div>
    `;
    
    // Add to container
    messagesList.insertAdjacentHTML('beforeend', messageHtml);
    
    // Scroll to bottom
    scrollToBottom();
    
    console.log('✅ Core Chat: Message added successfully');
}

// ===== 3. NIEUWE FUNCTIE: Image Modal Support =====
function initImageModal() {
    // Make modal functions available globally
    window.openChatImageModal = function(imageSrc) {
        const modalIds = ['imageModal', 'hyves-image-modal'];
        const imageIds = ['modalImage', 'hyves-modal-image'];
        
        let modal = null;
        let modalImage = null;
        
        // Find modal elements
        for (let i = 0; i < modalIds.length; i++) {
            modal = document.getElementById(modalIds[i]);
            if (modal) {
                modalImage = document.getElementById(imageIds[i]);
                break;
            }
        }
        
        if (modal && modalImage) {
            modalImage.src = imageSrc;
            modal.style.display = 'flex';
        }
    };

    window.closeChatImageModal = function() {
        const modalIds = ['imageModal', 'hyves-image-modal'];
        
        modalIds.forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.style.display = 'none';
            }
        });
    };

    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeChatImageModal();
        }
    });

    // Click outside to close modal
    document.addEventListener('click', function(e) {
        const modals = ['imageModal', 'hyves-image-modal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && e.target === modal) {
                window.closeChatImageModal();
            }
        });
    });

    console.log("✅ Image modal initialized for", chatState.currentInterface);
}

/**
 * Set form disabled state
 */
function setFormDisabled(disabled) {
    const { messageInput, sendButton } = chatState.elements;
    
    messageInput.disabled = disabled;
    sendButton.disabled = disabled;
    
    if (disabled) {
        if (chatState.currentInterface === 'theme') {
            sendButton.innerHTML = '⏳ Bezig...';
        } else {
            sendButton.textContent = '⏳';
        }
    } else {
        if (chatState.currentInterface === 'theme') {
            sendButton.innerHTML = '📩 Versturen';
        } else {
            sendButton.textContent = '➤';
        }
    }
}

/**
 * Reset form to initial state
 */
function resetForm() {
    const { messageInput, sendButton, fileInput, charCounter } = chatState.elements;
    
    messageInput.value = '';
    messageInput.style.height = 'auto';
    messageInput.disabled = false;
    
    sendButton.disabled = true;
    
    if (chatState.currentInterface === 'theme') {
        sendButton.innerHTML = '📩 Versturen';
    } else {
        sendButton.textContent = '➤';
    }
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (charCounter) {
        if (chatState.currentInterface === 'theme') {
            charCounter.textContent = '0/1000';
        } else {
            charCounter.textContent = '0';
        }
        charCounter.style.color = '#666';
    }
    
    // Hide photo preview (theme specific)
    const photoPreview = document.getElementById('hyves-photo-preview');
    if (photoPreview) {
        photoPreview.style.display = 'none';
    }
    
    messageInput.focus();
}

/**
 * Start polling for new messages (conversation pages)
 */
function startMessagePolling() {
    if (!chatState.config.features || !chatState.config.features.real_time || chatState.pollingInterval) {
        return;
    }
    
    chatState.pollingInterval = setInterval(() => {
        pollForNewMessages();
    }, 3000);
    
    console.log("🔄 Message polling started");
}

/**
 * Poll for new messages (conversation pages)
 */
function pollForNewMessages() {
    const url = `${chatState.config.urls.poll}?friend_id=${chatState.config.friend_id}&last_message_id=${chatState.lastMessageId}`;
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.messages && data.messages.length > 0) {
            console.log("🔄 New messages received:", data.messages.length);
            appendNewMessages(data.messages);
            scrollToBottom();
        }
    })
    .catch(error => {
        console.error("🔄 Polling error:", error);
    });
}

/**
 * Append new messages to the conversation (conversation pages)
 */
function appendNewMessages(messages) {
    const messagesList = chatState.elements.messagesList || chatState.elements.messagesContainer;
    if (!messagesList) return;
    
    messages.forEach(message => {
        if (message.id > chatState.lastMessageId) {
            chatState.lastMessageId = message.id;
            // TODO: Create and append message element
        }
    });
}

/**
 * Scroll to bottom of messages (conversation pages)
 */
function scrollToBottom() {
    const container = chatState.elements.messagesContainer;
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

/**
 * ✅ NIEUWE HELPER FUNCTIE
 */
function ensureValidConfig() {
    if (!chatState.config) {
        chatState.config = {};
    }
    
    if (!chatState.config.features) {
        chatState.config.features = {
            real_time: false,
            search: true,
            emoji_picker: true,
            file_upload: true
        };
    }
    
    if (!chatState.config.urls && typeof window.SOCIALCORE_CHAT_CONFIG !== 'undefined') {
        chatState.config = {
            ...chatState.config,
            ...window.SOCIALCORE_CHAT_CONFIG,
            features: {
                ...chatState.config.features,
                ...window.SOCIALCORE_CHAT_CONFIG.features
            }
        };
    }
    
    // Set defaults if still missing
    if (!chatState.config.max_message_length) {
        chatState.config.max_message_length = 1000;
    }
    
    if (!chatState.config.max_file_size) {
        chatState.config.max_file_size = 2048;
    }
}

// ===== 4. NIEUWE FUNCTIE: Advanced Notification System =====
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.socialcore-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `socialcore-notification socialcore-notification-${type}`;
    notification.textContent = message;
    
    // Base styling
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '12px 16px',
        borderRadius: '8px',
        color: 'white',
        fontWeight: '500',
        fontSize: '14px',
        zIndex: '10001',
        maxWidth: '300px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        animation: 'slideInRight 0.3s ease-out',
        cursor: 'pointer'
    });
    
    // Type-specific styling
    switch(type) {
        case 'success':
            notification.style.background = 'linear-gradient(135deg, #25d366 0%, #20b358 100%)';
            break;
        case 'error':
            notification.style.background = 'linear-gradient(135deg, #ff4757 0%, #ff3742 100%)';
            break;
        case 'warning':
            notification.style.background = 'linear-gradient(135deg, #ffa726 0%, #ff9800 100%)';
            break;
        default:
            notification.style.background = 'linear-gradient(135deg, #667781 0%, #556572 100%)';
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Click to dismiss
    notification.addEventListener('click', function() {
        removeNotification(notification);
    });
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        removeNotification(notification);
    }, 3000);
    
    // Add animations if not already present
    addNotificationStyles();
}

function removeNotification(notification) {
    if (notification && notification.parentElement) {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.parentElement.removeChild(notification);
            }
        }, 300);
    }
}

function addNotificationStyles() {
    if (document.getElementById('socialcore-notification-styles')) return;
    
    const styles = document.createElement('style');
    styles.id = 'socialcore-notification-styles';
    styles.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(styles);
}

// ===== 6. NIEUWE FUNCTIE: Auto Resize Textarea =====
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

// ===== 7. NIEUWE FUNCTIE: Update Character Counter =====
function updateCharacterCounter() {
    const { messageInput, charCounter } = chatState.elements;
    
    if (!messageInput || !charCounter) return;
    
    const currentLength = messageInput.value.length;
    const maxLength = chatState.config.max_message_length || 1000;
    
    // Update counter text based on interface
    if (chatState.currentInterface === 'theme') {
        charCounter.textContent = `${currentLength}/${maxLength}`;
    } else {
        charCounter.textContent = currentLength;
    }
    
    // Color coding for character limit
    if (currentLength > maxLength) {
        charCounter.style.color = '#dc3545';
    } else if (currentLength > maxLength * 0.9) {
        charCounter.style.color = '#ffc107';
    } else if (currentLength > maxLength * 0.8) {
        charCounter.style.color = '#ff6b35';
    } else {
        charCounter.style.color = '#667781';
    }
}

// ===== 8. NIEUWE FUNCTIE: Update Send Button State =====
function updateSendButtonState() {
    const { messageInput, fileInput, sendButton } = chatState.elements;
    
    if (!messageInput || !sendButton) return;
    
    const hasText = messageInput.value.trim().length > 0;
    const hasFile = fileInput && fileInput.files.length > 0;
    const captionTextarea = document.getElementById('chatImageCaption');
    const hasCaption = captionTextarea && captionTextarea.value.trim().length > 0;
    
    const maxLength = chatState.config.max_message_length || 1000;
    const tooLong = messageInput.value.length > maxLength;
    
    sendButton.disabled = (!hasText && !hasFile && !hasCaption) || tooLong;
}

/**
 * Utility function to check if value is empty
 */
function empty(value) {
    return value === null || value === undefined || value === '';
}

/**
 * Initialize when DOM is ready
 */
// document.addEventListener('DOMContentLoaded', function() {
//     console.log("🔄 DOM Ready - checking for universal chat initialization");
    
//     // Wacht even tot alle elementen zeker beschikbaar zijn
//     setTimeout(() => {
//         initUniversalChat();
//     }, 50); // Korte delay om zeker te zijn dat alles geladen is
// });

// Export main function for manual initialization
//window.initUniversalChat = initUniversalChat;

console.log("✅ SocialCore Main Chat JavaScript Loaded");