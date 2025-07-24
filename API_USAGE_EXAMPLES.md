# HomeVideo API Usage Examples

## 📤 Upload a New Video (Admin Only)

### Using curl:
```bash
curl -X POST "http://your-domain.com/api/home-video" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json" \
  -F "title=Festival Highlights 2025" \
  -F "description=Amazing moments from our festival" \
  -F "video_file=@/path/to/your/video.mp4" \
  -F "thumbnail=@/path/to/your/thumbnail.jpg" \
  -F "is_active=true"
```

### Using JavaScript (FormData):
```javascript
const formData = new FormData();
formData.append('title', 'Festival Highlights 2025');
formData.append('description', 'Amazing moments from our festival');
formData.append('video_file', videoFile); // File object from input
formData.append('thumbnail', thumbnailFile); // File object from input
formData.append('is_active', 'true');

fetch('/api/home-video', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${adminToken}`,
    'Accept': 'application/json'
  },
  body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

## ✏️ Update Existing Video (Admin Only)

### Using curl:
```bash
curl -X PUT "http://your-domain.com/api/home-video/1" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -H "Accept: application/json" \
  -F "title=Updated Festival Video" \
  -F "description=Updated description" \
  -F "video_file=@/path/to/new/video.mp4" \
  -F "thumbnail=@/path/to/new/thumbnail.jpg"
```

## 📺 Get Active Video (Public Access)

### Using curl:
```bash
curl -X GET "http://your-domain.com/api/home-video" \
  -H "Accept: application/json"
```

### Response Format:
```json
{
  "success": true,
  "message": "Home video fetched successfully",
  "data": {
    "id": 1,
    "title": "Festival Highlights 2025",
    "description": "Amazing moments from our festival",
    "video_file": "home_videos/abc123.mp4",
    "thumbnail": "home_videos/thumb123.jpg",
    "is_active": true,
    "created_at": "2025-07-24T08:30:00.000000Z",
    "updated_at": "2025-07-24T08:30:00.000000Z",
    "thumbnail_url": "http://your-domain.com/storage/home_videos/thumb123.jpg",
    "video_url": "http://your-domain.com/storage/home_videos/abc123.mp4"
  }
}
```

## 🗂️ Supported Video Formats
- MP4 (.mp4)
- AVI (.avi)
- MOV (.mov)
- WMV (.wmv)
- FLV (.flv)
- WebM (.webm)
- MKV (.mkv)

## 📏 File Size Limits
- Video files: Maximum 50MB
- Thumbnail images: Maximum 2MB

## 🛡️ Authentication
- **Public endpoints**: No authentication required
- **Admin endpoints**: Require `Authorization: Bearer {token}` header with admin privileges
