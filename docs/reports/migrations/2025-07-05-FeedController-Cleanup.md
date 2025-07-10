# FeedController Cleanup Report

## 🗑️ **Te verwijderen: Uitgecommentarieerde methods**

### **1. addLike() method** (regels ~827-847)
```php
// private function addLike($postId, $userId) { ... }
```
**→ Gemigreerd naar:** `LikeService->addPostLike()`

### **2. removeLike() method** (regels ~852-872) 
```php
// private function removeLike($postId, $userId) { ... }
```
**→ Gemigreerd naar:** `LikeService->removePostLike()`

### **3. getLikeCount() method** (regels ~877-884)
```php
// private function getLikeCount($postId) { ... }
```
**→ Gemigreerd naar:** `LikeService->getPostLikeCount()`

### **4. addCommentLike() method** (regels ~1074-1090)
```php
// private function addCommentLike($commentId, $userId) { ... }
```
**→ Gemigreerd naar:** `LikeService->addCommentLike()`

### **5. removeCommentLike() method** (regels ~1095-1115)
```php
// private function removeCommentLike($commentId, $userId) { ... }
```
**→ Gemigreerd naar:** `LikeService->removeCommentLike()`

### **6. getCommentLikeCount() method** (regels ~1120-1127)
```php
// private function getCommentLikeCount($commentId) { ... }
```
**→ Gemigreerd naar:** `LikeService->getCommentLikeCount()`

### **7. detectSuspiciousLikeActivity() method** (regels ~1358-1371)
```php
// private function detectSuspiciousLikeActivity($userId, $userIP) { ... }
```
**→ Gemigreerd naar:** `LikeService->detectSuspiciousLikeActivity()`

### **8. Alle comment-gerelateerde methods** (regels ~948-1072)
```php
// private function saveComment($postId, $userId, $content) { ... }
// private function getCommentById($commentId) { ... }
// public function deleteComment() { ... }
```
**→ Gemigreerd naar:** `CommentService`

## 🔄 **Te verwijderen: Andere uitgecommentarieerde code**

### **1. Image upload methods** (regels ~717-784)
```php
// private function handleImageUpload() { ... }
// private function savePostMedia($post_id, $image_path, $file_data) { ... }
// private function handleSecureImageUpload() { ... }
```
**→ Gemigreerd naar:** `PostService->handleImageUpload()`

### **2. Link preview methods** (regels ~1144-1286)
```php
// private function processLinkPreview($content) { ... }
// private function getLinkPreviewFromCache($url) { ... }
// private function generateLinkPreview($url) { ... }
// private function fetchAndParseMetadata($url) { ... }
```
**→ Nog te migreren naar:** `LinkPreviewService` (toekomstige service)

### **3. Hashtag methods** (regels ~1380-1427)
```php
// private function getOrCreateHashtag($tag) { ... }
// private function linkHashtagsToPost($postId, $hashtags) { ... }
```
**→ Nog te migreren naar:** `HashtagService` (toekomstige service)

## ✅ **Behouden: Actieve methods die nog gebruikt worden**

### **Controller routing methods:**
- `index()` - Hoofdpagina weergave
- `create()` - Post creation (gebruikt PostService)
- `delete()` - Post deletion
- `toggleLike()` - Like routing (gebruikt LikeService) 
- `addComment()` - Comment routing (gebruikt CommentService)
- `toggleCommentLike()` - Comment like routing (gebruikt LikeService)

### **Helper methods:**
- `hasUserLikedPost()` - Gebruikt LikeService
- `getUserAvatar()` - Avatar helper
- `getCurrentUser()` - User data
- `formatDate()` - Date formatting
- `getCommentsForPosts()` - Comment display

### **Security methods:**
- `checkRateLimit()` - Rate limiting
- `logActivity()` - Activity logging
- `sanitizePostContent()` - Content security

## 📊 **Cleanup resultaat:**

**Voor cleanup:** ~1500+ regels code
**Na cleanup:** ~800-900 regels code  
**Verwijderd:** ~600-700 regels uitgecommentarieerde code
**Code reductie:** ~40-45%

## 🎯 **Architectuur na cleanup:**

```
FeedController (Thin routing layer)
├── index() → getAllPosts(), view rendering
├── create() → PostService->createPost()
├── delete() → Direct database + security
├── toggleLike() → LikeService->togglePostLike()
├── addComment() → CommentService->addComment()
└── toggleCommentLike() → LikeService->toggleCommentLike()
```

**Services die alle business logic bevatten:**
- `LikeService` - Alle like gerelateerde logica
- `CommentService` - Alle comment gerelateerde logica  
- `PostService` - Post creation en media handling

## 🚀 **Volgende migratie mogelijkheden:**

1. **HashtagService** - Voor hashtag processing
2. **LinkPreviewService** - Voor URL preview generation
3. **FeedService** - Voor timeline aggregation
4. **NotificationService** - Voor user notifications